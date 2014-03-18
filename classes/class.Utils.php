<?php

Class Utils {
    static $smarty = null;

    static $bots_user_agents_signs = array('bot', 'spider', 'crawler', 'curl');

    static $ajax_scripts = array('/cmd.php', "/display_image.php", "/display_screenshot.php",
                                    "/autocomplete. php", "/track.php", "/uploadify/uploadify.php", "/autocomplete.php");

    static $ignored_last_pages = array('/signin.php', '/join.php', '/logout.php', '/new_password.php',
                                        '/ext_auth.php', '/join_invites.php', '/join_follow.php');

    const CONTEST_TOP_CONTENT_MARKETING_ID=1;
    const CONTEST_MARKETO_ID=2;

    const SALES_MARKETING_TAG_ID = 1;

    static $contest_urls = array(self::CONTEST_TOP_CONTENT_MARKETING_ID => 'top_content_marketing_posts_of_2013',
                                    self::CONTEST_MARKETO_ID  => 'content_marketing_nation');


                                    static $tags_parents_vendor = array();
    static $tags_list_vendor = array();
    static $tags_list_sizes = array();
    static $industry_tags = array();
    static $permissions = array();
    static $current_page = '';
    static $purifier = null;

    const INDEX_PAGE = '/';
    const SUMMARY_FOLLOWERS_COUNT = 14;
    const FOLLOWS_ON_PAGE = 20;
    const MAX_ABOUT_LENGTH = 140;
    const UNREGISTERED_VOTES_COUNT = 3;

    static function initSmarty() {
        self::$smarty = new Smarty();
        self::$smarty->setTemplateDir(DOCUMENT_ROOT . '/templates/');
        self::$smarty->setCompileDir(DOCUMENT_ROOT . '/includes/libs/templates_c/');
        self::$smarty->setConfigDir(DOCUMENT_ROOT . '/includes/libs/configs/');
        self::$smarty->setCacheDir(DOCUMENT_ROOT . '/includes/libs/cache/');

        if (is_logged_in()) {
            if (!Utils::userData('first_name') || !Utils::userData('last_name')) {
                Log::$logger->warn("Empty user_info error\nscript: " . $_SERVER['SCRIPT_NAME'].
                    "\nurl: " . $_SERVER['REQUEST_URI'] .
                    "\nuser data from session: " . print_r($_SESSION['user_info'], true));
            }
        }

        // in auth .php, should be refactored too
        $smarty_params = array(
            'show_beta_border'=>Settings::SHOW_BETA_BORDER,
            'logged_in'=>is_logged_in(),
            'contest_voter'=>is_contest_voter(),
            'is_admin'=>is_admin(),
            'user_info'=>isset($_SESSION['user_info']) ? $_SESSION['user_info'] : array(),
            'show_new_vs_invite'=> true,
            'session_id'=>session_id(),
            'dev_mode'=>Settings::DEV_MODE,
            'shouldUseCssRefresh'=>self::shouldUseCssRefresh(),
            'debug_panel'=>self::shouldUseDebugPanel(),
            'permissions'=>self::$permissions,
            'login_redir_path'=>self::getLoginUrl(),
            'join_redir_path'=>self::getJoinUrl(),
            'current_page_path'=>self::$current_page,
            'header_notifications'=>  Notification::getNotificationsForHeader(),
            'script_times'=>Log::$script_times,
            'vendor_category_parents'=>self::$tags_parents_vendor,
            'vendor_category_list'=>self::$tags_list_vendor,
            'tags_list_sizes'=>self::$tags_list_sizes,
            'industry_tags'=>self::$industry_tags,
            'base_url'=>self::getBaseUrl(),
            'index_page'=>self::INDEX_PAGE,
            'active_submenu' => '',
            'init_image_upload' => false,
            'init_clipboard_copy' => false,
            'body_font' => self::sVar('body_font'),
            'alert_message' => self::sVar('alert_message', ''),
            'max_about_length' => self::MAX_ABOUT_LENGTH,
            'follows_on_page' => self::FOLLOWS_ON_PAGE,
            'use_contest_vote' => 0,
            'contest_votes_count' => self::userData('votes_count', self::UNREGISTERED_VOTES_COUNT),
            'show_join_widget' => 0,
        );

        self::unsetSVar('alert_message');

        self::$smarty->assign($smarty_params);
        return self::$smarty;
    }

    static function isBot($agent) {
        if (!$agent) {
            return false;
        }

        foreach (self::$bots_user_agents_signs as $sign) {
            if (stristr($agent, $sign) !== false) {
                return 1;
            }
        }
        return 0;
    }

    static function isAjaxRequest() {
        return (in_array($_SERVER['SCRIPT_NAME'], self::$ajax_scripts));
    }

    static function getLoginUrl() {
        return "/signin";
    }

    static function getJoinUrl() {
        return "/join";
    }

    // todo direct HTTP_HOST using should be replaced with this
    static function getBaseUrl() {
        if (php_sapi_name() != 'cli') {
            return "http://" . $_SERVER['HTTP_HOST'];
        }

        if (Settings::SHOW_BETA_BORDER) {
            return "http://beta.sharebloc.com";
        }

        return "http://www.sharebloc.com";
    }

    static function getLogosDir() {
        return DOCUMENT_ROOT . "/html/logos";
    }

    static function getScreenshotsDir() {
        return DOCUMENT_ROOT . "/html/screenshots";
    }

    static function initTags() {
        $vendor_cats  = new SiteCategory('vendor');
        self::$tags_parents_vendor = $vendor_cats->get_category_parents();
        self::$tags_list_vendor = $vendor_cats->get_category_list();

        $industries  = new SiteCategory('industry');
        self::$industry_tags = $industries->get_category_list();

        $vendor_obj  = new Vendor();
        $vendor_fields = $vendor_obj->get_fields();
        self::$tags_list_sizes = $vendor_fields['company_size']['options'];
    }

    static function shouldUseCssRefresh() {
        //return false;
        if (!Settings::DEV_MODE || HELPER_SCRIPT) {
            return false;
        }

        if (isset($_REQUEST['nocss'])) {
            return false;
        }
        $allowed_ips = array("192.168.56.101",
                            "192.168.73.253");
        if (in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
            return true;
        }
        return false;
    }

    static function shouldUseDebugPanel() {
        return false;
        if (!Settings::DEV_MODE) {
            return false;
        }
        $ALLOWED_IP = "192.168.73.253";
        if ($_SERVER['REMOTE_ADDR'] == $ALLOWED_IP) {
            return true;
        }
        return false;
    }

    static function check_if_unsupported_ie() {
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'compatible; MSIE ') !== false && isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'sorrynoie') === false) {
            // will detect IE version (we support >=8)
            $matches = array();
            preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $matches);
            if (count($matches) > 1 && $matches[1] < 8) {
                header('location: /sorrynoie.php');
                exit;
            } else {
                if (!isset($_SESSION['modal_popups']['ie_pop'])) {
                    $_SESSION['modal_popups']['ie_pop'] = 1;
                }
            }
        }
    }

    // todo don't clean the whole session
    static function logout(){
        if (is_logged_in() && !HELPER_SCRIPT) {
            $user = new User(get_user_id());
            $user->clearRememberMeCookie();
        }

        $_SESSION = array();
        redirect(Utils::getDefaultPage());
    }

    static function getDefaultPermissions(){
        $permissions = array(
            'edit_vendor'      => false,
            'edit_company'     => false,
            'edit_user'        => false,
            'add_screenshot'   => false,
            'add_any_use_this' => false
        );
        return $permissions;
    }

    static function getAdminPermissions(){
        $permissions = array(
            'edit_vendor'      => true,
            'edit_company'     => true,
            'edit_user'        => true,
            'add_screenshot'   => true,
            'add_any_use_this' => true
        );
        return $permissions;
    }

    static function setPermissions(){
        if (is_admin()) {
            self::$permissions = self::getAdminPermissions();
        } else {
            self::$permissions = self::getDefaultPermissions();
        }
    }

    static function storeParametersToUseAfterLogin(){
        // if not logged in user tries to store some data but is redirected to signin, data will be stored here and used after

        $contest_open_nomin_popup = get_input('contest_open_nomin_popup');
        if ($contest_open_nomin_popup) {
            $_SESSION['contest_open_nomin_popup'] = true;
            redirect(Utils::getLoginUrl());
        }
    }

    static function processActionsWithRedirects(){
        if (isset($_SESSION['invite_front_data'])) {
            require_once('class.InviteCustom.php');
            InviteCustom::processInviteKey($_SESSION["invite_front_data"]['confirm_key']);
            self::setPageToReturnAfterLogIn(Utils::INDEX_PAGE);
        } elseif (isset($_SESSION['posted_comment'])) {

            require_once('class.Comment.php');

            $posted_comment = new Comment();
            $posted_comment->processPostedComment($_SESSION['posted_comment']);
            unset($_SESSION['posted_comment']);
            self::setPageToReturnAfterLogIn($posted_comment->get_data('my_url'));
        } elseif (isset($_SESSION['posted_link_data'])) {

            require_once('class.PostedLink.php');

            $data = Utils::unsetSVar('posted_link_data');
            $posted_link = new PostedLink(null, null, $data['f_contest']);
            $posted_link->processPostedLink($data);
            self::setPageToReturnAfterLogIn($posted_link->get_data('my_url'));

        } elseif (isset($_SESSION['posted_question_data'])) {

            require_once('class.Question.php');

            $data = Utils::unsetSVar('posted_question_data');
            $posted_question = new Question();
            $posted_question->processPostedQuestion($data);
            self::setPageToReturnAfterLogIn($posted_question->get_data('my_url'));

        }
    }

    // todo - should be refactored, is used as a simple transition from old style to new
    static function processSuccessfullLogin(){
        Utils::processActionsWithRedirects();
    }

    static function validateSignUpParams($user_data){
        $errors = array();
        if (empty($user_data['terms'])) {
            $errors['terms'] = "You must agree to the terms.";
        }

        if (!validate_email($user_data['email'])) {
            $errors['email'] = "You must enter a valid email address.";
        }

        if (!$errors) {
            $user = new User();
            $user->load_email(trim(strtolower($user_data['email'])));
            if ($user->is_loaded()) {
                $errors['email'] = "This email already exists.";
            }
        }
        return $errors;
    }

    static function sendWelcomeEmail($user, $generated_password, $use_email_confirmation = true){
        require_once('class.Mailer.php');
        $email_data                       = array();
        $email_data['user_obj']           = $user;
        $email_data['generated_password'] = $generated_password;
        $email_data['use_email_confirmation'] = $use_email_confirmation;
        $mailer                           = new Mailer('welcome');
        $mailer->sendWelcomeEmail($email_data);
    }


    static function deleteJoinRequestByEmail($email){
        $sql = sprintf("DELETE FROM join_requests
                WHERE email='%s'",
                Database::escapeString($email));
        Database::exec($sql);
    }

    // todo should be simplified
    static function signUpUser($user_data, $remember_me = false){
        $use_email_confirmation = true;

        // we have to check if this is a contest voter who wants to became a real user
        $f_contest_voter = false;
        $user = new User();
        $user->load_email(strtolower($user_data['email']));
        if ($user->is_loaded()) {
            if (!$user->get_data('f_contest_voter')) {
                // normally this should not happen, just to be sure.
                $errors['email'] = array('name' => 'email', 'msg'  => "This email already exists. <a href='" . Utils::getLoginUrl() . "'>Sign in now!</a>");
                return $errors;
            }
            $f_contest_voter = true;
            $user_data['f_contest_voter'] = 0;
            Log::$logger->warn("Contest voter has been signed up, email = " . $user_data['email']);
            $use_email_confirmation = false;
        }

        $oauth_data = Utils::sVar('oauth_data');

        if (!empty($_SESSION['invite_front_data']['invited_by'])) {
            $user_data['invited_by']  = $_SESSION['invite_front_data']['invited_by'];
        }

        if ($oauth_data) {
            $user_data['description'] = $oauth_data['about'];
        }

        $generated_password = '';
        if (empty($user_data['password'])) {
            if (!$oauth_data) {
                Log::$logger->warn("User has no both password and oauth data when signing up");
            }
            // this user signs up with oauth, so we will generate a password for him
            $generated_password    = User::generatePassword(8, true);
            $user_data['password'] = User::getPasswordHash($generated_password);
        } else {
            $user_data['password'] = User::getPasswordHash(trim($user_data['password']));
        }

        $ref_params = Utils::unsetSVar('last_referrer_params');
        if ($ref_params) {
            if ($ref_params['type']=='bloc_feed') {
                $user_data['join_source'] = 'subscription';
                Log::$logger->info("User is joined with join_source=subscription, details: " . $ref_params['details']);
            }
        }

        if (!$f_contest_voter) {
            $user = new User();
        }

        if (!$user->set($user_data)) {
            return $user->get_errors();
        }

        if (isset($user_data['company']) && strlen($user_data['company'])) {
            $company = new Vendor();

            $company->load_by_name($user_data['company']);

            if ($company->is_loaded()) {
                $user_data['company_id'] = $company->get_data('vendor_id');
                $user->set($user_data);
            } else {
                $company->set(array('vendor_name' => $user_data['company']));
                // todo fix later as industry tags are broken
                /*
                if (isset($user_data['company_industry'])) {
                    $tag      = new Tag();
                    $tag->load_name_type($user_data['company_industry'], 'company');
                    $tag_list = array($tag->get_data('tag_id'));
                    $company->set(array('tag_list' => $tag_list));
                }
                 *
                 */
                $company->save();
                $user_data['company_id'] = $company->get_data('vendor_id');
                $user->set($user_data);
            }
        }
        $user->save();

        if (!$user->is_loaded()) {
            // todo bear this is not shown to user currently
            Log::$logger->fatal("User is not loaded after save on signing up. Data: " . print_r($user_data, true));
            return array();
        }

        // is user has a logo url provided by oAuth provider, it will be used inside addOAuthRegistration()
        // here we attach logo uploaded manually
        if (Utils::sVar('not_finished_upload_logo')) {
            $user->attachUploadedLogoToNewEntity();
        }

        if ($remember_me) {
            $user->setRememberMeCookie();
        }

        user_login($user);
        self::setPermissions();

        if ($oauth_data) {
            User::addOAuthRegistration($oauth_data);
            unset($_SESSION['oauth_data']);
        }

        $user->setAutoFollowing();

        self::deleteJoinRequestByEmail($user->get_data('email'));
        require_once('class.Subscription.php');
        Subscription::deleteBecauseOfJoin($user->get_data('email'), $user->get_data('user_id'));

        self::sendWelcomeEmail($user, $generated_password, $use_email_confirmation);
        $_SESSION['show_join_welcome_popup'] = 1;

        return true;
    }

    /*
     * Default pages are:
     * for not logged in - "/"
     * for logged in who allowed to see new pages - Utils::INDEX_PAGE
     * then will be used $additional_page which is set for login (user's page) and join
     * then will be used "/" too
     */
    static function getDefaultPage(){
        if (!is_logged_in()) {
            return "/";
        }

        return Utils::INDEX_PAGE;
    }

    // we redirect to:
    // 1) action specific pages which is in $_SESSION['return_after_login_path'] var
    // 2) last visited page which is in self::getLastPage()
    // 3) default page which is different for different users, is in self::getDefaultPage()
    static function getPageToReturnAfterLogIn(){
        if (!empty($_SESSION['return_after_login_path'])) {
            return $_SESSION['return_after_login_path'];
        }
        if (self::getLastPage()) {
            return self::getLastPage(true);
        }
        return self::getDefaultPage();
    }

    static function setPageToReturnAfterLogIn($path){
        $_SESSION['return_after_login_path'] = $path;
    }

    static function setCurrentPage(){
        if (self::isAjaxRequest() || in_array($_SERVER['SCRIPT_NAME'], self::$ignored_last_pages)) {
            return;
        }

        if (!is_logged_in() && $_SERVER['REQUEST_URI']=="/") {
            // https://vendorstack.atlassian.net/browse/VEN-294
            // todo should be removed when we will be not in beta
            // when user not logged in '/' shows launch page
            return;
        }

        self::$current_page = $_SERVER['REQUEST_URI'];
        $_SESSION['last_page_path'] = self::$current_page;
    }

    static function getLastPage($clear = false){
        if (!empty($_SESSION['last_page_path'])) {
            $page = $_SESSION['last_page_path'];
            if ($clear) {
                unset($_SESSION['last_page_path']);
            }
            return $page;
        }
        return '';
    }

    static function userData($field_name, $default='') {
        if (empty($_SESSION['user_info'][$field_name])) {
            return $default;
        }
        return $_SESSION['user_info'][$field_name];
    }

    static function getCategoriesInCustomOrder() {
        $sorted_categories = array();

        $main_tags_custom_order = array(1, 5, 6, 7, 23, 2, 101, 3, 4, 13);

        $categories = self::$tags_parents_vendor;

        foreach ($main_tags_custom_order as $tag_id) {
            if (!empty($categories[$tag_id])) {
                $sorted_categories[$tag_id] = $categories[$tag_id];
                unset($categories[$tag_id]);
            }
        }
        foreach ($categories as $key=>$data) {
            $sorted_categories[$key] = $data;
        }
        return $sorted_categories;
    }

    static function getContestCategories($with_all = false) {
        $contest_cats  = new SiteCategory('contest');
        $categories = $contest_cats->get_category_list();
        foreach ($categories as $key=>$category) {
            if (!$category['parent_tag_id']) {
                unset($categories[$key]);
            }
        }

        if ($with_all) {
            $temp_category = array();
            $temp_category['tag_id'] = '0';
            $temp_category['name'] = 'All';
            $temp_category['code_name'] = 'all';
            $temp_category['my_url'] = '/'.Utils::$contest_urls[Utils::CONTEST_TOP_CONTENT_MARKETING_ID].'/all';
            $temp_array = array(0=>$temp_category);
            // to preserve numeric keys
            $categories = $temp_array + $categories;
        }

        return $categories;
    }

    static function saveImplicitTagsForPost($post_id, $post_type, $vendors, $post_explicit_tags=array()) {
        if (!$vendors) {
            return false;
        }

        $implicit_tags = array();

        foreach ($vendors as $vendor) {
            $tags_detail = $vendor->get_data('tag_list_details');
            foreach ($tags_detail as $details) {
                array_push($implicit_tags, $details['tag_id']);
                if ($details['parent_tag_id']) {
                    array_push($implicit_tags, $details['parent_tag_id']);
                }
            }
        }

        $implicit_tags = array_unique($implicit_tags);

        foreach ($implicit_tags as $key => $tag) {
            if (in_array($tag, $post_explicit_tags)) {
                unset($implicit_tags[$key]);
            }
        }

        $implicit_tag_list = new TagList('vendor');
        $implicit_tag_list->set_selection_criteria('tag_selection',
                                                    array('entity_id'=>$post_id, 'entity_type'=>$post_type,
                                                        'tag_type'=>'vendor', 'f_explicit'=>'0'),
                                                    'tag_id');
        $implicit_tag_list->set_selections($implicit_tags);
        $implicit_tag_list->save_selections();
    }

    static function getWordsFromMentionText ($text) {
        if (!trim($text)) {
            return array();
        }

        $text = preg_replace('/\s[\s]+/', ' ', trim(urldecode($text)));

        $max_processed_str_length = 50;
        $begin_index = 0;
        if (strlen($text) > $max_processed_str_length) {
            $begin_index = strrpos($text, " ", -$max_processed_str_length);
        }

        $text = trim(substr($text, $begin_index));
        $words = explode(' ', $text);

        if (!$words) {
            return array();
        }

        $clean_words = array();
        foreach ($words as $word) {
            $from = array('/^[^A-Za-z0-9]+/', '/[^A-Za-z0-9]+$/');
            $to = array('', '');
            $word = preg_replace($from, $to, $word);
            $clean_words[] = $word;
         }

         return $clean_words;
    }

    static function getVendorMentioned($question_text) {

        global $db;

        $words = Utils::getWordsFromMentionText($question_text);
        if (!$words) {
            return false;
        }

        $search_terms = array();

        foreach ($words AS $i => $word) {
            if (strlen($words[$i]) > 2) {
                $search_terms[1][] = $words[$i];
            } else {
                continue;
            }

            if (isset($words[$i + 1])) {
                $search_terms[2][] = $words[$i] . " " . $words[$i + 1];
            } else {
                continue;
            }

            if (isset($words[$i + 2])) {
                $search_terms[3][] = $words[$i] . " " . $words[$i + 1] . " " . $words[$i + 2];
            }
        }

        if (isset($search_terms[1]) && is_array($search_terms[1]))
            krsort($search_terms[1]);
        if (isset($search_terms[2]) && is_array($search_terms[2]))
            krsort($search_terms[2]);
        if (isset($search_terms[3]) && is_array($search_terms[3]))
            krsort($search_terms[3]);

        $search_ordered = array();

        if (isset($search_terms[3]) && is_array($search_terms[3]) && count($search_terms[3]) > 0)
            $search_ordered = array_merge($search_ordered, $search_terms[3]);

        if (isset($search_terms[2]) && is_array($search_terms[2]) && count($search_terms[2]) > 0)
            $search_ordered = array_merge($search_ordered, $search_terms[2]);

        if (isset($search_terms[1]) && is_array($search_terms[1]) && count($search_terms[1]) > 0)
            $search_ordered = array_merge($search_ordered, $search_terms[1]);

        foreach ($search_ordered AS $search_term) {
            //$query = "SELECT v.vendor_id, v.vendor_name FROM vendor v WHERE levenshtein('".$db->escape_text( $search_term )."', v.vendor_name) BETWEEN 0 AND 1";
            $query = "SELECT v.vendor_id, v.vendor_name FROM vendor v WHERE v.vendor_name LIKE '" . $db->escape_text($search_term) . "'";

            $result = $db->query($query);

            if (is_array($result) && count($result) > 0) {
                return $result[0];
            }
        }
        return false;
    }

	// todo maybe we should replace old get_input by this function
    public static function reqParam($name, $default=null) {
        $return = $default;
        if (isset($_POST[$name])) {
            $return = $_POST[$name];
        } elseif (isset($_GET[$name])) {
            $return = $_GET[$name];
        }

        if (is_string($return)) {
            $return = trim($return);
        }

        return $return;
    }

    public static function sVar($name, $default=null) {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return $default;
    }

    public static function unsetSVar($name) {
        $value = null;
        if (isset($_SESSION[$name])) {
            $value = $_SESSION[$name];
            unset($_SESSION[$name]);
        }
        return $value;
    }

    static function showMaintenance() {
        if (!defined("Settings::MAINTENANCE_MODE") || !Settings::MAINTENANCE_MODE || Settings::DEV_MODE) {
           return;
        }

        if (Utils::reqParam('admin')) {
            $_SESSION['ignore_maintenance'] = true;
        }

        if (self::sVar('ignore_maintenance')) {
            return;
        }

        self::$smarty->assign('message', "ShareBloc is down for maintenance. We'll be back up shortly.<br>Thanks for your patience.");
        self::$smarty->display('pages/message.tpl');
        exit();
    }

    static function getFullNameByFNameLName($fname, $lname) {
        $full_name = $fname;
        if (strlen($lname)) {
            $full_name .= " " . $lname;
        }
        return $full_name;
    }

    // todo should be removed after activity handling refactoring
    static function getSelfOr($table_alias) {
        if (is_logged_in()) {
            return sprintf("OR %s.user_id = %d",
                            $table_alias,
                            get_user_id());
        }
        return '';
    }

    static function getCategoriesStructure() {
        $categories = array();

        $ordered_categories = self::getCategoriesInCustomOrder();

        foreach ($ordered_categories as $id=>$tag_ids) {
            $category = array();
            $category['id'] = $id;
            $category['name'] = self::$tags_list_vendor[$id]['name'];
            $category['tags'] = array();
            foreach ($tag_ids as $tag_id) {
                $category['tags'][$tag_id] = array();
                $category['tags'][$tag_id]['id'] = $tag_id;
                $category['tags'][$tag_id]['name'] = self::$tags_list_vendor[$tag_id]['name'];
            }

            $categories[$id] = $category;
        }

        // e($result);
        return $categories;
    }

    static function populateFullCompanyAndUsersList() {
        $full_company_list = Database::execArray("SELECT vendor_id, vendor_name FROM vendor ORDER BY vendor_name ASC");
        $full_user_list    = Database::execArray("SELECT user_id, first_name, last_name FROM user ORDER BY first_name ASC");
        $categories = self::getCategoriesStructure();

        self::$smarty->assign('full_company_list', $full_company_list);
        self::$smarty->assign('full_user_list', $full_user_list);
        self::$smarty->assign('categories_structure', $categories);
    }

    static function getContestExperts() {
        $experts = array();
        $experts_ids = array('alen_mayer','ardath_albee','craig_rosenberg','dave_brock','douglas_karr','justin_gray',
                            'lori_richardson','matt_heinz','tibor_shanto','trish_bertuzzi', 2295, 2255);

        foreach($experts_ids as $experts_id) {
            if (is_integer($experts_id)) {
                $user = new User($experts_id);
            } else {
                $user = new User(null, $experts_id);
            }

            if (!$user->is_loaded()) {
                if (!Settings::DEV_MODE && !Settings::SHOW_BETA_BORDER) {
                    Log::$logger->error("Can't load expert $experts_id");
                }
                continue;
            }

            $experts[] = $user->get();
        }
        return $experts;
    }

    static function prepareFollow($data) {
        $data['related'] = array();
        switch ($data['entity_type']) {
            case 'user':
                $entity_obj = new User($data['entity_id']);
                if ($entity_obj->get_data('company_id')) {
                    $company = new Vendor($entity_obj->get_data('company_id'));
                    $data['related']['name'] = $company->getName();
                    $data['related']['my_url'] = $company->get_data('my_url');
                }
                break;
            case 'vendor':
                $entity_obj = new Vendor($data['entity_id']);
                $main_tag = Vendor::getMainTag($data['entity_id']);
                if ($main_tag) {
                    $data['related']['name'] = $main_tag['tag_name'];
                    $data['related']['my_url'] = $main_tag['my_url'];
                }
                break;
            case 'tag':
                $entity_obj = new Tag($data['entity_id']);
                break;
            default :
                Log::$logger->error("Unknown entity type, data: " . print_r($data, true));
        }

        if (!$entity_obj->is_loaded()) {
            // todo now broken follower link will be shown once.
            // While bitton will be shown and can be pressed, it's pressing will not cause any errors.
            Log::$logger->error("Entity is not loaded when loading follows, and follow link will be deleted, data: " . print_r($data, true));
            Link::deleteBrokenFollowLinks($data);
        }

        $entity_data = $entity_obj->get();

        $data['logo']['my_url'] = $entity_data['logo']['my_url'];
        $data['my_url'] = $entity_data['my_url'];
        $data['name'] = $entity_obj->getName();
        $data['entity_uid'] = $entity_data['uid'];
        $data['followed_by_curr_user'] = $entity_data['followed_by_curr_user'];

        return $data;
    }

    static function getFollowQuery($get_followers = false, $count = false, $load_fakes = false) {
        $order_str = '';
        $join_str = '';
        $join_arr = array();

        $select_numb = 2;
        $where_numb = 1;
        if ($get_followers) {
            $select_numb = 1;
            $where_numb = 2;
        }

        $join_arr[] = sprintf("LEFT JOIN user ON user.user_id=link.entity%d_id AND link.entity%d_type='user'",
                               $select_numb,
                               $select_numb);

        if ($count) {
            $select_part = "COUNT(1) as follow_count";
        } else {
            $select_part = sprintf("entity%d_type AS entity_type, entity%d_id AS entity_id,
                                    COALESCE(posted_links_count, 0) AS posts_count, (COALESCE(logo_id, 0)!=0) AS has_logo",
                                    $select_numb,
                                    $select_numb);

            $join_arr[] = sprintf("LEFT JOIN
                                      (SELECT user_id, count(1) AS posted_links_count
                                       FROM posted_link
                                       GROUP BY user_id) AS posted ON posted.user_id = user.user_id");
            $order_str = "ORDER BY has_logo desc, posts_count DESC, user.user_id";
        }

        $where_str = sprintf("WHERE entity%d_type='%%s' AND entity%d_id=%%d",
                             $where_numb,
                             $where_numb);
        if (!$load_fakes) {
            $where_str .= " AND (user.user_id IS NULL OR user.f_test=0)";
        }

        if ($join_arr) {
            $join_str = implode("\n", $join_arr);
        }

        $sql = sprintf("SELECT %s
                        FROM link
                        %s
                        %s
                        %s",
                        $select_part,
                        $join_str,
                        $where_str,
                        $order_str);
        return $sql;
    }

    static function getFollowCount($entity_id, $type, $get_followers = false) {
        $count = null;

        $sql = self::getFollowQuery($get_followers, true);
        $sql = sprintf($sql, $type, $entity_id);
        $result = Database::execArray($sql, true);
        if ($result) {
            $count = $result["follow_count"];
        }

        return $count;
    }

    // $get_followers = true to get followers
    //                = false to get following
    static function getFollow($entity_id, $type, $get_followers = false, $limit = false, $offset=0, $load_fakes=false) {
        $follows = array();
        if (!in_array($type, array('user', 'vendor', 'tag'))) {
            return $follows;
        }

        $follow_type = 'following';

        if ($get_followers) {
            $follow_type = 'follower';
        }

        $sql = self::getFollowQuery($get_followers, false, $load_fakes);
        $sql = sprintf($sql, $type, $entity_id);

        if ($limit) {
            $sql .= sprintf("\n LIMIT %d,%d;", $offset, self::FOLLOWS_ON_PAGE);
        }

        $results = Database::execArray($sql);
        foreach ($results as $result) {
            $follow = self::prepareFollow($result);
            $follow['follow_type'] = $follow_type;
            $follows[$follow['entity_uid']] = $follow;
        }

        return $follows;
    }

    static function prepareFollowDataForUsers($users = array()) {
        foreach($users as $key => $user) {
            $users[$key] = self::prepareFollow(array('entity_id' => $user['user_id'], 'entity_type' => 'user'));
            $users[$key]['follow_type'] = 'recent_connection';
        }

        return $users;
    }

    static function validate_url($address) {
        $address = strtolower(trim($address));
        if (filter_var($address, FILTER_VALIDATE_URL)) {
            return true;
        } else {
            return false;
        }
    }

    static function getCompaniesByTags($tags, $limit = false) {
        if (!$tags) {
            return array();
        }
        $limit_str = '';
        if ($limit) {
            $limit_str = sprintf("LIMIT %d", $limit);
        }

        $sql = sprintf("SELECT distinct (vendor_id), f.followers_count
                        FROM vendor v
                        JOIN tag_selection ts ON entity_type='vendor' AND entity_id=v.vendor_id
                        JOIN tag t ON t.tag_id = ts.tag_id
                        JOIN (SELECT entity2_id, COUNT(1) AS followers_count FROM link
                                WHERE entity2_type='vendor'
                                AND entity1_type='user'
                                GROUP BY entity2_id
                              ) AS f ON f.entity2_id = v.vendor_id
                        WHERE f_explicit=1 AND
                            ( t.`tag_id` IN (%s) OR t.parent_tag_id IN (%s) )
                        ORDER BY followers_count DESC
                        %s",
                        implode(', ', $tags),
                        implode(', ', $tags),
                        $limit_str);

        $results = Database::execArray($sql);
        return $results;
    }

    static function getBlocsToFollow() {
        $follows = array();
        $categories = self::getCategoriesInCustomOrder();
        $temp_tags = array();
        foreach ($categories as $key=>$tag) {
            $temp_tags[] = array('entity_type'=>'tag', 'entity_id'=>$key);
        }
        foreach ($temp_tags as $entity) {
            $follow = self::prepareFollow($entity);
            $follow['follow_type'] = 'following';
            $follows[$follow['entity_uid']] = $follow;
        }

        return $follows;
    }

    static function isConsoleCall() {
        return (php_sapi_name() === 'cli');
    }

    static function validateCommonProfileData($data) {
        $errors = array();

        foreach ($data as $name => $field) {
            if ($field['f_needed'] && !$field['value']) {
                $errors[$name] = array('name' => $name, 'msg'  => "You must enter a value.");
                continue;
            }

            if ($field['value'] && in_array($name, array('website', 'linkedin', 'facebook', 'twitter', 'google_plus'))) {
                if (!Utils::validate_url($field['value'])) {
                    $errors[$name] = array('name'=>$name, 'msg'=>"You must enter a valid URL or leave this empty.");
                }
            }

            if ($name == 'email') {
                if (!validate_email($field['value'])) {
                    $errors[$name] = array('name'=>$name, 'msg'=>"You must enter a valid email address.");
                }
            }

            if (isset($field['max_length']) && strlen($field['value']) > $field['max_length']) {
                $errors[$name] = array('name'=>$name, 'msg'=>"Maximum " . $field['max_length'] . " characters allowed.");
            }
        }

        return $errors;
    }

    public static function sanitizeHTML($dirty) {
        if (!$dirty) {
            return '';
        }

        if (!self::$purifier) {
            require_once("../includes/htmlpurifier/HTMLPurifier.standalone.php");

            $clean = "";
            $config = HTMLPurifier_Config::createDefault();
            $config->set('Cache.DefinitionImpl', null);

            $config->set("HTML.AllowedElements",array("a"));
            $config->set("HTML.AllowedAttributes",array("a.href"));
            $config->set('CSS.AllowedProperties', array());
            self::$purifier = new HTMLPurifier($config);
        }

        $clean = self::$purifier->purify($dirty);

        return $clean;
    }

    public static function countTwitterSymbolsLeft($mandatory_text='') {
        $twitter_symbols = 140;
        $twitter_link_length = 22;

        if (!$mandatory_text) {
            $mandatory_text = "Congratulations to  for being a #2013Top50ContentMarketing winner via @ShareBloc ";
        }

        $twitter_symbols_left = $twitter_symbols - strlen($mandatory_text) - $twitter_link_length;

        return $twitter_symbols_left;
    }

    public static function showMessagePageAndExit($message) {
        self::$smarty->assign('message', $message);
        self::$smarty->display('pages/message.tpl');
        exit();
    }

    public static function processRefIfNeeded() {
        $allowed_refs = array("bloc_feed");
        $ref = self::reqParam('ref');
        if (!$ref) {
            return;
        }

        $ref_params = explode('-', $ref);
        if (count($ref_params)!=2 || !in_array($ref_params[0], $allowed_refs)) {
            Log::$logger->error("Strange ref parameter, ref = " . $ref);
            return;
        }

        $_SESSION['last_referrer_params'] = array();
        $_SESSION['last_referrer_params']['type'] = $ref_params[0];
        $_SESSION['last_referrer_params']['details'] = $ref_params[1];
    }

    public static function getHostByUrl($url) {
        $url_parts = parse_url($url);
        if ($url_parts===false || empty($url_parts['host']) || empty($url_parts['scheme'])) {
            return '';
        }

        return $url_parts['scheme'] . '://' . $url_parts['host'];
    }

    public static function isLive() {
        return !Settings::DEV_MODE && !Settings::SHOW_BETA_BORDER;
    }

    public static function getCategoryIdByCodeName($code_name) {
        $query = sprintf("SELECT tag_id FROM tag WHERE code_name='%s';",
                            Database::escapeString($code_name));
        $result = Database::execArray($query, true);
        if (!$result) {
            return false;
        }
        return $result['tag_id'];
    }

    public static function isMysqlFormattedDate($str_date) {
        $mysql_formatted = false;
        if ($str_date == date('Y-m-d H:i:s', strtotime($str_date))) {
            $mysql_formatted = true;
        }
        return $mysql_formatted;
    }

    public static function usersArrayUnique($users) {
        $unique_users = array();
        $user_ids = array();

        foreach ($users as $user) {
            if ($user['code_name'] == 'anonymous') {
                $unique_users[] = $user;
            } else if (isset($user['user_id']) && !in_array($user['user_id'], $user_ids)) {
                $unique_users[] = $user;
                $user_ids[] = $user['user_id'];
            }
        }

        return $unique_users;
    }

}

