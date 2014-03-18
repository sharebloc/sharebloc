<?php

require_once('../includes/global.inc.php');
require_once('class.FrontStream.php');
require_once('class.VoteContest.php');

$selected_category_codename = Utils::reqParam('code', 'all');
$contest_categories_with_all = Utils::getContestCategories(true);
$selected_category = getContestCategoryIdByCodename($selected_category_codename, $contest_categories_with_all);

$contest_id = get_input('contest_id');
if (!$contest_id) {
    redirect(Utils::getDefaultPage());
}
if ($contest_id == 2 && !is_logged_in()) {
    redirect(Utils::getLoginUrl());
}
$contest_url = Utils::$contest_urls[$contest_id];

$content = FrontStream::getContent(FrontStream::POSTS_ON_PAGE,
                                    0,
                                    array('type'=>'contest_all',
                                            'id'=>$selected_category,
                                            'contest_id'=>$contest_id)
                                );

FrontStream::setCommonSmartyParams(true);

$smarty_params = array(
    'content' => $content,
    'active_submenu' => 'contest',
    'init_clipboard_copy' => true,
    'contest_votes_left' => VoteContest::getVotesLeft(),
    'use_contest_vote' => 1,
    'twitter_symbols_left' => Utils::countTwitterSymbolsLeft(),
    'contest_categories_with_all' => $contest_categories_with_all,
    'selected_category' => $selected_category,
    'show_join_widget' => 1,
    'contest_id' => $contest_id,
    'contest_url' => $contest_url,
);

$template->assign($smarty_params);
$template->display('pages/contest_all.tpl');

function getContestCategoryIdByCodename($codename, $categories) {
    foreach ($categories as $category) {
        if ($codename==$category['code_name']) {
            return $category['tag_id'];
        }
    }

    $first_tag = reset($categories);
    return $first_tag['tag_id'];
}