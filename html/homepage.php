<?php

require_once('../includes/global.inc.php');
require_once('class.FrontStream.php');

//showFrontPageForNotLoggedIn();

$show_popup = get_input('p');
if ($show_popup) {
    // this is special url asked by Andrew to instant popup show. This parameter is not used in the code.
    $_SESSION['front_page_open_invite_popup'] = true;
    // redirecting to this page without this parameter in GET to show the popup only once
    redirect($_SERVER['REQUEST_URI']);
}

if (get_input('redirect_to_get')) {
   // all data posted is already processed in included FrontStream.php
   redirect($_SERVER['REQUEST_URI']);
}

$show_invite_popup= 0;
if (!empty($_SESSION['front_page_open_invite_popup'])) {
    $show_invite_popup = 1;
    unset($_SESSION['front_page_open_invite_popup']);
}

$content = FrontStream::getContent(FrontStream::POSTS_ON_PAGE, 0, array('type'=>'feed'));
FrontStream::setCommonSmartyParams();

$show_contest_widget = false;
if (is_logged_in()) {
    $user_follow_list = Utils::userData('following');
    if(!empty($user_follow_list['tag_'.Utils::SALES_MARKETING_TAG_ID])) {
        $show_contest_widget = true;
    }
}

// todo remove after testing om beta
if (!Utils::sVar('show_join_welcome_popup')) {
    $_SESSION['show_join_welcome_popup'] = Utils::reqParam('test_welcome');
}

$smarty_params = array(
    'content' => $content,
    'show_invite_popup' => $show_invite_popup,
    'active_submenu' => 'home',
    'init_clipboard_copy' => true,
    'show_join_welcome_popup' => Utils::unsetSVar('show_join_welcome_popup'),
    'show_contest_widget' => $show_contest_widget,
    'twitter_symbols_left' => Utils::countTwitterSymbolsLeft(" via @ShareBloc "),
);

$template->assign($smarty_params);
$template->display('pages/index.tpl');

function showFrontPageForNotLoggedIn() {
    if (is_logged_in()) {
        return;
    }

    $smarty = Utils::$smarty;
    $smarty->display('pages/frontpage.tpl');
    exit;
}