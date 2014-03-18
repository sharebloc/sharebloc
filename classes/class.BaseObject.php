<?php

class BaseObject {

    protected $data;
    protected $fields;
    protected $required;
    protected $primary_key;
    protected $secondary_key;
    protected $entity_name;
    protected $table_name;
    protected $stats;
    private $errors;

    function BaseObject($primary_id = null, $secondary_id = null) {
        global $cache;

        if ($secondary_id) {
            $primary_id = $this->lookup_primary_id($secondary_id);
        }

        $data_result   = '';
        $fields_result = '';

        $data_key   = $primary_id;
        $fields_key = $primary_id . "_fields";

        $data_result   = $cache->get(get_class($this), $data_key);
        $fields_result = $cache->get(get_class($this), $fields_key);

        if ($data_result) {
            $this->data = $data_result;
        } else if ($primary_id) {
            $this->load($primary_id);
        }

        if ($fields_result) {
            $this->fields = $fields_result;
        } else {
            $this->fields = $this->populate_fields();
        }

        if (empty($data_result) || empty($fields_result)) {
            $cache->set(get_class($this), $data_key, $this->data);
            $cache->set(get_class($this), $fields_key, $this->fields);
        }
    }

    function load($primary_id = null) {
        global $db;

        $where = array();
        if (isset($primary_id)) {
            $where[$this->primary_key] = $primary_id;
        } else {
            // todo bear WARN if $primary_id is not set, we will load from DB first entity without any "where"
            error_log("Error - empty 'where' query used when loading entity.");
        }

        $result = $db->select($this->table_name, null, null, $where, null, null, null, null, 1);

        if (isset($result[0][$this->primary_key]) && $result[0][$this->primary_key] > 0) {
            $this->data = $result[0];

            $this->data['uid'] = sprintf("%s_%s", $this->getEntityType(), $primary_id);
            return true;
        } else {
            return false;
        }
    }

    function recache() {
        global $cache;

        if ($this->is_loaded()) {
            $data_key = $this->data[$this->primary_key];
            $cache->clear(get_class($this), $data_key);
            $this->load($this->data[$this->primary_key]);

            // just in memory of how it worked from the beginning to oct'2013
            // $this->load($this->primary_key);
        }
    }

    function lookup_primary_id($secondary_id) {
        global $db;

        $where                       = array();
        $where[$this->secondary_key] = $secondary_id;

        $result = $db->select($this->table_name, array($this->primary_key), null, $where, null, null, null, null, 1);

        if (isset($result[0][$this->primary_key]) && $result[0][$this->primary_key] > 0) {
            return $result[0][$this->primary_key];
        } else {
            return false;
        }
    }

    function get() {
        return $this->data;
    }

    function get_data($field_name) {
        if (isset($this->data[$field_name])) {
            return $this->data[$field_name];
        } else {
            return null;
        }
    }

    function get_fields() {
        return $this->fields;
    }

    function set($data) {
        if (isset($data[$this->primary_key]) && !is_numeric($data[$this->primary_key]))
            unset($data[$this->primary_key]);

        if (!isset($this->data)) {
            $new_data = $data;
        } else {
            $new_data = array_merge($this->data, $data);
        }

        $this->errors = $this->validate($new_data);

        if (count($this->errors) == 0) {
            $this->data = $new_data;
            return true;
        } else {
            return false;
        }
    }

    function set_data($key, $val) {
        $this->data[$key] = $val;
    }

    function save() {
        $new_id = $this->save_data();

        if ($new_id) {
            $this->load($new_id);
        }

        return $new_id;
    }

