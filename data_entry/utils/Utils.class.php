<?php

/**
 * Utility functions container
 */
class Utils {

    public static $networks = array(
        0 => array('id'=>'crawled_google_url', 'display_name'=>'URL',"site"=>''),
        1 => array('id'=>'linkedin', 'display_name'=>'LinkedIn',"site"=>'linkedin.com'),
        2 => array('id'=>'facebook', 'display_name'=>'Facebook',"site"=>'facebook.com'),
        3 => array('id'=>'twitter', 'display_name'=>'Twitter',"site"=>'twitter.com'),
        4 => array('id'=>'crunchbase', 'display_name'=>'CrunchBase',"site"=>'crunchbase.com'),
        5 => array('id'=>'angel', 'display_name'=>'AngelList',"site"=>'angel.co'),
        6 => array('id'=>'all_done', 'display_name'=>'all_done',"site"=>''),
    );

    public static $NETWORK_IDS = array(1,2,3,4,5);

    public static $save_data_steps = array(
        0 => array('id' => 'city', 'display_name'=>'City'),
        1 => array('id' => 'country', 'display_name'=>'Country'),
        2 => array('id' => 'logo', 'display_name'=>'Logo'),
        3 => array('id' => 'description', 'display_name'=>'Description'),
        4 => array('id' => 'size', 'display_name'=>'Size'),
        5 => array('id' => 'industry', 'display_name'=>'Industry'),
        6 => array('id' => 'all_done', 'display_name'=>'all_done')
    );

    public static $VENDOR_STEP_IDS = array(0,1,2,3,6);
    public static $COMPANY_STEP_IDS = array(0,1,2,3,4,5,6);

    private static $session_var = 'data_entry';

    public static function init() {
        global $script_times;
        session_start();
        $script_times['session start'] = microtime(true);

        $script_times['logger init'] = microtime(true);
        DBUtils::initAdoConn();
        $script_times['ado conn init'] = microtime(true);
    }

    public static function sVar($name, $value = null) {
        if (is_null($value)) {
            if (is_array($name)) {
                if (isset($_SESSION[self::$session_var][$name[0]][$name[1]])) {
                    $value = $_SESSION[self::$session_var][$name[0]][$name[1]];
                }
            } else {
                if (isset($_SESSION[self::$session_var][$name])) {
                    $value = $_SESSION[self::$session_var][$name];
                }
            }
            return $value;
        }

        if (is_array($name)) {
            $_SESSION[self::$session_var][$name[0]][$name[1]] = $value;
        } else {
            $_SESSION[self::$session_var][$name] = $value;
        }
        return;
    }

    public static function sGlobalVar($name, $value = null) {
        if ($value !== null) {
            $_SESSION[$name] = $value;
            return;
        }
        if (isset($_SESSION[$name])) {
            $value = $_SESSION[$name];
        }
        return $value;
    }

    public static function unsetGlobalVar($name) {
        $value = null;
        if (isset($_SESSION[$name])) {
            $value = $_SESSION[$name];
        }
        unset($_SESSION[$name]);
        return $value;
    }

    public static function unsetSVar($name) {
        $value = null;
        if (isset($_SESSION[self::$session_var][$name])) {
            $value = $_SESSION[self::$session_var][$name];
            unset($_SESSION[self::$session_var][$name]);
        }
        return $value;
    }

    public static function reqParam($name, $default = null) {
        $return = $default;
        if (isset($_POST[$name])) {
            $return = $_POST[$name];
        } elseif (isset($_GET[$name])) {
            $return = $_GET[$name];
        }
        return $return;
    }

    // for IIS - as there REQUEST_URI can be not set
    // http://ua2.php.net/manual/en/reserved.variables.server.php#108186
    public static function getRequestUri() {
        if (!isset($_SERVER['REQUEST_URI'])) {
            // added ltrim for IIS - for some reason it won't go to path starting with /.
            $_SERVER['REQUEST_URI'] = ltrim($_SERVER['SCRIPT_NAME'], "/");
            if (!empty($_SERVER['QUERY_STRING'])) {
                $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
            }
        }
        return $_SERVER['REQUEST_URI'];
    }

