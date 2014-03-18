<?php
require_once('../includes/global.inc.php');
require_once('class.ExtAuth.php');

$provider = get_input('provider');
$type = get_input('type');
$REMEMBER_ME = true;

if (get_input('hybrid'))  {
    include DOCUMENT_ROOT . "/includes/hybridauth/index.php";
} else {
    if (!ExtAuth::connect($provider)) {
        Log::$logger->warn("Can't connect by oAuth");
        $template->assign('message', "Unknown server error, please try later");
        $template->display('pages/message.tpl');
        exit();
    }

    if (!ExtAuth::$user_data['provider_uid']) {
        Log::$logger->error("Сonnect by oAuth, but oauth id is empty");
        $template->assign('message', "Unknown server error, please try later");
        $template->display('pages/message.tpl');
        exit();
    }

    $redirect_url = '';
    if ($type=='join' || $type=='signin') {
        if (User::loginByOAuth(ExtAuth::$user_data, $REMEMBER_ME)) {
            Utils::processSuccessfullLogin();
            $redirect_url = Utils::getPageToReturnAfterLogIn();
        } else {
            // to use this data for fields prefilling + use after joining
            $_SESSION['oauth_data'] = ExtAuth::$user_data;
            $redirect_url = Utils::getJoinUrl();
        }
    } elseif ($type=='connect' || $type=='follow_connect') {
        User::addOAuthRegistration(ExtAuth::$user_data);
        if ($type=='connect') {
            $redirect_url = Utils::userData('my_url').'/account?active_tab=networks_tab';
        } else {
            $redirect_url = Utils::getBaseUrl().'/join_follow.php?type=people';
        }

    }
    redirect($redirect_url);
    exit;
}