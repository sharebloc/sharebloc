<?php

/**
 * Index script
 */
$smarty = null;
require_once ("../utils/init.php");

$logged_in = Utils::isLoggedIn();
$is_admin = Utils::isAdmin();
$login_error = "";

if (Utils::sVar("use_new_work_vendor")) {
    Utils::sVar("use_new_work_vendor", false);
    Utils::sVar('work_vendor', array());
}

if ($logged_in) {
    if (Utils::reqParam('logout')) {
        Utils::logOut();
        $logged_in = false;
        $is_admin = false;
    }
}

$cmd = Utils::reqParam('cmd');
if ($cmd == 'log_in') {
    $login_error = Utils::logIn();
}

$is_admin = Utils::isAdmin();
if ($is_admin) {
    header("Location: /import_file.php");
    exit();
}

$smarty->assign('login_error', $login_error);
$smarty->assign('is_admin', Utils::isAdmin());
$smarty->assign('logged_in', Utils::isLoggedIn());
$smarty->assign('curr_page_title', "Index page");

$smarty->display('index.tpl');

Log::$logger->trace("Index page done");
exit();
?>