/* ---------------------- END OF UTILS CLASS ----------------------- */

// todo bear move all this functions to Utils methods
function parse_text_between($text, $starting_text, $ending_text, $offset = 0) {
    $no_starting_pos = false;
    $no_ending_pos   = false;

    //Find the starting position in the text.
    if (!$starting_text) {
        $starting_pos = 0;
    } else {
        if (strpos($text, $starting_text, $offset) === false)
            $no_starting_pos = true;
        else
            $starting_pos    = strpos($text, $starting_text, $offset) + strlen($starting_text);
    }

    // If it could not fund the starting text, return false;
    if ($no_starting_pos == true) {
        //echo "Could not find starting position.\n";
        return 0;
    } else {
        //echo "Starting position: $starting_pos\n";
        //It found the starting text, now find where it ends.
        if (!$ending_text) {
            $ending_pos = strlen($text);
        } else {
            if (strpos($text, $ending_text, $starting_pos) === false)
                $no_ending_pos = true;
            else
                $ending_pos    = strpos($text, $ending_text, $starting_pos);
        }

        if ($no_ending_pos == true) {
            //echo "Could not find ending position.\n";
            return 0;
        } else {
            //echo "Ending position: $ending_pos\n";

            $between_text = substr($text, $starting_pos, ($ending_pos - $starting_pos));
            return $between_text;
        }
    }
}

