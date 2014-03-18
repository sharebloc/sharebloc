<?php

/**
 * Class to provide common access to a single logger object
 */
class Log {

    public static $logger = null;

    public static function error_handler($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return true;
        }
        error_log($errstr);
        return true;
    }

    public static function getNewLogger($path_to_config, $loggerName = "data_entry_logger") {
        // used only to intercept errors triggered by log4php LoggerAppenderPhp
        set_error_handler('Log::error_handler', E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);

        Logger::configure(DATA_ENTRY_ROOT_PATH . $path_to_config, 'LoggerConfiguratorXml');
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

    public static function init($logger = null) {
        if (!self::$logger) {
            if (!$logger) {
                $logger_file = "/config/log4php.xml";
                if (defined("Settings::LOG4PHP_CONF_FILE") && Settings::LOG4PHP_CONF_FILE) {
                    $logger_file = Settings::LOG4PHP_CONF_FILE;
                }
                $logger = self::getNewLogger($logger_file);
            }
            self::$logger = $logger;
            self::$logger->trace("Logger initialized.");
        }
        return self::$logger;
    }

}

?>