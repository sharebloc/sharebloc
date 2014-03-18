<?php
define('HELPER_SCRIPT', true);

require_once('../includes/global.inc.php');
require_once('class.FrontStream.php');
require_once('class.Feed.php');
require_once('class.Mailer.php');
require_once('class.Notification.php');

set_time_limit(9000);

if (!Utils::isConsoleCall() && !is_admin()) {
    redirect(Utils::INDEX_PAGE);
}

if (!Utils::isConsoleCall()) {
    ob_implicit_flush(true);
    ob_end_flush();
    $admin_id = get_user_id();
    if (Utils::reqParam('publish_posts')) {
        Feed::$publish_posts = true;
    }
} else {
    $cli_parameters = Notification::getCLIArgs();
    if (!empty($cli_parameters['publish_posts'])) {
        Feed::$publish_posts = true;
    }
}

if (Utils::reqParam('crawl_test')) {
    Feed::testRSSCrawler();
    exit;
}

$msg = 'Crawling RSS script started. Use ?publish_posts=1 url parameter to publish, not test.';
Log::$logger->warn($msg); e($msg);

Feed::crawlRSSFeeds();

$admin_emails = array();
if (Utils::isConsoleCall()) {
    $admin_emails[] = 'bear@deepshiftlabs.com';
    if (!Settings::DEV_MODE && !Settings::SHOW_BETA_BORDER) {
        // this is the live server
        $admin_emails[] = 'david@sharebloc.com';
        $admin_emails[] = 'andrew@sharebloc.com';
    }
}

$report = Feed::getAutopostReport();
if (!Utils::isConsoleCall()) {
    echo($report);
}

foreach ($admin_emails as $email) {
    $mailer = new Mailer('admin_message');
    $mailer->sendMessageToAdmin($report, $email, 'auto-post script report');
}

$msg = "Crawling RSS script finished successfully.";
Log::$logger->warn($msg); e($msg);