    public static function initSmarty() {
        $smarty = new Smarty();

        // this is path to directories with writing allowed
        $path_prefix = DATA_ENTRY_ROOT_PATH . "/tpl";

        $smarty->setTemplateDir($path_prefix);
        $smarty->setCacheDir($path_prefix . '/cache');
        $smarty->setCompileDir($path_prefix . '/templates_c');
        $smarty->setConfigDir($path_prefix . '/configs');

        return $smarty;
    }

    public static function escapeHTML($string) {
        $string = htmlspecialchars($string);
        // used to allow options formatting
        $string = str_replace("  ", "&nbsp;&nbsp;", $string);
        $string = nl2br($string);
        return $string;
    }

    public static function cache($name, $value = null, $ttl = 60) {
        if (!Settings::USE_SESSION_CACHE) {
            return null;
        }
        $session_name = array('cache', $name);
        if ($value !== null) {
            $session_value = array('val' => $value, 'exp' => time() + $ttl);
            self::sVar($session_name, $session_value);
            return;
        }

        $session_value = self::sVar($session_name);
        if (!$session_value || $session_value['exp'] < time()) {
            return null;
        }
        return $session_value['val'];
    }

    // warn works only with scripts located in root dir.
    // Should be rewrited if scripts in subdirs will be accessed strictly by urls
    public static function getBaseUrl($is_console = false) {
        if (defined("Settings::BASE_URL") && trim(Settings::BASE_URL)) {
            return Settings::BASE_URL;
        }

        $subdir = str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

        $base_url = 'http://' . $_SERVER['HTTP_HOST'] . $subdir;
        return $base_url;
    }

    public static function getIndexUrl() {
        $base_url = self::getBaseUrl();
        return $base_url . "index.php";
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

    public static function generatePassword($length = 8) {
        $password = '';
        $pool = range('!', '~');
        $high = count($pool) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $pool[rand(0, $high)];
        }
        return $password;
    }

    public static function generateRandomKey() {
        return md5(self::generatePassword(60));
    }

    public static function getHashForPassword($pass) {
        $hash = crypt($pass, self::salt());
        return $hash;
    }

    public static function shouldUseCssRefresh() {
        if (self::reqParam('nocss')) {
            return false;
        }

        if ($_SERVER['REMOTE_ADDR'] == Settings::CSS_REFRESH_IP) {
            return true;
        }
        return false;
    }

    public static function logIn() {
        if (!self::reqParam('email') || !self::reqParam('password')) {
            return 'You have to enter e-mail and password to log in.';
        }

        $user = DBUtils::getUserInfoByEmail(trim(self::reqParam('email')));
        if (!$user) {
            return 'The e-mail you entered was incorrect.';
        }
        $pass_hash = $user['password'];
        $post_pass = self::reqParam('password');
        $post_hash = crypt($post_pass, $pass_hash);

        if ($pass_hash !== $post_hash) {
            return 'The password you entered was incorrect.';
        }

        self::sVar('logged_in', true);
        self::sVar('user', $user);
        //DBUtils::setUserActivity($user['id'], Settings::USER_IS_ACTIVE);

        return '';
    }

    public static function logOut() {

        $user = self::sVar('user');
        //DBUtils::setUserActivity($user['id'], Settings::USER_IS_INACTIVE);

        if (self::sVar('work_vendor')) {
            self::unsetSVar('work_vendor');
        }

        self::unsetSVar('logged_in');
        self::unsetSVar('user');
        $redirect_header = "Location: /index.php";
        header($redirect_header);
        exit();
    }

    public static function isLoggedIn() {
        if (self::sVar('logged_in')) {
            return true;
        }
        return false;
    }

