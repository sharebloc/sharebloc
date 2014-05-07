<?php

require_once('class.BaseObject.php');
require_once('class.Logo.php');
require_once('class.GenericList.php');
require_once('class.Link.php');

class User extends BaseObject {

    protected $data;
    protected $fields;
    protected $primary_key   = 'user_id';
    protected $secondary_key = 'code_name';
    protected $entity_name   = 'full_name';
    protected $table_name    = 'user';
    protected $required      = array('email', 'password', 'first_name', 'last_name');
    private $company;
    public $logo;
    public static $logo_hash_suffix = '_u';
    const codename_url_prefix = '/users/';

    function User($user_id = null, $code_name = null) {
        parent::BaseObject($user_id, $code_name);
        if ($this->is_loaded()) {
            $this->data['followed_by_curr_user'] = $this->isFollowedByCurrentUser();
        }
    }

    static function loginByUserId($user_id, $remember_me = false) {
        if (!$user_id) {
            return false;
        }
        $user = new User($user_id);

        // suspended users can sign in in a usual way
//        if ($user->get_data('status')=='inactive') {
//            $user->clearRememberMeCookie();
//            $_SESSION['alert_message'] = 'Your account has been suspended.';
//            return false;
//        }

        if ($remember_me) {
            $user->setRememberMeCookie();
        }

        $user->load_vendors_used();
        user_login($user);
        Utils::setPermissions();

        Vote::applyVotesAfterLoggingIn();

        if (!is_logged_in() && !is_contest_voter()) {
            Log::$logger->error("Strange - user $user_id found but is_logged_in() is false");
            return false;
        }

        return true;
    }

    static function loginByCredentials($email, $password, $remember_me = false) {
        $user_id = self::getUserIdByCredentials($email, $password);
        if (self::loginByUserId($user_id, $remember_me)) {
            Log::$logger->info("User $user_id logged in by credentials");
            return true;
        }
        return false;
    }

    public static function logInByCookie() {
        $user_id = self::getUserIdByCookie();
        if (!$user_id) {
            return;
        }

        User::loginByUserId($user_id, true);
    }

    static function loginByOAuth($oauth_data, $remember_me = false) {
        $provider = $oauth_data['provider'];
        $provider_uid = $oauth_data['provider_uid'];
        $email = $oauth_data['email'];

        $user_id = self::getUserIdByOauthRegistration($provider, $provider_uid);
        if (self::loginByUserId($user_id, $remember_me)) {
            Log::$logger->info("User $user_id logged in by $provider oauth");
            return true;
        }

        $user_data = self::getUserByEmail($email);
        if ($user_data) {
            $user_id = $user_data['user_id'];
            // we trust to emails fetched by oAuth. Will login this user.
            if (!self::loginByUserId($user_id, $remember_me)) {
                Log::$logger->warn("Failed to log in user $user_id by email $email fetched by $provider oauth");
                return false;
            }

            // if this user is a contest voter, we should make him a real user
            if (is_contest_voter()) {
                $user = new User($user_data['user_id']);
                $user->set_data('f_contest_voter', 0);
                $user->save();
                $_SESSION['show_join_welcome_popup'] = 1;
                Utils::sendWelcomeEmail($user, false, false);
                Log::$logger->warn("Contest voter has been signed up (oauth), user_id = $user_id");
            }

            // will implicitly link oauth registration to the existing account
            User::addOAuthRegistration($oauth_data);
            Log::$logger->warn("Implicitly added oauth registration for $user_id by email $email fetched by $provider oauth");
            return true;
        }

        return false;
    }

    static function oauthDisconnect($provider) {
         global $db;

        $query = sprintf("DELETE FROM oauth WHERE user_id = '%d' AND provider = '%s'",
                            get_user_id(),
                            $provider);
        $db->query($query);

        $user = new User(get_user_id());
        $user->recache();
        user_login($user);

    }

    public static function hasOauthConnections() {
         global $db;
         $sql = sprintf("SELECT provider FROM oauth
                            WHERE user_id=%d",
                            get_user_id()
                        );
         $result = $db->query($sql);

         if ($result) {
             return true;
         } else {
             return false;
         }
    }


