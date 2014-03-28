<?php

// JT: Basic routing, replaces .htaccess. This is a dirty hack.

date_default_timezone_set('UTC');

function redirect301($uri) {
    header('HTTP/1.1 301 Moved Permanently');
    header($uri);
}

// this is a simple utility function for debugging
function pd($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    die;
}

// canonicalize on www.sharebloc.com
if ($_SERVER['HTTP_HOST'] == 'sharebloc.com') {
    redirect301('Location: http://www.sharebloc.com'.$_SERVER['REQUEST_URI']);
}

if (preg_match('%^/contest([/].+)?$%', $_SERVER['REQUEST_URI'], $matches)) {
    redirect301('/contest'.$matches[1]);
}

$path = explode('?', $_SERVER['REQUEST_URI']);
define('REQUEST_PATH', $path[0]);

/* routing starts here */

if (preg_match('%^/$%', REQUEST_PATH, $matches)) {
    require('homepage.php');
    exit;
}

if (preg_match('%^/team/?$%', REQUEST_PATH, $matches)) {
    require('team.php');
    exit;
}

if (preg_match('%^/terms/?$%', REQUEST_PATH, $matches)) {
    require('terms.php');
    exit;
}

if (preg_match('%^/resources/?$%', REQUEST_PATH, $matches)) {
    require('resources.php');
    exit;
}

if (preg_match('%^/every_sale_is_a_space_race/?$%', REQUEST_PATH, $matches)) {
    require('splash_space.php');
    exit;
}

if (preg_match('%^/lead_farming_three_steps_to_grow_leads/?$%', REQUEST_PATH, $matches)) {
    require('lead_farming_three_steps_to_grow_leads.php');
    exit;
}

if (preg_match('%^/back_office_human_resources/?$%', REQUEST_PATH, $matches)) {
    require('back_office_human_resources.php');
    exit;
}

if (preg_match('%^/seven_questions_by_mobile_developers/?$%', REQUEST_PATH, $matches)) {
    require('seven_questions_by_mobile_developers.php');
    exit;
}

if (preg_match('%^/privacy/?$%', REQUEST_PATH, $matches)) {
    require('privacy.php');
    exit;
}

if (preg_match('%^/join/?$%', REQUEST_PATH, $matches)) {
    require('join.php');
    exit;
}

if (preg_match('%^/signin/?$%', REQUEST_PATH, $matches)) {
    require('signin.php');
    exit;
}

if (preg_match('%^/screenshots/(.+)?$%', REQUEST_PATH, $matches)) {
    $_GET['code'] = $matches[1];
    require('display_screenshot.php');
    exit;
}

if (preg_match('%^/files/(.+)?$%', REQUEST_PATH, $matches)) {
    $_GET['file'] = $matches[1];
    require('file_download.php');
    exit;
}

if (preg_match('%^/companies/([^/]+)[/]?([^/]+)?[/]?$%', REQUEST_PATH, $matches)) {
    $_GET['code'] = $matches[1];
    $_GET['cmd'] = $matches[2];
    require('vendor.php');
    exit;
}

if (preg_match('%^/users/([^/]+)[/]?([^/]+)?[/]?$%', REQUEST_PATH, $matches)) {
    $_GET['code'] = $matches[1];
    $_GET['cmd'] = $matches[2];
    require('user.php');
    exit;
}

if (preg_match('%^/pw_rst/(.+)?$%', REQUEST_PATH, $matches)) {
    $_GET['reset_key'] = $matches[1];
    require('new_password.php');
    exit;
}

if (preg_match('%^/post/(.+)?$%', REQUEST_PATH, $matches)) {
    $_GET['type'] = $matches[1];
    require('post.php');
    exit;
}

if (preg_match('%^/invite/(.+)?$%', REQUEST_PATH, $matches)) {
    $_GET['code'] = $matches[1];
    require('process_invite.php');
    exit;
}

if (preg_match('%^/share/(posted_link|question)s/([^/]+)?[/]?$%', REQUEST_PATH, $matches)) {
    $_GET['type'] = $matches[1];
    $_GET['code_name'] = $matches[2];
    $_GET['shared_post'] = 1;
    require('show_post.php');
    exit;
}

