<?php

/**
 * Utility functions for DB interaction container
 */
class DBUtils {

    public static $adoConn = null;
    private static $transaction_open = false;
    public static $last_error_msg = 0;

    /**
     * Prepares a connection to DB.
     * @see Settings::DB_HOST
     * @see Settings::DB_USER
     * @see Settings::DB_PASSWORD
     * @see Settings::DB_DBNAME
     * @return ADODBConnection
     */
    public static function getADOConn($params = array()) {
        self::$last_error_msg = "";
        if (!$params) {
            $params['DB_TYPE'] = Settings::DB_TYPE;
            $params['DB_HOST'] = Settings::DB_HOST;
            $params['DB_USER'] = Settings::DB_USER;
            $params['DB_PASSWORD'] = Settings::DB_PASSWORD;
            $params['DB_DBNAME'] = Settings::DB_DBNAME;
        }

        global $ADODB_FETCH_MODE;
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

        if ($params['DB_TYPE'] === 'pgsql') {
            $adoConn = ADONewConnection('postgres');
        } elseif ($params['DB_TYPE'] === 'mysql') {
            $adoConn = ADONewConnection('mysqli');
        } else {
            if (Log::$logger) {
                Log::$logger->fatal("Unknown database type!");
            } else {
                error_log("Unknown database type!");
            }
            return false;
        }

        try {
            $result = $adoConn->Connect($params['DB_HOST'], $params['DB_USER'], $params['DB_PASSWORD'], $params['DB_DBNAME']);
        } catch (exception $e) {
            $result = false;
        }

        if (!$result) {
            if (Log::$logger) {
                $errorMsg = $adoConn->ErrorMsg();
                $params['DB_PASSWORD'] = '*';
                $errorMsg = "Can't connect to database.\nMessage: $errorMsg.\nParams: " . print_r($params, true);
                Log::$logger->fatal($errorMsg);
                self::$last_error_msg = $errorMsg;
            }
            return false;
        }

        if (Log::$logger) {
            Log::$logger->debug("Connected to database.");
        }

        return $adoConn;
    }

    public static function initAdoConn($adoConn = null) {
        if (!self::$adoConn) {
            if (!$adoConn) {
                $adoConn = self::getADOConn();
            }
            if (!$adoConn) {
                Log::$logger->fatal("Can't init DB connection.");
                return false;
            }
            self::$adoConn = $adoConn;
            Log::$logger->trace("DBUtils connection initialized.");
        }
        return self::$adoConn;
    }

    public static function closeAdoConn() {
        if (self::$adoConn) {
            self::$adoConn->Close();
            self::$adoConn = null;
        }
        if (Log::$logger) {
            Log::$logger->trace("DB connection closed.");
        }
        return true;
    }

    public static function executeQuery($sql, $input_arr = false) {
        //Log::$logger->trace("Query will be executed:\n" . $sql);
        self::$last_error_msg = "";

        try {
            if ($input_arr) {
                $_sql = self::$adoConn->Prepare($sql);
                self::$adoConn->Execute($_sql, $input_arr);
            } else {
                self::$adoConn->Execute($sql);
            }
            $result = true;
        } catch (exception $e) {
            $result = false;
        }
        if ($result === false) {
            $err = " executeQuery error: " . self::$adoConn->ErrorMsg() . " Query was:\n[ " . $sql . " ]";
            if ($input_arr) {
                $err.= "\n params were:\n" . print_r($input_arr, true);
            }

            self::$last_error_msg = $err;

            self::$adoConn->RollbackTrans();
            self::$transaction_open = false;
            Log::$logger->fatal($err);
        } else {
            //Log::$logger->trace("Executed OK.");
        }
        return $result;
    }

