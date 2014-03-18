<?php

require_once('../includes/global.inc.php');
require_once('class.ExtAuth.php');

if (is_logged_in()) {
    redirect(Utils::getDefaultPage());
}

$user_data = User::getEmptySignupData();
$provider  = '';
$image_url = '';
$active_join_step = 'join';

if (isset($_SESSION['oauth_data'])) {
    $oauth_data = $_SESSION['oauth_data'];
    $provider   = $oauth_data['provider'];
    foreach ($user_data as $name => &$data) {
        if (!empty($oauth_data[$name])) {
            $data['value'] = $oauth_data[$name];
        }
    }

    if (!empty($oauth_data['image_url'])) {
        $image_url = $oauth_data['image_url'];
    }
}

$from_frontpage = false;
// only front invites have email, custom do not have
if (isset($_SESSION['invite_front_data']['email'])) {
    $user_data['email']['value']      = $_SESSION['invite_front_data']['email'];
    $user_data['first_name']['value'] = $_SESSION['invite_front_data']['first_name'];
    $user_data['last_name']['value']  = $_SESSION['invite_front_data']['last_name'];
} elseif (is_contest_voter()) {
    $user_data['email']['value']      = Utils::userData('email');
    $user_data['first_name']['value'] = Utils::userData('first_name');
    $user_data['last_name']['value']  = Utils::userData('last_name');
} elseif (Utils::reqParam('join_sharebloc_email')) {
    $_SESSION['frontpage_join_sharebloc_email'] = Utils::reqParam('join_sharebloc_email');
    $from_frontpage = true;
}

if (Utils::sVar('frontpage_join_sharebloc_email')) {
    // as this can be not just direct redirect from frontpage but a redirect from oAuth reg
    // where oAuth provider can provide other email
    // @see https://vendorstack.atlassian.net/browse/VEN-389
    $user_data['email']['value'] = Utils::sVar('frontpage_join_sharebloc_email');
}

$open_email_join = 0;
if ($user_data['email']['value'] && !$from_frontpage) {
    $open_email_join = 1;
}

$smarty_params = array(
    'image_url' => $image_url,
    'provider' => $provider,
    'user_data' => $user_data,
    'open_email_join' => $open_email_join,
    'active_join_step' => $active_join_step,
    'init_image_upload' => true,
    'active_submenu' => 'join',
);
$template->assign($smarty_params);
$template->display('pages/join.tpl');