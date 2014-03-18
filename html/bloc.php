<?php

require_once('../includes/global.inc.php');
require_once('class.FrontStream.php');
require_once('class.Subscription.php');
require_once('class.Feed.php');

$tag_id = null;
$mode = "view";
$tag_posts = array();
$headline_posts = array();

if (get_input('redirect_to_get')) {
   // all data posted is already processed in included FrontStream.php
   redirect($_SERVER['REQUEST_URI']);
}

$code_name = get_input('code');
$command   = get_input('cmd');

if (!$code_name && !$command) {
    $main_blocs = Utils::getBlocsToFollow();
    $active_submenu = 'blocs';

    $smarty_params = array(
        "main_blocs" => $main_blocs,
        'active_submenu' => $active_submenu,
        'init_clipboard_copy' => true,
        'no_image_follows' => true,
    );
    $template->assign($smarty_params);
    $template->display('pages/blocs.tpl');
    exit();
}

$tag = new Tag($tag_id, $code_name);

if (!$tag->is_loaded()) {
    $template->assign('message', "This bloc does not exist.");
    $template->display('pages/message.tpl');
    exit();
}

$tag_id = $tag->get_data('tag_id');

$tag->load_follows(true);

// $seo_attributes = $tag->get_seo_attributes();

$tag_data = $tag->get();

$show_content_type = 'feed';
$active_submenu = '';
if ($command == 'connections') {
    $show_content_type = 'connections';
    $active_submenu = 'following';
}

$tag_posts = FrontStream::getContent(FrontStream::POSTS_ON_PAGE, 0, array('type'=>'tag', 'id'=>$tag_id));
FrontStream::setCommonSmartyParams();

// todo should change to ids or column in the db later.
// select * from tag where tag_name in ('Accounting and Finance', 'Creative', 'Entreprenuer', 'Human Resources', 'Sales and Marketing', 'Technology');
// see https://vendorstack.atlassian.net/secure/RapidBoard.jspa?rapidView=1&selectedIssue=VEN-281
//
// Move all major blocs into minor blocs
// https://vendorstack.atlassian.net/browse/VEN-495
//
//$main_blocs = array('Accounting', 'Finance', 'Creative', 'Entreprenuer', 'Human Resources',
//                    'Sales & Marketing', 'Technology', 'Real Estate', 'eCommerce');
$main_blocs = array();

if (in_array($tag->get_data('tag_name'), $main_blocs)) {
    $headline_posts = FrontStream::getContent(3, 0, array('type'=>'tag_top', 'id'=>$tag_id));
    if ($headline_posts) {
        $top_posts_uids = array();
        foreach ($headline_posts as $top_post) {
            $top_posts_uids[] = $top_post['uid'];
        }

        // extract top posts from feed
        foreach ($tag_posts as $key=>$post) {
            if (in_array($post['uid'], $top_posts_uids)) {
                unset($tag_posts[$key]);
    }
}
    }
}

$show_rss = false;
if (in_array($tag->get_data('tag_name'), Feed::$BLOCS_WITH_RSS)) {
    $show_rss = true;
}

$show_contest_widget = false;
if ($tag->get_data('code_name') == 'sales__marketing') {
    $show_contest_widget = true;
}

// https://vendorstack.atlassian.net/browse/VEN-424
$show_subscription_bloc = 0;
if (!is_logged_in() && !is_contest_voter() && in_array($tag_id, Subscription::$SUBSCRIPTIONS_BLOCS) && !isset($_COOKIE['sb_subs'][$tag_id])) {
    $show_subscription_bloc = 1;
}

$smarty_params = array(
    "can_edit" => false,
    "mode" => $mode,
    "tag" => $tag_data,
    'tag_posts' => $tag_posts,
    'show_content_type' => $show_content_type,
    'max_follow_icons_number' => Utils::SUMMARY_FOLLOWERS_COUNT,
    'active_submenu' => $active_submenu,
    'headline_posts' => $headline_posts,
    'init_image_upload' => true,
    'show_subscription_bloc' => $show_subscription_bloc,
    'show_join_widget' => 1,
    'show_rss' => $show_rss,
    'show_contest_widget' => $show_contest_widget,
    'twitter_symbols_left' => Utils::countTwitterSymbolsLeft(' via @ShareBloc '),
);

$template->assign($smarty_params);
$template->display('pages/bloc.tpl');
