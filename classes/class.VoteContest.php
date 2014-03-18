<?php

require_once('class.BaseObject.php');

class VoteContest {
    private $post_id;
    private $user_id;
    private $vote_id;
    private $data = array();

    function VoteContest($post_id=null, $user_id = null) {
        $this->post_id = $post_id;
        $this->user_id = $user_id;
        $this->load();
    }

    private static function getVoteByUserByPost($post_id, $user_id) {
        if (!$user_id) {
            $user_votes = self::getUnregisteredUserVotes();
            if (isset($user_votes[$post_id])) {
                return $user_votes[$post_id];
            }
            return array();
        }

        $sql = sprintf("SELECT * FROM vote_contest WHERE user_id=%d AND post_id=%d AND day_added = '%s'",
                        $user_id,
                        $post_id,
                        date('Y-m-d 00:00:00'));
        return Database::execArray($sql, true);
    }

    private static function getVotesByPost($post_id) {
        $sql = sprintf("SELECT sum(value) total, count(1) count FROM vote_contest
                        WHERE post_id=%d
                        GROUP BY post_id",
                        $post_id);
        return Database::execArray($sql, true);
    }

    public static function getPostVotesDetails($post_id) {
        $sql = sprintf("SELECT user.*, vote_contest.value, vote_contest.date_added as vote_date
                        FROM vote_contest
                        JOIN user ON user.user_id = vote_contest.user_id
                        WHERE post_id=%d
                        ORDER BY vote_contest.date_added DESC",
                        $post_id);
        return Database::execArray($sql);
    }

    private static function getVotesCountForUserToday($user_id) {
        if (!$user_id) {
            $user_votes = self::getUnregisteredUserVotes();
            $count = 0;
            foreach ($user_votes as $vote_data) {
                $count += abs($vote_data['value']);
            }
            return $count;
        }

        $sql = sprintf("SELECT sum(abs(value)) times_count FROM vote_contest
                        WHERE user_id=%d
                        AND day_added = '%s'
                        GROUP BY user_id",
                        $user_id,
                        date('Y-m-d 00:00:00'));
        $result = Database::execArray($sql, true);
        if (!$result) {
            return 0;
        }
        return intval($result['times_count']);
    }

    static function getVotesLeft() {
        $votes_per_day = Utils::UNREGISTERED_VOTES_COUNT;
        if (is_logged_in()) {
            $votes_per_day = Utils::userData('votes_count');
        }

        $today_votes = self::getVotesCountForUserToday(get_user_id());
        $votes_left = $votes_per_day - $today_votes;
        if ($votes_left < 0) {
            $votes_left = 0;
        }
        return $votes_left;
    }

    static function getUnregisteredUserVotes() {
        // unregistered, votes are in session
        return Utils::sVar('unregistered_user_votes', array());
    }

    function load() {
        if (!$this->post_id) {
            return;
        }

         if (!$this->user_id) {
             // this is an unregistered user who wants to vote
         }

        $data = self::getVoteByUserByPost($this->post_id, $this->user_id);
        if ($data) {
            $this->data = $data;
            $this->vote_id = $data['vote_id'];
        }
    }

    function is_loaded() {
        return $this->vote_id ? true : false;
    }

    function getVotesForPost() {
        $data = array();
        $data['count'] = 0;
        $data['total'] = 0;
        $data['user_vote'] = 0;

        if ($this->is_loaded()) {
            $data['user_vote'] = $this->data['value'];
        }

        $post_data = self::getVotesByPost($this->post_id);
        if ($post_data) {
            $data['count'] = $post_data['count'];
            $data['total'] = $post_data['total'];
        }

        if (!$this->user_id && $data['user_vote']) {
            // this user is unregistered so his votes are only in session so they are not counted. We should add these votes manually.
            $data['count']++;
            $data['total'] += $data['user_vote'];
        }

        return $data;
    }

    function delete() {
        if (!$this->is_loaded()) {
            return;
        }

        if (!$this->user_id) {
            $user_votes = self::getUnregisteredUserVotes();
            if (isset($user_votes[$this->post_id])) {
                unset($user_votes[$this->post_id]);
                $_SESSION['unregistered_user_votes'] = $user_votes;
            }
            $this->vote_id = null;
            return;
        }

        $sql = sprintf("DELETE FROM vote_contest WHERE vote_id=%d",
                        $this->vote_id);
        Database::execArray($sql, true);
        $this->vote_id = null;
    }

    function insert($value) {
        if (!$this->user_id) {
            $user_votes = self::getUnregisteredUserVotes();
            $user_votes[$this->post_id]['value'] = $value;
            $user_votes[$this->post_id]['post_id'] = $this->post_id;
            $user_votes[$this->post_id]['vote_id'] = -1;
            $_SESSION['unregistered_user_votes'] = $user_votes;
            $this->load();
            return;
        }

        $sql = sprintf("INSERT INTO vote_contest
                            (post_id, user_id, value, date_added, day_added)
                        VALUES
                            (%d, %d, %d, '%s', '%s')",
                        $this->post_id,
                        $this->user_id,
                        $value,
                        date('Y-m-d H:i:s'),
                        date('Y-m-d 00:00:00'));
        Database::exec($sql);
        $this->load();
    }

    function update($value) {
        if (!$this->user_id) {
            $user_votes = self::getUnregisteredUserVotes();
            $user_votes[$this->post_id]['value'] = $value;
            $_SESSION['unregistered_user_votes'] = $user_votes;
            $this->load();
            return;
        }

        $sql = sprintf("UPDATE vote_contest SET value=%d
                        WHERE vote_id=%d",
                        $value,
                        $this->vote_id);
        Database::exec($sql);
        $this->load();
    }

    function save($value) {
        if ($this->is_loaded()) {
            $curr_value = $this->data['value'];
            $new_value = $curr_value + $value;

            if (abs($new_value) > abs($curr_value)) {
                // user votes count will increase, should check if he has free votes
                if (self::getVotesLeft()<1) {
                    // user has no free votes for this vote
                    return false;
                }
            }
            $this->update($new_value);
        } else {
            if (self::getVotesLeft()<1) {
                return false;
            }
            $this->insert($value);
        }
        return true;
    }

    function applyVoteIfNeeded($vote_value) {
        $is_applyed = $this->save($vote_value);
        return $is_applyed;
    }

    public static function isPostInTop50onLive($id) {
        if (Settings::DEV_MODE || Settings::SHOW_BETA_BORDER) {
            return true;
        }

        /* This data will not be changed anymore, no need to ask DB each time or cache */
        $top_50_ids = array(1844,1645,1632,1750,1895,1919,1691,2142,1634,1647,2059,2245,1641,1962,1799,2130,1812,1988,
                            2071,2037,2041,2099,1905,1760,1643,1695,2060,2116,1845,2029,1926,2023,1832,1761,1653,1692,
                            1907,2209,1986,2087,2038,2195,2098,2232,2088,2221,1646,1762,2036,2039);

        if (in_array($id, $top_50_ids)) {
            return true;
        }
        return false;
    }
}