    public static function isAdmin() {
        if ($user = self::sVar('user')) {
            if ($user['is_admin'] == 1) {
                return true;
            }
        }
        return false;
    }

    public static function redirectIfNotLoggedInAsAdmin() {
        if (self::isLoggedIn() && self::isAdmin()) {
            if (self::reqParam('logout')) {
                self::logOut();
            }
        } else {
            $redirect_header = "Location: /index.php";
            header($redirect_header);
            exit();
        }
    }

    public static function redirectIfNotLoggedIn() {
        if (self::isLoggedIn()) {
            if (self::reqParam('logout')) {
                self::logOut();
            }
        } else {
            $redirect_header = "Location: /index.php";
            header($redirect_header);
            exit();
        }
    }

    private static function parseLine($line) {
        Log::$logger->trace("Line for parcing: " . $line);
        $last_delimeter_pos = strrpos($line, Settings::$DELIMITER_CSV);
        if ($last_delimeter_pos === false) {
            Log::$logger->warn("File line has no delimeter. Line: ".$line);
            return false;
        }
        $vendor = array();
        $vendor_name = substr($line, 0, $last_delimeter_pos);
        $vendor['vendor'] = trim(str_replace(array("'", '"'), "", $vendor_name));
        $vendor['source'] = trim(substr($line, $last_delimeter_pos+1));
        Log::$logger->trace("Vendor - ".$vendor['vendor']."\nSource - ".$vendor['source']);
        if (!$vendor['vendor'] || !$vendor['source']) {
            Log::$logger->warn("Incorrect vendor name or source name. Line: ".$line);
            return false;
        }
        return $vendor;
    }

    public static function parseCSVData($content) {
        Log::$logger->debug('Start parsing CSV data');
        $vendors = array();
        $err_msg = array();
        foreach ($content as $line) {
            if (!trim($line)) {
                continue;
            }
            $vendor = self::parseLine($line);
            if (!$vendor) {
                $err_msg[] = $line;
                continue;
            }
            $vendors[$vendor['vendor']] = $vendor;
        }
        Log::$logger->debug('End of parsing CSV data');

        $parsed_data = array();
        $parsed_data['vendors'] = $vendors;
        $parsed_data['err_msg'] = $err_msg;

        return $parsed_data;
    }

    public static function cleanVendorsNames($vendors_data) {
        $vendors_data['cleaned_count'] = 0;
        Log::$logger->trace("Start cleanVendorsNames");
        foreach ($vendors_data['vendors'] as &$vendor) {
            $vendor['name_cleaned'] = false;
            $vendor['vendor'] = self::cleanName($vendor['vendor_raw']);
            if ($vendor['vendor'] !== $vendor['vendor_raw']) {
                $vendor['name_cleaned'] = true;
                $vendors_data['cleaned_count']++;
            }
            DBUtils::saveCleanedVendorName($vendor['id'], $vendor['vendor']);
        }
        Log::$logger->trace("End cleanVendorsNames");
        return $vendors_data;
    }

    private static function cleanName($name) {
        $clean_name = "";
        $clean_name = str_replace(Settings::$ERASE_STRINGS, "", $name);
        $clean_name = trim($clean_name);
        return $clean_name;
    }

    public static function updateVendorNames() {
        Log::$logger->trace("Searching in POST data for db vendor names updating");
        $vendors_cleaned = self::reqParam('vendor');
        Log::$logger->trace($vendors_cleaned);
        if (!$vendors_cleaned) {
            return;
        }
        foreach ($vendors_cleaned as $key => $value) {
            $vend_id_int = intval($key);
            if ($vend_id_int) {
                DBUtils::saveCleanedVendorName($vend_id_int, $value);
            } else {
                Log::$logger->error("Error saving new name. Data [" . $key . "]=>[" . $value . "]");
            }
        }
    }

