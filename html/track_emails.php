<?php
require_once('../includes/global.inc.php');

showPixelImage();
countOpen();
exit;

function showPixelImage() {
    $logo_file = DOCUMENT_ROOT . "/html/images/track_emails.png";
    header("Content-Type: image/png");
    readfile($logo_file);
    return;
}

function countOpen() {
    $code = Utils::reqParam('code');
    if (!$code) {
        return;
    }
    $sql = sprintf("UPDATE email_log
                    SET opens_count = opens_count+1, last_open_ts=NOW()
                    WHERE email_code='%s'",
                    Database::escapeString($code));
    Database::exec($sql);
    return;
}