<?php

// todo bear I did not extend this class from BaseObject as it's not stable now, may be later we should do this
// Another reason is that BaseObject is really overlinked with memcache and should be refactored too
class InviteCustom {
    public static function insertInvite($data) {
        global $db;
        $query = sprintf("INSERT INTO invite_custom
                                (confirm_key, comment, user_id, created_ts)
                                VALUES
                                ('%s', %s, %d, now())",
                                $data['confirm_key'],
                                $data['comment'] ? "'" . $db->escape_text($data['comment']) . "'" : 'NULL',
                                get_user_id());
        $db->query($query);
    }

    public static function insertInviteCustomSent($data) {
        global $db;
        $query = sprintf("INSERT INTO invite_custom_sent
                                (first_name, last_name, email,
                                text, created_ts, user_id)

                                VALUES
                                (%s, %s, '%s',
                                '%s', now(), %d)",
                                $data['first_name'] ? "'".$db->escape_text($data['first_name'])."'" : 'NULL',
                                $data['last_name'] ? "'".$db->escape_text($data['last_name'])."'" : 'NULL',
                                $db->escape_text($data['email']),

                                $db->escape_text($data['text']),
                                $data['user_id']);
        $db->query($query);
    }

    public static function getInvite($invite_id) {
        global $db;
        $query = sprintf("SELECT * FROM invite_custom
                            WHERE deleted_ts IS NULL
                            AND inv_id=%d", $invite_id);

        $result = $db->query($query);
        if ($result) {
            return $result[0];
        }
        return array();
    }

    public static function deleteInvite($invite_id) {
        global $db;
        $query = sprintf("UPDATE invite_custom
                                SET deleted_ts=NOW()
                                WHERE deleted_ts IS NULL
                                AND inv_id = %d", $invite_id);
        $db->query($query);
    }

    public static function getInviteByKey($confirm_key) {
        global $db;
        $query = sprintf("SELECT * FROM invite_custom
                            WHERE deleted_ts IS NULL
                            AND confirm_key='%s'", $confirm_key);

        $result = $db->query($query);
        if ($result) {
            $result[0]['invite_id'] = $result[0]['inv_id'];
            $result[0]['invite_type'] = 'InviteCustom';
            return $result[0];
        }
        return array();
        }

    private static function deleteInviteUsedBy($invite_id, $user_id) {
        global $db;
        $query = sprintf("DELETE FROM invite_custom_users
                            WHERE inv_id=%d AND user_id=%d",
                            $invite_id,
                            $user_id);

        $db->query($query);
    }

    public static function addInviteUsedBy($invite_id) {
        global $db;

        $user_id = get_user_id();
        if (!$user_id) {
            Log::$logger->error("Missed user id when adding user to invite_custom_users, invite_id = $invite_id");
            return;
        }
        self::deleteInviteUsedBy($invite_id, $user_id);

        $query = sprintf("INSERT INTO invite_custom_users (inv_id, user_id, created_ts)
                            VALUES (%d, %d, now())",
                            $invite_id,
                            $user_id);

        $db->query($query);
    }

    public static function addCustomInviteSent($data) {
        if (!$data['first_name'] || !$data['last_name'] || !$data['email'] || !$data['text']) {
            // normally this will be validated on client side
            Log::$logger->warn("Some data missed when adding a custom_invite_sent, data is:= " . print_r($data, true));
            return "Please fill in all the fields.";
        }

        if (!validate_email($data['email'])) {
            Log::$logger->info("Invalid email " . $data['email'] . " when adding a custom_invite_sent, data is:= " . print_r($data, true));
            return "Email " . $data['email'] . " is invalid.";
        }

        $data['user_id'] = get_user_id();

        self::deletePreviousCustomInvitesSent($data['email'], $data['user_id']);
        self::insertInviteCustomSent($data);

        return true;
    }

    public static function deletePreviousCustomInvitesSent($email, $user_id) {
        global $db;
        $query = sprintf("DELETE FROM invite_custom_sent
                                WHERE email = '%s'
                                    AND user_id=%d",
                                $db->escape_text($email),
                                $user_id);
        $db->query($query);
    }

    public static function processJoinInvites($invites) {
        require_once('class.Mailer.php');

        $user_contacts = User::getOauthContactsByUserId();

        foreach ($invites as $key=>$email) {
            $invite_data = array();
            $invite_data['email'] = $email;
            $invite_data['user_id'] = get_user_id();
            $invite_data['confirm_key'] = get_user_code_name();
            $invite_data['first_name'] = '';
            $invite_data['last_name'] = '';
            $invite_data['full_name'] = '';
            $invite_data['text'] = '';
            if (isset($user_contacts[$key])) {
                $invite_data['first_name'] = $user_contacts[$key]['first_name'];
                $invite_data['last_name'] = $user_contacts[$key]['last_name'];
                $invite_data['full_name'] = $user_contacts[$key]['full_name'];
            }

            self::deletePreviousCustomInvitesSent($invite_data['email'], $invite_data['user_id']);
            self::insertInviteCustomSent($invite_data);

            $mailer = new Mailer('invite_join');
            $mailer->sendInviteJoin($invite_data);
        }
    }
    public static function addCustomInvite($data) {
        if (!$data['confirm_key']) {
            return "No invite key set";
        }

        $data['confirm_key'] = str_replace(' ', '_', $data['confirm_key']);

        if (strlen($data['confirm_key'])>128) {
            return "Key should not contain more than 128 symbols.";
        }

        if (!preg_match('/^[a-zA-Z_0-9]+$/', $data['confirm_key'])) {
            return "Key should contain only numbers, letters and underscores.";
        }

        if (strpos($data['confirm_key'], "sbbeta_")===0) {
            return "Key should not start by 'sbbeta_'";
        }

        $existing_key = self::getInviteByKey($data['confirm_key']);
        if ($existing_key) {
            return "This key already exists in DB.";
        }

        self::insertInvite($data);
        return true;
    }

    public static function processInviteKey($invite_key) {
        $invite = self::getInviteByKey($invite_key);
        if (!$invite) {
            return false;
        }

        self::addInviteUsedBy($invite['inv_id']);

        $user = new User(get_user_id());
        $user->setAccessToNewVS(true);

        return $invite;
    }

    public static function getInvitesStats() {
        global $db;
        $query = sprintf("SELECT ic.*, icu.created_ts AS invite_used_ts,
                            user.user_id, user.code_name, user.email, user.first_name, user.last_name
                            FROM invite_custom ic
                            LEFT JOIN invite_custom_users icu ON icu.inv_id = ic.inv_id
                            LEFT JOIN user on user.user_id = icu.user_id");
        $results = $db->query($query);

        if (!$results) {
            return array();
        }

        $data = array();
        foreach ($results as $invite) {
            if (!isset($data[$invite['inv_id']])) {
                $data[$invite['inv_id']] = $invite;
                $data[$invite['inv_id']]['my_url'] = "http://" . $_SERVER['HTTP_HOST'] . "/invite/" . $invite['confirm_key'];
                $data[$invite['inv_id']]['users'] = array();
            }

            if (isset($invite['user_id'])) {
                $data[$invite['inv_id']]['users'][$invite['user_id']] = $invite;
                $data[$invite['inv_id']]['users'][$invite['user_id']]['my_url'] = User::getUrlByCodeName($invite['code_name']);
                $data[$invite['inv_id']]['users'][$invite['user_id']]['full_name'] = $invite['first_name'] . " " .  $invite['last_name'];
            }
        }

        return $data;
    }
}

?>