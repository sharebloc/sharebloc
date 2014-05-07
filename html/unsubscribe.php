<?php

require_once('../includes/global.inc.php');
require_once('class.Subscription.php');

$type = get_input('type');
$code = get_input('code');

if (in_array($type, array('weekly', 'updates', 'contest', 'daily', 'suggestion','deactivate'))) {
    $message = User::unsubscribe($type, $code);
} elseif ($type=='subscription') {
    $message = Subscription::unsubscribe($code);
} else {
    $message = sprintf('Sorry, there doesn\'t seem to be anything here. <a href="%s">Go back to the homepage</a>.',
        Utils::getDefaultPage());
}

if ($message===false) {
    $message = 'Sorry, this unsubscribe link is in error.<br>Please <a href="mailto:support@sharebloc.com">contact us</a> if you are trying to unsubscribe and cannot.';
}

Utils::showMessagePageAndExit($message);