    function save_data() {
        global $db, $cache;

        $primary_id = null;

        $this->errors = $this->validate($this->data);

        if (count($this->errors) == 0) {
            $insert    = $this->data;
            $no_update = array($this->primary_key);

            foreach ($insert AS $key => $val) {
                if (!isset($this->fields[$key]['database']) || $this->fields[$key]['database'] == 0) {
                    unset($insert[$key]);
                }
            }

            if (isset($this->fields['date_added'])) {
                $insert['date_added'] = "now()";
                $no_update[] = 'date_added';
            }

            if (isset($this->fields['added_by']) && is_logged_in()) {
                $insert['added_by'] = $_SESSION['user_info']['user_id'];
                $no_update[] = 'added_by';
            }

            if (isset($this->fields['date_modified'])) {
                $insert['date_modified'] = "now()";
            }

            if (isset($this->fields['modified_by'])) {
                if (is_logged_in()) {
                    $insert['modified_by'] = $_SESSION['user_info']['user_id'];
                } else {
                    Log::$logger->info("Entity will be inserted or updated without logged in user. May be this is DE import. Data: " . print_r($insert, true));
                }
            }

            $cache->check_recache_keys($insert);

            $db->insert_on_duplicate_key_update($this->table_name, $insert, $no_update);

            if ($db->insert_id > 0)
                $primary_id = $db->insert_id;
            else
                $primary_id = $this->data[$this->primary_key];

            $cache->clear(get_class($this), $primary_id);
        }

        return $primary_id;
    }

    function delete() {
        global $db;

        if ($this->is_loaded()) {
            $where = array($this->primary_key => $this->data[$this->primary_key]);

            $db->delete($this->table_name, $where, array(), 1);

            $this->recache();
        }
    }

    function is_loaded() {
        if (isset($this->data[$this->primary_key]) && $this->data[$this->primary_key] > 0)
            return true;
        else
            return false;
    }

    function validate(&$data) {
        $errors = array();

        foreach ($data AS $key => $value) {
            if (in_array($key, array_keys($this->fields))) {
                if (isset($this->fields[$key]['null_allowed']) && $this->fields[$key]['null_allowed'] == true && is_null($value))
                    break;

                if (isset($this->fields[$key]['type']) && $this->fields[$key]['type'] == 'number' && !is_numeric($value)) {
                    $errors[$key] = $key . " value must be numeric.";
                    break;
                }

                if (isset($this->fields[$key]['range']) && $value < $this->fields[$key]['range']['min']) {
                    $errors[$key] = $key . " must be at least " . $this->fields[$key]['range']['min'] . ".";
                    break;
                }

                if (isset($this->fields[$key]['range']) && $value > $this->fields[$key]['range']['max']) {
                    $errors[$key] = $key . " must be no greater than " . $this->fields[$key]['range']['max'] . ".";
                    break;
                }

                if (isset($this->fields[$key]['length']) && strlen($value) < $this->fields[$key]['length']['min']) {
                    $errors[$key] = $key . " must at least " . $this->fields[$key]['range']['min'] . " characters in length.";
                    break;
                }

                if (isset($this->fields[$key]['length']) && strlen($value) > $this->fields[$key]['length']['max']) {
                    $errors[$key] = $key . " must be no greater than " . $this->fields[$key]['range']['max'] . " characters in length.";
                    break;
                }

                if (isset($this->fields[$key]['type']) && $this->fields[$key]['type'] == 'date' && $value != date('Y-m-d', strtotime($value))) {
                    $errors[$key] = $key . " must be a valid date.";
                    break;
                }

                if (isset($this->fields[$key]['type']) && $this->fields[$key]['type'] == 'datetime' && $value !== "0000-00-00 00:00:00" && $value != date('Y-m-d H:i:s', strtotime($value))) {
                    $errors[$key] = $key . " must be a valid date.";
                    break;
                }

                if (isset($this->fields[$key]['type']) && $this->fields[$key]['type'] == 'list') {
                    if (is_array($value)) {
                        foreach ($value AS $i => $k) {
                            if (!in_array($k, array_keys($this->fields[$key]['options']))) {
                                $errors[$key] = "Invalid option $k selected for " . $key . " got an invalid option selected.";
                                Log::$logger->info($errors[$key].", additional data: ". print_r($value, true));
                                break;
                            }
                        }
                    }
                }
            } else {
                unset($data[$key]);
            }
        }

        foreach ($this->fields AS $key => $more) {
            if (isset($more['required']) && $more['required'] == true && (!isset($data[$key]) || !$data[$key] )) {
                $errors[$key] = ucwords(strtolower(str_replace("_", " ", $key))) . " is a required field.";
            }
        }

        if ($errors) {
            Log::$logger->warn("Errors while validating data: " . print_r($errors, true));
        }

        return $errors;
    }

