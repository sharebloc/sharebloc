<?php

require_once('../includes/global.inc.php');
require_once('class.ExtAuth.php');

if (!is_logged_in()) {
    redirect(Utils::getLoginUrl());
}

$invites_count = 5;
$active_join_step = 'invite';

$contacts = User::getOauthContactsByUserId();

$emails_to_invite_count = $invites_count - count($contacts);
if ($emails_to_invite_count < 0) {
    $emails_to_invite_count = 0;
}

// e($contacts);
$template->assign('contacts', $contacts);
$template->assign('emails_to_invite_count', $emails_to_invite_count);
$template->assign('active_join_step', $active_join_step);
$template->assign('skip_redirect', Utils::getPageToReturnAfterLogIn());
$template->display('pages/join_invites.tpl');

?>