    public static function prepareSearchQuery($method, $vendor_name) {
        $encoded_vendor = urlencode($vendor_name);

        if ($method == 'ajax') {
            $search_query = sprintf(Settings::GOOGLE_AJAX_SEARCH_URL, $encoded_vendor);
        } else {
            $search_query = sprintf(Settings::GOOGLE_SEARCH_URL_SIMPLE, $encoded_vendor);
        }
        Log::$logger->trace("Query prepared: ".$search_query);
        return $search_query;
    }

    public static function crawlVendors($timeout = null) {
        if (!$timeout) {
            $timeout = Settings::SLEEP_TIMEOUT;
        }
        Log::$logger->trace("Crawling vendor names in Google started...");
        $db_vendors = DBUtils::getVendorsByStep('cleaned');

        Log::$logger->info("Will process ". count($db_vendors)." vendors.");

        $execution_time = 3 * $timeout * count($db_vendors) + 30;
        set_time_limit($execution_time);

        Log::$logger->info("Time limit set to $execution_time");

        $first = true;

        foreach ($db_vendors as $vendor) {
            if ($vendor['crawled_google_url']) {
                Log::$logger->info("Skipping vendor ". $vendor['vendor'] . ", it has url already.");
                continue;
            }
            if ($first) {
                $first = false;
            } else {
                Log::$logger->debug("Sleeping between search queries");
                self::sleep($timeout + rand(1, 3));
            }

            Log::$logger->info("Will search site url for vendor ". $vendor['vendor']);
            $crawled_url = self::getUrlForVendorFromGoogle($vendor['vendor'], $timeout);
            DBUtils::saveCrawledUrl($vendor['id'], $crawled_url);
        }
    }

    private static function parseUrl($method, $body) {

        require_once ("../utils/Parser.class.php");

        if (!$body) {
            return false;
        }
        if ($method=='ajax') {
            return Parser::parseUrlFromJson($body);
        } else {
            return Parser::parseUrlFromHtml($body);
        }
    }

    private static function getUrlForVendorFromGoogle($query, $timeout=null) {
        if (!$timeout) {
            $timeout = Settings::SLEEP_TIMEOUT;
        }
        Log::$logger->debug("Will search result for query " . $query);

        $crawled_url = null;
        $attempts = 0;

        while (!$crawled_url && $attempts < Settings::MAX_ATTEMPTS_COUNT) {
            if ($attempts) {
                $sleep_time = $timeout + $attempts * 2;
                Log::$logger->warn("Will sleep for $sleep_time seconds for attempt $attempts.");
                self::sleep($sleep_time);
            }
            $attempts++;

            $search_url = self::prepareSearchQuery(Settings::FIRST_SEARCH_METHOD, $query);
            $body = file_get_contents($search_url);
            $crawled_url = self::parseUrl(Settings::FIRST_SEARCH_METHOD, $body);
            if (!$crawled_url) {
                Log::$logger->warn("No data received by method ".Settings::FIRST_SEARCH_METHOD." on query $query. Will try second method.");
                $search_url = self::prepareSearchQuery(Settings::SECOND_SEARCH_METHOD, $query);
                $body = file_get_contents($search_url);
                $crawled_url = self::parseUrl(Settings::SECOND_SEARCH_METHOD, $body);
            }
        }
        if ($crawled_url) {
            $crawled_url = rtrim($crawled_url, '/');
        } else {
            Log::$logger->error("No data received by method ".Settings::FIRST_SEARCH_METHOD." on query $query");
        }
        return $crawled_url;
    }

    public static function sleep($seconds) {
        Log::$logger->debug("Will sleep for $seconds seconds");
        sleep($seconds);
    }