    // @modified 19 Apr 2013 by bear@deepshiftlabs.com
    // used $this->secondary_key instead of 'code_name' to be compatible with logos and screenshots
    // fixed duplicate names generation (some_name_1_1_1 => some_name_3)
    function generate_code_name($entity_name, $entity_id = null, $next = 0) {
        global $db;

        $code_name = $entity_name;
        $code_name = substr(strtolower($code_name), 0, 64);
        $code_name = str_replace(' ', '_', $code_name);
        $code_name = preg_replace('/[^0-9a-z_]/', '', $code_name);
        $code_name = preg_replace('/[_]$/', '', $code_name);

        $not_where = array();
        if ($entity_id > 0) {
            $not_where[$this->primary_key] = $entity_id;
        }

        if ($next) {
            $code_name = $code_name . "_" . $next;
        }
        $where = array($this->secondary_key => $code_name);

        $result = $db->select($this->table_name, null, null, $where, $not_where);

        if (count($result) == 0)
            return $code_name;
        else
            return $this->generate_code_name($entity_name, $entity_id, $next + 1);
    }

    function get_errors() {
        return $this->errors;
    }

    function get_primary_key() {
        return $this->primary_key;
    }

    function get_table_name() {
        return $this->table_name;
    }

    function get_entity_name() {
        return $this->entity_name;
    }

    function getUrl() {
        $my_url = '/';
        if ($this->is_loaded()) {
            $my_url = self::getUrlByCodeName($this->data['code_name']);
        }
        return $my_url;
    }

    // todo bear - strange
    function calculate_completion_percentage() {
        return;
    }

    function populate_fields() {
        global $db;

        $fields = array();

        $squery = "DESC " . $this->table_name;

        $sresult = $db->query($squery);

        foreach ($sresult AS $sres) {
            if (strpos($sres['Type'], 'varchar(') !== false || strpos($sres['Type'], 'char(') !== false || strpos($sres['Type'], 'text') !== false) {
                $fields[$sres['Field']]['type'] = "text";

                if (strpos($sres['Type'], '(') !== false) {
                    $max_length                         = parse_text_between($sres['Type'], '(', ')');
                    $fields[$sres['Field']]['length'] = array('min' => 0, 'max' => $max_length);
                } else {
                    $fields[$sres['Field']]['length'] = array('min' => 0, 'max' => 25000);
                }
            } else if (strpos($sres['Type'], 'tinyint(') !== false) {
                $fields[$sres['Field']]['type'] = "number";

                if (strpos($sres['Type'], 'unsigned') !== false)
                    $fields[$sres['Field']]['range'] = array('min' => 0, 'max' => 255);
                else
                    $fields[$sres['Field']]['range'] = array('min' => -128, 'max' => 127);
            }
            else if (strpos($sres['Type'], 'smallint(') !== false) {
                $fields[$sres['Field']]['type'] = "number";

                if (strpos($sres['Type'], 'unsigned') !== false)
                    $fields[$sres['Field']]['range'] = array('min' => 0, 'max' => 65535);
                else
                    $fields[$sres['Field']]['range'] = array('min' => -32768, 'max' => 32767);
            }
            else if (strpos($sres['Type'], 'mediumint(') !== false) {
                $fields[$sres['Field']]['type'] = "number";

                if (strpos($sres['Type'], 'unsigned') !== false)
                    $fields[$sres['Field']]['range'] = array('min' => 0, 'max' => 16777215);
                else
                    $fields[$sres['Field']]['range'] = array('min' => -8388608, 'max' => 8388607);
            }
            else if (strpos($sres['Type'], 'bigint(') !== false) {
                $fields[$sres['Field']]['type'] = "number";

                if (strpos($sres['Type'], 'unsigned') !== false)
                    $fields[$sres['Field']]['range'] = array('min' => 0, 'max' => 18446744073709551615);
                else
                    $fields[$sres['Field']]['range'] = array('min' => -9223372036854775808, 'max' => 9223372036854775807);
            }
            else if (strpos($sres['Type'], 'int(') !== false) {
                $fields[$sres['Field']]['type'] = "number";

                if (strpos($sres['Type'], 'unsigned') !== false)
                    $fields[$sres['Field']]['range'] = array('min' => 0, 'max' => 4294967295);
                else
                    $fields[$sres['Field']]['range'] = array('min' => -2147483648, 'max' => 2147483647);
            }
            else if (strpos($sres['Type'], 'datetime') !== false) {
                $fields[$sres['Field']]['type'] = "datetime";
            } else if (strpos($sres['Type'], 'date') !== false) {
                $fields[$sres['Field']]['type'] = "date";
            } else if (strpos($sres['Type'], 'enum') !== false) {
                $fields[$sres['Field']]['type'] = "selection";

                $opt_text                            = str_replace("enum('", "", substr($sres['Type'], 0, -2));
                $fields[$sres['Field']]['options'] = explode("','", $opt_text);
                $fields[$sres['Field']]['default'] = $sres['Default'];
            } else {
                $fields[$sres['Field']] = "unhandled!";
            }

            if (isset($this->required) && is_array($this->required) && in_array($sres['Field'], $this->required)) {
                $fields[$sres['Field']]['required'] = true;
            }

            $fields[$sres['Field']]['database'] = 1;
        }

        return $fields;
    }

