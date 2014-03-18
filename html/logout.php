<?php

require_once('../includes/global.inc.php');

Utils::logout();

redirect(Utils::getLastPage());

?>