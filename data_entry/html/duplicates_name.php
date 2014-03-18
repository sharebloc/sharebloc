<?php

$smarty = null;
require_once ("../utils/init.php");

Utils::redirectIfNotLoggedInAsAdmin();

if (Utils::reqParam('cmd') == 'cleaned') {
    Log::$logger->debug("cmd==cleaned");
    Utils::updateVendorNames();
}

$vendors_data = array();
$vendors_data['vendors'] = DBUtils::getVendorsByStep('cleaned');
if ($vendors_data['vendors']) {
    $vendors_data = Utils::searchDuplicatesByName($vendors_data);
}

$smarty->assign('duplicates_count', $vendors_data['duplicates_count']);
$smarty->assign('vendors', $vendors_data['vendors']);
$smarty->assign('total_count', count($vendors_data['vendors']));
$smarty->assign('time_per_vendor', Settings::SLEEP_TIMEOUT+3);
$smarty->assign('curr_page_title', "Duplicates by name page");
$smarty->assign('ajax_refresh', Settings::AJAX_REFRESH_PAGE_TIMEOUT);
$smarty->assign('url_page', false);
$smarty->assign('google_banned', array());

$smarty->display('duplicates.tpl');

Log::$logger->trace("Duplicates by name page done");
exit();
?>

