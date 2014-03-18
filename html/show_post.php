<?php

require_once('../includes/global.inc.php');
require_once('class.FrontStream.php');
require_once('class.VoteContest.php');
require_once('class.AlsoViewed.php');

$post_type = get_input('type');
$post_id = intval(get_input('id'));
$code_name = get_input('code_name');

$f_iframe = false;
if ($post_type == 'link') {
    $post_type = 'posted_link';
    $f_iframe = true;
}

if (!in_array($post_type, FrontStream::$allowed_post_types) || (!$post_id && !$code_name) ) {
    redirect(Utils::getDefaultPage());
}

$post_data = FrontStream::prepareOnePost($post_id, $post_type, $code_name);
// e($post_data);

if (!$post_data) {
    display_404();
}

if (isset($post_data['user']['status']) && $post_data['user']['status']=='inactive'
        && $post_data['user']['user_id'] !=  get_user_id() && !is_admin()) {
    display_404();
}

if (!$post_id) {
    $post_id = $post_data['post_id'];
}

$use_contest_vote = 0;
$show_share_links = true;
if ($post_type=='posted_link' && $post_data['f_contest']) {
    $use_contest_vote = 1;
    $contest_id = $post_data['f_contest'];

    if ($contest_id == Utils::CONTEST_MARKETO_ID && !is_logged_in()) {
        redirect(Utils::getLoginUrl());
    }

    $contest_url = Utils::$contest_urls[$contest_id];
    $twitter_symbols_left = Utils::countTwitterSymbolsLeft();
    if (!VoteContest::isPostInTop50onLive($post_id)) {
        // we show share links only for top 50 contest posts
        $show_share_links = false;
    }
} else {
    $twitter_symbols_left = Utils::countTwitterSymbolsLeft(" via @ShareBloc ");
}

$tweet_after_post = Utils::unsetSVar('tweet_after_post') ? 1 : 0;

$votes_data = array();
$users_allowed_to_see_votes = array(2, 458, 951, 966);
if ($use_contest_vote && is_admin() && in_array(get_user_id(), $users_allowed_to_see_votes)) {
    $votes_data = VoteContest::getPostVotesDetails($post_id);
}

$also_viewed = new AlsoViewed($post_type, $post_id);
$also_viewed->record_visit();

$smarty_params = array(
    'post_data' => $post_data,
    'popup_tweet_link_url' => $post_data['my_url'],
    'seo' => $post_data['seo'],
    'twitter_symbols_left' => $twitter_symbols_left,
    'order' => FrontStream::$order,
    'init_image_upload' => true,
    'init_clipboard_copy' => true,
    'show_join_welcome_popup' => Utils::unsetSVar('show_join_welcome_popup'),
    'use_contest_vote' => $use_contest_vote,
    'contest_votes_left' => VoteContest::getVotesLeft(),
    'tweet_after_post' => $tweet_after_post,
    'votes_data' => $votes_data,
    'show_share_links' => $show_share_links,
    'show_join_widget' => 1,
    'reposted_popup_type' => Utils::unsetSVar('reposted_popup_type'),
    'f_iframe' => $f_iframe,
    'contest_id' => isset($contest_id) ? $contest_id : 0,
    'contest_url' => isset($contest_url) ? $contest_url : '',
);

//e($post_data);

Notification::clearNotificationsIfNeeded($post_type, $post_id);

$template->assign($smarty_params);

if ($f_iframe) {
    $template->display('pages/show_post_iframe.tpl');
} else {
    $template->display('pages/show_post.tpl');
}

?>