if (preg_match('%^/(posted_link|question|link)s/([^/]+)[/]?$%', REQUEST_PATH, $matches)) {
    $_GET['type'] = $matches[1];
    $_GET['code_name'] = $matches[2];
    require('show_post.php');
    exit;
}

if (preg_match('%^/invite_experts(.+)?$%', REQUEST_PATH, $matches)) {
    $_GET['p'] = 1;
    require('homepage.php');
    exit;
}

if (preg_match('%^/join/(.+)$%', REQUEST_PATH, $matches)) {
    $_GET['code'] = 'sbbeta_'.$matches[1];
    require('process_invite.php');
    exit;
}

if (preg_match('%^/blocs/$%', REQUEST_PATH, $matches)) {
    require('bloc.php');
    exit;
}

if (preg_match('%^/blocs/([^/]+)[/]?([^/]+)?[/]?$%', REQUEST_PATH, $matches)) {
    $_GET['code'] = $matches[1];
    $_GET['cmd'] = $matches[2];
    require('bloc.php');
    exit;
}

if (preg_match('%^/unsubscribe/([^/]+)[/]([^/]+)$%', REQUEST_PATH, $matches)) {
    $_GET['type'] = $matches[1];
    $_GET['code'] = $matches[2];
    require('unsubscribe.php');
    exit;
}

if (preg_match('%^/recent/$%', REQUEST_PATH, $matches)) {
    require('recent_connections.php');
    exit;
}

if (preg_match('%^/guidelines/$%', REQUEST_PATH, $matches)) {
    require('guidelines.php');
    exit;
}

if (preg_match('%^/sitemap.xml$%', REQUEST_PATH, $matches)) {
    require('sitemap.php');
    exit;
}

if (preg_match('%^/top_content_marketing_posts_of_2013$%', REQUEST_PATH, $matches)) {
    $_GET['contest_id'] = 1;
    require('contest.php');
    exit;
}

if (preg_match('%^/top_content_marketing_posts_of_2013/(.+)$%', REQUEST_PATH, $matches)) {
    $_GET['contest_id'] = 1;
    $_GET['code'] = $matches[1];
    require('contest_all.php');
    exit;
}

if (preg_match('%^/content_marketing_nation$%', REQUEST_PATH, $matches)) {
    $_GET['contest_id'] = 2;
    require('contest.php');
    exit;
}

if (preg_match('%^/sharebloc_content_marketing_nation_contest_rules/?$%', REQUEST_PATH, $matches)) {
    require('contest_rules.php');
    exit;
}

if (preg_match('%^/confirm/votes/([^/]+)$%', REQUEST_PATH, $matches)) {
    $_GET['type'] = 'confirm_votes';
    $_GET['code'] = $matches[1];
    require('contest.php');
    exit;
}

if (preg_match('%^/confirm/email/([^/]+)$%', REQUEST_PATH, $matches)) {
    $_GET['type'] = 'confirm_email';
    $_GET['code'] = $matches[1];
    require('signin.php');
    exit;
}

if (preg_match('%^/confirm/subscribe/([^/]+)$%', REQUEST_PATH, $matches)) {
    $_GET['type'] = 'subscribe';
    $_GET['code'] = $matches[1];
    require('confirm.php');
    exit;
}

if (preg_match('%^/img/([^/]+)(.png)$%', REQUEST_PATH, $matches)) {
    $_GET['code'] = $matches[1];
    require('track_emails.php');
    exit;
}

if (preg_match('%^/rss/(blocs|companies|users)/(.+)$%', REQUEST_PATH, $matches)) {
    $_GET['type'] = $matches[1];
    $_GET['code'] = $matches[2];
    require('rss.php');
    exit;
}

if (preg_match('%^/calendar/(.+)$%', REQUEST_PATH, $matches)) {
    $_GET['tag'] = $matches[1];
    require('calendar.php');
    exit;
}

// if we got here, it means that URL couldn't be routed. 404 time.
require('404.php');
