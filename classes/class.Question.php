<?php

require_once('class.BaseObject.php');
require_once('class.User.php');
require_once('class.Comment.php');
require_once('class.Vote.php');

class Question extends BaseObject {

    protected $data;
    protected $fields;
    protected $primary_key   = 'question_id';
    protected $secondary_key = 'code_name';
    protected $table_name    = 'question';
    protected $required      = array('user_id');
    // question_text was removed from $required for the compatibility with new "Ask a question" page
    private $tag_list;
    public $post_type = 'question';
    public $post_type_name = 'question';
    const codename_url_prefix = '/questions/';

    function Question($question_id = null, $code_name = null) {
        $this->tag_list      = new TagList('vendor');
        parent::BaseObject($question_id, $code_name);
        $this->fields['tag_list']      = array('type'    => 'list', 'options' => $this->tag_list->get_options_list('tag_id', array('tag_id', 'tag_name', 'parent_tag_id')));
        if ($this->is_loaded()) {
            $this->load_vote();
            $this->data['reposted_by_curr_user'] = $this->isRepostedByCurrentUser();
            $this->data['curr_user_is_author'] = 0;
            if (is_logged_in() && $this->data['user_id']==  get_user_id()) {
                $this->data['curr_user_is_author'] = 1;
            }
        }
    }

    function load_vote() {
            $vote = new Vote($this->data['question_id'], 'question', get_user_id());
            $this->data['vote'] = $vote->get();
    }

    function load($primary_id = null, $secondary_id = null) {
        parent::load($primary_id, $secondary_id);

        if (isset($this->data['question_id'])) {
            if (isset($this->data['user_id']) && $this->data['user_id'] > 0) {
                $user               = new User($this->data['user_id']);
                $this->data['user'] = $user->get();
            }

            $this->tag_list->set_selection_criteria('tag_selection', array('entity_id'   => $this->data['question_id'], 'entity_type' => 'question', 'tag_type'    => 'vendor'), 'tag_id');
            $this->tag_list->load_selections();
            $this->data['tag_list']         = $this->tag_list->get_selections_list();
            $this->data['tag_list_details'] = $this->tag_list->get_selections_list_details();

            $this->load_vote();
            $this->load_vendors();
            $this->load_reposters();

            $this->data['comment_list']  = array();
            $this->data['comment_count'] = 0;

            $this->data['my_url'] = $this->getUrl();
        }
    }

    function set($data) {
        // sanitizing html
        $data['question_text'] = Utils::sanitizeHTML($data['question_text']);

        $result = parent::set($data);

        if ($result == true) {
            $this->data['code_name'] = $this->generate_code_name(substr($this->data['question_title'], 0, 80), ( isset($this->data['question_id']) ? $this->data['question_id'] : null));

            if (!is_object($this->tag_list) && isset($this->data['question_id']) && $this->data['question_id'] > 0) {
                $this->tag_list = new TagList('vendor');
                $this->tag_list->set_selection_criteria('tag_selection', array('entity_id'   => $this->data['question_id'], 'entity_type' => 'question', 'tag_type'    => 'vendor'), 'tag_id');
            }

            if (!isset($data['tag_list']) || !is_array($data['tag_list'])) {
                $data['tag_list'] = array();
            }
            $this->tag_list->set_selections($data['tag_list']);
        }

        return $result;
    }

    function save_data() {
        $primary_id = parent::save_data();

        if ($primary_id > 0) {
            $this->tag_list->set_selection_criteria('tag_selection', array('entity_id'   => $primary_id, 'entity_type' => 'question', 'tag_type'    => 'vendor'), 'tag_id');
            $this->tag_list->save_selections();
        }

        return $primary_id;
    }

    // used only to process old reviews and questions which have empty title fields.
    // should be removed later
    function get_title() {
        $post_title = $this->get_data('question_title');
        if (!$post_title) {
            $post_title = $this->get_data('question_text');
        }
        return $post_title;
    }

