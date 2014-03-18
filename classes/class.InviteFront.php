<?php

// todo bear I did not extend this class from BaseObject as it's not stable now, may be later we should do this
// Another reason is that BaseObject is really overlinked with memcache and should be refactored too
class InviteFront {

    const INVITE_KEY_TIME_TO_LIVE_HOURS = 24;

    public static function insertInvite($data) {
        global $db;
        $query = sprintf("INSERT INTO invite_front
                                (confirm_key, first_name, last_name, email,
                                text, created_ts, user_id)

                                VALUES
                                ('%s', '%s', '%s', '%s',
                                '%s', now(), %d)",
                                $data['confirm_key'],
                                $db->escape_text($data['first_name']),
                                $db->escape_text($data['last_name']),
                                $db->escape_text($data['email']),

                                $db->escape_text($data['text']),
                                $data['user_id']);
        $db->query($query);
    }

    public static function getInvite($invite_id) {
        global $db;
        $query = sprintf("SELECT * FROM invite_front
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
        $query = sprintf("UPDATE invite_front
                                SET deleted_ts=NOW(), status='deleted'
                                WHERE deleted_ts IS NULL
                                AND inv_id = %d", $invite_id);
        $db->query($query);
    }

    public static function setInviteConfirmed($invite_id) {
        global $db;
        $query = sprintf("UPDATE invite_front
                                SET status='confirmed',
                                confirm_key = NULL
                                WHERE inv_id = %d", $invite_id);
        $db->query($query);
    }

    public static function setInvitedUserId($invite_id, $user_id) {
        global $db;
        $query = sprintf("UPDATE invite_front
                                SET invited_user_id=%d
                                WHERE inv_id = %d", $user_id, $invite_id);
        $db->query($query);
    }

    public static function deletePreviousInvites($user_id, $email) {
        global $db;
        $query = sprintf("UPDATE invite_front SET deleted_ts=NOW(), status='deleted'
                                WHERE deleted_ts IS NULL
                                AND user_id=%d
                                AND email = '%s'",
                                $user_id,
                                $db->escape_text($email));
        $db->query($query);
    }

    public static function getInviteByKey($confirm_key) {
        global $db;
        $query = sprintf("SELECT * FROM invite_front
                            WHERE deleted_ts IS NULL
                            AND confirm_key='%s'", $confirm_key);

        $result = $db->query($query);
        if ($result) {
            $result[0]['invite_id'] = $result[0]['inv_id'];
            $result[0]['invite_type'] = 'InviteFront';
            return $result[0];
        }
        return array();
    }

    public static function addFrontInvite($data) {
        if (!$data['first_name'] || !$data['last_name'] || !$data['email'] || !$data['text']) {
            // normally this will be validated on client side
            Log::$logger->warn("Some data missed when adding a invite_front, data is:= " . print_r($data, true));
            return "Please fill in all the fields.";
        }

        if (!validate_email($data['email'])) {
            Log::$logger->info("Invalid email " . $data['email'] . " when adding a invite_front, data is:= " . print_r($data, true));
            return "Email " . $data['email'] . " is invalid.";
        }

        $data['user_id'] = get_user_id();

        self::deletePreviousInvites($data['user_id'], $data['email']);
        self::insertInvite($data);

        $user = new User($data['user_id']);

        return true;
    }


    // is like a front, but simplified and batched + mails
    // see https://vendorstack.atlassian.net/browse/VEN-230
    public static function processJoinInvites($invites) {
        require_once('class.Mailer.php');

        $user_contacts = User::getOauthContactsByUserId();

        foreach ($invites as $key=>$email) {
            $invite_data = array();
            $invite_data['email'] = $email;
            $invite_data['user_id'] = get_user_id();
            $invite_data['confirm_key'] = User::generateRandomKey();
            $invite_data['first_name'] = '';
            $invite_data['last_name'] = '';
            $invite_data['full_name'] = '';
            $invite_data['text'] = '';
            if (isset($user_contacts[$key])) {
                $invite_data['first_name'] = $user_contacts[$key]['first_name'];
                $invite_data['last_name'] = $user_contacts[$key]['last_name'];
                $invite_data['full_name'] = $user_contacts[$key]['full_name'];
            }

            self::deletePreviousInvites($invite_data['user_id'], $invite_data['email']);
            self::insertInvite($invite_data);

            $mailer = new Mailer('invite_join');
            $mailer->sendInviteJoin($invite_data);
        }
    }

    public static function processInviteKey($invite_key) {
        $invite = self::getInviteByKey($invite_key);
        if (!$invite) {
            return false;
        }

        self::setInviteConfirmed($invite['invite_id']);
        self::setInvitedUserId($invite['invite_id'], get_user_id());

        $user = new User(get_user_id());
        $user->setAccessToNewVS(true);

        return $invite;
    }
}