function validate_email($address) {
    $address = strtolower(trim($address));
    if (filter_var($address, FILTER_VALIDATE_EMAIL) && strpos($address, '.') !== false) {
        return true;
    } else {
        return false;
    }
}

//todo bear this is some magic
function sort_array_by_array($array, $fields) {
    $sorted = array_merge(array_flip($fields), $array);
    return $sorted;
}

function is_valid_filename($text) {
    if (preg_match('/^[a-zA-Z0-9_\-\.]+$/', $text)) {
        return true;
    } else {
        return false;
    }
}

function handle_csv_characters(&$text) {
    if (strpos($text, ',') !== false) {
        $text = "\"" . addslashes($text) . "\"";
    }
}

function redirect($url) {
    session_write_close();
    header("location: $url");
    exit();
}

function display_404() {
    echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n";
    echo "<html><head>\n";
    echo "<title>404 Not Found</title>\n";
    echo "</head><body>\n";
    echo "<h1>Not Found</h1>\n";
    echo "<p>The requested URL was not found on this server.</p>\n";
    echo "</body></html>\n";
    exit();
}

function getEmailDomainFromUrl($url) {
    if (!$url) {
        return '';
    }
    return strtolower(str_replace('www.', '', parse_url($url, PHP_URL_HOST)));
}

