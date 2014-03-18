<?php

$smarty = null;
require_once ("../utils/init.php");

Utils::redirectIfNotLoggedInAsAdmin();
/*
if (Utils::reqParam('cmd') == 'del_dupl') {
    Log::$logger->debug("cmd==del_dupl");
    DBUtils::deleteNameDuplicates();
    Utils::crawlVendors();
}
*/
$vendors_data = array();
$vendors_data['vendors'] = DBUtils::getVendorsByStep('cleaned');
if ($vendors_data['vendors']) {
    $vendors_data = Utils::searchDuplicatesByUrl($vendors_data);
}

$smarty->assign('duplicates_count', $vendors_data['duplicates_count']);
$smarty->assign('vendors', $vendors_data['vendors']);
$smarty->assign('google_banned', $vendors_data['google_banned']);
$smarty->assign('curr_page_title', "Duplicates by url page");
$smarty->assign('ajax_refresh', Settings::AJAX_REFRESH_PAGE_TIMEOUT);
$smarty->assign('url_page', true);
$smarty->assign('total_count', count($vendors_data['vendors']));
$smarty->assign('time_per_vendor', 0);

$smarty->display('duplicates.tpl');

Log::$logger->trace("Duplicates by url page done");
exit();
?>