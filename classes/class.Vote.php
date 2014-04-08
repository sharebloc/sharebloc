<?php

require_once('class.BaseObject.php');

class Vote {

    private $table_name = 'vote';
    private $entity_id;
    private $entity_type;
    private $user_id    = 0;
    private $data;
    private $allowed_entity_types = array('question', 'comment', 'posted_link');

    function Vote($entity_id = null, $entity_type = null, $user_id = null) {
        if ($entity_id > 0 && is_numeric($entity_id) && in_array($entity_type, $this->allowed_entity_types)) {
            $this->load($entity_id, $entity_type, $user_id);
        }
    }

    function load($entity_id, $entity_type, $user_id = null) {
        global $db;

        if (isset($entity_id) && $entity_id > 0 && in_array($entity_type, $this->allowed_entity_types)) {
            $this->entity_id   = $entity_id;
            $this->entity_type = $entity_type;

            $this->data = array('total' => 0);

            if ($user_id > 0 && is_numeric($user_id)) {
                $this->user_id = $user_id;
            }

            $this->data['count'] = 0;
            $this->data['total'] = 0;

            $squery = "SELECT COUNT(v.vote_id) AS count, SUM(v.value) AS total " .
                    "FROM vote v " .
                    "WHERE v.entity_id = '" . $db->escape_text($this->entity_id) . "' AND v.entity_type = '" . $db->escape_text($this->entity_type) . "' " .
                    "GROUP BY v.entity_id";

            $sresult = $db->query($squery);

            if (is_array($sresult) && count($sresult) > 0) {
                $this->data = $sresult[0];
            }

            $this->data['user_vote'] = 0;
            if ($this->user_id) {
                $where   = array('entity_id'   => $this->entity_id, 'entity_type' => $this->entity_type, 'user_id'     => $this->user_id);
                $sresult = $db->select($this->table_name, null, null, $where, null);
                if (is_array($sresult) && count($sresult) > 0) {
                    $this->data['user_vote'] = $sresult[0]['value'];
                }
            }
        }
    }
//This also looks important
    function set_user_vote($value) {
        global $db;

        if (isset($this->entity_id) && $this->entity_id > 0 && in_array($this->entity_type, $this->allowed_entity_types) && isset($this->user_id) && $this->user_id > 0 && is_numeric($value)) {
            $data = array(
                'entity_id'     => $this->entity_id,
                'entity_type'   => $this->entity_type,
                'user_id'       => $this->user_id,
                'value'         => $value,
                'date_added'    => 'now()',
                'date_modified' => 'now()'
            );
            $db->insert_on_duplicate_key_update($this->table_name, $data, array('entity_id', 'entity_type', 'user_id', 'date_added'));
        }
    }

    function clear_user_vote() {
        global $db;

        if (isset($this->entity_id) && $this->entity_id > 0 && in_array($this->entity_type, $this->allowed_entity_types) && isset($this->user_id) && $this->user_id > 0) {
            $where = array(
                'entity_id'   => $this->entity_id,
                'entity_type' => $this->entity_type,
                'user_id'     => $this->user_id
            );
            $db->delete($this->table_name, $where, null, 1);
        }
    }

    function get_user_vote() {
        if (empty($this->data['user_vote'])) {
            return 0;
        }
        return intval($this->data['user_vote']);
    }

    function get_vote_totals() {
        return $this->data['total'];
    }

    function get() {
        return $this->data;
    }
    // This looks important -AK 
//ORDER
    //confirmvotes
    //process_vote
    //Applyvvoteifneeded
    //getNewVoteValue


    private function getNewVoteValue($vote_value) {
        $current_user_val = $this->get_user_vote();
        $new_user_val = $current_user_val + $vote_value ;
        
        if (is_admin()) {
            // admin can vote unlimited
            $new_user_val = $current_user_val + $vote_value ;
            return $new_user_val;
        }
        //this changes the vote value before it set all novte value to 1
        //if there's no vote before vote value should be equal to the amount
        //passed in
        if ($current_user_val == 0){
            $new_user_val=$vote_value;
        

        }
        //elseif the user votes up and there is already an up vote set the new
        //value to the user's vote amount
        elseif ($vote_value > 0 && $current_user_val > 0) {
            $new_user_val = $vote_value;
        } 
        //if the vote is down and prevous vote was up vote should undo
        //previous upvote
        elseif ($vote_value < 0 && $current_user_val > 0) {
            $new_user_val = 0;
        }
        // If there was a previous downvote, then followed by an upvote, set to 0
        elseif($vote_value > 0 && $current_user_val < 0){
            $new_user_val = 0;
        }

        return $new_user_val;
    }

    private function getVotedObject() {
        switch ($this->entity_type) {
            case "question":
                $qobj = new Question($this->entity_id);
                break;
            case "comment":
                $qobj = new Comment($this->entity_id);
                break;
            case "posted_link":
                $qobj = new PostedLink($this->entity_id);
                break;
        }

        if (!$qobj->is_loaded()) {
            return null;
        }

        return $qobj;
    }

//This function contains logic to either count the full vote, 
//take away the previous upvote or create a downvote