function shutdown_procedure() {
    Log::tick("done", true);
    // todo bear logVisit() call should be moved here possibly to track request time

    if (!empty($_SESSION['status_message']))
        $_SESSION['status_message'] = "";

    if (isset($_SESSION['modal_popups']['ie_pop']) && $_SESSION['modal_popups']['ie_pop'] == 1) {
        $_SESSION['modal_popups']['ie_pop'] = -1;
    }
}

function get_input($key, $ignored = 'ignored', $force_type = '', $allowed_values = '') {
    $result = '';

    if (isset($_POST[$key])) {
        $result = $_POST[$key];
    } elseif (isset($_GET[$key])) {
        $result = $_GET[$key];
    }

    if ($force_type) {
        if ($force_type == "array" && !is_array($result)) {
            $result = '';
        } elseif ($force_type == 'numeric' && !is_numeric($result)) {
            $result = '';
        }
    }

    if (is_array($allowed_values) && !in_array($result, $allowed_values)) {
        $result = '';
    }

    return $result;
}

/* Functtions moved from authentication.php */

function user_login($user) {
    // todo should move to User::load. Now is here to not load oauth regs for other users objects, who are not logged in
    $user->loadOAuthRegistrations();
    $user->load_following(false, 0, true);

    $_SESSION['user_info'] = $user->get();

    if (is_contest_voter()) {
        $_SESSION['contest_voter'] = true;
    } else {
        $_SESSION['logged_in'] = true;
    }
}