    public static function searchDuplicatesByUrl($vendors_data) {
        Log::$logger->trace("Function searchDuplicatesByUrl");
        $vendors_data['duplicates_count'] = 0;
        $vendors_data['google_banned'] = 0;

        foreach ($vendors_data['vendors'] as &$vendor) {
            if (!trim($vendor['crawled_google_url'])) {
                continue;
            }
            $vendor['duplicates']['vs'] = DBUtils::searchDuplicatesByUrl($vendor['type'], $vendor['crawled_google_url'], 0);
            $vendor['duplicates']['de'] = DBUtils::searchDuplicatesByUrl('data_vendors', $vendor['crawled_google_url'], 0, $vendor['id']);
            if ($vendor['duplicates']['de'] || $vendor['duplicates']['vs']) {
                $vendors_data['duplicates_count']++;
            }
            if (!$vendor['crawled_google_url']) {
                $vendors_data['google_banned'] = $vendor['vendor'];
            }
        }
        return $vendors_data;
    }

    public static function searchDuplicatesByName($vendors_data) {
        Log::$logger->trace("Function searchDuplicatesByName");
        $vendors_data['duplicates_count'] = 0;

        foreach ($vendors_data['vendors'] as &$vendor) {
            $vendor['duplicates']['vs'] = DBUtils::searchDuplicatesByName($vendor['type'], $vendor['vendor'], 0);
            $vendor['duplicates']['de'] = DBUtils::searchDuplicatesByName('data_vendors', $vendor['vendor'], 0, $vendor['id']);
            if ($vendor['duplicates']['de'] || $vendor['duplicates']['vs']) {
                $vendors_data['duplicates_count']++;
            }
        }
        return $vendors_data;
    }

    public static function trimUrlForSearchingDupl($url) {
        $host = null;
        if (!$url) {
            return $host;
        }
        $parsed = parse_url($url);
        if (!$parsed) {
            return $host;
        }
        if (empty($parsed['host'])) {
            return '';
        }
        $host = $parsed['host'];
        return $host;
    }

    public static function getFileContentAndParseIt($filename) {
        Log::$logger->info("Will import new file.");
        ini_set("auto_detect_line_endings", true);

        $file_errors = array();
        $empty_file_msg = '';
        $err_msg = array();

        $content = file($filename, FILE_IGNORE_NEW_LINES);
        if (!$content) {
            $empty_file_msg = "File uploading error - maybe empty file";
            Log::$logger->error($empty_file_msg);
        } else {
            $vend_list = self::parseCSVData($content);
            if ($vend_list['err_msg']) {
                $err_msg = $vend_list['err_msg'];
            }
            if ($vend_list['vendors']) {
                $batch_id = md5(uniqid());
                DBUtils::saveImportedVendors($vend_list['vendors'], $batch_id);
                Log::$logger->info("Done, batch id = $batch_id");
                self::sVar('file_import_err_msg', $err_msg);
                header("Location: /clean.php");
                exit;
            }
        }
        $file_errors['err_msg'] = $err_msg;
        $file_errors['empty_file_msg'] = $empty_file_msg;

        return $file_errors;
    }


    public static function getWorkVendor() {
        $vendor = array();
        if (self::sVar('work_vendor')) {
            $vendor = self::sVar('work_vendor');
        }
        return $vendor;
    }

    private static function getSiteSubquery($site) {
        if (!$site) {
            return '';
        }
        return sprintf(" site:%s", $site);
    }

