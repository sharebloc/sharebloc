<?php

/**
 * Gate functions container
 */
class Gate {

    const ERR_CODE_OK = 0;
    const ERR_CODE_GENERAL_ERROR = -1;

    private static $results = array();
    private static $params_data = array();
    private static $params = array();
    private static $missed_params = array();
    private static $missed_rights = array();
    private static $type = '';
    private static $actions = array();
    private static $exception = null;
    private static $no_errors = false;

    public static function graceful_shutdown() {
        if (self::$no_errors) {
            exit;
        }
        if (!headers_sent()) {
            header('HTTP/1.1 200 OK');
        }

        $error = error_get_last();
        if (!is_null($error)) {
            error_log("Critical error while processing ajax query, errors are: " . print_r($error, true));
        }

        self::$results['err_code'] = self::ERR_CODE_GENERAL_ERROR;
        self::$results['message'] = "Unknown server errror";
        self::$results['debug'] = 'error';
        self::$results['debug'] = json_encode(self::$results);
        echo json_encode(self::$results);
        exit;
    }

    private static function addParameter($id, $display_name, $type) {
        self::$params_data[$id]['display_name'] = $display_name;
        self::$params_data[$id]['type'] = $type;
    }

    private static function setupParamsNames() {
        self::addParameter('id', 'id of deleted vendor', 'int');
        self::addParameter('url', 'url to check', 'string');
        self::addParameter('step', 'profile step', 'string');
        self::addParameter('vend_to_delete', 'vendors to delete', 'string');
    }

    private static function addAction($type) {
        self::$actions[$type]['params'] = array();
        self::$actions[$type]['rights'] = array();
        self::$actions[$type]['captcha'] = false;
    }

    private static function addActionParams($type, $names = array(), $obligatory = true) {
        if (!isset(self::$actions[$type])) {
            self::addAction($type);
        }
        if (!is_array($names)) {
            $names = array($names);
        }

        foreach ($names as $name) {
            if (!isset(self::$params_data[$name])) {
                Log::$logger->error("Config error - no '$name' param data");
            }
            self::$actions[$type]['params'][$name] = $obligatory;
        }
    }

    private static function setActionRights($type, $names) {
        if (!is_array($names)) {
            $names = array($names);
        }
        foreach ($names as $name) {
            self::$actions[$type]['rights'][$name] = true;
        }
    }

    private static function setActionCaptcha($type) {
        self::$actions[$type]['captcha'] = true;
    }

    private static function setupActions() {
   //   self::addActionParams('deleteVendor', array('id'));
        self::addActionParams('checkUrl', array('url'), false);
        self::addActionParams('checkUrl', array('step'), false);
        self::addActionParams('delDupl', array('vend_to_delete'), false);
        self::addActionParams('getReadyVend');

   //   self::addActionParams('processVendorName', array('мvendor_name'), false);
   //   self::addActionParams('processVendorName', array('if'));
    }

    public static function init() {
        self::setupParamsNames();
        self::setupActions();
        self::$type = Utils::reqParam('type');
        self::$results['err_code'] = self::ERR_CODE_OK;
        self::$results['message'] = '';
        self::$results['debug'] = 'ok';
        self::$results['type'] = self::$type;
    }

    // warn - modifies values, for example do intval for integers
    private static function validateParams() {
        $bad_params = array();

        foreach (self::$params as $paramName => $value) {
            if ($value === null || $value === '') {
                // we will not validate params which are not obligatory and was not set
                continue;
            }

            if (self::$params_data[$paramName]['type'] == 'email') {
                if (false === filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $bad_params[] = self::$params_data[$paramName]['display_name'];
                }
                self::$params[$paramName] = strtolower($value);
                continue;
            }

            if (self::$params_data[$paramName]['type'] == 'int') {
                if (false === filter_var($value, FILTER_VALIDATE_INT)) {
                    $bad_params[] = self::$params_data[$paramName]['display_name'];
                } else {
                    self::$params[$paramName] = intval($value);
                }
                continue;
            }
            if (self::$params_data[$paramName]['type'] == 'checkbox') {
                self::$params[$paramName] = self::$params[$paramName] ? true : false;
                continue;
            }

//            if (self::$params_data[$paramName]['type']=='password') {
//                $min_pass_length = 6;
//                if (strlen(self::$params[$paramName]) < $min_pass_length) {
//                    self::processError(sprintf(Constants::MSG_USER_WEAK_PASSWORD, $min_pass_length));
//                    return false;
//                }
//                continue;
//            }
        }

        if ($bad_params) {
            self::processError(sprintf('Sorry, value%s of %s %s invalid', (count($bad_params) > 1) ? 's' : '', implode(', ', $bad_params), (count($bad_params) > 1) ? 'are' : 'is'));
            return false;
        }

        return true;
    }

