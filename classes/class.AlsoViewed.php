<?php

/**
 * Tracks user's browsing history (FE for questions, vendors and companies)
 * @author bear@deepshiftlabs.com
 * @since 15 Apr 2013
 */
class AlsoViewed {

    protected $table_name = 'also_viewed';

    // this session var used to store history for current session

    const SESSION_VAR_PREFIX           = 'also_viewed_history_';
    // after this timeout user transition that was already counted will be counted again
    const NO_COUNT_TWICE_TIMEOUT       = 86400;
    // only if time between transitions is less than this value, entities are counted as "also viewed"
    const COUNT_AS_ALSO_VIEWED_TIMEOUT = 600;
    // only if steps between transitions is less than this value, entities are counted as "also viewed"
    const COUNT_AS_ALSO_VIEWED_STEPS   = 5;

    private $entity_id;
    private $entity_type;
    // reference to session var for convenience
    private $session_var;

    function AlsoViewed($entity_type, $entity_id) {
        if (is_null($entity_id)) {
            return false;
        }
        $this->entity_id   = $entity_id;
        $this->entity_type = $entity_type;

        if (!isset($_SESSION[self::SESSION_VAR_PREFIX . $entity_type])) {
            $_SESSION[self::SESSION_VAR_PREFIX . $entity_type] = array();
        }

        $this->session_var = & $_SESSION[self::SESSION_VAR_PREFIX . $entity_type];
    }

    /**
     * Records transition to the history in $_SESSION.
     * If there are entities in the history, that satisfy "also viewed" requirements, they are inserted to the DB table
     */
    function record_visit() {
        if (!$this->is_loaded()) {
            return false;
        }

        $this->add_to_history();

        $also_viewed_ids = $this->get_also_viewed_from_history();
        foreach ($also_viewed_ids as $also_viewed_id) {
            $this->insert_also_viewed($also_viewed_id);
        }
    }

    /**
     * Returns also viewed entities ids for the current entity, sorted by the popularity
     * @param int $limit how much entities to return
     */
    function get_also_viewed_ids($limit = 3) {
        if (!$this->is_loaded()) {
            return array();
        }
        global $db, $cache;
        $result_ids = array();
        $hash_key   = 'viewed_also_for-' . $this->entity_type . "-" . $this->entity_id;

        $cache_result = $cache->get(get_class($this), $hash_key);
        $cache_result = false;

        if (!$cache_result) {
            $query  = sprintf('SELECT entitya_id as entity_id, counter from also_viewed
                                        WHERE entityb_id=%1$d AND entity_type=\'%2$s\'
                                        UNION
                                        SELECT entityb_id as entity_id, counter from also_viewed
                                        WHERE entitya_id=%1$d AND entity_type=\'%2$s\'
                                        ORDER BY counter desc
                                        LIMIT %d', $this->entity_id, $this->entity_type, $limit);
            $db->query($query);
            $result = $db->query($query);
            foreach ($result as $row) {
                $result_ids[] = $row['entity_id'];
            }

            $cache->set(get_class($this), $hash_key, $result_ids, 1);
        } else {
            $result_ids = $cache_result;
        }
        return $result_ids;
    }

    private function get_also_viewed_from_history() {
        $result = array();

        if (count($this->session_var) < 2) {
            // the history is too short
            return $result;
        }

        $last_key         = count($this->session_var) - 1;
        $last_relevant_ts = time() - self::COUNT_AS_ALSO_VIEWED_TIMEOUT;
        $steps_limit      = self::COUNT_AS_ALSO_VIEWED_STEPS;
        for ($i = $last_key; $i >= 0; $i--) {
            // moving end to start to cut off history older than self::COUNT_AS_ALSO_VIEWED_TIMEOUT seconds
            $record = $this->session_var[$i];
            if ($record['ts'] < $last_relevant_ts) {
                // only non-relevant records left, ignoring them
                break;
            }
            if ($record['entity_id'] !== $this->entity_id) {
                $result[] = $record['entity_id'];
                $steps_limit--;
            }
            if (!$steps_limit) {
                // only non-relevant records left, ignoring them
                break;
            }
        }
        return $result;
    }

    private function add_to_history() {
        if (count($this->session_var)) {
            $last_key = count($this->session_var) - 1;
            if ($last_key >= 0 && $this->session_var[$last_key]['entity_id'] == $this->entity_id) {
                // no need to add to history again, but will renew ts
                $this->session_var[$last_key]['ts'] = time();
                return;
            }
        }
        $record              = array('entity_id' => $this->entity_id, 'ts'        => time());
        $this->session_var[] = $record;
    }

    private function insert_also_viewed($also_viewed_id) {
        global $db, $cache;

        if (!$also_viewed_id || $this->entity_id == $also_viewed_id) {
            // reinsuring
            return;
        }

        // to have not both combinations in db
        if ($this->entity_id > $also_viewed_id) {
            $tempa = $this->entity_id;
            $tempb = $also_viewed_id;
        } else {
            $tempa = $also_viewed_id;
            $tempb = $this->entity_id;
        }

        // to not increment also viewed count for one user walking here and there
        if (is_logged_in()) {
            $user_identity = $_SESSION['user_info']['user_id'];
        } else {
            $user_identity = $_SERVER['REMOTE_ADDR'];
        }

        $hash_key = "viewed_also-" . $this->entity_type . "-" . $tempa . "-" . $tempb . "-1" . $user_identity;

        $cache_result = $cache->get(get_class($this), $hash_key);

        if (!$cache_result) {
            // this also viewed has not been recorded yet or was recorded too long ago
            $data   = array('entity_type' => $this->entity_type,
            'entitya_id'  => $tempa,
            'entityb_id'  => $tempb);
            $update = array('counter' => 'counter + 1');

            // if such combination of entities exists, it's 'counter' will be incremented
            $db->insert_on_duplicate_key_update($this->table_name, $data, array('entity_type', 'entitya_id', 'entityb_id'), $update);
            $cache->set(get_class($this), $hash_key, 1, self::NO_COUNT_TWICE_TIMEOUT);
        }
    }

    function is_loaded() {
        if (!is_null($this->entity_id)) {
            return true;
        } else {
            return false;
        }
    }

}

?>