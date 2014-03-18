<?php

class Link {

    static protected $table_name = 'link';
    protected $entity1_id;
    protected $entity1_type;
    protected $entity2_id;
    protected $entity2_type;
    protected $link_type;

    function Link($entity1_id, $entity1_type, $entity2_id, $entity2_type) {
        $this->entity1_id   = $entity1_id;
        $this->entity1_type = $entity1_type;

        $this->entity2_id   = $entity2_id;
        $this->entity2_type = $entity2_type;
    }

    function check() {
        global $db;

        if ($this->is_loaded()) {
            $where   = array('entity1_id'   => $this->entity1_id, 'entity2_id'   => $this->entity2_id, 'entity1_type' => $this->entity1_type, 'entity2_type' => $this->entity2_type, 'link_type'    => $this->link_type);
            $sresult = $db->select($this->table_name, array('count(*) AS count'), null, $where, null);

            if ($sresult[0]['count'] > 0)
                return true;
        }

        return false;
    }

    function add() {
        global $db;

        if ($this->is_loaded()) {
            $data = array(
                'entity1_id'   => $this->entity1_id,
                'entity1_type' => $this->entity1_type,
                'entity2_id'   => $this->entity2_id,
                'entity2_type' => $this->entity2_type,
                'link_type'    => $this->link_type,
                'date_added'   => 'now()'
            );
            $db->insert($this->table_name, $data, true);
        }
    }

    function remove() {
        global $db;

        if ($this->is_loaded()) {
            $where = array(
                'entity1_id'   => $this->entity1_id,
                'entity1_type' => $this->entity1_type,
                'entity2_id'   => $this->entity2_id,
                'entity2_type' => $this->entity2_type,
                'link_type'    => $this->link_type
            );
            $db->delete($this->table_name, $where, null, 1);
        }
    }

    function is_loaded() {
        if (isset($this->entity1_id) && $this->entity1_id > 0 &&
                isset($this->entity2_id) && $this->entity2_id > 0 &&
                isset($this->entity1_type) && $this->entity1_type &&
                isset($this->entity2_type) && $this->entity2_type &&
                isset($this->link_type)) {
            return true;
        } else {
            return false;
        }
    }

    public static function createAutoFollowLink($user1_id, $user2_id) {
        $data = array(
            'entity1_id'   => $user2_id,
            'entity1_type' => 'user',
            'entity2_id'   => $user1_id,
            'entity2_type' => 'user',
            'link_type'    => 'follow'
        );
        self::createLink($data);

        $data = array(
            'entity1_id'   => $user1_id,
            'entity1_type' => 'user',
            'entity2_id'   => $user2_id,
            'entity2_type' => 'user',
            'link_type'    => 'follow'
        );
        self::createLink($data);
    }

    public static function createFollowLink($follow_data) {

        $users_for_autofollowing = array(2, 966); //('david_cheng', 'andrew_koller')

        if (!is_logged_in()) {
            return false;
        }

        if ($follow_data['whom_type'] == 'user' && in_array($follow_data['whom_id'], $users_for_autofollowing)) {
            self::createAutoFollowLink(get_user_id(), $follow_data['whom_id']);
            return;
        }

        $data = array(
            'entity1_id'   => get_user_id(),
            'entity1_type' => 'user',
            'entity2_id'   => $follow_data['whom_id'],
            'entity2_type' => $follow_data['whom_type'],
            'link_type'    => 'follow'
        );
        self::createLink($data);
    }

    // todo this should be solved by FKs
    public static function deleteBrokenFollowLinks($data) {
        global $db;
        $where = array(
            'entity2_id'   => $data['entity_id'],
            'entity2_type' => $data['entity_type'],
            'link_type'    => 'follow',
        );
        $db->delete(self::$table_name, $where);

        $where = array(
            'entity1_id'   => $data['entity_id'],
            'entity1_type' => $data['entity_type'],
            'link_type'    => 'follow',
        );
        $db->delete(self::$table_name, $where);
    }

    public static function deleteFollowLink($follow_data) {
        if (!is_logged_in()) {
            return false;
        }
        global $db;
        $where = array(
            'entity1_id'   => get_user_id(),
            'entity1_type' => 'user',
            'entity2_id'   => $follow_data['whom_id'],
            'entity2_type' => $follow_data['whom_type'],
            'link_type'    => 'follow',
        );
        $db->delete(self::$table_name, $where, null, 1);
    }

    public static function createLink($data) {
        global $db;
        $result_exist = $db->select(self::$table_name, array('count(*) AS count'), null, $data, null);
        if ($result_exist[0]['count'] > 0) {
            Log::$logger->debug("Adding already existing follow link.");
            return;
        }

        $data['date_added'] = 'now()';
        $db->insert(self::$table_name, $data, true);
    }
}

?>