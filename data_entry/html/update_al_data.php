<?php

$smarty = null;
require_once ("../utils/init.php");
require_once ("../utils/ALParser.class.php");

set_time_limit(600);

Utils::redirectIfNotLoggedIn();

$vendors = ALParser::parseNewVendors();

$smarty->display('update_al_data.tpl');

Log::$logger->trace("update_al_data done");
exit();
?>

