<?php

error_reporting(E_ALL);
ini_set('display_errors', '0');
date_default_timezone_set('America/Los_Angeles');
set_time_limit(90);

if (!defined('HELPER_SCRIPT')) {
    define('HELPER_SCRIPT', false);
}

define('DOCUMENT_ROOT', realpath(dirname(dirname(__FILE__))) );
set_include_path(get_include_path(). PATH_SEPARATOR . DOCUMENT_ROOT . "/classes");

/* requires */
require_once('class.Settings.php');
require_once('class.Log.php');
require_once('class.Utils.php');
require_once('class.Database.php');
require_once('class.Cache.php');
require_once('class.Vendor.php');
require_once('class.User.php');
require_once('class.Notification.php');
require_once('class.SiteCategory.php');
require_once('libs/Smarty.class.php');

register_shutdown_function('shutdown_procedure');

$cache = new Cache();
$db = new Database();

if (Settings::DEV_MODE) {
    set_time_limit(1000);
}

if (Utils::reqParam('uploadifysess')) {
    session_id(Utils::reqParam('uploadifysess'));
}

session_start();

Utils::initTags();
if (!HELPER_SCRIPT) {
    if (!is_logged_in() && !is_contest_voter()) {
        Utils::processRefIfNeeded();
        User::logInByCookie();
    }

    Utils::setPermissions();

    Utils::setCurrentPage();

    Log::logVisit();

    if (!Utils::isAjaxRequest()) {
        Notification::populateNotifications();
        Utils::check_if_unsupported_ie();
    }

    $_SESSION['body_font'] = Utils::reqParam('font', Utils::sVar('body_font'));

    $template = Utils::initSmarty();

    Utils::showMaintenance();

    $_SESSION['no_feed'] = Utils::reqParam('no_feed', Utils::sVar('no_feed'));
} else {
    $template = Utils::initSmarty();
}