<?php

require_once('../includes/global.inc.php');

if (isset($_REQUEST['file']) && $_REQUEST['file'] && is_valid_filename($_REQUEST['file']) && substr($_REQUEST['file'], 0, 1) != ".") {
    $file_name = $_REQUEST['file'];
} else {
    display_404();
}

$full_filename = "./file_download/$file_name";

if (!file_exists($full_filename)) {
    display_404();
}

if (!is_logged_in()) {
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if (!Utils::isBot($user_agent)) {
        // not logged on users should log in, bots can download files
        $_SESSION['download_resource_after_login'] = $_SERVER['REDIRECT_URL'];
        redirect(Utils::getLoginUrl());
    }
}

// We'll be outputting a pdf
header('Content-type: application/pdf');
header("Content-Disposition: attachment; filename=\"$file_name\"");

// The PDF source is in original.pdf
readfile($full_filename);
?>