    public static function executeQueryArray($sql, $oneRow = false, $input_arr = false) {
        //Log::$logger->trace("Query will be executed:\n" . $sql);
        self::$last_error_msg = "";
        $_sql = $sql;
        try {
            if ($input_arr) {
                $_sql = self::$adoConn->Prepare($sql);
            }
            if ($oneRow) {
                $result = self::$adoConn->GetRow($_sql, $input_arr);
            } else {
                $result = self::$adoConn->GetArray($_sql, $input_arr);
            }
        } catch (exception $e) {
            $result = false;
        }

        if ($result === false) {
            $err = " executeQueryArray error: " . self::$adoConn->ErrorMsg() . " Query was:\n [ " . $sql . " ]";
            if ($input_arr) {
                $err.= "\n params were:\n" . print_r($input_arr, true);
            }
            self::$last_error_msg = $err;
            self::$adoConn->RollbackTrans();
            self::$transaction_open = false;
            Log::$logger->fatal($err);
        } else {
            //Log::$logger->trace("Executed OK.");
        }
        //Log::$logger->trace("Result: " . print_r($result, true));
        return $result;
    }

    public static function executeQueryArrayLimit($sql, $limit_str = '', $offset_str = '', $count = false) {
        self::$total_found = null;
        if ($count) {
            self::$total_found = 0;
        }
        $limit_sql = sprintf("%s \n %s %s", $sql, $limit_str, $offset_str);
        $result = self::executeQueryArray($limit_sql);
        if ($result && $count) {
            $limit_sql = sprintf("select COUNT(1) count from (%s) temp", $sql);
            $count = self::executeQueryArray($limit_sql, true);
            self::$total_found = $count['count'];
        }
        return $result;
    }

    public static function beginTransaction() {
        self::$last_error_msg = "";
        if (self::$transaction_open) {
            Log::$logger->error("Attempt to open transaction, but there is already a transaction in progress.");
            //Log::$logger->error("Backtrace:". print_r(debug_backtrace(), true));
            return false;
        }

        try {
            $result = self::executeQuery('BEGIN');
        } catch (exception $e) {
            $result = false;
        }

        if ($result) {
            self::$transaction_open = true;
            return true;
        } else {
            $err = self::$adoConn->ErrorMsg() . " - problems on transaction opening.";
            self::$last_error_msg = $err;
            Log::$logger->fatal("" . $err);
            Log::$logger->fatal("Failed to open transaction.");
            self::$adoConn->RollbackTrans();
            return false;
        }
    }

    public static function rollbackTransaction() {
        if (!self::$transaction_open) {
            Log::$logger->warn("Attempt to rollback transaction, but there is no transaction in progress.");
            return false;
        }
        $result = self::executeQuery('ROLLBACK');
        if (!$result) {
            Log::$logger->warn("Rollback failed.");
            return false;
        }
        self::$transaction_open = false;
        Log::$logger->trace("Rollback done.");
        return true;
    }

    public static function commitTransaction($db_type = null) {
        if (!$db_type) {
            $db_type = Settings::DB_TYPE;
        }

        if (!self::$transaction_open) {
            Log::$logger->warn("Attempt to commit transaction, but there is no transaction in progress.");
            if ($db_type == 'pgsql') {
                return false;
            }
        }
        $result = self::executeQuery('COMMIT');
        self::$transaction_open = false;
        if (!$result) {
            Log::$logger->warn("Commit failed.");
            return false;
        } else {
            Log::$logger->trace("Commit done.");
            return true;
        }
    }

    public static function getNextId($sequence_name) {
        try {
            $result = self::$adoConn->GenID($sequence_name);
        } catch (exception $e) {
            $result = false;
        }
        if ($result === false) {
            Log::$logger->fatal("Failed to get next ID from $sequence_name.");
        }
        return $result;
    }

    // only for my_sql
    public static function getLastId() {
        try {
            $result = self::$adoConn->Insert_ID();
        } catch (exception $e) {
            $result = false;
        }
        if ($result === false) {
            Log::$logger->fatal("Failed to get last inserted ID.");
        }
        return $result;
    }

    /*
     * Escapes all specific symbols by DB engine, and rounds string with quotes
     * Should be used instead of Utils::sqlsafe()
     * Warn: does a query to DB to get escaped string, so connection should be established.
     */

    // todo use own or not use at all as PG qstr does escaping \ with \\ which is not standard (should use E)
    public static function sqlSafe($string, $use_own = false) {
        if ($use_own) {
            $result = str_replace("'", "''", $string);
        } else {
            $result = self::$adoConn->qstr($string);
        }
        return $result;
    }

