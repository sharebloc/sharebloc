<?php

// JT: Basic routing, replaces .htaccess. This is a dirty hack.

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

/* routing starts here */

if (preg_match('%^/$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    require('homepage.php');
    exit;
}

if (preg_match('%^/team/?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    require('team.php');
    exit;
}

if (preg_match('%^/terms/?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    require('terms.php');
    exit;
}

if (preg_match('%^/resources/?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    require('resources.php');
    exit;
}

if (preg_match('%^/every_sale_is_a_space_race/?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    require('splash_space.php');
    exit;
}

if (preg_match('%^/lead_farming_three_steps_to_grow_leads/?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    require('lead_farming_three_steps_to_grow_leads.php');
    exit;
}

if (preg_match('%^/back_office_human_resources/?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    require('back_office_human_resources.php');
    exit;
}

if (preg_match('%^/seven_questions_by_mobile_developers/?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    require('seven_questions_by_mobile_developers.php');
    exit;
}

if (preg_match('%^/privacy/?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    require('privacy.php');
    exit;
}

if (preg_match('%^/join/?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    require('join.php');
    exit;
}

if (preg_match('%^/signin/?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    require('signin.php');
    exit;
}

if (preg_match('%^/screenshots/(.+)?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['code'] = $matches[1];
    require('display_screenshot.php');
    exit;
}

if (preg_match('%^/files/(.+)?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['file'] = $matches[1];
    require('file_download.php');
    exit;
}

if (preg_match('%^/companies/([^/]+)[/]?([^/]+)?[/]?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['code'] = $matches[1];
    $_GET['cmd'] = $matches[2];
    require('vendor.php');
    exit;
}

if (preg_match('%^/users/([^/]+)[/]?([^/]+)?[/]?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['code'] = $matches[1];
    $_GET['cmd'] = $matches[2];
    require('users.php');
    exit;
}

if (preg_match('%^/pw_rst/(.+)?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['reset_key'] = $matches[1];
    require('new_password.php');
    exit;
}

if (preg_match('%^/post/(.+)?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['type'] = $matches[1];
    require('post.php');
    exit;
}

if (preg_match('%^/invite/(.+)?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['code'] = $matches[1];
    require('process_invite.php');
    exit;
}

if (preg_match('%^/share/(posted_link|question)s/([^/]+)?[/]?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['type'] = $matches[1];
    $_GET['code_name'] = $matches[2];
    $_GET['shared_post'] = 1;
    require('show_post.php');
    exit;
}

if (preg_match('%^/(posted_link|question|link)s/([^/]+)[/]?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['type'] = $matches[1];
    $_GET['code_name'] = $matches[2];
    require('show_post.php');
    exit;
}

if (preg_match('%^/invite_experts(.+)?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['p'] = 1;
    require('homepage.php');
    exit;
}

if (preg_match('%^/join/(.+)$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['code'] = 'sbbeta_'.$matches[1];
    require('process_invite.php');
    exit;
}

if (preg_match('%^/blocs/$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    require('bloc.php');
    exit;
}

if (preg_match('%^/blocs/([^/]+)[/]?([^/]+)?[/]?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['code'] = $matches[1];
    $_GET['cmd'] = $matches[2];
    require('bloc.php');
    exit;
}

if (preg_match('%^/unsubscribe/([^/]+)[/]([^/]+)$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['type'] = $matches[1];
    $_GET['code'] = $matches[2];
    require('unsubscribe.php');
    exit;
}

if (preg_match('%^/recent/$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    require('recent_connections.php');
    exit;
}

if (preg_match('%^/guidelines/$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    require('guidelines.php');
    exit;
}

if (preg_match('%^/sitemap.xml$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    require('sitemap.php');
    exit;
}

if (preg_match('%^/top_content_marketing_posts_of_2013$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['contest_id'] = 1;
    require('contest.php');
    exit;
}

if (preg_match('%^/top_content_marketing_posts_of_2013/(.+)$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['contest_id'] = 1;
    $_GET['code'] = $matches[1];
    require('contest_all.php');
    exit;
}

if (preg_match('%^/content_marketing_nation$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['contest_id'] = 2;
    require('contest.php');
    exit;
}

if (preg_match('%^/sharebloc_content_marketing_nation_contest_rules/?$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    require('contest_rules.php');
    exit;
}

if (preg_match('%^/confirm/votes/([^/]+)$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['type'] = 'confirm_votes';
    $_GET['code'] = $matches[1];
    require('contest.php');
    exit;
}

if (preg_match('%^/confirm/email/([^/]+)$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['type'] = 'confirm_email';
    $_GET['code'] = $matches[1];
    require('signin.php');
    exit;
}

if (preg_match('%^/confirm/subscribe/([^/]+)$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['type'] = 'subscribe';
    $_GET['code'] = $matches[1];
    require('confirm.php');
    exit;
}

if (preg_match('%^/img/([^/]+)(.png)$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['code'] = $matches[1];
    require('track_emails.php');
    exit;
}

if (preg_match('%^/rss/(blocs|companies|users)/(.+)$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['type'] = $matches[1];
    $_GET['code'] = $matches[2];
    require('rss.php');
    exit;
}

if (preg_match('%^/calendar/(.+)$%', $_SERVER['DOCUMENT_URI'], $matches)) {
    $_GET['tag'] = $matches[1];
    require('calendar.php');
    exit;
}

// if we got here, it means that URL couldn't be routed. 404 time.
require('404.php');