    private static function getParams() {
        foreach (self::$actions[self::$type]['params'] as $paramName => $isObligatory) {
            $value = Utils::reqParam($paramName);
            if (is_string($value)) {
                $value = trim($value);
            }
            self::$params[$paramName] = $value;
            if ($isObligatory && ($value === null || $value === '')
                    && self::$params_data[$paramName]['type'] !== 'checkbox') {
                self::$missed_params[] = self::$params_data[$paramName]['display_name'];
            }
        }

        if (self::$missed_params) {
            self::processError(sprintf('Please, enter %s', implode(', ', self::$missed_params)));
            return false;
        }
        return true;
    }

    private static function checkRights() {
//        foreach (self::$actions[self::$type]['rights'] as $rightName => $value) {
//            if (!User::$user_rights[$rightName]) {
//                self::$missed_rights = $rightName;
//                Log::$logger->warn("No permissions when doing " . self::$type);
//            }
//        }
//        if (self::$missed_rights) {
//            self::processError('Permission denied');
//            return false;
//        }
        return true;
    }

    private static function processError($user_message, $is_error = false) {
        self::$results['err_code'] = self::ERR_CODE_GENERAL_ERROR;
        if (!$user_message) {
            $user_message = 'Unknown server error';
        }
        self::$results['message'] = $user_message;

        $msg = sprintf("Error when doing %s. Params are: %s. %s. \n (%s)", self::$type, print_r(self::$params, true), '', $user_message);

        if ($is_error) {
            Log::$logger->error($msg);
        } else {
            Log::$logger->info($msg);
        }
    }

    public static function doAction() {
        Log::$logger->trace("Will do ajax action '" . self::$type . "'");
        if (!isset(self::$actions[self::$type])) {
            self::$results['type'] = 'error';
            self::processError("Unknown server errror");
            Log::$logger->fatal("Unregistered ajax action '" . self::$type . "'");
        } else {
            try {
                if (self::checkRights() && self::getParams()
                        && self::validateParams()) {
                    $function_name = self::$type;
                    self::$function_name();
                }
            } catch (Exception $e) {
                self::$exception = $e;
                self::processError("Unknown server errror");
            }
        }

//        if (Config::get('DEBUG_AJAX')) {
//            self::$results['debug'] = json_encode(self::$results);
//        }
        echo json_encode(self::$results);

        self::$no_errors = true;
        Log::$logger->trace("Ajax action " . self::$type . " done.");
    }

    // **** actions **** //
    private static function deleteVendor() {
        if (true) {
            self::$results['id'] = 55;
            self::$results['bear'] = 'dsclksndmcklnd';
        } else {
            self::processError("Unknown server errror", true);
        }
    }

    private static function checkUrl() {
        session_write_close();
        if (!self::$params['url']) {
            return;
        }
        if (!Utils::checkUrlHeader(self::$params['url'], self::$params['step'])) {
            if (self::$params['step'] == 0) {
                self::$results['message'] = "The url entered is unavailable. Do you want to save this url?";
                return;
            } else {
                self::processError("The url entered is unavailable. Please correct it or click the 'I can't find profile' button.");
                return;
            }
        }
        $result = Utils::getAdditionalWarns(self::$params['url'], self::$params['step']);
        if ($result) {
            self::$results['message'] = $result;
        }
    }

    private static function delDupl() {
        session_write_close();
        Log::$logger->debug('AJAX - delDupl function (deleting of the duplicates and then crawling all other vendors)');
        //Utils::sleep(40);
        Log::$logger->debug(self::$params['vend_to_delete']);
        DBUtils::deleteNameDuplicates(self::$params['vend_to_delete']);
        Utils::crawlVendors();
    }

    private static function getReadyVend() {
        Log::$logger->debug("AJAX page refreshing...........");
        self::$results['ready'] = DBUtils::getNumberOfReadyVendors();
        self::$results['all'] = DBUtils::getNumberOfAllCrawlingVendors();
    }

}

?>