<?php

require_once(DOCUMENT_ROOT . '/includes/log4php/Logger.php');

/**
 * Class to provide common access to a single logger object
 */
class Log {

    public static $logger         = null;
    public static $script_times   = array();
    public static $last_tick_time = 0;

    public static function error_handler($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return true;
        }
        error_log($errstr);
        return true;
    }

    public static function getNewLogger($config_file, $loggerName = "vs_logger") {
        // used only to intercept errors triggered by log4php LoggerAppenderPhp
        set_error_handler('Log::error_handler', E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);

        Logger::configure($config_file, 'LoggerConfiguratorXml');
        try {
            $logger = Logger::getLogger($loggerName);
        } catch (Exception $e) {
            error_log("getNewLogger: can't create new logger $loggerName");
            return false;
        }
        if (!$logger) {
            error_log("getNewLogger: can't create new logger $loggerName");
            return false;
        }
        return $logger;
    }

    public static function init() {
        if (!self::$logger) {
            $logger = self::getNewLogger(DOCUMENT_ROOT . "/includes/log4php.xml");
            self::$logger = $logger;
            self::$logger->trace("Logger initialized.");
            self::tick('start');
        }
        return self::$logger;
    }

    public static function tick($message = '', $last = false) {
        // tick will not be logged if last tick was logged less than $TICKS_TIMEOUT_MS ago.
        // this is to not overflow memory with ticks when we do 10.000 db queries
        // + not overwrite previous ticks
        $TICKS_TIMEOUT_MS = 10;

        // todo move to settings
        $LOG_LONG_TICKS      = false;
        $TICKS_INFO_TIMEOUT  = 100;
        $TICKS_WARN_TIMEOUT  = 300;
        $TICKS_ERROR_TIMEOUT = 500;

        $time = floor(microtime(true) * 1000);

        $diff = 0;
        if (self::$last_tick_time) {
            $diff = $time - self::$last_tick_time;
            if ($diff < $TICKS_TIMEOUT_MS) {
                return;
            }
        }

        self::$last_tick_time = $time;
        if (!$message) {
            $message = $time;
        }

        if ($last) {
            $message = $message . " (" . $_SERVER['SCRIPT_NAME'] . ")";
        }


        self::$script_times[$time] = $message;

        if ($LOG_LONG_TICKS && $diff > $TICKS_INFO_TIMEOUT) {
            $msg = sprintf("Too long tick. Tick message: $message. Tick time: %d ms", $diff);
            if ($diff > $TICKS_WARN_TIMEOUT) {
                if ($diff > $TICKS_ERROR_TIMEOUT) {
                    self::$logger->error($msg);
                } else {
                    self::$logger->warn($msg);
                }
            } else {
                self::$logger->info($msg);
            }
        }

        if ($last) {
            self::logLongRequestIfNeeded();
        }
    }

    public static function logLongRequestIfNeeded() {
        $first_time = key(self::$script_times);
        $last_msg   = end(self::$script_times);
        $last_time  = key(self::$script_times);

        $diff = $last_time - $first_time;
        if ($diff > Settings::REQUEST_TIMEOUT_INFO) {
            $msg = sprintf("Too long request to '%s'. Request time: %.3f s", $_SERVER['SCRIPT_NAME'], $diff / 1000);
            if ($diff > Settings::REQUEST_TIMEOUT_WARN) {
                if ($diff > Settings::REQUEST_TIMEOUT_ERROR) {
                    self::$logger->error($msg);
                } else {
                    self::$logger->warn($msg);
                }
            } else {
                self::$logger->info($msg);
            }
        }
    }

    static function logVisit() {
        $VISIT_TIMEOUT_SEC = 1800;
        global $db;

        $cmd = get_input('cmd');
        $allow_ajax = false;

        if ($cmd === "track_link") {
            $req_url = get_input('dest');
            $allow_ajax = true;
        } else {
            $req_url = $_SERVER['REQUEST_URI'];
        }

        if (Utils::isAjaxRequest() && !$allow_ajax) {
            return;
        }

        if ($_SERVER['SCRIPT_NAME']=='/ext_auth.php') {
            return;
        }

        /* Filling data */
        $track_data                = array();
        $track_data['referrer']    = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $track_data['agent']       = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $track_data['ip']          = $_SERVER['REMOTE_ADDR'];
        $track_data['query']       = $_SERVER['QUERY_STRING'];
        $track_data['script_name'] = $_SERVER['SCRIPT_NAME'];
        $track_data['session_id']  = session_id();
        $track_data['url']         = $req_url;
        $track_data['details']     = '';

        $track_data['f_bot'] = Utils::isBot($track_data['agent']);

        if ($cmd == "track_link") {
            $track_data['target'] = 'out';
            if (get_input('type') == "share") {
                $track_data['target'] = 'share';
            }
        } elseif (!$track_data['referrer']) {
            $track_data['target'] = 'direct';
        } elseif (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) !== false) {
            $track_data['target'] = 'local';
        } else {
            $track_data['target'] = 'in';
        }

        $track_data['user_id'] = '';
        if (is_logged_in()) {
            $track_data['user_id'] = $_SESSION['user_info']['user_id'];
        }

        // $_COOKIE['vz'] is for tracking users which are not logged in.
        if (!empty($_COOKIE['vz'])) {
            $track_data['visitor_id'] = $_COOKIE['vz'];
            $track_data['f_first']    = 0;
        } else {
            $track_data['visitor_id'] = md5(uniqid(rand(), true));
            setcookie('vz', $track_data['visitor_id'], 2114398800, '/');
            $track_data['f_first']    = 1;
        }

        // $_COOKIE['vz_v'] is a vizitor_visit id. It renews if visitor has a gap of $VISIT_TIMEOUT_SEC from previous request.
        // In this case a new vizitor_visit starts. @see https://support.google.com/analytics/answer/2731565?topic=2524483&ctx=topic&hl=en
        if (!empty($_COOKIE['vz_v'])) {
            $track_data['visit_id'] = $_COOKIE['vz_v'];
            $track_data['f_visit']  = 0;
        } else {
            $track_data['visit_id'] = md5(uniqid(rand(), true));
            $track_data['f_visit']  = 1;
        }
        setcookie('vz_v', $track_data['visit_id'], time() + $VISIT_TIMEOUT_SEC, '/');

        $track_data['f_from_search'] = 0;

        $query = sprintf("INSERT INTO track (url, user_id, visitor_id, session_id, visit_id, referrer,
                                             f_bot, agent, details, target, query,
                                             f_from_search, ip, script_name, f_first, f_visit,
                                             year, month, day, hour,
                                             minute)
                                      VALUES ('%s', %s, '%s', %s, '%s', %s,
                                              %d, %s, %s, '%s', %s,
                                              %d, '%s', '%s', %d, %d,
                                              %d, %d, %d, %d,
                                              %d)",
                                    $db->escape_text_local($track_data['url']),
                                    $track_data['user_id'] ? $track_data['user_id'] : 'NULL',
                                    $db->escape_text_local($track_data['visitor_id']),
                                    $track_data['session_id'] ? "'".$db->escape_text_local($track_data['session_id'])."'" : 'NULL',
                                    $db->escape_text_local($track_data['visit_id']),
                                    $track_data['referrer'] ? "'".$db->escape_text_local($track_data['referrer'])."'" : 'NULL',

                                    $track_data['f_bot'],
                                    $track_data['agent'] ? "'".$db->escape_text_local($track_data['agent'])."'" : 'NULL',
                                    $track_data['details'] ? "'".$db->escape_text_local($track_data['details'])."'" : 'NULL',
                                    $track_data['target'],
                                    $track_data['query'] ? "'".$db->escape_text_local($track_data['query'])."'" : 'NULL',

                                    $track_data['f_from_search'],
                                    $track_data['ip'],
                                    $track_data['script_name'],
                                    $track_data['f_first'],
                                    $track_data['f_visit'],

                                    date("Y"),
                                    date("m"),
                                    date("j"),
                                    date("H"),

                                    date("i") );

        $db->query($query);
    }

}

Log::init();
?>