    public static function crawlProfiles() {
        require_once '../utils/NetUtils.class.php';
        NetUtils::init();

        $work_vendor = self::sVar('work_vendor');
        $work_vendor['err_msg'] = array();

        Log::$logger->info("Will crawl profile pages for vendor ".$work_vendor['vendor']);

        foreach (self::$networks as $key=>$network) {
            if (!in_array($key, self::$NETWORK_IDS)) {
                continue;
            }
            $network_id = $network['id'];
            $network_url = $work_vendor[$network_id]['network_link'];

            if(!trim($network_url)) {
                $work_vendor[$network_id]['data'] = self::getEmptyData($work_vendor['type']);
                continue;
            }

            if ($network_id == 'facebook') {
                $body = NetUtils::goToPage($network_url);
            } else {
                Log::$logger->debug("(file_get_contents) Going to link $network_url");
                $body = file_get_contents($network_url);
            }

            if (!$body) {
                Log::$logger->error("Getting html data error from $network_id. Link - $network_url");
                $work_vendor['err_msg'][] = "Getting page data error. Link - $network_url".
                                                ". Data from this page is not parsed. Maybe incorrect link";
                $work_vendor[$network_id]['data'] = self::getEmptyData($work_vendor['type']);
                continue;
            }
            Log::$logger->trace("Body is present");
            $work_vendor[$network_id]['data'] = Parser::parseProfileData($network_id, $body);
            if ($network_id == 'facebook') {
                NetUtils::curlSetUserAgent('mozilla');
                $img_page_body = NetUtils::goToPage($network_url);
                if ($img_page_body) {
                    $work_vendor[$network_id]['data']['logo'] = Parser::parseImgSourceFromHtml($img_page_body);
                }
            }
        }
        self::sVar('work_vendor', $work_vendor);
    }

     public static function getNewVendor() {
        $vendor = self::sVar('work_vendor');
        if ($vendor) {
            Log::$logger->error("will use previous vendor though setVendorUnderWork called");
            return $vendor;
        }

        $vendor = DBUtils::getVendorForWork();
        return $vendor;
    }

    public static function prepareNewVendorStructure($vendor) {
        $first = true;

        if (isset($vendor['links_parsed'])) {
            Log::$logger->debug("Don't need search for all networks links");
            return $vendor;
        }

        Log::$logger->info("Will crawl profiles urls for vendor " . $vendor['vendor']);

        for ($i=0; $i<(count(self::$networks)-1); $i++) {
            if ($first) {
                $first = false;
            } else {
                self::sleep(Settings::SLEEP_TIMEOUT);
            }

            $network = self::$networks[$i];
            $network_id = $network['id'];

            $vendor[$network_id] = array();
            $search_string = $vendor['vendor'] . self::getSiteSubquery($network['site']);
            $vendor[$network_id]['google_search_url'] = self::prepareSearchQuery(null, $search_string);

            $network_link = self::getUrlForVendorFromGoogle($search_string);
            $vendor[$network_id]['network_link_source'] = $network_link;
            $vendor[$network_id]['network_link'] = $network_link;

        }

        $vendor['links_parsed'] = true;

        return $vendor;
        }

    public static function storeLinkIfNeeded($vendor, $current_step) {
        $url_to_store = trim(self::reqParam('new_url'));
        if ($url_to_store) {
            $url_to_store = rtrim($url_to_store, '/');
        }
        $should_save = self::reqParam('should_save');
        if (!($current_step >= 0)) {
            return $vendor;
        }
        $network_id = self::$networks[$current_step]['id'];
        if ($should_save) {
            $vendor[$network_id]['network_link'] = $url_to_store;
        }
        return $vendor;
    }

    public static function saveDataSource($vendor, $current_step) {
        $save_data_steps = Utils::getSaveStepsForEntityByType($vendor['type']);
        $last_step = count($save_data_steps)-1;
        if ($current_step>=$last_step || $current_step == -1) {
            return $vendor;
        }

        $source_id = self::reqParam('source_id');
        if (self::reqParam('should_save') && $source_id) {
            $step_id = $save_data_steps[$current_step]['id'];
            Log::$logger->debug("Saving $step_id for ".$vendor['vendor']);
            $save_field_source = $step_id . '_source';
            $vendor[$save_field_source] = $source_id;
        }

        return $vendor;
    }

    public static function getEmptyData($type = 'company') {
        $empty_data = array();
        $req_steps = self::getSaveStepsForEntityByType($type);
        foreach ($req_steps as $data) {
            $empty_data[$data['id']] = '';
        }
        return $empty_data;
    }

