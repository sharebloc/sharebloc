<?php

require_once('../includes/global.inc.php');
require_once('class.Track.php');

if (!is_logged_in() || !is_admin()) {
    header('location: /index.php');
    exit();
}

$command = get_input('cmd');

$template->assign('types', Track::$types);
$template->assign('show_old_types', Utils::reqParam('old'));

$template->display('pages/track.tpl');
?>
