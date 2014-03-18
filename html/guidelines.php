<?php
require_once('../includes/global.inc.php');

$smarty_params = array(
    'init_clipboard_copy' => true,
);

$template->assign($smarty_params);
$template->display('pages/guidelines.tpl');
?>
