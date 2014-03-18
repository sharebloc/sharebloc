<?php

$smarty = null;
require_once ("../utils/init.php");

Utils::redirectIfNotLoggedInAsAdmin();

$err_upload = "";

$vendors_data = array();
$vendors_data['cleaned_count'] = 0;
$vendors_data['vendors'] = DBUtils::getVendorsByStep('new');
if ($vendors_data['vendors']) {
    $vendors_data = Utils::cleanVendorsNames($vendors_data);
}

$smarty->assign('err_msg', Utils::sVar("file_import_err_msg"));
$smarty->assign('cleaned_count', $vendors_data['cleaned_count']);
$smarty->assign('vendors', $vendors_data['vendors']);
$smarty->assign('err_upload', $err_upload);
$smarty->assign('curr_page_title', "Clean page");

$smarty->display('clean.tpl');

Log::$logger->trace("Clean page done");
exit();
?>
