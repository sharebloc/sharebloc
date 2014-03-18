<?php

/**
 * Script prepares all common things
 */
$script_times = array();
$script_times['start'] = microtime(true);

require_once ("../config/common.php");

Log::init();
Utils::init();

$smarty = Utils::initSmarty();
$base_url = Utils::getBaseUrl();

$smarty_params = array(
    'is_admin' => Utils::isAdmin(),
    'logged_in' => Utils::isLoggedIn(),
    'base_url' => $base_url,
    'shouldUseCssRefresh' => Utils::shouldUseCssRefresh(),
);

$smarty->assign($smarty_params);

Log::$logger->trace("Init done");
$script_times['init'] = microtime(true);
?>