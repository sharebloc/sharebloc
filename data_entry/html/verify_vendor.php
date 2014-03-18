<?php

$smarty = null;
require_once ("../utils/init.php");

Utils::redirectIfNotLoggedInAsAdmin();

$err_msg = '';

$verify_data = Utils::reqParam('verify_data');
$vend_id = Utils::reqParam('id');

if ($verify_data) {
    $err_msg = DBUtils::updateVendorWithVerifiedData($vend_id);
    header("Location: /ready_vendors.php");
    exit();

}

if($vend_id) {
    $vendor = DBUtils::getVendorById($vend_id);
    $vendor['descr_html'] = str_replace("<br>", "\n", $vendor['description']);
    $vendor = Utils::getLinkErrorsForVendor($vendor);
} else {
    $err_msg = "Error - there is no vendor info to update";
}
//e($vendor);
$smarty->assign('networks', Utils::getPreparedNetworks());
$smarty->assign('vendor', $vendor);
//$smarty->assign('logo_filename', $logo_filename);
$smarty->assign('err_msg', $err_msg);

$smarty->display('verify_vendor.tpl');

Log::$logger->trace("Vendor verifiing page done");
exit();

?>