    function getEntityType() {
        return strtolower(get_class($this));
    }

    function isFollowedByCurrentUser() {
        if (!is_logged_in()) {
            return 0;
        }
        $following = Utils::userData('following');
        if (isset($following[$this->get_data('uid')])) {
            return 1;
        }
        return 0;
    }

    function load_followers($limit = false, $offset = 0) {
        $this->data['followers'] = array();
        if (!$this->is_loaded() || !in_array($this->getEntityType(), array('user', 'vendor', 'tag'))) {
            $this->data['followers'] = array();
            return;
        }

        $this->data['followers'] = Utils::getFollow($this->data[$this->primary_key], $this->table_name, true, $limit, $offset);
    }

    function load_following($limit = false, $offset = 0, $load_fakes = false) {
        $this->data['following'] = array();
        if (!$this->is_loaded() || $this->getEntityType() !=='user') {
            return;
        }
        $this->data['following'] = Utils::getFollow($this->data[$this->primary_key], $this->table_name, false, $limit, $offset, $load_fakes);
        $this->loadFollowingByEntityTypes();
    }

    function load_follows($limit = false, $offset = 0) {
        if (!in_array($this->getEntityType(), array('user', 'vendor', 'tag'))) {
            return array();
        }

        $this->load_followers($limit, $offset);
        $this->load_following($limit, $offset);
    }

    public function attachUploadedLogoToNewEntity() {
        if (!in_array($this->getEntityType(), array('user', 'vendor'))) {
            return array();
        }

        $logo_code_name = Utils::unsetSVar('not_finished_upload_logo');
        $logo = new Logo(null, $logo_code_name);
        if (!$logo->is_loaded()) {
            return;
        }

        $new_logo_code_name = $this->get_data('code_name') . $this::$logo_hash_suffix;

        $logo->rename($new_logo_code_name);

        $this->set(array('logo_id' => $logo->get_data('logo_id')));
        $this->save();
    }

    // we can use load_comments() for this, but I suppose it can be slow because of cache reasons
    // used only for posted_links and questions
    function getCommentators() {
        global $db;

        if (!in_array($this->getEntityType(), array('question', 'postedlink'))) {
            return array();
        }

        $query = sprintf("SELECT user_id FROM comment
                            WHERE post_type='%s' AND post_id=%d
                            GROUP BY user_id",
                            $this->post_type,
                            $this->get_data($this->primary_key));

        $results = $db->query($query);
        if (!$results) {
            return array();
        }

        $commentators = array();
        foreach($results as $result) {
            $commentators[$result['user_id']] = $result['user_id'];
        }

        return $commentators;
    }

