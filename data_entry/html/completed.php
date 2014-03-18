<?php
$smarty = null;
require_once ("../utils/init.php");

Utils::redirectIfNotLoggedInAsAdmin();

if (Utils::reqParam('cmd')=='del_dupl') {
    Log::$logger->debug("cmd==del_dupl");
    DBUtils::deleteUrlDuplicates();
    DBUtils::markOtherAsCompleted();
    Log::$logger->trace("Batch import completed, batch_id = ".Utils::sVar('batch_id'));
}

$vendors_data = array();
$vendors_data['vendors'] = DBUtils::getVendorsByStep('completed');

$smarty->assign( 'vendors_count', count($vendors_data['vendors']));
$smarty->assign( 'vendors', $vendors_data['vendors']);
$smarty->assign( 'curr_page_title', "Duplicates by url page");

$smarty->display('completed.tpl');

Log::$logger->trace("Completed page done");
exit();
?>