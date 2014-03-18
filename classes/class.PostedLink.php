<?php

require_once('class.BaseObject.php');
require_once('class.User.php');
require_once('class.Vote.php');
require_once('class.VoteContest.php');

class PostedLink extends BaseObject {

    protected $data;
    protected $fields;
    protected $primary_key   = 'post_id';
    protected $secondary_key = 'code_name';
    protected $table_name    = 'posted_link';
    protected $required      = array('url', 'title');
    private $tag_list;
    public $logo;
    public static $logo_hash_suffix = '_l';
    public $post_type = 'posted_link';
    public $post_type_name = 'post';
    const codename_url_prefix = '/posted_links/';
    const MAX_SYMBOLS_FOR_TEXT = 500;

    function PostedLink($post_id = null, $code_name = null, $f_contest = 0) {
        $tag_type = 'vendor';
        if ($f_contest) {
            $tag_type = 'contest';
        }

        $this->tag_list      = new TagList($tag_type);
        parent::BaseObject($post_id, $code_name);
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
        if ($this->data['f_contest']) {
            $vote = new VoteContest($this->data['post_id'], get_user_id());
            $this->data['vote'] = $vote->getVotesForPost();
        } else {
            $vote = new Vote($this->data['post_id'], 'posted_link', get_user_id());
            $this->data['vote'] = $vote->get();
        }
    }

    function load($primary_id = null, $secondary_id = null) {
        parent::load($primary_id, $secondary_id);

        if (isset($this->data['post_id'])) {
            if (isset($this->data['user_id']) && $this->data['user_id'] > 0) {
                $user = new User($this->data['user_id']);
                $this->data['user'] = $user->get();
            }

            $tag_type = 'vendor';
            if ($this->data['f_contest']) {
                $tag_type = 'contest';
                // we have to reload it as we can't load right list on postedlink creation
                $this->tag_list = new TagList($tag_type);
                $this->data['my_url_share'] = "/".Utils::$contest_urls[$this->data['f_contest']]."?post=" . $this->data['post_id'];
            }
            $this->tag_list->set_selection_criteria('tag_selection', array('entity_id'   => $this->data['post_id'], 'entity_type' => 'posted_link', 'tag_type'    => $tag_type), 'tag_id');
            $this->tag_list->load_selections();
            $this->data['tag_list']         = $this->tag_list->get_selections_list();
            $this->data['tag_list_details'] = $this->tag_list->get_selections_list_details();

            $this->load_vote();
            $this->load_vendors();
            $this->load_reposters();

            $this->data['logo_hash'] = '';
            $this->data['logo_url_thumb'] = '';
            $this->data['logo_url_full'] = '';
            if (isset($this->data['logo_id']) && $this->data['logo_id'] > 0) {
                $this->logo = new Logo($this->data['logo_id']);
                $this->data['logo_hash'] = $this->logo->get_hash();
                $this->data['logo_url_thumb'] = $this->logo->get_data('url_thumb');
                $this->data['logo_url_full'] = $this->logo->get_data('url_full');
                $this->data['logo_url_src'] = $this->logo->get_data('url_src');
                $logo_height = $this->logo->get_data('height');
                if (!$logo_height) {
                    $logo_height = 1;
                }
                $this->data['logo_ratio'] = $this->logo->get_data('width')/$logo_height;
            }
            $this->data['my_url'] = $this->getUrl();
            $this->data['iframe_url'] = $this->getIframeUrl();
        }
    }

    function set($data) {
        if (empty($data['logo_id']) && !empty($data['logo_hash'])) {
            $logo            = new Logo(null, $data['logo_hash']);
            $data['logo_id'] = $logo->get_data('logo_id');
            unset($logo);
        }

        // sanitizing html
        if (empty($data['image_upload'])) {
            // just because sanitizer includes in wrong path for uploadify - todo fix
            $data['text'] = Utils::sanitizeHTML($data['text']);
        }

        $result = parent::set($data);

        if ($result == true) {
            $this->data['code_name'] = $this->generate_code_name(substr($this->data['title'], 0, 80), ( isset($this->data['post_id']) ? $this->data['post_id'] : null));
        }

        if (!is_object($this->tag_list) && isset($this->data['post_id']) && $this->data['post_id'] > 0) {
            $tag_type = 'vendor';
            if ($this->data['f_contest']) {
                $tag_type = 'contest';
            }
            $this->tag_list = new TagList($tag_type);
            $this->tag_list->set_selection_criteria('tag_selection', array('entity_id'   => $this->data['post_id'], 'entity_type' => 'posted_link', 'tag_type'    => $tag_type), 'tag_id');
        }

        if (!isset($data['tag_list']) || !is_array($data['tag_list'])) {
            $data['tag_list'] = array();
        }
        $this->tag_list->set_selections($data['tag_list']);

        if (!empty($data['logo_hash'])) {
            $logo = new Logo(null, $data['logo_hash']);
            if ($logo->get_data('logo_hash') !== $this->data['code_name'] . $this::$logo_hash_suffix) {
                $logo->rename($this->data['code_name'] . $this::$logo_hash_suffix);
            }

            $this->data['logo_id'] = $logo->get_data('logo_id');
        }

        return $result;
    }