    // note - currently used only for company, vendor, posted link an user. Will do nothing and return false for other classes.
    function saveLogoByUrl($url) {
        if (!in_array($this->getEntityType(), array('vendor', 'postedlink', 'user', 'tag'))) {
            return false;
        }

        if (!$url) {
            $msg = "Empty image address";
            return $msg;
        }

        $data = file_get_contents($url);
        if (!$data) {
            $msg = "Can't load logo from $url";
            Log::$logger->warn($msg);
            return $msg;
        }
        // todo here we should check things which uploadify.php checks to generate error logs.

        if (!$this->data['code_name']) {
            $msg = "Can't save logo file: empty codename.";
            Log::$logger->error($msg);
            return $msg;
        }

        $logos_dir = Utils::getLogosDir();
        if (!is_writable($logos_dir)) {
            $msg = "Can't save logo file as destination '$logos_dir' is not writable.";
            Log::$logger->error($msg);
            return $msg;
        }

        $temp_file = $logos_dir . "/" . $this->data['code_name'] . $this::$logo_hash_suffix;

        if (!file_put_contents($temp_file, $data)) {
            $msg = "Can't save logo file $url";
            Log::$logger->error($msg);
            return $msg;
        }

        list($image_width, $image_height, $image_type) = getimagesize($temp_file);

        if (!$image_width || !$image_height) {
            $msg = "Can't get logo $url dimensions";
            Log::$logger->info($msg);
            unlink($temp_file);
            return $msg;
        }

        if (!in_array( $image_type, Logo::$allowed_image_types)) {
           $msg = "Unsupported image type for url $url";
           Log::$logger->error($msg);
           return $msg;
        }

        $logo      = new Logo();
        $logo_data = array('logo_hash'=>$this->data['code_name'] . $this::$logo_hash_suffix, 'temp_file'=>$temp_file);
        $logo->set($logo_data);
        $logo->save();
        unlink($temp_file);

        $my_current_data            = $this->get();
        $my_current_data["logo_id"] = $logo->get_data('logo_id');
        $this->set($my_current_data);
        $this->save_data();

        return true;
    }

    // $repost_type - auto or manual
    function repost($f_auto = false) {
        if (!in_array($this->getEntityType(), array('postedlink', 'question'))) {
            return false;
        }

        if ($this->data['reposted_by_curr_user'] || $this->data['curr_user_is_author']) {
            return;
        }

        $sql = sprintf("INSERT INTO repost (entity_id, entity_type, user_id, date_added)
                        VALUES (%d, '%s', %d, NOW());",
                        $this->data[$this->primary_key],
                        $this->table_name,
                        get_user_id());
        Database::exec($sql);

        $vote = new Vote($this->data[$this->primary_key], $this->table_name, get_user_id());
        $vote->set_user_vote(1);

        $this->recache();

        $_SESSION['reposted_popup_type'] = $f_auto ? 'auto' : 'manual';
    }

    function load_reposters() {
        if (!in_array($this->getEntityType(), array('postedlink', 'question'))) {
            return false;
        }
        $sql = sprintf("SELECT user_id FROM repost
                WHERE entity_id = %d AND entity_type = '%s'",
                $this->data[$this->primary_key],
                $this->table_name);
        $results =  Database::execArray($sql);

        $user_ids = array();
        foreach ($results as $result) {
            $user_ids[$result['user_id']] = $result['user_id'];
        }
        $this->data['reposters'] = $user_ids;
    }

    function isRepostedByCurrentUser() {
        if (!in_array($this->getEntityType(), array('postedlink', 'question'))) {
            return 0;
        }

        if (!is_logged_in()) {
            return 0;
        }
        if (isset($this->data['reposters'][get_user_id()])) {
            return 1;
        }
        return 0;
    }

    // $custom_user_name can be used to replace 'Author' to 'Reviewer' FE
    static function getAnonymousUser($custom_user_name = 'Author') {
        $anonymous_user = array(
                                'user_id'    => 0,
                                'code_name'  => 'anonymous',
                                'first_name' => 'Anonymous',
                                'last_name'  => 'User',
                                'short_name' => 'Anonymous User',
                                'full_name' => 'Anonymous User',
                                'my_url' => '',
                                'about'      => $custom_user_name . ' identity hidden',
                                'location'   => '&nbsp;',
                                'company'    => array(),
                                'logo' => array(),
                                'status' => 'active',
                            );
        return $anonymous_user;
    }

    // for comments and reviews lists only
    static function replace_privacy(&$entities_list, $custom_user_name = 'Author') {
        if (isset($entities_list) && count($entities_list) > 0) {
            $current_user_id = get_user_id();
            foreach ($entities_list AS $k => $v) {
                if ($v['privacy'] !== 'public' && $v['user_id']!=$current_user_id) {
                    $entities_list[$k]['user'] = self::getAnonymousUser($custom_user_name);
                }
            }
        }
    }

    public static function getUrlByCodeName($codename) {
        if (!defined('static::codename_url_prefix')) {
            Log::$logger->error('attempt to use absent codename_url_prefix constant.');
            return '/';
        }

        return static::codename_url_prefix . $codename;
    }

