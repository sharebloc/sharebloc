<?php

require_once('../includes/global.inc.php');
require_once('class.InviteCustom.php');


$confirm_key = get_input('code');
if (!$confirm_key) {
    $template->assign('message', "Your Invite Key is Invalid.");
    $template->display('pages/message.tpl');
    exit();
}

$invite = InviteCustom::getInviteByKey($confirm_key);

if (!$invite) {
    $user = new User(null, $confirm_key);
    if ($user->is_loaded()) {
        $invite['invited_by'] = $user->get_data('user_id');
        $invite['confirm_key'] = $confirm_key;
    } else {
        $template->assign('message', "Your Invite Key is Invalid.");
        $template->display('pages/message.tpl');
        exit();
    }
}

if (is_logged_in()) {
    InviteCustom::processInviteKey($confirm_key);
    redirect(Utils::INDEX_PAGE);
} else {
    $_SESSION['invite_front_data'] = $invite;
    redirect(Utils::getJoinUrl());
}
?>
