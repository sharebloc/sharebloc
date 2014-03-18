<?php

$smarty = null;
require_once ("../utils/init.php");

Utils::redirectIfNotLoggedInAsAdmin();

if (Utils::reqParam('start_over')) {
    DBUtils::deleteLoadedVendors();
}

$err_msg = array();
$empty_file_msg = "";

if (isset($_FILES['userfile'])) {

    if (!$_FILES['userfile']['error']) {

        $file_errors = Utils::getFileContentAndParseIt($_FILES['userfile']['tmp_name']);

        $err_msg = $file_errors['err_msg'];
        $empty_file_msg = $file_errors['empty_file_msg'];
    } else {
        Log::$logger->warn("File uploading error - maybe file not selected");
    }
}

$smarty->assign('empty_file_msg', $empty_file_msg);
$smarty->assign('err_msg', $err_msg);
$smarty->assign('curr_page_title', "Import file page");

$smarty->display('import_file.tpl');

Log::$logger->trace("Import file page done");
exit();
?>
