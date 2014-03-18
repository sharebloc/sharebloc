<?php
class Subscription {
    // bear - actually subscriptions data shpuld be stored in two tables - emails and subscriptions.
    // Now stored in one denormalized table to simplify.

    // 'Sales & Marketing', 'Technology', 'Real Estate'
    public static $SUBSCRIPTIONS_BLOCS = array('1', '5', '6');

    private static function insertSubscription($email, $tag_id) {
        $sql = sprintf("INSERT INTO subscriptions
                                (email, tag_id, confirm_key, created_ts)
                                VALUES
                                ('%s', %d, '%s', now())",
                                Database::escapeString($email),
                                $tag_id,
                                User::generateRandomKey());
        Database::exec($sql);
    }

    private static function setSubscriptionConfirmed($id) {
        $sql = sprintf("UPDATE subscriptions
                        SET confirmed_ts = NOW(), confirm_key='%s'
                        WHERE id=%d",
                        User::generateRandomKey(),
                        $id);
        Database::exec($sql);
    }

    private static function setSubscriptionDeleted($id) {
        $sql = sprintf("UPDATE subscriptions
                        SET deleted_ts = NOW(), confirm_key='%s'
                        WHERE id=%d",
                        User::generateRandomKey(),
                        $id);
        Database::exec($sql);
    }

    private static function setSubscriptionNotDeleted($id) {
        $sql = sprintf("UPDATE subscriptions
                        SET deleted_ts = NULL
                        WHERE id=%d",
                        $id);
        Database::exec($sql);
    }

    private static function getSubscriptionByEmailAndTagId($email, $tag_id) {
        $sql = sprintf("SELECT * FROM subscriptions
                        WHERE email='%s' AND tag_id=%d",
                        Database::escapeString($email),
                        $tag_id);
        $result = Database::execArray($sql, true);
        return $result;
    }

    public static function getSubscriptionById($id) {
        $sql = sprintf("SELECT * FROM subscriptions
                        WHERE id=%d",
                        $id);
        $result = Database::execArray($sql, true);
        return $result;
    }

    private static function getSubscriptionByConfirmationKey($key) {
        $sql = sprintf("SELECT * FROM subscriptions
                        WHERE confirm_key='%s'",
                        Database::escapeString($key));
        $result = Database::execArray($sql, true);
        return $result;
    }

    private static function isEmailConfirmed($email) {
        $sql = sprintf("SELECT * FROM subscriptions
                        WHERE email='%s' AND confirmed_ts IS NOT NULL",
                        Database::escapeString($email));
        $result = Database::execArray($sql, true);
        if ($result) {
            return true;
        }
        return false;
    }

    private static function setSubscriptionCookie($tag_id) {
        $time_to_store = time()+ 60*60*24*365;
        setcookie("sb_subs[$tag_id]", 1, $time_to_store, '/', '', false, true);
    }

    private static function clearSubscriptionCookie($tag_id) {
        $time_to_store = time()- 60*60*24*365;
        setcookie("sb_subs[$tag_id]", "", $time_to_store, '/', '', false, true);
    }

    // todo probably we should not ask user to confirm his email if we already have a confirmed subscription
    public static function deleteBecauseOfJoin($email, $user_id) {
        $sql = sprintf("UPDATE subscriptions
                        SET confirm_key=null, deleted_ts = NOW(), user_id=%d
                        WHERE email='%s'",
                        $user_id,
                        Database::escapeString($email));
        Database::exec($sql);
    }

    public static function processSubscription($email, $tag_id) {
        $subscription = self::getSubscriptionByEmailAndTagId($email, $tag_id);
        if (!$subscription) {
            // some overhead - but does not matter for this rare action
            self::insertSubscription($email, $tag_id);
            $subscription = self::getSubscriptionByEmailAndTagId($email, $tag_id);
        }

        if ($subscription['deleted_ts']) {
            self::setSubscriptionNotDeleted($subscription['id']);
        }

        if (self::isEmailConfirmed($email)) {
            self::setSubscriptionConfirmed($subscription['id']);
            self::setSubscriptionCookie($tag_id);
            return 'You have been successfully subscribed.';
        }

        require_once('class.Mailer.php');
        $mailer = new Mailer('confirm_subscription');
        $mailer->sendConfirmSubscriptionEmail($email, $tag_id, $subscription['confirm_key']);
        return true;
    }

    public static function confirmSubscription() {
        $key = Utils::reqParam('code');

        $subscription = self::getSubscriptionByConfirmationKey($key);
        if (!$subscription) {
            return false;
        }

        self::setSubscriptionConfirmed($subscription['id']);
        self::setSubscriptionCookie($subscription['tag_id']);
        return true;
    }

    public static function unsubscribe($code) {
        $subscription = self::getSubscriptionByConfirmationKey($code);
        if (!$subscription) {
            return false;
        }

        self::setSubscriptionDeleted($subscription['id']);
        self::clearSubscriptionCookie($subscription['tag_id']);

        $message = sprintf('You have been successfully unsubscribed from %s emails. We\'re sorry to see you go.<br>If this was in error, please <a href="mailto:support@sharebloc.com">contact us</a>.',
                            Utils::$tags_list_vendor[$subscription['tag_id']]['tag_name']);
        return $message;
    }
}