    function delete() {
        global $db;

        $dquery = "DELETE FROM question WHERE question_id = '" . $db->escape_text($this->data['question_id']) . "'";
        $db->query($dquery);

        $dquery = "DELETE FROM comment WHERE post_type='question' AND post_id = '" . $db->escape_text($this->data['question_id']) . "'";
        $db->query($dquery);

        $dquery = "DELETE FROM relation WHERE entity_type = 'question' AND entity_id = '" . $db->escape_text($this->data['question_id']) . "'";
        $db->query($dquery);

        $dquery = "DELETE FROM vote WHERE entity_type = 'question' AND entity_id = '" . $db->escape_text($this->data['question_id']) . "'";
        $db->query($dquery);

        $dquery = "DELETE FROM notifications WHERE post_type='question' and post_id = " . $this->data['question_id'];
        $db->query($dquery);

        $this->recache();
    }

    function load_comments() {
        global $db;

        $this->data['comment_list']  = array();
        $this->data['comment_count'] = 0;

        $self_or = Utils::getSelfOr('a');

        $custom_query = "SELECT DISTINCT a.comment_id " .
                "FROM comment a " .
                "WHERE a.post_id = '" . $db->escape_text($this->data['question_id']) . "' " .
                "AND a.post_type = 'question' " .
                "AND ( a.status IN ( 'active', 'review' ) $self_or ) " .
                "GROUP BY a.comment_id " .
                "ORDER BY a.date_added ASC";

        $comment_list = new CustomList('comment', $custom_query);
        $comment_list->set_containing_entity('question', $this->get_data('question_id'));

        $comment_list->set_sort('relevance');
        $comment_list->sort();

        if (count($comment_list) > 0) {
            $this->data['comment_list'] = $comment_list->get();

            BaseObject::replace_privacy($this->data['comment_list']);

            if (isset($this->data['comment_list']) && count($this->data['comment_list']) > 0) {
                foreach ($this->data['comment_list'] AS $k => $v) {
                    if (in_array($v['status'], array('active', 'review'))) {
                        $this->data['comment_count']++;
                    }
                }
            }
        }
    }

    function load_vendors() {
        global $db;

        $this->data['vendor_list']  = array();
        $this->data['vendor_count'] = 0;

        $custom_query = "SELECT DISTINCT v.vendor_id, v.vendor_name, v.code_name " .
                "FROM relation r " .
                "LEFT JOIN vendor v ON v.vendor_id = r.vendor_id " .
                "WHERE r.entity_id = '" . $db->escape_text($this->data['question_id']) . "' " .
                "AND r.entity_type = 'question' " .
                "GROUP BY v.vendor_id " .
                "ORDER BY v.vendor_name ASC";

        $vendor_list = new CustomList('vendor', $custom_query);
        $vendor_list->set_containing_entity('question', $this->get_data('question_id'));

        $this->data['vendor_list']  = $vendor_list->get();
        $this->data['vendor_count'] = count($this->data['vendor_list']);
    }

    /*
     * When the "also viewed" table is empty for this question, a random list is used.
     * @modified 18 Apr 2013 by bear@deepshiftlabs.com - switched from the random questions to also viewed selecting*
     */

    function get_related_questions() {
        if (!isset($this->data['vendor_list']) || count($this->data['vendor_list']) == 0) {
            $this->load_vendors();
        }

        $related_questions = array();
        $also_viewed       = new AlsoViewed('question', $this->get_data('question_id'));
        $also_viewed_ids   = $also_viewed->get_also_viewed_ids();

        if (count($also_viewed_ids)) {
            $question_list     = new GenericList('question', array(), array('question_id' => $also_viewed_ids), array(), null, null);
            $related_questions = $question_list->get();
        } else {
            // using previous version of get_related_questions() (randomly)
            $custom_query      = "SELECT q.question_id " .
                    "FROM question q " .
                    "ORDER BY rand() LIMIT 5";
            $question_list     = new CustomList('question', $custom_query);
            $question_list->set_containing_entity('question', $this->get_data('question_id'));
            $related_questions = $question_list->get();
        }
        return $related_questions;
    }

    function add_vendor($vendor_id) {
        global $db;

        if ($this->is_loaded()) {
            $data = array(
            'entity_id'     => $this->data['question_id'],
            'entity_type'   => 'question',
            'vendor_id'     => $vendor_id,
            'date_added'    => 'now()',
            'date_modified' => 'now()'
            );
            $db->insert('relation', $data, true);
        }
    }

    function clear_vendors() {
        global $db;

        if ($this->is_loaded()) {
            $where = array(
            'entity_id'   => $this->data['question_id'],
            'entity_type' => 'question'
            );
            $db->delete('relation', $where, null, 1);
        }
    }