function is_logged_in() {
    return !empty($_SESSION['logged_in']);
}

function is_contest_voter() {
    return !empty($_SESSION['contest_voter']);
}

function is_admin() {
    if (is_logged_in() && $_SESSION['user_info']['status'] == "admin") {
        return true;
    }

    return false;
}

function get_user_id() {
    if (!is_logged_in() && !is_contest_voter()) {
        return null;
    }

    if (empty($_SESSION['user_info']['user_id'])) {
        Log::$logger->error("Empty user ID while logged_in value is true");
        return null;
    }

    return intval($_SESSION['user_info']['user_id']);
}

function get_user_code_name() {
    if (!is_logged_in()) {
        return null;
    }

    if (empty($_SESSION['user_info']['code_name'])) {
        Log::$logger->error("Empty user codename while logged_in value is true");
        return null;
    }

    return $_SESSION['user_info']['code_name'];
}

function get_user_full_name() {
    if (!is_logged_in()) {
        return '';
    }

    if (empty($_SESSION['user_info']['full_name'])) {
        Log::$logger->error("Empty full_name while logged_in value is true");
        return null;
    }

    return $_SESSION['user_info']['full_name'];
}

/* Developers functions */
function e($arr) {
    if (!Settings::DEV_MODE && !HELPER_SCRIPT) {
        return;
    }

    if (is_array($arr)) {
        $t = print_r($arr, true);
    } else {
        $t = $arr;
    }

    $t .= "\n\n";

    if (Utils::isConsoleCall()) {
        echo $t;
        return;
    }

    $t = str_replace(" ", "&nbsp;", $t);
    $t = str_replace("\n", "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $t);
    $t = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $t;
    echo($t);
}

function r($arr) {
    if (!Settings::DEV_MODE) {
        return;
    }

    if (is_array($arr)) {
        $t = print_r($arr, true);
    } else {
        $t = $arr;
    }
    error_log($t);
}