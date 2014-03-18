<?php

$smarty = null;
require_once ("../utils/init.php");
require_once ("../utils/Parser.class.php");

Utils::redirectIfNotLoggedIn();

Utils::crawlProfiles();

header("Location: /select_data.php");
Log::$logger->trace("Parse data page done");
exit();
?>
