<?php

require_once('../includes/global.inc.php');

Utils::storeParametersToUseAfterLogin();

if (Utils::reqParam('type')==='confirm_email') {
    $user = User::confirmEmail();

    if ($user) {
        $message = "Your email address has been confirmed.";
        $template->assign('message', $message);
        $template->display('pages/message.tpl');
        exit();
    } else {
        $message = 'Sorry, this confirmation link is in error.<br><a href="mailto:support@sharebloc.com">Contact us</a>';
        $template->assign('message', $message);
        $template->display('pages/message.tpl');
        exit();
    }
}

if (is_logged_in()) {
    redirect(Utils::getDefaultPage());
}

// only front invites have email, custom do not have
if (isset($_SESSION['invite_front_data']['email'])) {
    $template->assign("email", $_SESSION['invite_front_data']['email']);
}

$template->display('pages/signin.tpl');