    // added by bear@deepshiftlabs.com, used to get questions stream on home page
    function get_last_questions($limit = 5) {
        global $db;
        $last_questions = array();

        $custom_query = sprintf("SELECT q.question_id " .
                "FROM question q " .
                "ORDER BY question_id DESC LIMIT %d", $limit);

        $question_list = new CustomList('question', $custom_query);

        $last_questions = $question_list->get();

        return $last_questions;
    }

    function get_seo_attributes() {
        $seo_attributes                = array();
        $seo_attributes['title']       = '';
        $seo_attributes['keywords']    = '';
        $seo_attributes['description'] = '';

        if ($this->is_loaded()) {
            $seo_attributes['title']    = $this->data['question_title'];
            $seo_attributes['keywords'] = "";

            $tag_names = array();

            if (is_array($this->data['vendor_list'])) {
                foreach ($this->data['vendor_list'] AS $vendor_detail) {
                    // todo added isset by bear 22 feb 2013, but this should be fixed using empty array on data load
                    // see also fix in question.tpl
                    if (isset($vendor_detail['tag_list_details'])) {
                        foreach ($vendor_detail['tag_list_details'] AS $tag_detail) {
                            if (!empty($seo_attributes['keywords']))
                                $seo_attributes['keywords'] .= ", ";

                            $seo_attributes['keywords'] .= $tag_detail['tag_name'];
                        }
                    }
                }
                foreach ($this->data['vendor_list'] AS $vendor_detail) {
                    $seo_attributes['keywords'] .= ", " . $vendor_detail['vendor_name'];
                }
            }

            if (strlen($this->data['question_text']) > 3) {
                if (strlen($this->data['question_text']) < 100) {
                    $seo_attributes['description'] = $this->data['question_text'];
                } else {
                    $pos                           = strpos($this->data['question_text'], ' ', 100);
                    $seo_attributes['description'] = substr($this->data['question_text'], 0, $pos) . "...";
                }
            } else {
                $seo_attributes['description'] = $this->data['question_text'];
            }
        }

        return $seo_attributes;
    }

    static function getPostDataFromRequest() {
        $data = array();
        $data['question_title'] = trim(get_input('title'));
        $data['question_text'] = trim(get_input('text'));
        $data['privacy'] = get_input('f_anonym') ? 'anonymous' : 'public';
        $category = get_input('category');
        $subcategory = get_input('subcategory');
        $data['vendor_list'] = get_input('vendors');
        $data['tweet_after_post'] = get_input('tweet_after_post');

        $data['tag_list'] = array();
        if ($category) {
            $data['tag_list'][] = $category;
        }
        if ($subcategory) {
            $data['tag_list'][] = $subcategory;
        }
        return $data;
    }

    function processPostedQuestion($data = array()) {
        if(!$data) {
            $data = $this::getPostDataFromRequest();
        }

        if (is_logged_in()) {
            $data['user_id'] = $_SESSION['user_info']['user_id'];
        } else {
            $_SESSION['posted_question_data'] = $data;
            return true;
        }

        if (!$data['question_title'] || !$data['tag_list']) {
            return "Title and main tag fields are required";
        }

        if (!$this->set($data)) {
            return "Unknown server error. Please try again.";
        }

        $entity_id = $this->save();

        if (!$entity_id) {
            Log::$logger->error("Error while saving a new entity, data = " . print_r($data, true));
            return "Unknown server error. Please try again.";
        }

        if ($data['tweet_after_post']) {
            $_SESSION['tweet_after_post'] = 1;
        }

        $vote = new Vote($entity_id, $this->post_type, get_user_id());
        $vote->set_user_vote(1);

        if ($data['vendor_list']) {
            $vendor_list = array();
            foreach ($data['vendor_list'] AS $vendor_id) {
                $vendor = new Vendor($vendor_id);
                if ($vendor->is_loaded()) {
                    $this->add_vendor($vendor_id);
                    $vendor->recache();
                    $vendor_list[] = $vendor;
                }
            }
            Utils::saveImplicitTagsForPost($entity_id, $this->post_type, $vendor_list, $data['tag_list']);
        }

        $this->recache();
        return true;
    }
}

?>