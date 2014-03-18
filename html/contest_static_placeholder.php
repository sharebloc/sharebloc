<?php

require_once('../includes/global.inc.php');

$smarty_params = array(
    'use_contest_vote' => 1,
    'contest_id' => 2,
    'contest_url' => 'content_marketing_nation',
);
$template->assign($smarty_params);
$template->display('contest_marketo/contest_static_placeholder.tpl');
