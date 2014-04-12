<?php

require_once('../includes/global.inc.php');
require_once('class.Vendor.php');
require_once('class.User.php');
require_once('class.LinkUse.php');
require_once('class.LinkFollow.php');
require_once('class.Question.php');
require_once('class.Comment.php');
require_once('class.Vote.php');
require_once('class.Screenshot.php');
require_once('class.PostedLink.php');

register_shutdown_function('Gate::graceful_shutdown');
Gate::doAction();

class Gate {

    const MSG_ERR_UNKNOWN           = "Unknown server error, please try later";
    const MSG_ERR_NOT_LOGGED        = "Sorry, you are not logged in";
    const MSG_ERR_PERMISSION_DENIED = "Permission denied";

    private static $results          = array();
    private static $params           = array();
    private static $type             = '';
    private static $user_id          = '';
    public static $actions          = array('vote', 'set_users', 'change_login',
        'delete_vendor', 'delete_user', 'delete_question', 'delete_comment',
        'delete_screenshot', 'check_mentions', 'merge_company', 'track_link',
        'import_vendors_from_de', 'get_tracks', 'claim_vendor', 'postContent',
        'get_images_by_url', 'delete_posted_link', 'getMoreEntities', 'forgot_password', 'postComment', 'recacheEntity',
        'clearNotifications', 'changeUserEmailSetting', 'addCustomInvite', 'signup', 'signin', 'oauth_disconnect',
        'changeUserSettings', 'saveProfile', 'savePubPostSettings', 'changePassword', 'joinInvites', 'setFollow', 'getFollowBlock',
        'sendCustomInvite', 'sendShareLink', 'saveVendorProfile', 'submitContestVotes', 'suspendUser', 'disableUser', 'subscribe', 'getPostStats', 'repost',
        'processEvent', 'getEventData', 'delEvent', 'updateUserFSponsor');

    public static $no_login_actions = array('track_link', 'import_vendors_from_de', 'postContent', 'postComment', 'get_images_by_url',
        'check_mentions', 'getMoreEntities', 'forgot_password', 'signup', 'signin', 'submitContestVotes', 'vote', 'subscribe');

    public static $admin_actions    = array('delete_vendor', 'delete_user',
        'get_tracks', 'recacheEntity', 'addCustomInvite', 'update_vendor', 'suspendUser', 'disableUser', 'getPostStats',
        'getEventData', 'delEvent');

    private static $exception_msg        = '';
    private static $no_errors        = false;

    public static function checkAction() {
        if (!self::$type) {
            self::processError(self::MSG_ERR_UNKNOWN);
            return false;
        }

        if (!in_array(self::$type, self::$actions)) {
            self::processError("Invalid command.");
            return false;
        }

        if (!is_logged_in() && !in_array(self::$type, self::$no_login_actions)) {
            self::processError(self::MSG_ERR_NOT_LOGGED);
            return false;
        }

        if (in_array(self::$type, self::$admin_actions) && !is_admin()) {
            self::processError(self::MSG_ERR_PERMISSION_DENIED);
            return false;
        }

        return true;
    }

    private static function processError($user_message = '', $is_error = false) {
        if (!$user_message) {
            $user_message = self::MSG_ERR_UNKNOWN;
        }

        $msg = sprintf("Error when doing %s. Exception: %s. Params are: %s. \n (%s)",
                        self::$type,
                        self::$exception_msg,
                        print_r(self::$params, true),
                        $user_message);

        if ($is_error) {
            Log::$logger->error($msg);
        } else {
            Log::$logger->info($msg);
        }

        self::$results['message'] = $user_message;
        self::$results['status'] = 'failure';
        return false;
    }

    public static function graceful_shutdown() {
        if (self::$no_errors) {
            // if no PHP errors are risen, result is output by doAction() method itself, and $no_errors is set to true
            exit;
        }
        if (!headers_sent()) {
            header('HTTP/1.1 200 OK');
        }

        $error = error_get_last();
        if (!is_null($error)) {
            error_log("Critical error while processing ajax query, errors are: " . print_r($error, true));
        }

        self::$results['status'] = 'failure';
        self::$results['message'] = self::MSG_ERR_UNKNOWN;
        echo json_encode(self::$results);
        exit;
    }

