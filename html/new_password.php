<?php

require_once('../includes/global.inc.php');

$login_error   = 0;
$reset_done    = false;
$bad_reset_key = false;

$reset_key     = trim(get_input("reset_key"));
$error_message = '';

if (!$reset_key) {
    $bad_reset_key = true;
} else {
    $temp_user = new User;
    $user_id   = $temp_user->getUserIdByResetPasswordKey($reset_key);

    if (!$user_id) {
        $bad_reset_key = true;
    }

    $new_password    = trim(get_input("new_password"));
    $verify_password = trim(get_input("verify_password"));
}

if (!$bad_reset_key && $new_password && $verify_password) {
    if ($new_password !== $verify_password) {
        $error_message = "Please check your passwords, they are not equal.";
    } else {
        $user = new User($user_id);
        if ($user->is_loaded()) {
            $current_data             = $user->get();
            $current_data['password'] = User::getPasswordHash($new_password);

            $needs_welcome_email = false;
            if ($current_data['f_contest_voter']) {
                // if contest voter resets his password, he becames an usual user
                $current_data['f_contest_voter'] = 0;
                $needs_welcome_email = true;
                Log::$logger->warn("Contest voter has been signed up (password), user_id = $user_id");
            }

            $user->set($current_data);
            $user->save();

            $user->clearPasswordResetKey();
            $reset_done = true;

            // We can log this user in as it knows password and email, so it's he
            user_login($user);
            Utils::setPermissions();

            if ($needs_welcome_email) {
                $_SESSION['show_join_welcome_popup'] = 1;
                Utils::sendWelcomeEmail($user, false, false);
            }
        } else {
            $error_message = "Password was not changed due to server error. Please try once more.";
        }
    }
}

//http://cws.tril2.trillium.lan:8989/pw_rst/d9fa4ec085577670839dbff4afeee99d


$template->assign('bad_reset_key', $bad_reset_key);
$template->assign('error_message', $error_message);
$template->assign('password_reset_done', $reset_done);
$template->assign('password_reset_key', $reset_key);
$template->display('pages/new_password.tpl');
?>