<?php

require_once('../includes/global.inc.php');

if (!empty($_SESSION['download_resource_after_login'])) {
    $template->assign('download_now', $_SESSION['download_resource_after_login']);
    unset($_SESSION['download_resource_after_login']);
}

$template->display('pages/resources.tpl');
?>