    public static function init() {
        self::$type = trim(get_input('cmd'));
        self::$user_id = get_user_id();

        self::$results['status'] = 'success';
        self::$results['message'] = '';
        self::$results['type'] = self::$type;

        Log::$logger->trace("Will do ajax action '" . self::$type . "'");

        if (!self::checkAction()) {
            return false;
        }

        self::$params['vendor_id'] = intval(get_input('vendor_id'));
        self::$params['company_id'] = intval(get_input('company_id'));
        self::$params['entity_id'] = intval(get_input('entity_id'));
        self::$params['entity_type'] = get_input('entity_type');

        self::$params['users'] = get_input('users');
        self::$params['vendors'] = get_input('vendors');

        return true;
    }

    public static function doAction() {
        if (self::init()) {
            try {
                $function_name = self::$type;
                if (method_exists('Gate', $function_name)) {
                    self::$function_name();
                } else {
                    self::processOldActions();
                }
            } catch (Exception $e) {
                self::$exception_msg = $e->getMessage();
                self::processError(self::MSG_ERR_UNKNOWN, true);
            }
        }

        echo json_encode(self::$results);
        //r(json_encode(self::$results));
        self::$no_errors = true;
        Log::$logger->trace("Ajax action " . self::$type . " done.");
    }

    // todo bear should process errors + return false on all errors, true should be returned by default.
    private static function processOldActions() {
        if (self::$type == "set_users" && self::$params['vendor_id'] && self::$params['users'] && Utils::$permissions['add_any_use_this']) {
            $vendor = new Vendor(self::$params['vendor_id']);

            if ($vendor->is_loaded()) {
                $user_ids      = array('company_ids' => array(), 'user_ids'    => array());
                $raw_ids_array = explode(',', self::$params['users']);

                foreach ($raw_ids_array as $user) {
                    list($entity_type, $entity_id) = explode('_', $user);
                    if ($entity_type == 'company') {
                        $user_ids['company_ids'][] = $entity_id;
                    } elseif ($entity_type == 'user') {
                        $user_ids['user_ids'][] = $entity_id;
                    }
                }

                $vendor->set_users($user_ids);
                return true;
            }
        } elseif (self::$type == "change_login") {
            $user = new User(self::$user_id);

            if ($user->is_loaded()) {
                $new_login = strtolower(trim(get_input('new_login')));

                $current_data = $user->get();

                $error_msg = "";

                if (!$new_login)
                    $error_msg = "You must specify your new login e-mail address.";
                else if (!validate_email($new_login))
                    $error_msg = "You must specify a valid e-mail address.";
                else {
                    $new_email_parts = explode('@', $new_login);
                    $old_email_parts = explode('@', $current_data['email']);
                    if ($new_email_parts[1] !== $old_email_parts[1]) {
                        $user->deleteClaimsAndOwnership();
                    }

                    $current_data['email'] = $new_login;
                    $user->set($current_data);
                    $errors                = $user->get_errors();

                    if (count($errors) > 0) {
                        $error_msg = current($errors);
                    } else {
                        $user->save();
                    }
                }

                if ($error_msg) {
                    self::processError($error_msg);
                    return false;
                } else {
                    return true;
                }
            }
        } elseif (self::$type == "delete_vendor") {
            $vendor_id = get_input('delete_vendor_id', 'request', 'integer');

            $vendor = new Vendor($vendor_id);

            if ($vendor->is_loaded()) {
                $vendor->delete();

                if (isset($error_msg)) {
                    self::processError($error_msg);
                    return false;
                } else {
                    return true;
                }
            }
        } elseif (self::$type == "delete_user") {
            $user_id = get_input('delete_user_id', 'request', 'integer');

            $user = new User($user_id);

            if ($user->is_loaded()) {
                $user->delete();

                if (isset($error_msg)) {
                    self::processError($error_msg);
                    return false;
                } else {
                    return true;
                }
            }
        } elseif (self::$type == "delete_question") {
            $question_id = get_input('question_id', 'request', 'integer');

            $question = new Question($question_id);

            if ($question->is_loaded()) {
                if ($question->get_data('user_id') == self::$user_id || is_admin()) {
                    $vendor_list = $question->get_data('vendor_list');

                    $question->delete();
                    $question->recache();

                    if (!empty($vendor_list) && count($vendor_list) > 0) {
                        foreach ($vendor_list AS $vid => $vend) {
                            $vendor = new Vendor($vid);
                            $vendor->recache();
                        }
                    }
                } else {
                    self::processError("You do not own this comment.");
                    return false;
                }

                if (isset($error_msg)) {
                    self::processError($error_msg);
                    return false;
                } else {
                    return true;
                }
            }
        } elseif (self::$type == "delete_comment") {
            $comment_id = get_input('comment_id', 'request', 'integer');

            $comment = new Comment($comment_id);

            if ($comment->is_loaded()) {
                if ($comment->get_data('user_id') == self::$user_id || is_admin()) {
                    $question_id = $comment->get_data('post_id');
                    $vendor_list = $comment->get_data('vendor_list');

                    $comment->delete();

                    $question = new Question($question_id);
                    $question->recache();

                    if (!empty($vendor_list) && count($vendor_list) > 0) {
                        foreach ($vendor_list AS $vid => $vend) {
                            $vendor = new Vendor($vid);
                            $vendor->recache();
                        }
                    }
                } else {
                    self::processError("You do not own this comment.");
                    return false;
                }

                if (isset($error_msg)) {
                    self::processError($error_msg);
                    return false;
                } else {
                    return true;
                }
            }
        } elseif (self::$type == "delete_posted_link") {
            $posted_link_id = get_input('posted_link_id', 'request', 'integer');
            $posted_link    = new PostedLink($posted_link_id);

            if ($posted_link->is_loaded()) {
                if ($posted_link->get_data('user_id') == self::$user_id || is_admin()) {
                    $vendor_list = $posted_link->get_data('vendor_list');
                    $posted_link->delete();

                    if (!empty($vendor_list) && count($vendor_list) > 0) {
                        foreach ($vendor_list AS $vid => $vend) {
                            $vendor = new Vendor($vid);
                            $vendor->recache();
                        }
                    }
                } else {
                    self::processError("You do not own this posted link.");
                    return false;
                }

                if (isset($error_msg)) {
                    self::processError($error_msg);
                    return false;
                } else {
                    return true;
                }
            }
        } elseif (self::$type == "delete_screenshot" && Utils::$permissions['edit_vendor']) {
            $screenshot_id = get_input('screenshot_id', 'request', 'numeric');

            $screenshot = new Screenshot($screenshot_id);
            if ($screenshot->is_loaded()) {
                $screenshot->delete();
                return true;
            } else {
                self::processError("Screenshot load failed.");
                return false;
            }
        } elseif (self::$type == "merge_company" && Utils::$permissions['edit_company']) {
            // added by bear@deepshiftlabs.com - 26 March 2013
            $error_msg        = '';
            $merge_company_id = get_input('merge_company_id', 'request', 'numeric');
            if (!$merge_company_id) {
                $error_msg = "Merge failed (destination company is not set).";
            }

            if (!$error_msg) {
                if (self::$params['company_id'] == $merge_company_id) {
                    $error_msg = "Merge failed (destination is the same as source).";
                }
            }

            if (!$error_msg) {
                $company = new Vendor(self::$params['company_id']);
                if (!$company->is_loaded()) {
                    $error_msg = "Merge failed (nothing was done).";
                }
            }

            if (!$error_msg) {
                if (!$company->merge($merge_company_id)) {
                    $error_msg = "Merge failed.";
                }
            }

            if (!$error_msg) {
                $company->delete();
            }

            if ($error_msg) {
                self::processError($error_msg);
                return false;
            } else {
                return true;
            }
        } elseif (self::$type == "track_link") {
            // added by bear@deepshiftlabs.com - 24 Apr 2013
            // @modified bear@deepshiftlabs.com - 15 May 2013 - moved to global tracking
            // this cmd will be processed inside the logVisit() in global.inc.php
            return true;
        } elseif (self::$type == "get_tracks") {
            $type = get_input('type');
            $download_csv = get_input('csv');

            require_once('class.Track.php');
            $results = Track::getTracks($type, $download_csv);
            if ($download_csv) {
                self::$no_errors = true;
                exit;
            }

            if (!is_array($results)) {
                self::processError($results);
                return false;
            }

            self::$results['data'] = $results;
            return true;
        } elseif (self::$type == "claim_vendor") {
            /*TODO remove - Not used anymore (and in the list of functions on the top of this page
             * and claim_bar.tpl)*/
            $error_msg = '';

            $first_name = trim(get_input('first_name'));
            $last_name  = trim(get_input('last_name'));
            $email      = trim(get_input('email'));

            $user = new User(self::$user_id);
            if (!$user->is_loaded()) {
                $error_msg = "Unknown server error. Please try again.";
                Log::$logger->error("Cant load user " . self::$user_id . " to claim vendor.");
            } elseif (!$email || !$first_name || !$last_name) {
                $error_msg = "Please fill all the fields in.";
            } elseif (!validate_email($email)) {
                $error_msg = "You must specify a valid e-mail address.";
            } else {
                $result = $user->claimVendor($first_name, $last_name, $email, self::$params['vendor_id']);
                if ($result !== true) {
                    $error_msg = $result;
                }
            }

            if ($error_msg) {
                self::processError($error_msg);
                return false;
            } else {
                return true;
            }
        } elseif (self::$type == "get_images_by_url") {
            require_once('class.NetUtils.php');
            $url    = get_input('url');
            $page_data = NetUtils::getImgLinksAndH1TitleFromPage($url);
            self::$results['data'] = $page_data;
            return true;
        } elseif (self::$type == "import_vendors_from_de") {
            // todo bear this should be moved to config file when we will have it
            $allowed_domains = array("http://cws.tril2.trillium.lan:9292",
                "http://cws.tril2.trillium.lan:9191",
                "http://tagtagrevenge.com",
                "http://test.tagtagrevenge.com");
            if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_domains)) {
                header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
            } else {
                //header("Access-Control-Allow-Origin: *");
                self::$results['message'] = "Access denied";
                return true;
            }