    /* START OF STATS FUNCTIONS */
    function getPageViews() {
        if (!in_array($this->getEntityType(), array('question', 'postedlink', 'tag', 'user', 'company'))) {
            return 0;
        }

        $sql = sprintf("SELECT count(1) as count
                        FROM track
                        WHERE url='%s'
                            AND ts > '%s'
                            AND f_bot=0",
                        $this->data['my_url'],
                        $this->data['date_added']);
        $result = Database::execArray($sql, true);
        return $result['count'];
    }

    function getClicksOnPostedUrl() {
        if (!in_array($this->getEntityType(), array('postedlink'))) {
            return 0;
        }

        $sql = sprintf("SELECT count(1) as count
                        FROM track
                        WHERE url='%s'
                            AND ts > '%s'
                            AND target='out'
                            AND f_bot=0",
                        $this->data['url'],
                        $this->data['date_added']);
        $result = Database::execArray($sql, true);
        return $result['count'];
    }

    function getClicksOnPostedUrlsFromPostPage() {
        if (!in_array($this->getEntityType(), array('postedlink'))) {
            return 0;
        }

        $sql = sprintf("SELECT count(1) as count
                        FROM track
                        WHERE url IN ('%s', '%s')
                            AND referrer='%s'
                            AND ts > '%s'
                            AND target='out'
                            AND f_bot=0",
                        $this->data['url'],
                        Utils::getHostByUrl($this->data['url']),
                        Utils::getBaseUrl() . $this->data['my_url'],
                        $this->data['date_added']);
        $result = Database::execArray($sql, true);
        return $result['count'];
    }

    function getClicksOnShareButtons() {
        if (!in_array($this->getEntityType(), array('question', 'postedlink'))) {
            return 0;
        }

        $sql = sprintf("SELECT count(1) as count
                        FROM track
                        WHERE referrer='%s'
                            AND ts > '%s'
                            AND target='share'
                            AND f_bot=0",
                        Utils::getBaseUrl() . $this->data['my_url'],
                        $this->data['date_added']);
        $result = Database::execArray($sql, true);
        return $result['count'];
    }

    function getStatsTableByQuery($sql) {
        $data = Database::execArray($sql);

        if (Settings::DEV_MODE) {
            $data[0]['sql'] = $sql;
        }

        if (!$data) {
            return '';
        }

        Utils::$smarty->assign('data', $data);
        Utils::$smarty->assign('type', 'getPostStats');
        Utils::$smarty->assign('metric_id', 'getPostStats');
        Utils::$smarty->assign('no_rows_numbers', true);
        return Utils::$smarty->fetch('components/admin/track_table.tpl');
    }

    function getPostStats() {
        if (!in_array($this->getEntityType(), array('question', 'postedlink'))) {
            return array();
        }

        $results = array();
        $results['html'] = '';

        // todo reuse getPageViews()
        $sql = sprintf("SELECT count(1) as `Page Views`
                        FROM track
                        WHERE url='%s' AND f_bot=0",
                        $this->data['my_url']);
        $results['html'] .= $this->getStatsTableByQuery($sql);

        if ($this->getEntityType() === 'postedlink') {
            // todo reuse getClicksOnPostedUrl()
            $sql = sprintf("SELECT count(1) as `Click throughs from post page or feed`
                            FROM track
                            WHERE url='%s' AND target='out' AND f_bot=0",
                            $this->data['url']);
            $results['html'] .= $this->getStatsTableByQuery($sql);

            // todo reuse getClicksOnPostedUrlsFromPostPage()
            $sql = sprintf("SELECT count(1) as `Click throughs from post page`
                            FROM track
                            WHERE url IN ('%s', '%s') AND referrer='%s' AND target='out' AND f_bot=0",
                            $this->data['url'],
                            Utils::getHostByUrl($this->data['url']),
                            Utils::getBaseUrl() . $this->data['my_url']);
            $results['html'] .= $this->getStatsTableByQuery($sql);
        }

        // see getClicksOnShareButtons()
        $sql = sprintf("SELECT url as provider, count(1) as `Share Button Clicks`
                        FROM track
                        WHERE referrer='%s' AND target='share' AND f_bot=0
                        GROUP BY url
                        ORDER BY url",
                        Utils::getBaseUrl() . $this->data['my_url']);
        $results['html'] .= $this->getStatsTableByQuery($sql);

        return $results;
    }

    /* END OF STATS FUNCTIONS */

}