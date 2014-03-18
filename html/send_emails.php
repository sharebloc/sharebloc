<?php
define('HELPER_SCRIPT', true);

require_once('../includes/global.inc.php');
require_once('../classes/class.Notification.php');
require_once('../classes/class.FrontStream.php');
require_once('../classes/class.Subscription.php');
require_once('class.Mailer.php');

set_time_limit(9000);

if (!Utils::isConsoleCall() && !is_admin()) {
    redirect(Utils::INDEX_PAGE);
}

if (!Utils::isConsoleCall()) {
    ob_implicit_flush(true);
    ob_end_flush();
    $admin_id = get_user_id();
}

$admin_email = '';
if (Utils::isConsoleCall()) {
    $admin_email = 'bear@deepshiftlabs.com';
    if (!Settings::DEV_MODE && !Settings::SHOW_BETA_BORDER) {
        // this is the live server
        $admin_email = 'david@sharebloc.com';
    }
}

$show_help = Utils::reqParam('test');
Utils::$smarty->assign("show_help", $show_help);
Utils::$smarty->assign("current_user_id", get_user_id());
Utils::$smarty->assign("subscription_blocs", Subscription::$SUBSCRIPTIONS_BLOCS);

if ($show_help) {
    echo(Utils::$smarty->fetch('pages/send_emails.tpl'));
    echo(Utils::$smarty->fetch('components/footer_new.tpl'));
    exit;
}

$msg = 'Mailing script started.';
Log::$logger->info($msg);
e($msg);

$mailer = new Mailer('admin_message');
$mailer->sendMessageToAdmin($msg, $admin_email, 'periodic emails script started');

$result_msg = Notification::processPeriodicEmails();

if (!Utils::isConsoleCall()) {
    $user = new User($admin_id);
    user_login($user);
}

$msg = "Script finished successfully.";
Log::$logger->warn($msg);
e($msg);

$mailer->sendMessageToAdmin($msg . "<br>" . $result_msg, $admin_email, 'periodic emails script finished');