<?php

$smarty = null;
require_once ("../utils/init.php");
require_once ("../utils/Parser.class.php");

Utils::redirectIfNotLoggedIn();
$err_msg = '';
$can_save = true;
$back_step = 0;

$vendor = Utils::getWorkVendor();
if ($vendor) {
    if(Utils::reqParam('save_data')) {
        $can_save = false;
        if ($vendor['status'] != 'ready') {
            $vendor = Utils::saveData($vendor);
            Utils::sVar('work_vendor', $vendor);
        }
    }
    $back_step = count(Utils::getSaveStepsForEntityByType($vendor['type']))-2;
    Utils::sVar('use_new_work_vendor', true);
} else {
    $err_msg = "You have no vendors to process";
}

$prepared_networks = Utils::getPreparedNetworks();

$popup_params = Utils::getPopupParams();
$smarty->assign('popup_params', $popup_params);

$smarty->assign('vendor', $vendor);
$smarty->assign('can_save', $can_save);
$smarty->assign('err_msg', $err_msg);
$smarty->assign('networks', $prepared_networks);
$smarty->assign('curr_page_title', "Show data page");
$smarty->assign('back_step', $back_step);
$smarty->display('show_data.tpl');

Log::$logger->trace("Show data page done");
exit();
?>

