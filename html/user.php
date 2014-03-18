<?php

require_once('../includes/global.inc.php');
require_once('class.FrontStream.php');

$user_id = null;
$my_account = false;
$mode = "view";
$user_posts = array();

if (get_input('redirect_to_get')) {
   // all data posted is already processed in included FrontStream.php
   redirect($_SERVER['REQUEST_URI']);
}

$code_name = get_input('code');
$command   = get_input('cmd');

if (is_logged_in() && !$code_name) {
    $user_id = get_user_id();
}

$user = new User($user_id, $code_name);

if (!$user->is_loaded()) {
    $template->assign('message', "This user does not exist.");
    $template->display('pages/message.tpl');
    exit();
}

if ($user->get_data('status')=='inactive' && $user->get_data('user_id') !=  get_user_id() && !is_admin()) {
    display_404();
}

$user_id = $user->get_data('user_id');

if ($user_id == get_user_id()) {
    $my_account = true;
    if ($command == 'account' || $command == 'profile') {
        showEditAccountPage($command, $user_id);
    }
}

if ($command == 'follow_user' && is_logged_in()) {
    if ($user->get_data('user_id') !== get_user_id()) {
        $user->setCurrentUserAsFollower();
        $current_user = new User(get_user_id());
        $current_user->recache();
        user_login($current_user);

        // to populate followed_by_curr_user field
        $user = new User($user->get_data('user_id'));
    }
}

$show_content_type = 'feed';
$active_submenu = '';
$tab_selected = 'following';
if ($command == 'connections') {
    $tab_selected = Utils::reqParam('tab_selected', $tab_selected);
    $show_content_type = 'connections';
    $active_submenu = 'connections';
    $user->load_follows(true);
} else {
    // we need full data to show counts in feed mode
    $user->load_follows();
}

$user->loadRecentConnections();

$seo_attributes = $user->get_seo_attributes();

$tags_filter_enabled = false;

$user_data = $user->get();

$user_data['company_tag'] = Vendor::getMainTag($user_data['company_id']);

$user_posts = FrontStream::getContent(FrontStream::POSTS_ON_PAGE, 0, array('type'=>'user', 'id'=>$user_id));
FrontStream::setCommonSmartyParams();

$smarty_params = array(
    "can_edit" => $my_account,
    "my_account" => $my_account,
    "seo" => $seo_attributes,
    "mode" => $mode,
    "user" => $user_data,
    'user_posts' => $user_posts,
    'show_content_type' => $show_content_type,
    'max_follow_icons_number' => Utils::SUMMARY_FOLLOWERS_COUNT,
    'active_submenu' => $active_submenu,
    'tab_selected' => $tab_selected,
    'show_join_widget' => 1,
    'twitter_symbols_left' => Utils::countTwitterSymbolsLeft(' via @ShareBloc '),
);
//e($user->get());
$template->assign($smarty_params);
$template->display('pages/user.tpl');


/*
* Shows both versions of edit account page depending on $type:
* 'account' - page with oAuth, password, notification fields (user meny->my account)
* 'profile' - all other fields, logo (user page -> edit)
*/
function showEditAccountPage($type, $user_id) {
    $active_tab = Utils::reqParam('active_tab', 'account_tab');

    $notifications_div = Utils::reqParam('selected', 'email');
    $publisher_div = Utils::reqParam('selected_pub_div', 'post');

    $user_account_data = User::getEmptyEditAccountData();
    $user_account_data = User::fillInEditAccountData($user_account_data);

    $password_data = User::getEmptyPasswordData();

    $seo_attributes = array('title' => '',
                            'keywords' => 'General Account Settings',
                            'description' => 'ShareBloc is a community of like-minded professionals who share, curate and discuss business content that matters.');
    $smarty_params = array(
        'notifications' => Notification::$notifications,
        'notify_weekly' => Utils::userData('notify_weekly'),
        'notify_post_responded' => Utils::userData('notify_post_responded'),
        'notify_comment_responded' => Utils::userData('notify_comment_responded'),
        'notify_product_update' => Utils::userData('notify_product_update'),
        'notify_daily' => Utils::userData('notify_daily'),
        'notify_suggestion' => Utils::userData('notify_suggestion'),
        'active_tab' => $active_tab,
        'account_data' => $user_account_data,
        'password_data' => $password_data,
        'seo' => $seo_attributes,
        'edit_type' => $type,
        'init_image_upload' => true,
        'notifications_div' => $notifications_div,
        'publisher_div' => $publisher_div,
        'categories_structure' => Utils::getCategoriesStructure(),
        'autopost_allowed' => Utils::userData('f_auto_allowed'),
    );

    if (!isset($_SESSION['user_info']['my_url'])) {
        Log::$logger->error('Maybe not logged in user. Email: ' . $_SESSION['user_info']['email']);
    }

    Utils::$smarty->assign($smarty_params);
    Utils::$smarty->display('pages/user_edit.tpl');
    exit();
}