    public static function checkUrlHeader($url, $step) {
        $status = '';
        Log::$logger->debug("Link to check header - $url");
        if (!parse_url($url)) {
            Log::$logger->debug("Parse_url error. Incorrect link");
            return false;
        }

        // to avoid warnings in the php_error.log if the website name is invalid
        $headers = @get_headers($url);
        if (!$headers) {
            Log::$logger->debug("Get_headers error. Incorrect link");
            return false;
        }
        $status = $headers[0];
        if (strpos($status, '200')===false && strpos($status, '302')===false && strpos($status, '301')===false) {
            Log::$logger->debug("Link Status - $status. Check entered link");
            return false;
        }
        Log::$logger->debug("Link is good. Status - $status");
        return true;

    }

    public static function getAdditionalWarns($url, $step) {
        $msg = '';
        $wrong_site_msg = self::isWrongSite($url, $step);
        if ($wrong_site_msg) {
            $msg .= $wrong_site_msg;
        }

        if (strpos($url, '?') !== false) {
            $msg .= sprintf("Entered link contains invalid character - '?'\n");// needs wording
        }

        if ($step == 2) {
            if (!self::checkFacebookUrl($url)) {
                $msg .= "This Facebook url should have numeric ID at the end\n"; // needs wording
            }
        }

        if (!$msg) {
            return false;
        }
        $msg = "This URL is probably wrong:\n" . $msg; // needs wording
        return $msg;
    }

    private static function isWrongSite($url, $step) {
        $site_to_check = self::$networks[$step]['site'];
        //Log::$logger->debug("Website to search in url - $site_to_check");
        if ($site_to_check) {
            if (!strstr($url, $site_to_check)) {
                return sprintf("Entered link doesn't refer to %s.\n", $site_to_check); // needs wording
            }
        }
        return false;
    }

    private static function checkFacebookUrl($url) {
        if (!strpos($url, '/pages/')) {
            return true;
        }
        $id = substr($url, strrpos($url, '/')+1);
        Log::$logger->debug("End of the url - " . $id);
        if (is_numeric($id)) {
            return true;
        }
        return false;
    }

    public static function saveVendorLogo($vendor) {
        Log::$logger->info("Saving of the logo for vendor id = ".$vendor['id']);

        if (empty($vendor['logo_source']) || $vendor['logo_source']=='none') {
            Log::$logger->debug("No logo to save");
            return;
        }
        $img = $vendor[$vendor['logo_source']]['data']['logo'];
        if ($img && parse_url($img)) {
            $data = file_get_contents($img);
            if (!$data) {
                Log::$logger->error("Image file reading error. Link ".$img);
                return;
            }
        } else {
            Log::$logger->error("Bad image link " . $img);
            return;
        }
        $img_url_path = parse_url($img, PHP_URL_PATH);
        if (!$img_url_path) {
            Log::$logger->error("Image url is not parsed well".$img);
            return;
        }
        $img_path_parts = pathinfo($img_url_path);
        if (!$img_path_parts) {
            Log::$logger->error("Getting path parts filename from url error");
            return;
        }
        if (isset($img_path_parts['extension'])){
            $ext = $img_path_parts['extension'];
        } else {
            Log::$logger->error("Image file has no extention");
            return;
        }
        $new_img_name = 'logo_'.$vendor['id'].'.'.$ext;
        Log::$logger->debug("New image name " . $new_img_name);
        $save_data = file_put_contents("logos/$new_img_name", $data);
        if($save_data) {
            Log::$logger->debug("Image saved");
            DBUtils::saveLogoFilename($vendor, $new_img_name);
        } else {
            Log::$logger->error("Image file saving error");
        }
    }

    public static function saveData($vendor) {
        DBUtils::saveWorkVendorData($vendor);
        self::saveVendorLogo($vendor);
        $vendor['status'] = 'ready';
        return $vendor;
    }