    function load_email($email) {
        global $db;
        if (!trim($email)) {
            return false;
        }

        $where = array('email' => strtolower(trim($email)));

        $result = $db->select($this->table_name, array('user_id'), null, $where, array(), array(), array(), '', 1);

        if (count($result) > 0) {
            if ($result[0]['user_id'] > 0) {
                $this->load($result[0]['user_id'], null);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function load($primary_id = null, $secondary_id = null) {
        parent::load($primary_id, $secondary_id);

        if (isset($this->data['user_id'])) {
            $no_logo = true;
            if (isset($this->data['logo_id']) && $this->data['logo_id'] > 0) {
                $this->logo              = new Logo($this->data['logo_id']);
                if ($this->logo->is_loaded()) {
                    $this->data['logo_hash'] = $this->logo->get_hash();
                    $this->data['logo']['my_url'] = $this->logo->get_data("url_full");
                    $this->data['logo']['my_url_thumb'] = $this->logo->get_data("url_thumb");
                    $no_logo = false;
                }
            }

            if ($no_logo) {
                $this->data['logo']['my_url'] = "/images/anonymous_user.png";
                $this->data['logo']['my_url_thumb'] = "/images/anonymous_user_thumb.jpg";
            }


            if (isset($this->data['last_name']) && strlen($this->data['last_name']) > 0) {
                $last_initial             = strtoupper(substr($this->data['last_name'], 0, 1));
                $this->data['short_name'] = trim($this->data['first_name'] . " " . $last_initial . ".");
                $this->data['full_name']  = $this->data['first_name'] . " " . $this->data['last_name'];
            } else {
                $this->data['short_name'] = trim($this->data['first_name']);
                $this->data['full_name']  = trim($this->data['first_name']);
            }
            $this->data['name']  = $this->data['full_name'];

            if (isset($this->data['company_id']) && strlen($this->data['company_id']) > 0) {
                $this->company         = new Vendor($this->data['company_id']);
                $this->data['company'] = $this->company->get();
            }
            $this->data['my_url'] = $this->getUrl();
        }
    }

    function loadOAuthRegistrations() {
        $providers = array();
        $query = sprintf("SELECT provider FROM oauth
                            WHERE oauth.user_id = %d",
                          $this->get_data('user_id'));
        $result = Database::execArray($query);
        foreach ($result as $provider) {
            $providers[$provider['provider']] = $provider['provider'];
        }

        $this->data['oauth'] = $providers;
    }

    function set($data) {
        if (isset($data['email'])) {
            $data['email'] = strtolower(trim($data['email']));
        }

        // second condition probably can be scipped, but is left to be sure
        if (isset($data['clear_company']) || empty($data['company_id']) && empty($this->data['company_id'])) {
            $data['company_id'] = 0;
        }

        if (empty($data['logo_id']) && empty($this->data['logo_id'])) {
            $data['logo_id'] = 0;
        }

        $result = parent::set($data);

        if ($result == 1) {
            $code_name               = strtolower($this->data['first_name'] . " " . $this->data['last_name']);
            $this->data['code_name'] = $this->generate_code_name($code_name, (isset($this->data['user_id']) ? $this->data['user_id'] : null));
        }

        if (!empty($data['logo_hash'])) {
            $logo = new Logo(null, $data['logo_hash']);
            if ($logo->get_data('logo_hash') !== $this->data['code_name'] . $this::$logo_hash_suffix) {
                $logo->rename($this->data['code_name'] . $this::$logo_hash_suffix);
            }
            $this->data['logo_id'] = $logo->get_data('logo_id');
        }

        return $result;
    }

    function save() {

        if (empty($this->data['unsubscribe_key'])) {
            $this->data['unsubscribe_key'] = User::generateRandomKey();
        }

        if (empty($this->data['confirm_email_key'])) {
            $this->data['confirm_email_key'] = User::generateRandomKey();
        }

        if (empty($this->data['cookie_key'])) {
            $this->data['cookie_key'] = User::generateRandomKey();
        }

        $primary_id = parent::save();
        if ($primary_id && $primary_id == get_user_id()) {
            user_login($this);
            Utils::setPermissions();
        }
        return $primary_id;
    }

    function delete() {
        global $db;

        $dquery = "DELETE FROM user WHERE user_id = '" . $db->escape_text($this->data['user_id']) . "'";
        $db->query($dquery);

        $dquery = "DELETE FROM link WHERE ( entity1_type = 'user' AND entity1_id = '" . $db->escape_text($this->data['user_id']) . "' ) OR ( entity2_type = 'user' AND entity2_id = '" . $db->escape_text($this->data['user_id']) . "' )";
        $db->query($dquery);

        $dquery = "DELETE FROM rating WHERE user_id = '" . $db->escape_text($this->data['user_id']) . "'";
        $db->query($dquery);

        $dquery = "DELETE FROM vote WHERE user_id = '" . $db->escape_text($this->data['user_id']) . "'";
        $db->query($dquery);

        $dquery = "DELETE FROM question WHERE user_id = '" . $db->escape_text($this->data['user_id']) . "'";
        $db->query($dquery);

        $dquery = "DELETE FROM comment WHERE user_id = '" . $db->escape_text($this->data['user_id']) . "'";
        $db->query($dquery);

        /* todo bear we should delete all notifications for deleted user's comments and posts too, but this is a hard task.
         * Postponed, may be we will use FK with cascade deletions..*/
        $dquery = "DELETE FROM notifications WHERE user_id = " . $this->data['user_id'];
        $db->query($dquery);

        $this->recache();
    }

    function load_vendors_used() {
        global $db;

        $this->data['vendors_used'] = array();
        $this->data['vendor_count'] = 0;

        $this->data['vendors_used_ids'] = array();

        $custom_query = "SELECT v.vendor_id " .
                "FROM vendor v  " .
                "INNER JOIN link li ON li.entity2_id = v.vendor_id AND li.entity2_type = 'vendor' AND li.link_type = 'use' " .
                "INNER JOIN user u ON u.user_id = li.entity1_id AND li.entity1_type = 'user' " .
                "WHERE li.entity1_id = '" . $db->escape_text($this->get_data('user_id')) . "' AND li.entity1_type = 'user' " .
                "ORDER BY rand()";

        $vendor_list = new CustomList('vendor', $custom_query);

        $vendor_list->set_containing_entity('user', $this->get_data('user_id'));

        $this->data['vendors_used'] = $vendor_list->get();
        $this->data['vendor_count'] = count($this->data['vendors_used']);

        if (isset($this->data['vendors_used']) && is_array($this->data['vendors_used']))
            $this->data['vendors_used_ids'] = array_keys($this->data['vendors_used']);

        $this->data['vendors_like_this']       = array();
        $this->data['vendors_like_this_count'] = 0;

        if (is_array($this->data['vendors_used'])) {
            $first = current($this->data['vendors_used']);
            $v     = new Vendor($first['vendor_id']);
            $v->load_similar_list();

            $this->data['vendors_like_this']       = $v->get_data('similar_list');
            $this->data['vendors_like_this_count'] = count($this->data['vendors_like_this']);
        }
    }

    function loadFollowingByEntityTypes() {
        $types = array('vendor'=>array(), 'user'=>array(), 'tag'=>array());
        foreach ($this->data['following'] as $follow) {
            switch ($follow['entity_type']) {
                case 'vendor':
                case 'user':
                case 'tag':
                    $types[$follow['entity_type']][$follow['entity_uid']] = $follow['entity_id'];
                    break;
            }
        }

        $this->data['following_by_entity_type'] = $types;
    }

    function loadRecentConnections() {
        $this->data['recent_connections'] = array();
        if (get_user_id() != $this->data['user_id']) {
            return;
        }

        $new_users = Notification::getUsersJoinedLastDays();
        $recent_people = Notification::combineUsersFollowersAndJoins(get_user_id(), $new_users);
        $this->data['recent_connections'] = Utils::prepareFollowDataForUsers($recent_people);
    }

    static function followUser($user_id, $no_recache = false) {
        if (!is_logged_in()) {
            return;
        }

        if ($user_id == get_user_id()) {
            return;
        }

        $follow_data = array();
        $follow_data['whom_type'] = 'user';
        $follow_data['whom_id'] = $user_id;
        Link::createFollowLink($follow_data);

        if ($no_recache) {
            return;
        }

        $user = new User(get_user_id());
        $user->recache();
        user_login($user);
    }

    function setAutoFollowing() {

        if (!$this->data['invited_by']) {
            return;
        }
        $inviter_id = $this->data['invited_by'];
        $user_id = $this->data['user_id'];

        Link::createAutoFollowLink($user_id, $inviter_id);

        $inviter = new User($inviter_id);
        $inviter->recache();

        $user = new User($user_id);
        $user->recache();
        if ($user_id == get_user_id()) {
            user_login($user);
        }
    }

    function setCurrentUserAsFollower() {
        $data = array(
            'entity1_id'   => get_user_id(),
            'entity1_type' => 'user',
            'entity2_id'   => $this->data['user_id'],
            'entity2_type' => 'user',
            'link_type'    => 'follow'
        );
        Link::createLink($data);
    }

    function load_comments() {
        global $db;

        $this->data['comment_list']  = array();
        $this->data['comment_count'] = 0;

        $self_or = Utils::getSelfOr('a');

        $custom_query = "SELECT DISTINCT a.comment_id, q.code_name as question_code_name, r.vendor_id " .
                "FROM comment a " .
                "INNER JOIN relation r ON r.entity_id = a.comment_id AND r.entity_type = 'comment' " .
                "LEFT JOIN question q ON q.question_id = a.post_id " .
                "WHERE a.post_type = 'question' " .
                "AND ( a.status IN ( 'active', 'review' ) $self_or ) " .
                "AND r.is_review = 1 AND a.user_id = '" . $db->escape_text(get_user_id()) . "' " .
                "GROUP BY a.comment_id " .
                "ORDER BY a.date_added DESC";

        $comment_list = new CustomList('comment', $custom_query);
        $comment_list->set_containing_entity('user', $this->get_data('user_id'));

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

    static function getOauthContactsByUserId($user_id=null) {
        $contacts = array();

        if (!$user_id) {
            $user_id = get_user_id();
            if (!$user_id) {
                return $contacts;
            }
        }

        $query = sprintf("SELECT user_contacts, provider FROM oauth WHERE user_id=%d",
                            $user_id);
        $results = Database::execArray($query);

        foreach ($results as $row) {
            $contacts_row = json_decode($row['user_contacts'], true);
            if (!is_array($contacts_row)) {
                continue;
            }
            foreach ($contacts_row as $contact) {
                $contact['provider'] = $row['provider'];
                if (!isset($contact['local_id'])) {
                    $contact['local_id'] = User::generateRandomKey();
                }
                $contacts[$contact['local_id']] = $contact;
            }
        }

        return $contacts;
    }

    function get_seo_attributes() {
        $seo_attributes                = array();
        $seo_attributes['title']       = '';
        $seo_attributes['keywords']    = '';
        $seo_attributes['description'] = '';

        if ($this->is_loaded()) {
            $seo_attributes['title']    = $this->data['full_name'];
            $seo_attributes['keywords'] = $this->data['full_name'];

            if (strlen($this->data['about']) > 3) {
                if (strlen($this->data['about']) < 100) {
                    $seo_attributes['description'] = $this->data['full_name'] . " - " . $this->data['about'];
                } else {
                    $pos                           = strpos($this->data['about'], ' ', 100);
                    $seo_attributes['description'] = $this->data['full_name'] . " - " . substr($this->data['about'], 0, $pos) . "...";
                }
            } else {
                $seo_attributes['description'] = $this->data['full_name'];
            }
        }

        return $seo_attributes;
    }

    function insertOAuthRegistration($oauth_data) {
        global $db;

        $query = sprintf("INSERT INTO oauth
                        (user_id, provider, provider_uid,
                        hauth_info, provider_info, user_contacts, created_ts)
                        VALUES (
                            %d, '%s', '%s',
                            '%s', '%s', '%s', NOW()
                            )",
                        $this->get_data('user_id'),
                        $oauth_data['provider'],
                        $oauth_data['provider_uid'],
                        $db->escape_text(json_encode($oauth_data['hauth_info'])),
                        $db->escape_text(json_encode($oauth_data['provider_info'])),
                        $db->escape_text(json_encode($oauth_data['user_contacts']))
                );

        $db->query($query);
        return;
    }

    function getName() {
        $name = '';
        if ($this->is_loaded()) {
            $name = $this->get_data('full_name');
        }
        return $name;
    }

    function resetPassword() {
        $new_password             = $this->generatePassword(8, true);
        $current_data             = $this->get();
        $current_data['password'] = self::getPasswordHash($new_password);
        $this->set($current_data);
        $this->save();
        return $new_password;
    }

    function getPasswordResetKey() {
        $reset_key                       = self::generateRandomKey();
        $current_data                    = $this->get();
        $current_data['reset_passw_key'] = $reset_key;
        $current_data['reset_passw_ts']  = date('Y-m-d H:i:s');
        $this->set($current_data);
        $this->save();
        return $reset_key;
    }

    function clearPasswordResetKey() {
        $current_data                    = $this->get();
        $current_data['reset_passw_key'] = '';
        // todo bear we should allow to store NULL for datetime fields
        // To not use 'standard' '0000-00-00 00:00:00', will use current time.
        $current_data['reset_passw_ts']  = date('Y-m-d H:i:s');
        $this->set($current_data);
        $this->save();
    }

    function getUserIdByResetPasswordKey($reset_key) {
        $RESET_KEY_TIME_TO_LIVE_HOURS = 24;
        global $db;
        $where                        = array('reset_passw_key' => $reset_key);

        $result = $db->select($this->table_name, array('user_id', 'reset_passw_ts'), null, $where);
        if (!$result || !$result[0]['user_id']) {
            return false;
        }

        $reset_passw_ts = strtotime($result[0]['reset_passw_ts']);
        $current_ts     = time();

        if (floor(($current_ts - $reset_passw_ts) / 60 / 60) > $RESET_KEY_TIME_TO_LIVE_HOURS) {
            $temp_user = new User($result[0]['user_id']);
            if ($temp_user->is_loaded()) {
                $temp_user->clearPasswordResetKey();
            }
            return false;
        }

        return $result[0]['user_id'];
    }

    public function claimVendor($first_name, $last_name, $email, $vendor_id, $by_admin = false) {
        /* TODO remove - Not used anymore */
        Log::$logger->fatal("Usage of old processCustomerInvites function");
        return false;

        $BLOCK_VENDOR_CLAIMING_CLAIMS_COUNT = 5;
        $update_needed                      = false;

        $current_data = $this->get();

        if (!$by_admin) {
            // as admin enters user by email, we can't change it.
            if (strtolower($current_data['email']) !== strtolower($email)) {
                $current_data['email'] = $email;
                $update_needed         = true;
            }
        }
        if ($current_data['first_name'] !== $first_name) {
            $current_data['first_name'] = $first_name;
            $update_needed              = true;
        }
        if ($current_data['last_name'] !== $last_name) {
            $current_data['last_name'] = $last_name;
            $update_needed             = true;
        }

        $vendor = new Vendor($vendor_id);
        if (!$vendor->is_loaded()) {
            return 'Server error. Please try again.';
        }

        if (!$vendor->checkEmailDomain($email)) {
            $allowed_domain = $vendor->getEmailDomain();
            return "Sorry, only a user with an email from @$allowed_domain can claim this profile.";
        }

        if ($update_needed) {
            $this->set($current_data);
            $errors = $this->get_errors();
            if ($errors) {
                return current($errors);
            } else {
                $this->save();
            }
        }

        $claim_key = self::generateRandomKey();

        require_once('class.Claim.php');
        require_once('class.Mailer.php');

        Claim::insertClaim($this->get_data('user_id'), $vendor_id, 'vendor', $claim_key);

        if (!$by_admin && (Claim::getDailyClaimsCount($vendor_id, 'vendor') >= $BLOCK_VENDOR_CLAIMING_CLAIMS_COUNT)) {
            $vendor->setClaimBlockFlag(true);
            Log::$logger->error("Vendor " . $vendor->getName() . " is locked for claiming now as it has " . $BLOCK_VENDOR_CLAIMING_CLAIMS_COUNT . " claims for last 24 hours.");
        }

        $mail_type = 'claim_entity';
        if ($by_admin) {
            $mail_type = 'claim_invite';
        }

        // mailer class constructor changed
        // $mailer = new Mailer($mail_type, $this->get_data('user_id'), $claim_key, $vendor);
        // $mailer->send();

        return true;
    }

    function hasClaims() {
        global $db;

        require_once('class.Claim.php');
        $claim_requests = Claim::getClaimsByUser($this->get_data('user_id'));

        if ($claim_requests) {
            return true;
        }

        $where       = array('owner_user_id' => $this->get_data('user_id'));
        $own_vendors = $db->select('vendor', array('vendor_id'), null, $where);

        if ($own_vendors) {
            return true;
        }

        return false;
    }

    function deleteClaimsAndOwnership() {
        global $db;

        require_once('class.Claim.php');
        $claim_requests = Claim::getClaimsByUser($this->get_data('user_id'));

        if ($claim_requests) {
            Claim::deleteUsersClaims($this->get_data('user_id'));
        }

        $where       = array('owner_user_id' => $this->get_data('user_id'));
        $own_vendors = $db->select('vendor', array('vendor_id'), null, $where);

        if ($own_vendors) {
            foreach ($own_vendors as $vendor) {
                $vendor = new Vendor($vendor['vendor_id']);
                $vendor->deleteClaim();
            }
        }
    }

    function setAccessToNewVS($allowed = true) {
        if (!$this->is_loaded()) {
            return false;
        }

        $new_vs_allowed = $allowed ? 1 : 0;

        $current_data = $this->get();
        $current_data['new_vs_allowed'] = $new_vs_allowed;
        $this->set($current_data);
        $this->save();
    }

    function suspend($suspend) {
        $status = 'active';
        if ($suspend) {
            $status = 'inactive';
        }

        $this->data['status'] = $status;
        $this->save();
    }

    function disable() {
        $raw_user_data = array();
        $sql = sprintf("SELECT * FROM user WHERE user_id = %d", $this->data['user_id']);
        $raw_user_data['user'] = Database::execArray($sql, true);
        $sql = sprintf("SELECT provider, provider_uid FROM oauth WHERE user_id = %d", $this->data['user_id']);
        $raw_user_data['oauth'] = Database::execArray($sql);

        $sql = sprintf("SELECT entity1_type AS entity_type, entity1_id AS entity_id
                        FROM link
                        WHERE entity2_type='user' AND entity2_id=%d", $this->data['user_id']);
        $raw_user_data['following'] = Database::execArray($sql);

        $sql = sprintf("SELECT entity2_type AS entity_type, entity2_id AS entity_id
                        FROM link
                        WHERE entity1_type='user' AND entity1_id=%d", $this->data['user_id']);
        $raw_user_data['followers'] = Database::execArray($sql);

        $sql = sprintf("INSERT INTO disabled_users (user_id, email, first_name, last_name, account_data, created_ts)
                        VALUES (%d, '%s', '%s', '%s', '%s', now());",
                        $this->data['user_id'],
                        Database::escapeString($this->data['email']),
                        Database::escapeString($this->data['first_name']),
                        Database::escapeString($this->data['last_name']),
                        Database::escapeString(json_encode($raw_user_data)));
        Database::exec($sql);

        $this->delete();
    }

    static function getEmptyEditAccountData() {
        $data = array();
        $data['first_name'] = array('type'=>'input', 'title'=>'First Name', 'value'=>'', 'f_needed' => true);
        $data['last_name'] = array('type'=>'input', 'title'=>'Last Name', 'value'=>'', 'f_needed' => true);
        $data['email'] = array('type'=>'input', 'title'=>'Primary Email', 'value'=>'', 'f_needed' => true);
        return $data;
    }

    static function getEmptyProfileData() {
        $data = array();
        $data['first_name'] = array('type'=>'input', 'title'=>'First Name', 'value'=>'', 'f_needed' => true);
        $data['last_name'] = array('type'=>'input', 'title'=>'Last Name', 'value'=>'', 'f_needed' => true);
        $data['position'] = array('type'=>'input', 'title'=>'Position', 'value'=>'', 'f_needed' => true);
        $data['company_name'] = array('type'=>'input', 'title'=>'Company', 'value'=>'', 'f_needed' => false);
        $data['location'] = array('type'=>'input', 'title'=>'Location', 'value'=>'', 'f_needed' => false);
        $data['about'] = array('type'=>'textarea', 'title'=>'My Byline', 'value'=>'', 'f_needed' => false, 'max_length'=>Utils::MAX_ABOUT_LENGTH);
        $data['website'] = array('type'=>'input', 'title'=>'Website', 'value'=>'', 'f_needed' => false);
        $data['linkedin'] = array('type'=>'input', 'title'=>'LinkedIn', 'value'=>'', 'f_needed' => false);
        $data['facebook'] = array('type'=>'input', 'title'=>'Facebook', 'value'=>'', 'f_needed' => false);
        $data['twitter'] = array('type'=>'input', 'title'=>'Twitter', 'value'=>'', 'f_needed' => false);
        $data['google_plus'] = array('type'=>'input', 'title'=>'Google Plus', 'value'=>'', 'f_needed' => false);
        $data['description'] = array('type'=>'input', 'title'=>'Summary', 'value'=>'', 'f_needed' => false);

        return $data;
    }

    static function getEmptyPasswordData() {
        $data = array();
        $data['password'] = array('type'=>'input', 'title'=>'Current password', 'value'=>'', 'f_needed' => true);
        $data['new_password'] = array('type'=>'input', 'title'=>'New password', 'value'=>'', 'f_needed' => true);

        return $data;
    }

    static function fillInEditAccountData($data, $user=null) {
        if ($user) {
            $data['first_name']['value'] = $user->get_data('first_name');
            $data['last_name']['value'] = $user->get_data('last_name');
            $data['email']['value'] = $user->get_data('email');

        } else {
            $data['first_name']['value'] = $_SESSION['user_info']['first_name'];
            $data['last_name']['value'] = $_SESSION['user_info']['last_name'];
            $data['email']['value'] = $_SESSION['user_info']['email'];
        }
        return $data;
    }

    static function getPasswordInfoFromRequest() {
        $data = self::getEmptyPasswordData();

        foreach ($data as $key=>&$field) {
            $field['value'] = Utils::reqParam($key);
        }

        return $data;
    }

    static function getEditInfoFromRequest() {
        $data = self::getEmptyEditAccountData();

        foreach ($data as $key=>&$field) {
            $field['value'] = Utils::reqParam($key);
        }

        return $data;
    }

    static function getProfileInfoFromRequest() {
        $data = self::getEmptyProfileData();

        foreach ($data as $key=>&$field) {
            $field['value'] = Utils::reqParam($key);
        }

        return $data;
    }

    static function validatePasswordData($data) {
        $errors = array();

        foreach ($data as $name => $field) {
            if ($field['f_needed'] && !$field['value']) {
               $errors[$name] = array('name' => $name, 'msg'  => "You must enter a value.");
               continue;
            } else if ($name == 'password') {
                if (crypt($field['value'], $_SESSION['user_info']['password']) !== $_SESSION['user_info']['password']) {
                    $errors[$name] = array('name' => $name, 'msg'  => "The current password you entered is incorrect.");
                }
            }
        }
        return $errors;
    }

    static function validateEditData($data) {
        $errors = array();

        $errors = Utils::validateCommonProfileData($data);

        if (!$errors && $data['email']) {
            if (strtolower($data['email']['value']) !== $_SESSION['user_info']['email']) {
                $user = new User();
                $user->load_email(strtolower($data['email']['value']));
                if ($user->is_loaded()) {
                    // todo move html from here
                    $errors['email'] = array('name' => 'email', 'msg'  => "This email already used.");
                }
            }
        }

        return $errors;
    }

    static function validateProfileData($data) {
        $errors = Utils::validateCommonProfileData($data);
        return $errors;
    }

    static function validatePubPostSettings($data) {
        if ($data['default_tag_chk'] &&
                (!$data['autopost_tag_id'] || !isset(Utils::$tags_parents_vendor[$data['autopost_tag_id']]))
            )
        {
            return "Please, select default bloc!";
        }

        if ($data['f_autopost'] && !$data['default_tag_chk']) {
            return "Please, select default bloc for auto-post!";
        }

        require_once('class.Feed.php');
        if ($data['f_autopost'] && !Feed::validateRSSUrl($data['rss'])) {
            return "Sorry, invalid RSS feed.";
        }

        return true;
    }

    static function saveUpdatedPassword($new_data) {
        require_once('class.Mailer.php');

        $user = new User(get_user_id());

        $user_data = $user->get();
        $user_data['password'] = User::getPasswordHash($new_data['new_password']['value']);

        $user->set($user_data);
        $user->save();

        $mailer = new Mailer('password_changed');
        $mailer->sendPasswordChanged($user);
    }

    static function saveUpdatedFGetSponsorEmail($f_get_sponsor_email) {

        $value_updated = false;

        $user = new User(get_user_id());
        $current_data = $user->get();

        if ($current_data['f_get_sponsor_email'] != $f_get_sponsor_email) {
            $current_data['f_get_sponsor_email'] = $f_get_sponsor_email;
            $user->set($current_data);
            $user->save();

            $value_updated = true;
        }

        return $value_updated;
    }

    static function saveUpdatedUserInfo($info) {
        $user = new User(get_user_id());

        $user_data = $user->get();
        $user_data['first_name'] = $info['first_name']['value'];
        $user_data['last_name'] = $info['last_name']['value'];
        $user_data['email'] = $info['email']['value'];

        $user->set($user_data);
        $user->save();

        $redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . $user->data['my_url'] . '/account';

        return $redirect_url;
    }

    static function saveUpdatedUserProfile($info) {
        $user = new User(get_user_id());

        $user_data = $user->get();
        $user_data['first_name'] = $info['first_name']['value'];
        $user_data['last_name'] = $info['last_name']['value'];
        $user_data['position'] = $info['position']['value'];
        $user_data['location'] = $info['location']['value'];
        $user_data['about'] = $info['about']['value'];
        $user_data['website'] = $info['website']['value'];
        $user_data['linkedin'] = $info['linkedin']['value'];
        $user_data['facebook'] = $info['facebook']['value'];
        $user_data['twitter'] = $info['twitter']['value'];
        $user_data['google_plus'] = $info['google_plus']['value'];
        $user_data['description'] = $info['description']['value'];
        //$user_data['logo'] = $info['']['value'];

        if ($info['company_name']['value']) {
            $company = new Vendor();
            $company->load_by_name($info['company_name']['value']);

            if (!$company->is_loaded()) {
                $company->set(array('vendor_name' => $info['company_name']['value']));
                $company->save();
            }

            $user_data['company_id'] = $company->get_data('vendor_id');
        } else {
            $user_data['clear_company'] = true;
        }


        $user->set($user_data);
        $user->save();

        $redirect_url = Utils::getBaseUrl() . $user->data['my_url'];
        return $redirect_url;
    }

    static function savePubPostSettings($data) {
        $result = self::validatePubPostSettings($data);
        if ($result!==true) {
            return $result;
        }

        $user = new User(get_user_id());
        $user_data = $user->get();

        $user_data['autopost_tag_id'] = 0;
        if ($data['default_tag_chk']) {
            $user_data['autopost_tag_id'] = $data['autopost_tag_id'];
        }

        $user_data['f_autopost'] = $data['f_autopost'];
        $user_data['rss'] = $data['rss'];

        $user->set($user_data);
        $user->save();

        return true;
    }

    static function getEmptySignupData() {
        $data = array();
        $data['image_url'] = array('type'=>'image', 'title'=>'Photo', 'value'=>'', 'f_needed' => false);
        $data['first_name'] = array('type'=>'input', 'title'=>'First Name', 'value'=>'', 'f_needed' => true);
        $data['last_name'] = array('type'=>'input', 'title'=>'Last Name', 'value'=>'', 'f_needed' => true);
        $data['email'] = array('type'=>'input', 'title'=>'Email', 'value'=>'', 'f_needed' => true);
        $data['position'] = array('type'=>'input', 'title'=>'Position', 'value'=>'', 'f_needed' => true);
        $data['company'] = array('type'=>'input', 'title'=>'Company', 'value'=>'', 'f_needed' => true);
        $data['password'] = array('type'=>'password', 'title'=>'Password', 'value'=>'', 'f_needed' => true);
        $data['about'] = array('type'=>'textarea', 'title'=>'My Byline', 'value'=>'', 'f_needed' => false, 'max_length'=>Utils::MAX_ABOUT_LENGTH);
        return $data;
    }

    static function getSignupDataFromRequest() {
        $data = self::getEmptySignupData();

        foreach ($data as $key=>&$field) {
            $field['value'] = Utils::reqParam($key);
        }

        return $data;
    }

    static function validateSignUpData($data) {
        $errors = array();
        $provider = '';

        if (isset($_SESSION['oauth_data'])) {
            $oauth_data = $_SESSION['oauth_data'];
            $provider = $oauth_data['provider'];
            $data['password']['f_needed'] = false;
        }

        $errors = Utils::validateCommonProfileData($data);

        if (!$errors) {
            $user = new User();
            $user->load_email(strtolower($data['email']['value']));
            if ($user->is_loaded() && !$user->get_data('f_contest_voter')) {
                // todo move html from here
                $errors['email'] = array('name' => 'email', 'msg'  => "This email already exists. <a href='" . Utils::getLoginUrl() . "'>Sign in now!</a>");
            }
        }
        return $errors;
    }

    static function getUserByEmail($email) {
        global $db;
        if (!$email) {
            return false;
        }

        $where = array('email' => strtolower($email));
        $result = $db->select('user', array('user_id', 'password'), null, $where);

        if (!$result) {
            return false;
        }

        return $result[0];
    }

    static function getUserIdByUnsubscribeKey($confirm_key) {
        global $db;
        if (!$confirm_key) {
            return false;
        }

        $where = array('unsubscribe_key' => $confirm_key);
        $result = $db->select('user', array('user_id'), null, $where);

        if (!$result) {
            return false;
        }

        return $result[0]['user_id'];
    }

    static function unsubscribe($type, $code) {
        $user_id = User::getUserIdByUnsubscribeKey($code);
        if (!$user_id) {
            return false;
        }

        $user = new User($user_id);
        $current_data = $user->get();
        if ($type == 'weekly') {
            $current_data['notify_weekly'] = 0;
            $type_name = 'weekly';
        } elseif ($type == 'updates')  {
            $current_data['notify_product_update'] = 0;
            $type_name = 'product updates';
        } elseif ($type == 'contest')  {
            $current_data['notify_contest'] = 0;
            $type_name = 'contest';
        } elseif ($type == 'daily')  {
            $current_data['notify_daily'] = 0;
            $type_name = 'daily';
        } elseif ($type == 'suggestion')  {
            $current_data['notify_suggestion'] = 0;
            $type_name = 'suggestion';
        } elseif ($type == 'deactivate') {
            $current_data['notify_suggestion'] = 0;
            $type_name = 'deactivate';
        }


        $current_data['unsubscribe_key'] = $user->generateConfirmKey();
        $user->set($current_data);

        if (!$user->save()) {
            return false;
        }

        $message = sprintf('You have been successfully unsubscribed from %s emails. We\'re sorry to see you go.<br>If this was in error, you can manage your email preferences <a href="%s">here</a>.',
                        $type_name,
                        $user->getUrl() . '/account?active_tab=notifications_tab');
        return $message;
    }

    static function getUserIdByConfirmEmailKey($confirm_key) {
        global $db;
        if (!$confirm_key) {
            return false;
        }

        $where = array('confirm_email_key' => $confirm_key);
        $result = $db->select('user', array('user_id'), null, $where);

        if (!$result) {
            return false;
        }

        return $result[0]['user_id'];
    }

    static function getUserIdByCredentials($email, $password) {
        if (!$email || !$password) {
            return false;
        }

        $user_data = self::getUserByEmail($email);
        if (!$user_data) {
            return false;
        }

        if (crypt($password, $user_data['password']) !== $user_data['password']) {
            Log::$logger->debug(sprintf("User entered not existing combination of email and password, email: %s", $email));
            return false;
        }
        return $user_data['user_id'];
    }

    static function getUserIdByOauthRegistration($provider, $provider_uid) {
        $query = sprintf("SELECT user_id FROM oauth
                           WHERE provider = '%s' AND provider_uid = '%s'",
                        $provider,
                        $provider_uid);
        $result = Database::execArray($query, true);
        if (!$result) {
            return false;
        }

        return $result['user_id'];
    }

    static function addOAuthRegistration($data) {
        $user_id = get_user_id();
        if (!$user_id) {
            Log::$logger->warn('No user id when creating new oAuth registration. Data:' . print_r($data, true));
            return true;
        }

        $existed_user_id = self::getUserIdByOauthRegistration($data['provider'], $data['provider_uid']);
        if ($existed_user_id) {
            if ($existed_user_id == $user_id) {
                Log::$logger->warn("Tried to connect with oauth whith existing connection. Will do nothing. user_id = $user_id, existed_user_id = $existed_user_id");
            } else {
                Log::$logger->error("Another user requested the same oAuth registration. user_id = $user_id, existed_user_id = $existed_user_id");
                $_SESSION['alert_message'] = "You can't connect with this profile because it's already connected to another account.";
                return false;
            }
            return true;
        }

        $user = new User($user_id);
        $user->insertOAuthRegistration($data);
        $user->fillUserDataFromOAuth($data);
        $user->recache();
        user_login($user);
        return true;
    }

    // does not do relogin currently, only recache
    function fillUserDataFromOAuth($data) {
        $user_data = $this->get();
        $changed = false;

        if (!$user_data['linkedin'] && $data['provider']=='linkedin' && $data['profile_url']) {
            $user_data['linkedin'] = $data['profile_url'];
            $changed = true;
        }

        if (!$user_data['twitter'] && $data['provider']=='twitter' && $data['profile_url']) {
            $user_data['twitter'] = $data['profile_url'];
            $changed = true;
        }

        if (!$user_data['position'] && $data['position']) {
            $user_data['position'] = $data['position'];
            $changed = true;
        }

        if (!$user_data['about'] && $data['about']) {
            $user_data['about'] = $data['about'];
            $changed = true;
        }

        if (!$user_data['location'] && $data['location']) {
            $user_data['location'] = $data['location'];
            $changed = true;
        }

        if (!($user_data['company']) && $data['company']) {
            $company = new Vendor();
            $company->load_by_name($data['company']);

            if ($company->is_loaded()) {
                $user_data['company_id'] = $company->get_data('vendor_id');
                $changed = true;
            }
        }

        if ($changed) {
            $this->set($user_data);
            $this->save();
        }

        // should be last as refreshes user itself
        if (!$user_data['logo_id'] && $data['image_url']) {
            $this->saveLogoByUrl($data['image_url']);
        }
    }

    public function saveUserEdits() {
        $saved_info = get_input('user');
        if (isset($saved_info['company_name']) && !strlen(trim($saved_info['company_name']))) {
            $saved_info['clear_company'] = true;
        }
        // todo bear seems that we have only client-side email validation on user edit. Shoud recheck this and add server-side validation.
        if ($this->set($saved_info)) {
            if (isset($saved_info['company_name']) && strlen(trim($saved_info['company_name']))) {
                $vendor_name = trim($saved_info['company_name']);
                $vendor = new Vendor();
                $vendor->load_by_name($vendor_name);
                if (!$vendor->is_loaded()) {
                    if ($vendor->set(array('company_name' => $vendor_name))) {
                        $vendor->save();
                    }
                }
                $saved_info['company_id'] = $vendor->get_data('vendor_id');
                $this->set($saved_info);
            }
            $this->save();
        }
    }

    public function generateConfirmKey() {
        // adding userid to make key unique
        $key = User::generateRandomKey() . $this->get_data('user_id');
        return $key;
    }


    public static function getPasswordHash($password) {
        if (CRYPT_BLOWFISH != 1) {
            Log::$logger->fatal("Can't get crypted hash for a password as CRYPT_BLOWFISH is not available on the system. Will return password in plain text.");
            return $password;
        }
        return crypt(trim($password), self::salt());
    }

    public static function generatePassword($length = 8, $only_alphabet_and_numbers = false) {
        $password = '';
        $pool     = array();
        if ($only_alphabet_and_numbers) {
            $pool = range('A', 'Z');
            $pool = array_merge($pool, range('a', 'z'));
            $pool = array_merge($pool, range('0', '9'));
        } else {
            $pool = range('!', '~');
        }
        $high = count($pool) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $pool[rand(0, $high)];
        }
        return $password;
    }

    public static function generateRandomKey() {
        return md5(self::generatePassword(60));
    }

    public static function salt($rounds = 10, $rand_length = 22) {
        $rand = '';
        $pool = range('A', 'Z');
        $pool = array_merge($pool, range('a', 'z'));
        $pool = array_merge($pool, range('0', '9'));
        $high = count($pool) - 1;

        for ($i = 0; $i < $rand_length; $i++) {
            $rand .= $pool[rand(0, $high)];
        }

        $salt = sprintf('$2a$%02d$%s$', $rounds, $rand);
        return $salt;
    }

    public static function getUserFeedTags() {
        if (!is_logged_in()) {
            return '[]';
        }

        if (empty($_SESSION['user_info']['feed_tags'])) {
            return '[]';
        }

        return $_SESSION['user_info']['feed_tags'];
    }

    /* Methods working with remember me cookie */
    function setRememberMeCookie() {
        $days_to_be_logged_in = 365;
        $time_to_store = time()+ 60*60*24*$days_to_be_logged_in;

        $key = '';
        if ($this->get_data('cookie_key')) {
            // to share single key through different browsers
            $key = $this->get_data('cookie_key');
        } else {
            $key = self::generateRandomKey();
        }
        $cookie_key = $this->get_data('user_id') . ':' . $key;

        setcookie('sb_uid', $cookie_key, $time_to_store, '/', '', false, true);

        $this->set(array('cookie_key'=>$key));
        $this->save();
    }

    function clearRememberMeCookie() {
        if (!$this->is_loaded()) {
            return;
        }
        setcookie('sb_uid', "", time()-60*60*24*365, '/', '', false, true);
        $this->set(array('cookie_key'=>''));
        $this->save();
    }

    public static function getUserIdByCookie() {
        if (!isset($_COOKIE['sb_uid']) || !trim($_COOKIE['sb_uid'])) {
            return null;
        }

        $parts = explode(":",$_COOKIE['sb_uid'], 2);
        if (count($parts)<2 || !is_numeric($parts[0])) {
            return null;
        }

        $user_id = $parts[0];
        $cookie_key = $parts[1];

        $sql = sprintf("SELECT user_id FROM user WHERE user_id=%d AND cookie_key='%s'",
                            $user_id,
                            Database::escapeString($cookie_key));
        $user = Database::execArray($sql, true);

        if ($user) {
            return $user_id;
        }

        // this user has timed out cookie
        setcookie ("sb_uid", "", time()-60*60*24*365);
        return null;
    }

    /* End of Methods working with remember me cookie */
    public static function createContestUser($user_data) {
        $data = array();
        $data['f_contest_voter'] = 1;
        $data['first_name'] = $user_data['first_name']['value'];
        $data['last_name'] = $user_data['last_name']['value'];
        $data['email'] = $user_data['email']['value'];

        $new_password = self::generatePassword(8, true);
        $data['password'] = self::getPasswordHash($new_password);

        $user = new User();
        $user->set($data);
        $user->save();
        return $user;
    }

    public static function confirmEmail() {
        $code = Utils::reqParam('code');
        if (!$code) {
            return false;
        }

        $user_id = User::getUserIdByConfirmEmailKey($code);

        if (!$user_id) {
            return false;
        }

        $user = new User($user_id);
        $current_data = $user->get();
        $current_data['date_confirmed'] = date('Y-m-d H:i:s');
        $current_data['confirm_email_key'] = $user->generateConfirmKey();
        $user->set($current_data);
        $user->save();

        User::loginByUserId($user->get_data('user_id'), true);

        return $user;
    }
}