            $error_msg = '';

            $de_vendors_ids = get_input('de_vendors_ids');
            if (!$de_vendors_ids) {
                $error_msg = "Bad vendors list";
            }

            if (!$error_msg) {
                $ids_array = explode(',', $de_vendors_ids);
                if (!$ids_array) {
                    $error_msg = "Can't convert list to array.";
                }
            }

            $message = '';
            if (!$error_msg) {
                require_once('class.DEImport.php');
                $results         = DEImport::import_vendors_from_DE($ids_array);
                $vendors_count   = count($results['vendors']);
                $companies_count = count($results['companies']);
                $message         = sprintf("Successfully exported %d vendor%s, %d compan%s", $vendors_count, $vendors_count == 1 ? '' : 's', $companies_count, $companies_count == 1 ? 'y' : 'ies');

                if ($results['errors']) {
                    $message .= ", but there was some warnings:\n\n" . implode("\n", $results['errors']);
                }
            }

            if ($error_msg) {
                self::processError($error_msg);
                return false;
            } else {
                self::$results['message'] = $message;
                return true;
            }
        } else {
            Log::$logger->error("No action done by Gate.");
            self::processError(self::MSG_ERR_UNKNOWN, true);
            return false;
        }

        Log::$logger->warn("Action done by Gate, but with unprocessed error. Action was " . self::$type);
        return true;
    }

    private static function getMoreEntities() {
        $offset = intval(get_input('offset'));
        $page_type = get_input('page_type');
        $entity_id = intval(get_input('entity_id'));
        $follow_type = get_input('follow_type');

        if ($page_type == 'calendar') {
            require_once('class.Event.php');
            $data = Event::getEventsForMore($entity_id, $offset);
        } else {
            if ($follow_type) {
                require_once('class.LinkFollow.php');
                $data = LinkFollow::getFollowsForMore($page_type, $entity_id, $follow_type, $offset);
            } else {
                require_once('class.FrontStream.php');
                $data = FrontStream::getPostsForMore($page_type, $entity_id, $offset);
            }
        }
        if (!$data) {
            self::processError(self::MSG_ERR_UNKNOWN);
        }
        self::$results['data'] = $data;
    }

    private static function postContent() {
        $post_type = trim(get_input('post_type'));
        $allowed_post_types = array('posted_link', 'question', 'contest');
        if (!in_array($post_type, $allowed_post_types)) {
            self::processError();
            return;
        }
        $result = false;
        if ($post_type == "posted_link" || $post_type == "contest") {
            $f_contest = false;
            if ($post_type=='contest') {
                $f_contest = true; //get_input('contest_id')
            }

            $posted_entity = new PostedLink(null, null, $f_contest);
            $result = $posted_entity->processPostedLink();
            $redirect_url_type = ($post_type=='contest') ? 'my_url' : 'iframe_url';
        } elseif ($post_type == "question") {
            $posted_entity = new Question();
            $result = $posted_entity->processPostedQuestion();
            $redirect_url_type = 'my_url';
        }

        if ($result !== true) {
            self::processError($result);
        } elseif (is_logged_in()) {
            self::$results['result_url'] = $posted_entity->get_data($redirect_url_type);
        }
    }

    private static function forgot_password() {
        require_once('class.Mailer.php');

        $email = trim(get_input('email', 'request'));

        if (!validate_email($email)) {
            self::processError("Invalid e-mail.");
            return false;
        }

        $user = new User();
        $user->load_email($email);

        if (!$user->is_loaded()) {
            self::processError("No user registered with this email.");
            return false;
        }

        $mailer = new Mailer('lost_password');
        $mailer->sendResetPasswordEmail($user);
    }

    private static function postComment() {
        $comment = new Comment();
        $result = $comment->processPostedComment();
        if ($result !== true) {
            self::processError($result);
        }
    }

    private static function recacheEntity() {
        $type = get_input('entity_type');
        $code_name = get_input('code_name');
        switch ($type) {
            case 'user':
                $entity = new User(null, $code_name);
                break;
            case 'vendor':
                $entity = new Vendor(null, $code_name);
                break;
        }

        if (!$entity->is_loaded()) {
            self::processError("Entity not found");
            return false;
        }
        $entity->recache();
    }

    private static function sendCustomInvite() {
        require_once('class.Mailer.php');
        require_once('class.InviteCustom.php');

        $data = $_REQUEST;
        $data['addressee'] = $data;

        $data['confirm_key'] = get_user_code_name();
        $result = InviteCustom::addCustomInviteSent($data);

        if ($result !== true) {
            self::processError($result);
            return false;
        }

        $mailer = new Mailer('invite_custom_sent');
        $mailer->sendInvite($data);

    }

    private static function sendShareLink() {
        require_once('class.Mailer.php');

        $data = $_REQUEST;
        $data['addressee'] = $data;

        if (!validate_email($data['email'])) {
            Log::$logger->info("Invalid email " . $data['email'] . " when sharing a link, data is:= " . print_r($data, true));
            self::processError("Email " . $data['email'] . " is invalid.");
            return false;
        }

        $mailer = new Mailer('share_link');
        $mailer->sendShareLink($data);
    }

    private static function clearNotifications() {
        Notification::clearUserNotifications();
    }

    private static function changeUserEmailSetting() {
        $setting_name = get_input('setting_name');
        $state = get_input('state');

        if (!in_array($setting_name, array('notify_weekly', 'notify_post_responded',
                                            'notify_comment_responded', 'notify_product_update',
                                            'notify_daily', 'notify_suggestion'))) {
            self::processError("Bad setting name.");
            return false;
        }

        $user = new User(get_user_id());
        $current_data = $user->get();
        $current_data[$setting_name] = $state ? 1 : 0;
        $user->set($current_data);
        $user->save();
    }

    private static function addCustomInvite() {
        require_once('class.InviteCustom.php');

        $data = array();
        $data['confirm_key'] = trim(get_input('confirm_key'));
        $data['comment'] = trim(get_input('comment'));

        $result = InviteCustom::addCustomInvite($data);
        if ($result!==true) {
            self::processError($result);
        }
    }

    private static function check_mentions() {

        $question_text = get_input('question_text');

        $result = Utils::getVendorMentioned($question_text);
        if ($result) {
            self::$results['message'] = $result;
        }
    }

    private static function signup() {
        require_once('class.ExtAuth.php');
        $USE_REMEMBER_ME = true;

        self::$results['errors'] = false;
        self::$results['redirect_url'] = '';

        $user_data = User::getSignupDataFromRequest();
        $errors = User::validateSignUpData($user_data);
        if ($errors) {
            self::$results['errors'] = $errors;
            return;
        }

        $old_style_data = array();
        foreach ($user_data as $key=>$data) {
            $old_style_data[$key] = $data['value'];
        }

        if (Utils::signUpUser($old_style_data, $USE_REMEMBER_ME)!==true) {
            self::processError();
        } else {
            Utils::processActionsWithRedirects();
            self::$results['redirect_url'] = '/join_follow.php';
        }
    }

    private static function signin() {
        self::$results['errors'] = false;
        self::$results['redirect_url'] = '';
        $errors = array();

        $email = trim(get_input('email'));
        $password = trim(get_input('password'));
        $REMEMBER_ME = true;

        if (!$email) {
            $errors['email'] = array('name'=>'email', 'msg'=>"You must enter a value.");
        }
        if (!$password) {
            $errors['password'] = array('name'=>'password', 'msg'=>"You must enter a value.");
        }
        if ($errors) {
            self::$results['errors'] = $errors;
            return;
        }

        if (!User::loginByCredentials($email, $password, $REMEMBER_ME)) {
            $error_msg = Utils::unsetSVar('alert_message');
            if (!$error_msg) {
                $error_msg = "Wrong email or password";
            }

            self::processError($error_msg);
            return false;
        }

        Utils::processSuccessfullLogin();
        self::$results['redirect_url'] = Utils::getPageToReturnAfterLogIn();
    }

    private static function oauth_disconnect() {

         $provider = trim(get_input('provider'));

        if(!$provider) {
            Log::$logger->error("Provider name is empty");
            self::processError("Unknown server error");
            return false;
        } else if (!in_array($provider, array('linkedin', 'twitter', 'google'))) {
            Log::$logger->error("Provider name is incorrect");
            self::processError("Unknown server error");
            return false;
        }

        User::oauthDisconnect($provider);

    }

    private static function changeUserSettings() {
        self::$results['errors'] = false;

        $user_data = User::getEditInfoFromRequest();
        $errors = User::validateEditData($user_data);
        if ($errors) {
            self::$results['errors'] = $errors;
            return;
        }

        self::$results['redirect_url'] = User::saveUpdatedUserInfo($user_data);
    }

    private static function saveProfile() {
        self::$results['errors'] = false;

        $user_data = User::getProfileInfoFromRequest();
        $errors = User::validateProfileData($user_data);
        if ($errors) {
            self::$results['errors'] = $errors;
            return;
        }

        self::$results['redirect_url'] = User::saveUpdatedUserProfile($user_data);
    }

    private static function savePubPostSettings() {
        $settings = array();
        $settings['default_tag_chk'] = Utils::reqParam('default_tag_chk') ? 1 : 0;
        $settings['autopost_tag_id'] = Utils::reqParam('autopost_tag_id');
        $settings['f_autopost'] = Utils::reqParam('f_autopost') ? 1 : 0;
        $settings['rss'] = Utils::reqParam('rss');

        $error = User::savePubPostSettings($settings);
        if ($error!==true) {
            self::processError($error);
            return;
        }
    }

    private static function changePassword() {
        self::$results['errors'] = false;

        $password_data = User::getPasswordInfoFromRequest();
        $errors = User::validatePasswordData($password_data);
        if ($errors) {
            self::$results['errors'] = $errors;
            return;
        }

        User::saveUpdatedPassword($password_data);
    }

    private static function saveVendorProfile() {
        self::$results['errors'] = false;

        $data = Vendor::getProfileInfoFromRequest();
        $errors = Vendor::validateProfileData($data);
        if ($errors) {
            self::$results['errors'] = $errors;
            return;
        }

        self::$results['redirect_url'] = Vendor::saveVendorProfile($data);
    }

    private static function joinInvites() {
        require_once('class.InviteCustom.php');

        self::$results['errors'] = false;
        $emails = Utils::reqParam('contact_emails');

        if (!$emails) {
            self::processError("Please enter at least one email");
            return;
        }

        $errors = array();
        $invites = array();

        foreach ($emails as $key=>$email) {
            if (!validate_email($email)) {
                $errors[$key] = array('name'=>$key, 'msg'=>"You must enter a valid email address.");
                continue;
            }

            $invites[$key] = $email;
        }

        if ($errors) {
            self::$results['errors'] = $errors;
            return;
        }

        if (!$invites) {
            self::processError("Please enter at least one email");
            return;
        }

        InviteCustom::processJoinInvites($invites);
        self::$results['redirect_url'] = Utils::getPageToReturnAfterLogIn();
    }

    private static function setFollow() {
        $follow_data = get_input('follow_data');
        if (!in_array($follow_data['whom_type'], array('user', 'vendor', 'tag'))) {
            self::processError("Bad following type.");
            return false;
        }
        if (!$follow_data['whom_id']) {
            self::processError("Error - no follower or following id.");
            return false;
        }

        if (empty($follow_data['f_batch'])) {
            $whom_id_arr = array($follow_data['whom_id']);
        } else {
            $whom_id_arr = $follow_data['whom_id'];
        }

        foreach ($whom_id_arr as $whom_id) {
            $follow_data['whom_id'] = $whom_id;
            if ($follow_data['followed']) {
                Link::deleteFollowLink($follow_data);
            } else {
                Link::createFollowLink($follow_data);
            }
        }

        $user = new User(get_user_id());
        $user->recache();
        user_login($user);
    }

    private static function getFollowBlock() {
        $entity_type = get_input('entity_type');
        $entity_id = get_input('entity_id');

        if (!in_array($entity_type, array('user', 'vendor', 'tag'))) {
            self::processError("Bad following type.");
            return false;
        }
        if (!$entity_id) {
            self::processError("Error - no id.");
            return false;
        }

        $temp_vendor = array('entity_type'=>$entity_type, 'entity_id'=>$entity_id);

        $follow = Utils::prepareFollow($temp_vendor);
        $follow['follow_type'] = 'following';

        Utils::$smarty->assign('follow', $follow);
        $follow_block_html = Utils::$smarty->fetch('components/front/front_follows.tpl');

        self::$results['follow_block_html'] = $follow_block_html;
    }

    private static function vote() {
        $vote_data = get_input('vote');
        $use_contest_vote = Utils::reqParam('use_contest_vote');

        if (!in_array($vote_data['type'], array('question', 'comment', 'posted_link'))) {
            self::processError("Bad vote type.");
            return false;
        }

        $vote_value = intval($vote_data['vote_value']);
        // if ($vote_value > 1) {
        //     $vote_value = 1;
        // } elseif($vote_value < 1) {
        //     $vote_value = -1;
        // }

        $vote = new Vote($vote_data['entity_id'], $vote_data['type'], self::$user_id);
        $result = $vote->process_vote($vote_value);

        if (!is_array($result)) {
            self::processError($result);
            return false;
        }

        self::$results['vote_data'] = $result;

        if ($use_contest_vote) {
            self::$results['votes_left'] = VoteContest::getVotesLeft();
        }

        return true;
    }

    private static function submitContestVotes() {
        $user_data = Vote::getVotesSubmitDataFromRequest();
        $errors = Utils::validateCommonProfileData($user_data);
        if ($errors) {
            self::$results['errors'] = $errors;
            return;
        }

        self::$results['email_exists'] = 0;
        $user = new User();
        $user->load_email(strtolower($user_data['email']['value']));
        if ($user->is_loaded() && !$user->get_data('f_contest_voter')) {
            self::$results['errors']['email'] = array('name' => 'email', 'msg'  => "This email already exists");
            self::$results['email_exists'] = 1;
            return;
        }

        require_once('class.VoteContest.php');
        $votes = VoteContest::getUnregisteredUserVotes();

        if (!Vote::sendConfirmVotesEmail($votes, $user_data, $user)) {
            self::processError();
            return;
        }

        return true;
    }


    private static function suspendUser() {
        $user_id = Utils::reqParam('user_id');
        $is_suspended = Utils::reqParam('is_suspended');

        $suspend = $is_suspended ? 0 : 1;

        if (!$user_id) {
            self::processError('Empty user id');
        }

        $user = new User($user_id);
        $user->suspend($suspend);

        return true;
    }

    private static function disableUser() {
        $user_id = intval(Utils::reqParam('user_id'));

        if (!$user_id) {
            self::processError('Empty user id');
            return false;
        }

        $user = new User($user_id);

        if ($user->get_data('status')=='admin') {
            self::processError("You can't disable admin.");
            return false;
        }

        $user->disable();

        return true;
    }

    private static function subscribe() {
        if (is_logged_in() || is_contest_voter()) {
            self::processError("Only unregistered users can subscribe");
            return false;
        }

        $email = Utils::reqParam('email');
        $tag_id = intval(Utils::reqParam('tag_id'));

        if (!validate_email($email)) {
            self::processError("You must specify a valid e-mail address.");
            return false;
        }

        $user = new User();
        $user->load_email($email);

        if ($user->is_loaded() && !$user->get_data('f_contest_voter')) {
            self::processError("You already have an account. Please sign in and configure your feed to get your feed emails.");
            return false;
        }

        require_once('class.Subscription.php');
        if (!in_array($tag_id, Subscription::$SUBSCRIPTIONS_BLOCS)) {
            self::processError("Invalid bloc.");
            return false;
        }

        $result = Subscription::processSubscription($email, $tag_id);
        if ($result!==true) {
            self::$results['popup_message'] = $result;
        }

        return true;
    }

    private static function getPostStats() {
        $post_id = Utils::reqParam('post_id');
        $post_type = Utils::reqParam('post_type');

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

        $results = $temp_entity->getPostStats();
        if (!is_array($results)) {
            self::processError($results);
            return false;
        }

        self::$results['data'] = $results;

        return true;
    }

    private static function repost() {
        $post_id = Utils::reqParam('post_id');
        $post_type = Utils::reqParam('post_type');

        $temp_entity = null;

        switch ($post_type) {
            case 'posted_link':
                $temp_entity = new PostedLink($post_id);
                break;
            case 'question':
                $temp_entity = new Question($post_id);
                break;
            default:
                self::processError();
        }

        if (!$temp_entity->is_loaded()) {
            Log::$logger->error("Can't load post when commenting, type=$post_type, id=$post_id");
            return true;
        }

        $temp_entity->repost();

        self::$results['result_url'] = $temp_entity->get_data('my_url');
    }

    private static function processEvent() {
        require_once('class.Event.php');

        $result = Event::processPostedEvent();

        if ($result !== true) {
            self::processError($result);
            return false;
        }

        return true;
    }

    private static function getEventData() {
        require_once('class.Event.php');

        $event_id = Utils::reqParam('event_id');
        if (!$event_id) {
            self::processError("Can't load Event data - event_id missed.");
            return false;
        }

        $event = Event::getEventById($event_id);
        if (!$event) {
            self::processError("Can't load Event data");
            return false;
        }

        self::$results['event'] = $event;
    }

    private static function delEvent() {
        require_once('class.Event.php');

        $event_id = Utils::reqParam('event_id');
        if (!$event_id) {
            self::processError("Can't delete Event - event_id missed.");
            return false;
        }

        Event::deleteEvent($event_id);

    }

    private static function updateUserFSponsor() {
        require_once('class.User.php');

        $f_get_sponsor_email = Utils::reqParam('f_get_sponsor_email');
        if (!($f_get_sponsor_email == 1)) {
            $f_get_sponsor_email = 0;
        }
        if (User::saveUpdatedFGetSponsorEmail($f_get_sponsor_email)) {
            self::$results['need_refresh'] = true;
        }
    }
}