<?php

$smarty = null;
require_once ("../utils/init.php");

Utils::redirectIfNotLoggedIn();

$err_msg = '';

if (Utils::reqParam('new_vendor')) {

    $vendor = Utils::getNewVendor();
    if ($vendor) {
        $vendor = Utils::prepareNewVendorStructure($vendor);
    }
    $current_step = -1;
    $next_step = 0;
} else {
    $current_step = Utils::reqParam('current_step');
    $next_step = Utils::reqParam('next_step');
    $vendor = Utils::getWorkVendor();
    $vendor = Utils::storeLinkIfNeeded($vendor, $current_step);
}
if ($vendor) {
    Utils::sVar('work_vendor', $vendor);
} else {
    $err_msg = "You have no vendors to process";
}

$last_step = count(Utils::$networks)-1;
if ($next_step == $last_step) {
    header("Location: /parse_data.php");
    exit();
}
$next_step_id = Utils::$networks[$next_step]['id'];
$popup_params = Utils::getPopupParams();
$smarty->assign('popup_params', $popup_params);

$smarty->assign('vendor', $vendor);
$smarty->assign('step', $next_step);
$smarty->assign('step_id', $next_step_id);
$smarty->assign('err_msg', $err_msg);
$smarty->assign('network', Utils::$networks[$next_step]);
$smarty->assign('networks', Utils::$networks);
$smarty->assign('show_long_operation_msg', $next_step == 5 ? true : false);
$smarty->assign('curr_page_title', "Choosing $next_step_id profile page");

$smarty->display('search_profile.tpl');

Log::$logger->trace("Choosing $next_step profile page");
exit();
?>
