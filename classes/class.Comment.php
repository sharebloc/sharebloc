<?php

require_once('class.BaseObject.php');
require_once('class.User.php');
require_once('class.Vote.php');

class Comment extends BaseObject {

    protected $data;
    protected $fields;
    protected $primary_key = 'comment_id';
    protected $table_name  = 'comment';
    protected $required    = array('post_id', 'post_type', 'user_id', 'comment_text');

    function Comment($comment_id = null) {
        parent::BaseObject($comment_id, null);

        if ($this->is_loaded()) {
            $this->load_vote();
        }
    }

    function load_vote() {
            $vote               = new Vote($this->data['comment_id'], 'comment', get_user_id());
            $this->data['vote'] = $vote->get();
    }

    function load($primary_id = null, $secondary_id = null) {
        parent::load($primary_id, $secondary_id);

        if (isset($this->data['comment_id'])) {
            if (isset($this->data['user_id']) && $this->data['user_id'] > 0) {
                $user               = new User($this->data['user_id']);
                $this->data['user'] = $user->get();
            }

            $this->load_vote();
            $this->load_vendors();

            // todo probably is not used and can be removed.
            $this->data['my_url'] = sprintf("/show_post.php?type=%s&id=%d#comment_%d",
                                            $this->data['post_type'],
                                            $this->data['post_id'],
                                            $this->data['comment_id']);
        }
    }

    function set($data) {
        // sanitizing html
        $data['comment_text'] = Utils::sanitizeHTML($data['comment_text']);
        return parent::set($data);
    }

    function delete() {
        global $db;

        $dquery = "DELETE FROM comment WHERE comment_id = '" . $db->escape_text($this->data['comment_id']) . "'";
        $db->query($dquery);

        $dquery = "DELETE FROM relation WHERE entity_type = 'comment' AND entity_id = '" . $db->escape_text($this->data['comment_id']) . "'";
        $db->query($dquery);

        $dquery = "DELETE FROM vote WHERE entity_type = 'comment' AND entity_id = '" . $db->escape_text($this->data['comment_id']) . "'";
        $db->query($dquery);

        $dquery = "DELETE FROM notifications WHERE comment_id = " . $this->data['comment_id'];
        $db->query($dquery);

        $this->recache();
    }

    function load_vendors() {
        global $db;

        $this->data['vendor_list']  = array();
        $this->data['vendor_count'] = 0;

        $custom_query = "SELECT DISTINCT v.vendor_id, v.vendor_name, v.code_name, r.is_review " .
                "FROM relation r " .
                "LEFT JOIN vendor v ON v.vendor_id = r.vendor_id " .
                "WHERE r.entity_id = '" . $db->escape_text($this->data['comment_id']) . "' " .
                "AND r.entity_type = 'comment' " .
                "GROUP BY v.vendor_id " .
                "ORDER BY v.vendor_name ASC";

        $vendor_list = new CustomList('vendor', $custom_query);
        $vendor_list->set_containing_entity('comment', $this->get_data('comment_id'));

        $this->data['vendor_list']  = $vendor_list->get();
        $this->data['vendor_count'] = count($this->data['vendor_list']);
    }

    function add_vendor($vendor_id, $is_review = 0) {
        global $db;

        if ($this->is_loaded()) {
            $data = array(
                'entity_id'     => $this->data['comment_id'],
                'entity_type'   => 'comment',
                'vendor_id'     => $vendor_id,
                'is_review'     => $is_review,
                'date_added'    => 'now()',
                'date_modified' => 'now()'
            );
            $db->insert('relation', $data, true);

            $db->insert_on_duplicate_key_update('relation', $data, array('entity_id', 'entity_type', ''), $update_additional_fields = array());
        }
    }

    function clear_vendors() {
        global $db;

        if ($this->is_loaded()) {
            $where = array(
                'entity_id'   => $this->data['comment_id'],
                'entity_type' => 'comment'
            );
            $db->delete('relation', $where, null, 1);
        }
    }

    function createNotification($user_id, $post_object) {
        $post_author_id = $post_object->get_data('user_id');

        $temp_user = new User($user_id);

        if (!$temp_user->is_loaded()) {
            Log::$logger->warn("Notification creation: User is not exist. user_id=" . $user_id);
            return;
        }

        $user_is_author = $post_author_id==$user_id;

        if ( ($user_is_author && !$temp_user->get_data('notify_post_responded')) ||
                (!$user_is_author && !$temp_user->get_data('notify_comment_responded')) ) {
            Log::$logger->debug("Notification is not created because of user email settings.");
            return;
        }

        $data = array();
        $data['user_id'] = $user_id;
        $data['post_type'] = $this->get_data('post_type');
        $data['post_id'] = $this->get_data('post_id');
        $data['comment_id'] = $this->get_data('comment_id');
        if ($user_is_author) {
            $data['reason'] = 'my_post_commented';
        } else {
            $data['reason'] = 'post_i_commented_commented';
        }
        Notification::insertNotification($data);

        $data['post_title'] = $post_object->get_title();
        $data['post_type_name'] = $post_object->post_type_name;
        $data['comment_text'] = $this->get_data('comment_text');
        $data['post_url'] = $post_object->get_data('my_url');
        $data['addressee']['first_name'] = $temp_user->get_data('first_name');
        $data['addressee']['last_name'] = $temp_user->get_data('last_name');
        $data['addressee']['email'] = $temp_user->get_data('email');
        $data['addressee']['code_name'] = $temp_user->get_data('code_name');
        $data['addressee']['user_url'] = $temp_user->get_data('my_url');

        if ($this->get_data('privacy')!=='public') {
            $data['author_full_name'] = "An anonymous user";
        } else {
            $data['author_full_name'] = get_user_full_name();
        }

        $mailer = new Mailer('notification');
        $mailer->sendNotification($data);
    }

    function updateNotifications($users_to_notify, $post_object) {
        require_once('class.Notification.php');
        require_once('class.Mailer.php');
        $current_user_id = get_user_id();

        foreach ($users_to_notify as $user_id) {
            if ($user_id == $current_user_id) {
                continue;
            }
            $this->createNotification($user_id, $post_object);
        }
    }

    function processPostedComment($data = array()) {

        if(!$data) {
            $data = get_input('data');
        }

        $data['comment_text'] = trim($data['comment_text']);

        if (!$data['comment_text']) {
            return "Comment text can not be empty";
        }

         if (!is_logged_in()) {
            $_SESSION['posted_comment'] = $data;
            return true;
        }

        $data['user_id'] = get_user_id();

        if (!$this->set($data)) {
            return "Unknown server error. Please try again.";
        }

        $entity_id = $this->save();

        if ($entity_id) {
            $vote = new Vote($entity_id, 'comment', get_user_id());
            $vote->set_user_vote(1);
        }

        $post_id = $data['post_id'];
        $post_type = $data['post_type'];

        $temp_entity = null;

        switch ($post_type) {
            case 'posted_link':
                $temp_entity = new PostedLink($post_id);
                break;
            case 'question':
                $temp_entity = new Question($post_id);
                break;
        }

        if (!$temp_entity->is_loaded()) {
            Log::$logger->error("Can't load post when commenting, type=$post_type, id=$post_id");
            return true;
        }

        $temp_entity->recache();

        // notifications
        $post_author_id = $temp_entity->get_data('user_id');
        $users_to_notify = $temp_entity->getCommentators();
        $users_to_notify[$post_author_id] = $post_author_id;

        $this->updateNotifications($users_to_notify, $temp_entity);
        return true;
    }

}

?>