    function save_data() {
        $primary_id = parent::save_data();

        if ($primary_id > 0) {
            $tag_type = 'vendor';
            if ($this->data['f_contest']) {
                $tag_type = 'contest';
            }
            $this->tag_list->set_selection_criteria('tag_selection', array('entity_id'   => $primary_id, 'entity_type' => 'posted_link', 'tag_type'    => $tag_type), 'tag_id');
            $this->tag_list->save_selections();
        }

        return $primary_id;
    }

    // used only to process old reviews and questions which have empty title fields.
    // should be removed later
    function get_title() {
        return $this->get_data('title');
    }

    function getIframeUrl() {
        $iframe_url = '/';
        if ($this->is_loaded()) {
            $iframe_url = "/links/".$this->data['code_name'];
        }
        return $iframe_url;
    }

    function delete() {
        global $db;

        $dquery = "DELETE FROM posted_link WHERE post_id = '" . $db->escape_text($this->data['post_id']) . "'";
        $db->query($dquery);

        $dquery = "DELETE FROM comment WHERE post_type='posted_link' AND post_id = '" . $db->escape_text($this->data['post_id']) . "'";
        $db->query($dquery);

        $dquery = "DELETE FROM relation WHERE entity_type = 'posted_link' AND entity_id = '" . $db->escape_text($this->data['post_id']) . "'";
        $db->query($dquery);

        $dquery = "DELETE FROM vote WHERE entity_type = 'posted_link' AND entity_id = '" . $db->escape_text($this->data['post_id']) . "'";
        $db->query($dquery);

        $dquery = "DELETE FROM tag_selection WHERE entity_type = 'posted_link' AND entity_id = '" . $db->escape_text($this->data['post_id']) . "'";
        $db->query($dquery);

        $dquery = "DELETE FROM notifications WHERE post_type='posted_link' and post_id = " . $this->data['post_id'];
        $db->query($dquery);

        $this->recache();
    }

    // todo copied from Question method, should be refactored bacause is overcomplicated
    function load_comments() {
        global $db;

        $this->data['comment_list']  = array();
        $this->data['comment_count'] = 0;

        $self_or = Utils::getSelfOr('a');

        $custom_query = "SELECT DISTINCT a.comment_id " .
                "FROM comment a " .
                "WHERE a.post_id = '" . $db->escape_text($this->data['post_id']) . "' " .
                "AND a.post_type = 'posted_link' " .
                "AND ( a.status IN ( 'active', 'review' ) $self_or ) " .
                "GROUP BY a.comment_id " .
                "ORDER BY a.date_added ASC";

        $comment_list = new CustomList('comment', $custom_query);
        $comment_list->set_containing_entity('posted_link', $this->get_data('post_id'));

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
                "WHERE r.entity_id = '" . $db->escape_text($this->data['post_id']) . "' " .
                "AND r.entity_type = 'posted_link' " .
                "GROUP BY v.vendor_id " .
                "ORDER BY v.vendor_name ASC";

        $vendor_list = new CustomList('vendor', $custom_query);
        $vendor_list->set_containing_entity('posted_link', $this->get_data('post_id'));

        $this->data['vendor_list']  = $vendor_list->get();
        $this->data['vendor_count'] = count($this->data['vendor_list']);
    }

    function add_vendor($vendor_id) {
        global $db;

        if ($this->is_loaded()) {
            $data = array(
                    'entity_id'     => $this->data['post_id'],
                    'entity_type'   => 'posted_link',
                    'vendor_id'     => $vendor_id,
                    'date_added'    => 'now()',
                    'date_modified' => 'now()');
            $db->insert('relation', $data, true);
        }
    }

    function clear_vendors() {
        global $db;

        if ($this->is_loaded()) {
            $where = array(
            'entity_id'   => $this->data['post_id'],
            'entity_type' => 'posted_link'
            );
            $db->delete('relation', $where, null, 1);
        }
    }

    static function getPostDataFromRequest() {
        $data = array();
        $data['title'] = trim(get_input('title'));
        $data['url'] = trim(get_input('url'));
        $data['text'] = trim(get_input('text'));
        $data['privacy'] = get_input('f_anonym') ? 'anonymous' : 'public';
        $category = get_input('category');
        $subcategory = get_input('subcategory');
        $data['vendor_list'] = get_input('vendors');
        $data['tweet_after_post'] = get_input('tweet_after_post');

        $data['f_contest'] = 0;
        $post_type = get_input('post_type');
        if ($post_type=='contest') {
            $data['f_contest'] = get_input('contest_id');
            $data['author_name'] = trim(get_input('author_name'));
            $data['force_user_id'] = intval(get_input('force_user_id'));
        }

        $data['image'] = '';
        if (!trim(get_input('no_thumb'))) {
            $data['image'] = trim(get_input('image'));
        }

        $data['tag_list'] = array();
        if ($category) {
            $data['tag_list'][] = $category;
        }

        if ($subcategory) {
            $data['tag_list'][] = $subcategory;
        }
        return $data;
    }

