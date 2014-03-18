<?php
// todo bear should move all confirmations here

require_once('../includes/global.inc.php');
require_once('class.Subscription.php');

if (Utils::reqParam('type')==='subscribe') {
    if (is_logged_in()) {
        $message = 'Sorry, this confirmation link can not be used by signed in users.<br><a href="mailto:support@sharebloc.com">Contact us</a>';
        Utils::showMessagePageAndExit($message);
    }

    $message = 'Sorry, this confirmation link is in error.<br><a href="mailto:support@sharebloc.com">Contact us</a>';
    if (Subscription::confirmSubscription()) {
        $message = "Your email address has been confirmed.";
    }
    Utils::showMessagePageAndExit($message);
}

redirect(Utils::getDefaultPage());