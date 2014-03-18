<?php

/**
 * Script to server ajax requests
 */
require_once ("../config/common.php");
require_once ("../utils/Gate.class.php");
register_shutdown_function('Gate::graceful_shutdown');
$script_times = array();
$script_times['start'] = microtime(true);
Log::init();
Utils::init();
Gate::init();
Gate::doAction();
exit;
?>