    function processPostedLink($data = array()) {
        if(!$data) {
            $data = $this::getPostDataFromRequest();
        }

        if (!$data['title'] || !$data['url'] || !$data['tag_list']) {
            if (!($data['f_contest'] == Utils::CONTEST_MARKETO_ID)) {
                return "Title, URL and main tag fields are required";
            } elseif (!$data['title'] || !$data['url']) {
                return "Title and URL fields are required";
            }
        }

        if (!is_logged_in()) {
            $_SESSION['posted_link_data'] = $data;
            return true;
        }

        $existing_post = self::getPostedLinkByUrl($data['url']);
        if ($existing_post) {
            $existing_post_obj = new PostedLink($existing_post['post_id']);
            $existing_post_obj->repost(true);
            // as callee use my_url for redirect then
            $this->data['my_url'] = $existing_post_obj->get_data('my_url');
            $this->data['iframe_url'] = $existing_post_obj->get_data('iframe_url');
            return true;
        }

        if ($data['f_contest'] && is_admin() && $data['force_user_id']) {
            $data['user_id'] = $data['force_user_id'];
        } else {
            $data['user_id'] = get_user_id();
        }

        if (!$this->set($data)) {
            return "Unknown server error. Please try again.";
        }
        $entity_id = $this->save();

        if (!$entity_id) {
            Log::$logger->error("Error while saving a new entity, data = " . print_r($data, true));
            return "Unknown server error. Please try again.";
        }

        if ($data['tweet_after_post'] || $data['f_contest']==Utils::CONTEST_MARKETO_ID) {
            $_SESSION['tweet_after_post'] = 1;
        }

        if ($data['f_contest'] == Utils::CONTEST_MARKETO_ID) {
            $f_get_sponsor_email = Utils::reqParam('f_get_sponsor_email');
            if ($f_get_sponsor_email !== null) {
                if (!($f_get_sponsor_email == 1)) {
                    $f_get_sponsor_email = 0;
                }
                User::saveUpdatedFGetSponsorEmail($f_get_sponsor_email);
            }
        }

        if ($this->data['f_contest']) {
            $vote = new VoteContest($entity_id, $this->get_data('user_id'));
            $vote->save(1);
        } else {
            $vote = new Vote($entity_id, $this->post_type, get_user_id());
            $vote->set_user_vote(1);
        }

        $this->clear_vendors();

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

        if ($data['image']) {
            $this->saveLogoByUrl($data['image']);
        }

        $this->recache();
        return true;
    }

    /* Processing RSS crawled posts */
    static function getPostDataFromRSS($feed, $rss_post) {
        $data = $rss_post;

        if ($feed['entity_type']=='vendor') {
            $data['vendor_list'][] = $feed['entity_id'];
            $data['user_id'] = 0;
            $data['author_vendor_id'] = $feed['entity_id'];
        } else {
            $data['vendor_list'] = array();
            $data['user_id'] = $feed['entity_id'];
            $data['author_vendor_id'] = 0;
        }

        $data['tag_list'][] = $feed['tag_id'];

        return $data;
    }

    function publishPostFromRSS($feed, $rss_post) {
        $data = self::getPostDataFromRSS($feed, $rss_post);

        $data['text'] = trim(Utils::sanitizeHTML($data['text']));

        if (strlen($data['text']) > self::MAX_SYMBOLS_FOR_TEXT) {
            $data['text'] = substr($data['text'], 0, self::MAX_SYMBOLS_FOR_TEXT);
            // now we can get a broken link, so we have to sanitize again
            $data['text'] = trim(Utils::sanitizeHTML($data['text']));
            // and only now we can add '(...)'
            $data['text'] .= ' (...)';
        }

        $data['f_auto'] = 1;

        $DAVID_USER_ID = 2;

        if (!$this->set($data)) {
            return false;
        }
        $entity_id = $this->save();

        if (!$entity_id) {
            return false;
        }

        $vote_by = $DAVID_USER_ID;
        if ($data['user_id']) {
            $vote_by = $data['user_id'];
        }
        $vote = new Vote($entity_id, $this->post_type, $vote_by);
        $vote->set_user_vote(1);

        $this->clear_vendors();

        if ($data['author_vendor_id']) {
                $vendor = new Vendor($data['author_vendor_id']);
                if ($vendor->is_loaded()) {
                    $this->add_vendor($data['author_vendor_id']);
                    $vendor->recache();
                }
        }

        if ($data['image']) {
            $this->saveLogoByUrl($data['image']);
        }

        $this->recache();
        return true;
    }

    /* End of Processing RSS crawled posts */

    public static function getPostedLinkByUrl($url) {
        $sql = sprintf("SELECT post_id, code_name FROM posted_link
                        WHERE url = '%s'
                        LIMIT 1",
                        Database::escapeString($url));
        $result = Database::execArray($sql, true);
        return $result;
    }
}

?>