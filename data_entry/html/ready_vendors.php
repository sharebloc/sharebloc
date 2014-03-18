<?php

$smarty = null;
require_once ("../utils/init.php");

Utils::redirectIfNotLoggedInAsAdmin();
$err_msg = '';

$ids_to_delete = Utils::reqParam('ids_to_delete');
if ($ids_to_delete) {
    DBUtils::deleteVendors($ids_to_delete);
}

$vendors = DBUtils::getReadyForExportEntities();
if ($vendors) {
    $vendors = Utils::getAllErrorMsgsForHint($vendors);
} else {
    $err_msg = "No entities to export";
}
//e($vendors);
$smarty->assign('vendors', $vendors);
$smarty->assign('err_msg', $err_msg);
$smarty->assign('vs_host', Settings::VS_HOST);
$smarty->display('ready_vendors.tpl');

Log::$logger->trace("Results page done");
exit();
?>