    /*
     * Escapes all specific symbols in string by DB engine, and rounds string with % and with quotes
     */

    public static function prepareLikeString($string) {
        $string = trim($string);
        $string = "%" . $string . "%";
        $result = self::sqlSafe($string);
        return $result;
    }

    /* END OF COMMON FUNCTIONS */

    public static function getUsers() {
        $sql = "SELECT * FROM data_users";
        $result = self::executeQueryArray($sql);
        return $result;
    }
///AK 2014/04/10 -- note this appears to be for data entry not main product
    public static function getUserInfoByEmail($email) {
        $sql = sprintf("SELECT id, email, password, company, is_active, is_admin
                        FROM data_users
                        WHERE email=%s
                            AND is_active=1;", self::sqlSafe($email));
        $result = self::executeQueryArray($sql, true);
        return $result;
    }

    public static function saveImportedVendors($imp_vendors, $batch_id) {
        Log::$logger->trace('Function saveImportedVendors');
        Utils::sVar('batch_id', $batch_id);
        $type = Utils::reqParam('list_type');
        foreach ($imp_vendors as $vendor) {
            $sql = sprintf("INSERT INTO data_vendors (status, type, vendor_raw, source, batch_id)
                            VALUES ('new', '%s', %s, %s, '%s');",
                                $type == 'vendor' ? 'vendor' : 'company',
                                self::sqlSafe($vendor['vendor']),
                                self::sqlSafe($vendor['source']),
                                $batch_id);
            Log::$logger->trace($sql);
            self::executeQuery($sql);
        }
        Log::$logger->trace('End function saveImportedVendors');
    }

    public static function deleteLoadedVendors() {
        Log::$logger->trace('Function deleteLoadedVendors');
        $sql = sprintf("DELETE FROM data_vendors
                        WHERE batch_id='%s';", Utils::sVar('batch_id'));
        $result = self::executeQuery($sql);
        return $result;
    }

//TODO
    public static function getVendorsByStep($step) {
        Log::$logger->trace('Function getLoadedVendors');

        switch ($step) {
            case 'new':
                $sql = sprintf("SELECT * FROM data_vendors
                                WHERE status='new'
                                    AND vendor IS NULL
                                    AND deleted_ts IS NULL
                                    AND batch_id='%s'
                                    ORDER BY vendor, vendor_raw;", Utils::sVar('batch_id'));
                break;
            case 'cleaned':
                $sql = sprintf("SELECT * FROM data_vendors
                                WHERE vendor IS NOT NULL
                                    AND is_duplicate IS NULL
                                    AND deleted_ts IS NULL
                                    AND batch_id='%s'
                                    ORDER BY vendor, vendor_raw;", Utils::sVar('batch_id'));
                break;
            case 'completed':
                $sql = sprintf("SELECT * FROM data_vendors
                                WHERE vendor IS NOT NULL
                                    AND status='completed'
                                    AND batch_id='%s'
                                    ORDER BY vendor, vendor_raw;", Utils::sVar('batch_id'));
                break;
        }

        Log::$logger->trace($sql);
        $result = self::executeQueryArray($sql);
        return $result;
    }

    public static function saveCleanedVendorName($id, $name) {
        Log::$logger->trace('Function saveCleanedVendorName');
        $sql = sprintf("UPDATE data_vendors
                        SET vendor=%s
                        WHERE id=%d;",
                        self::sqlSafe(trim($name)),
                        $id);
        self::executeQuery($sql);
    }

    public static function searchDuplicatesByName($table_name, $vendor_name, $strong_check=1, $de_id = null) {
        Log::$logger->trace('Function searchDuplicatesByName strong_check='.$strong_check. " table=".$table_name);

        if ($table_name == 'vendor') {
            $id_field = 'vendor_id';
            $name_field = 'vendor_name';
            $url_field = 'website';
        } elseif ($table_name == 'company') {
            $id_field = 'company_id';
            $name_field = 'company_name';
            $url_field = 'website';
        } else {
            $id_field = 'id';
            $name_field = 'vendor';
            $url_field = 'crawled_google_url';
        }

        if ($strong_check) {
            $sql_expr = "SELECT %s, %s, %s  FROM %s WHERE %s='%s'";
        } else {
            $sql_expr = "SELECT %s, %s, %s FROM %s WHERE %s LIKE '%%%s%%'";
        }
        $sql = sprintf($sql_expr,
                        $id_field,
                        $name_field,
                        $url_field,
                        $table_name,
                        $name_field,
                        self::sqlSafe($vendor_name, true));
        if ($de_id && $table_name == 'data_vendors') {
            $sql .= sprintf(" AND id<>%d
                            AND deleted_ts IS NULL
                            AND status<>'exported';", $de_id);
        }
        Log::$logger->trace($sql);
        $result = self::executeQueryArray($sql);
        return $result;
    }

    public static function searchDuplicatesByUrl($table_name, $url, $strong_check=1, $de_id = null) {
        Log::$logger->trace('Function searchDuplicatesByUrl strong_check='.$strong_check. " table=".$table_name);
        Log::$logger->trace("url to check - ".$url);
        if (!$url) {
            return array();
        } else {
            if (strpos($url, 'http://')===false) {
                $url_second = str_replace('https://', 'http://', $url);
            } else {
                $url_second = str_replace('http://', 'https://', $url);
            }
            Log::$logger->trace("url_second to check - ".$url_second);
        }

        if ($table_name == 'vendor') {
            $id_field = 'vendor_id';
            $name_field = 'vendor_name';
            $url_field = 'website';
        } elseif ($table_name == 'company') {
            $id_field = 'company_id';
            $name_field = 'company_name';
            $url_field = 'website';
        } else {
            $id_field = 'id';
            $name_field = 'vendor';
            $url_field = 'crawled_google_url';
        }

        if ($strong_check) {
            $sql = sprintf("SELECT %s, %s, %s FROM %s WHERE %s=",
                            $id_field,
                            $name_field,
                            $url_field,
                            $table_name,
                            $url_field) . "%s";
            if ($de_id && $table_name == 'data_vendors') {
                $sql .= sprintf(" AND id<>%d
                                AND deleted_ts IS NULL
                                AND status<>'exported';", $de_id);
            }

            $sql_first = sprintf($sql, self::sqlSafe($url));
            $sql_second = sprintf($sql, self::sqlSafe($url_second));
            Log::$logger->trace($sql_first);
            Log::$logger->trace($sql_second);
            $result_first = self::executeQueryArray($sql_first);
            $result_second = self::executeQueryArray($sql_second);

            $result = array_merge($result_first, $result_second);

            return $result;

        } else {
            $host = Utils::trimUrlForSearchingDupl($url);
            if (!$host) {
                return array();
            }
            $sql = sprintf("SELECT %s, %s, %s FROM %s WHERE %s LIKE '%%%s%%'",
                            $id_field,
                            $name_field,
                            $url_field,
                            $table_name,
                            $url_field,
                            self::sqlSafe($host, true));
            if ($de_id && $table_name == 'data_vendors') {
                $sql .= sprintf(" AND id<>%d
                                AND deleted_ts IS NULL
                                AND status<>'exported';", $de_id);
            }
            Log::$logger->trace($sql);
            $result = self::executeQueryArray($sql);
            return $result;
        }
    }

    public static function saveCrawledUrl($id, $url) {
        Log::$logger->debug('saveCrawledUrl ID=' . $id . " url='" . $url . "'");
        $sql = sprintf("UPDATE data_vendors
                        SET is_crawled=1,
                            crawled_google_url_source=%s,
                            crawled_google_url=%s
                        WHERE id=%d;",
                        self::sqlSafe($url),
                        self::sqlSafe($url),
                        $id);
        Log::$logger->trace($sql);
        self::executeQuery($sql);
        //return $result;
    }

    public static function deleteNameDuplicates($vend_to_delete='') {
        Log::$logger->trace('deleteNameDuplicates');
        if (!$vend_to_delete) {
            $vend_to_delete = Utils::reqParam('vend_to_delete');
        }
        self::deleteDuplicates('name', $vend_to_delete);
    }

    public static function deleteUrlDuplicates() {
        Log::$logger->trace('deleteUrlDuplicates');
        self::deleteDuplicates('url', Utils::reqParam('vend_to_delete'));
    }

    private static function deleteDuplicates($duplicate_reason, $ids_to_delete = "") {
        if (!trim($ids_to_delete)) {
            Log::$logger->info('No duplicates to delete in the DB');
            return;
        }

        $sql = sprintf("UPDATE data_vendors
                            SET status='deleted',
                                deleted_ts=NOW(),
                                is_duplicate='%s'
                            WHERE batch_id='%s'
                                AND vendor_raw IS NOT NULL",
                        self::sqlSafe($duplicate_reason, true),
                        Utils::sVar('batch_id'));
        if ($ids_to_delete) {
            $sql .= sprintf(" AND id IN (%s);", $ids_to_delete);
        }
        Log::$logger->trace($sql);
        self::executeQuery($sql);
    }

    public static function markOtherAsCompleted() {
        Log::$logger->trace('markOtherAsCompleted');
        $sql = sprintf("UPDATE data_vendors
                        SET status='completed'
                        WHERE deleted_ts is NULL
                            AND status='new'
                            AND batch_id='%s'",
                        Utils::sVar('batch_id'));
        Log::$logger->debug($sql);
        //e($sql);
        self::executeQuery($sql);
    }

    public static function getVendorById($id) {
        $sql = sprintf("SELECT * FROM data_vendors
                        WHERE id=%d", $id);
        $vendor = self::executeQueryArray($sql, true);

        return $vendor;
    }

    private static function cleanUnfinishedVendors() {
        $sql_clean = sprintf("UPDATE data_vendors
                                SET worker_id=NULL,
                                    work_start_ts=NULL
                                WHERE worker_id IS NOT NULL
                                    AND work_start_ts IS NOT NULL
                                    AND work_end_ts IS NULL
                                    AND status='completed'
                                    AND UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(work_start_ts) > %d;",
                            Settings::CLEAN_DATA_TIMEOUT);
        Log::$logger->debug($sql_clean);
        self::executeQuery($sql_clean);
    }

    public static function getVendorForWork() {

        self::cleanUnfinishedVendors();

        $sql = sprintf("SELECT * FROM data_vendors
                        WHERE status='completed'
                            AND deleted_ts is NULL
                            AND is_duplicate IS NULL
                            AND worker_id IS NULL
                            AND work_end_ts IS NULL
                            AND work_start_ts IS NULL
                            ORDER BY id
                            LIMIT 1;");
        $vendor = self::executeQueryArray($sql, true);
        if($vendor) {
            Log::$logger->debug("NEW vendor ID=".$vendor['id']);
            self::addUserDataForVendor($vendor['id']);
            $updated_vendor = self::getVendorById($vendor['id']);
        } else {
            $updated_vendor = $vendor;
        }

        return $updated_vendor;
    }

    public static function addUserDataForVendor($vendor_id) {
        $user = Utils::sVar('user');
        $sql = sprintf("UPDATE data_vendors
                        SET worker_id=%d,
                            work_start_ts=NOW()
                        WHERE id=%d;",
                            $user['id'],
                            $vendor_id);
        //Log::$logger->debug($sql);
        self::executeQuery($sql);
    }

    public static function saveWorkVendorData($vendor) {
        $val_sql = "";

        foreach (Utils::$networks as $network) {
            $network_id = $network['id'];
            if ($network_id != 'all_done') {
                $network_source_id = $network_id . "_source";
                $network_url = $vendor[$network_id]['network_link'];
                $network_source_url = $vendor[$network_id]['network_link_source'];

                $val_sql .= sprintf("%s = %s, ", $network_id, self::sqlSafe($network_url));
                $val_sql .= sprintf("%s = %s, ", $network_source_id, self::sqlSafe($network_source_url));
            }
        }
        $save_data_steps = Utils::getSaveStepsForEntityByType($vendor['type']);
        foreach ($save_data_steps as $data) {
            $data_id = $data['id'];
            if ($data_id != "all_done") {
                $data_source_id = $data_id . "_source";
                $data_source = $vendor[$data_source_id];
                $data = '';
                if ($data_source != 'none') {
                    $data = $vendor[$data_source]['data'][$data_id];
                }
                $val_sql .= sprintf("%s = %s, ", $data_id, self::sqlSafe($data));
                $val_sql .= sprintf("%s = %s, ", $data_source_id, self::sqlSafe($data_source));
            }
        }

        $sql = sprintf("UPDATE data_vendors SET %s
                            status='ready',
                            work_end_ts=NOW()
                        WHERE id=%d",
                            $val_sql,
                            $vendor['id']);
        //Log::$logger->debug($sql);
        self::executeQuery($sql);
    }

    public static function saveLogoFilename($vendor, $new_img_name) {
        $sql = sprintf("UPDATE data_vendors
                        SET logo_filename=%s WHERE id=%d;",
                            self::sqlSafe($new_img_name),
                            $vendor['id']);
        self::executeQuery($sql);
    }

    public static function getCompletedEntitiesCount() {
        $entities = self::getEntitiesCountByStatus(array('completed'));
        return $entities;
    }

    public static function getExportedEntitiesCount() {
        $entities = self::getEntitiesCountByStatus(array('exported'));
        return $entities;
    }

    public static function getDeletedEntitiesCount() {
        $entities = self::getEntitiesCountByStatus(array('deleted'));
        return $entities;
    }

    public static function getReadyForExportEntitiesCount() {
        $entities = self::getEntitiesCountByStatus(array('ready', 'verified'));
        return $entities;
    }

    public static function getReadyForExportEntities() {
        $entities = self::getEntitiesByStatus(array('ready', 'verified'));
        return $entities;
    }

    public static function getAllEntitiesCount() {
        $entities = self::getEntitiesCountByStatus(array('new', 'completed', 'deleted', 'ready', 'verified', 'exported'));
        return $entities;
    }

    public static function getAllEntities() {
        $entities = self::getEntitiesByStatus(array('new', 'completed', 'deleted', 'ready', 'verified', 'exported'));
        return $entities;
    }

    public static function getAllEntitiesCountWithoutDeletedAndExported() {
        $entities = self::getEntitiesCountByStatus(array('new', 'completed', 'ready', 'verified'));
        return $entities;
    }

    public static function getAllEntitiesWithoutDeletedAndExported() {
        $entities = self::getEntitiesByStatus(array('new', 'completed', 'ready', 'verified'));
        return $entities;
    }

    private static function getEntitiesCountByStatus($status_arr) {
        if (!$status_arr) {
            return false;
        }
        $status_expr = sprintf("status IN (%s)", "'".implode("', '" , $status_arr)."'");
        $sql = sprintf("SELECT COUNT(*) FROM data_vendors
                            WHERE %s;",
                        $status_expr);
        $entities_count = self::executeQueryArray($sql, true);

        return $entities_count['COUNT(*)'];
    }

    private static function getEntitiesByStatus($status_arr) {
        if (!$status_arr) {
            return false;
        }
        $status_expr = sprintf("status IN (%s)", "'".implode("', '" , $status_arr)."'");
        $sql = sprintf("SELECT * FROM data_vendors
                            WHERE %s;",
                        $status_expr);
        $entities = self::executeQueryArray($sql);

        return $entities;
    }

    public static function getUserEmailById($user_id) {
        $sql = sprintf("SELECT * FROM data_users WHERE id=%d",
                        $user_id);
        $res = self::executeQueryArray($sql, true);
        if ($res) {
            return $res['email'];
        } else {
            return false;
        }
    }

    public static function updateVendorWithVerifiedData($id) {
        if (!$id) {
            $err_msg = "Error - there is no vendor info to update";
            return $err_msg;
        }

        $sql = sprintf("UPDATE data_vendors
                        SET status='verified',
                            vendor = %s,
                            source = %s,
                            crawled_google_url = %s,
                            linkedin = %s,
                            facebook = %s,
                            twitter = %s,
                            crunchbase = %s,
                            angel = %s,
                            city = %s,
                            country = %s,
                            logo = %s,
                            description = %s,
                            size = %s,
                            industry = %s
                        WHERE id = %d;",
                self::sqlSafe(Utils::reqParam('vendor')),
                self::sqlSafe(Utils::reqParam('source')),
                self::sqlSafe(Utils::reqParam('crawled_google_url')),
                self::sqlSafe(Utils::reqParam('linkedin')),
                self::sqlSafe(Utils::reqParam('facebook')),
                self::sqlSafe(Utils::reqParam('twitter')),
                self::sqlSafe(Utils::reqParam('crunchbase')),
                self::sqlSafe(Utils::reqParam('angel')),
                self::sqlSafe(Utils::reqParam('city')),
                self::sqlSafe(Utils::reqParam('country')),
                self::sqlSafe(Utils::reqParam('logo')),
                self::sqlSafe(Utils::reqParam('description')),
                self::sqlSafe(Utils::reqParam('size', '')),
                self::sqlSafe(Utils::reqParam('industry', '')),
                $id);
        self::executeQuery($sql);
        return false;
    }

    public static function updateUser() {
        $user_id = Utils::reqParam('user_id');
        if (!$user_id) {
            Log::$logger->error("User data updating error - no user_iв in POST");
            return false;
        }
        Log::$logger->info("Info updating for user id=".$user_id);
        $new_pass = trim(Utils::reqParam('new_pass_'.$user_id));
        $update_pass_str = '';
        if ($new_pass) {
            $update_pass_str = sprintf("password = '%s',",
                Utils::getHashForPassword($new_pass));
        }
        $sql = sprintf("UPDATE data_users
                        SET email = %s,
                            %s
                            company = %s,
                            is_active = %d,
                            is_admin = %d
                        WHERE id = %d;",
                self::sqlSafe(trim(Utils::reqParam('new_email_'.$user_id))),
                $update_pass_str,
                self::sqlSafe(trim(Utils::reqParam('new_company_'.$user_id))),
                Utils::reqParam('is_active_'.$user_id)=='on' ? 1 : 0,
                Utils::reqParam('is_admin_'.$user_id)=='on' ? 1 : 0,
                $user_id);

        self::executeQuery($sql);
    }

    public static function addNewUser() {
        $sql = sprintf("INSERT INTO data_users (email, password, company, is_active, is_admin)
                            VALUES (%s, '%s', %s, %d, %d)");
        //self::executeQuery($sql);
    }

    public static function getNumberOfReadyVendors() {
        $sql = sprintf("SELECT COUNT(*) FROM data_vendors
                                WHERE vendor IS NOT NULL
                                    AND is_duplicate IS NULL
                                    AND deleted_ts IS NULL
                                    AND is_crawled=1
                                    AND batch_id='%s'
                                    ORDER BY vendor, vendor_raw;",
                        Utils::sVar('batch_id'));
        $res = self::executeQueryArray($sql, true);
        return $res['COUNT(*)'];
    }

    public static function getNumberOfAllCrawlingVendors() {
        $sql = sprintf("SELECT COUNT(*) FROM data_vendors
                                WHERE vendor IS NOT NULL
                                    AND is_duplicate IS NULL
                                    AND deleted_ts IS NULL
                                    AND batch_id='%s'
                                    ORDER BY vendor, vendor_raw;",
                        Utils::sVar('batch_id'));
        $res = self::executeQueryArray($sql, true);
        return $res['COUNT(*)'];
    }

    public static function deleteVendors($ids_to_delete) {
        Log::$logger->debug("Vendors to delete - ".$ids_to_delete);
        $sql = sprintf("UPDATE data_vendors
                            SET status='deleted',
                                deleted_ts = NOW()
                            WHERE id IN (%s);",
                        self::sqlSafe($ids_to_delete, true));
        self::executeQuery($sql);
    }

    public static function deleteVendorsFromDB($ids_to_delete) {
        Log::$logger->debug("Vendors to delete from DB - ".$ids_to_delete);
        $sql = sprintf("DELETE FROM data_vendors
                            WHERE id IN (%s);",
                        self::sqlSafe($ids_to_delete, true));
        self::executeQuery($sql);
    }

}
?>