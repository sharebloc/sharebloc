<?php

require_once('../includes/global.inc.php');
require_once('class.InviteCustom.php');

if (!is_admin()) {
    redirect(Utils::getDefaultPage());
}

$invites = InviteCustom::getInvitesStats();

$template->assign('custom_invites', $invites);
$template->assign('http_host', $_SERVER['HTTP_HOST']);

$template->display('pages/invites_custom.tpl');
?>