    function applyVoteIfNeeded($vote_value) {
        //See the value of the user's current vote
        $current_user_val = $this->get_user_vote();

        //The value of all votes total 
        $current_total = $this->get_vote_totals();

        $new_user_val = $this->getNewVoteValue($vote_value);
        // if the user has voted once and after voting would be the same there's no need to update
        // for example: if the current value is 1 and value after the vote would be 1 there's no need
        // to process the vote
        if ($current_user_val === $new_user_val) {
            return false;
        }

        if ($vote_value == -1 && ($current_total + $vote_value) < 0) {
            // we do not allow voting below 0
            return false;
        }

        $this->set_user_vote($new_user_val);
        return true;
    }

    function process_vote($vote_value) {
        $qobj = $this->getVotedObject();
        if (!$qobj) {
            Log::$logger->error("Not found post when voting");
            return "Post not found.";
        }

        $f_contest_entity = false;
        if ($this->entity_type=="posted_link" && $qobj->get_data('f_contest')) {
            $f_contest_entity = true;
        }

        if ($this->user_id == $qobj->get_data('user_id') && !is_admin() && !$f_contest_entity) {
            return "You can not vote your own post.";
        }

        $no_votes_left_error = false;

        if ($f_contest_entity) {
            require_once('class.VoteContest.php');
            $contest_vote = new VoteContest($this->entity_id, $this->user_id);
            $vote_applied = $contest_vote->applyVoteIfNeeded($vote_value, $qobj);
            if (!$vote_applied) {
                $no_votes_left_error = true;
            }
        } else {
            if (!is_logged_in()) {
                return(Gate::MSG_ERR_NOT_LOGGED);
            }
            $vote_applied = $this->applyVoteIfNeeded($vote_value);
        }

        if ($vote_applied) {
            $qobj->recache();
        }

        $qdata     = $qobj->get();
        $vote_data = $qdata['vote'];
        if ($vote_data['total'] < 0) {
            $vote_data['total'] = 0;
        }

        $vote_data['user_vote'] = intval($vote_data['user_vote']);
        $vote_data['no_votes_left_error'] = $no_votes_left_error;

        return $vote_data;
    }

    static function getEmptyVotesSubmitData() {
        $data = array();
        $data['first_name'] = array('type'=>'input', 'title'=>'First Name', 'value'=>'', 'f_needed' => true);
        $data['last_name'] = array('type'=>'input', 'title'=>'Last Name', 'value'=>'', 'f_needed' => true);
        $data['email'] = array('type'=>'input', 'title'=>'Email', 'value'=>'', 'f_needed' => true);
        return $data;
    }

    static function getVotesSubmitDataFromRequest() {
        $data = self::getEmptyVotesSubmitData();

        foreach ($data as $key=>&$field) {
            $field['value'] = Utils::reqParam($key);
        }

        return $data;
    }

    static function serializeVotes($votes) {
        $votes_strings = array();
        foreach ($votes as $post_id=>$vote_data) {
            if (!$vote_data['value']) {
                continue;
            }
            $votes_strings[] = sprintf("v[%d]=%s",
                                $post_id,
                                $vote_data['value']);
        }

        $string = implode("&", $votes_strings);
        return $string;
    }

    static function sendConfirmVotesEmail($votes, $user_data, $user_obj) {
        require_once('class.Mailer.php');

        if (!$user_obj->is_loaded()) {
            $user_obj = User::createContestUser($user_data);

            if (!$user_obj->is_loaded()) {
                return false;
            }
        }

        $mailer = new Mailer('confirm_votes');
        return $mailer->sendSubmitVotesEmail(self::serializeVotes($votes), $user_obj);

        // todo ask if we have to add follow SM
    }

    public static function confirmVotes() {
        $votes_left = VoteContest::getVotesLeft();

        $votes = Utils::reqParam('v');
        if ($votes) {
            foreach ($votes as $post_id => $value) {
                if (!$value) {
                    continue;
}

                $votes_left -= abs(intval($value));
                if ($votes_left < 0) {
                    Log::$logger->error("User ". get_user_id() ." tried to confirm more votes than allowed. Votes data: " . print_r($votes, true));
                    break;
                }
                $vote = new Vote($post_id, 'posted_link', get_user_id());
                $vote->process_vote(intval($value));
            }
        }

        return true;
    }

    public static function applyVotesAfterLoggingIn() {
        require_once('class.VoteContest.php');
        require_once('class.PostedLink.php');
        $not_applied_contest_votes = VoteContest::getUnregisteredUserVotes();
        if (!$not_applied_contest_votes) {
            return;
        }

        $votes_left = VoteContest::getVotesLeft();
        foreach ($not_applied_contest_votes as $vote_data) {
            $vote_value = $vote_data['value'];
            if (!$vote_value) {
                continue;
            }
            $votes_left -= abs(intval($vote_value));
            if ($votes_left < 0) {
                Log::$logger->warn("User ". get_user_id() ." tried to apply more votes than allowed after login. Votes data: " . print_r($not_applied_contest_votes, true));
                break;
            }
            $vote_obj = new Vote($vote_data['post_id'], 'posted_link', get_user_id());
            $vote_obj->process_vote(intval($vote_data['value']));
        }

        Utils::unsetSVar('unregistered_user_votes');
    }
}