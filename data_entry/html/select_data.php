<?php

$smarty = null;
require_once ("../utils/init.php");

Utils::redirectIfNotLoggedIn();

$err_msg = '';
$err_parse_msg = array();

$current_step = Utils::reqParam('current_step', -1);
$next_step = Utils::reqParam('next_step', 0);

$vendor = Utils::getWorkVendor();
if ($vendor) {
    $save_data_steps = Utils::getSaveStepsForEntityByType($vendor['type']);
    $next_step_id = $save_data_steps[$next_step]['id'];
    $next_step_display_name = $save_data_steps[$next_step]['display_name'];
    $vendor = Utils::saveDataSource($vendor, $current_step);
    $err_parse_msg = $vendor['err_msg'];
    if ($err_parse_msg) {
        $vendor['err_msg'] = array();
    }
    Utils::sVar('work_vendor', $vendor);

    $selected_source_id = '';
    if (!empty($vendor[$next_step_id.'_source'])) {
        $selected_source_id = $vendor[$next_step_id.'_source'];
    }
} else {
    $err_msg = "You have no vendors to process";
}

$last_step = count($save_data_steps)-1;
if ($next_step == $last_step) {
    header("Location: /show_data.php");
    exit();
}

$prepared_networks = array();
foreach(Utils::$networks as $key=>$network) {
    if (!in_array($key, Utils::$NETWORK_IDS)) {
        continue;
    }
    $prepared_networks[$network['id']] = $network;
}

$popup_params = Utils::getPopupParams();
$smarty->assign('popup_params', $popup_params);

$smarty->assign('err_msg', $err_msg);
$smarty->assign('step', $next_step);
$smarty->assign('step_id', $next_step_id);
$smarty->assign('step_display_name', $next_step_display_name);
$smarty->assign('vendor', $vendor);
$smarty->assign('selected_source_id', $selected_source_id);
$smarty->assign('err_parse_msg', $err_parse_msg);
$smarty->assign('networks', $prepared_networks);
$smarty->assign('save_data_steps', $save_data_steps);
$smarty->assign('curr_page_title', "Select ".$next_step_display_name);

$smarty->display('select_data.tpl');

Log::$logger->trace("Select data page done");
exit();
?>