    public static function getPopupParams() {
        $params_str = sprintf("width=%d,height=%d,left=%d,top=%d",
                                Settings::POPUP_WIDTH,
                                Settings::POPUP_HEIGHT,
                                Settings::POPUP_LEFT,
                                Settings::POPUP_TOP);
        return $params_str;
    }

    public static function getPreparedNetworks() {
        $prepared_networks = array();
        foreach(self::$networks as $key=>$network) {
            if (!in_array($key, self::$NETWORK_IDS)) {
                continue;
            }
            $prepared_networks[$network['id']] = $network;
        }
        return $prepared_networks;
    }

    public static function getSaveStepsForEntityByType($entity_type) {
        $steps = array();
        $req_ids = $entity_type=='vendor' ? self::$VENDOR_STEP_IDS : self::$COMPANY_STEP_IDS;
        $i = 0;
        foreach ($req_ids as $id) {
            $steps[$i] = self::$save_data_steps[$id];
            $i++;
        }
        return $steps;
    }

    public static function getLinkErrorsForVendor (&$vendor) {
        foreach(self::$networks as $key=>$network) {
            $err = '';
            if (!in_array($key, self::$NETWORK_IDS)) {
                continue;
            }
            $url = $vendor[$network['id']];
            if (!$url) {
                $vendor['link_errors'][$network['id']] = '';

                continue;
            }
            $err = self::getAdditionalWarns($url, $key);
            $vendor['link_errors'][$network['id']] = $err;
        }
        return $vendor;
    }

    public static function getAllErrorMsgsForHint($vendors) {
        foreach ($vendors as &$vendor) {
            $vendor['worker'] = DBUtils::getUserEmailById($vendor['worker_id']);

            $all_errors = array();
            foreach(self::$networks as $key=>$network) {
                if (!in_array($key, self::$NETWORK_IDS)) {
                    continue;
                }
                $url = $vendor[$network['id']];
                if (!$url) {
                    continue;
                }

                if (self::getAdditionalWarns($url, $key)) {
                    $all_errors[] = "There can be some errors in the entered links. Check it please.";
                    break;
                }
            }

            $dupl_by_name = DBUtils::searchDuplicatesByName($vendor['type'], $vendor['vendor']);
            //r("URL - ".$vendor['crawled_google_url']);
            $dupl_by_url = array();
            if ($vendor['crawled_google_url']) {
                $dupl_by_url = DBUtils::searchDuplicatesByUrl($vendor['type'], $vendor['crawled_google_url']);
            }

            if ($dupl_by_name) {
                $all_errors[] = "There is the duplicate by name in VS DB.";
            }
            if ($dupl_by_url) {
                $all_errors[] = "There is the duplicate by url in VS DB.";
            }
            $vendor['all_errors'] = '';
            if ($all_errors) {
                $vendor['all_errors'] = implode("\n", $all_errors);
            }

        }
        return $vendors;
    }
    public static function addHttpPrefixIfNeeded($website) {
        if (strpos($website, '://') === false) {
            $crawled_url = "http://" . $website;
            Log::$logger->debug("http:// prefix was added: $website -> $crawled_url");
        } else {
            $crawled_url = $website;
        }
        return $crawled_url;
    }

}

function e($arr, $to_string = false) {
    if (is_array($arr)) {
        $t = print_r($arr, true);
    } else {
        $t = $arr;
    }
    $t = str_replace(" ", "&nbsp;", $t);
    $t = str_replace("\n", "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $t);
    $t .= "<br><br>";
    $t = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $t;
    if ($to_string) {
        return $t;
    } else {
        echo($t);
    }
}

function r($arr, $to_string = false) {
    if (is_array($arr)) {
        $t = print_r($arr, true);
    } else {
        $t = $arr;
    }
    if ($to_string) {
        return $t;
    } else {
        error_log($t);
    }
}

?>