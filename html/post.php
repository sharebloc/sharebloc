<?php

// todo bear this file should be fully refactored, with extracting some code and logic to functions

require_once('../includes/global.inc.php');
require_once('class.PostedLink.php');

$allowed_types = array('posted_link', 'question', 'contest');
$type  = get_input('type');
if ($type == 'link') {
    $type = 'posted_link';
}
if (!$type || !in_array($type, $allowed_types)) {
    $type = isset($_SESSION['type']) ? $_SESSION['type'] : "posted_link";
}
$_SESSION['type'] = $type;

$refer_vendor = null;

$vendor_id = get_input('vendor_id');
if ($vendor_id) {
    $vendor = new Vendor($vendor_id);
    $refer_vendor = array();
    $refer_vendor['vendor_id'] = $vendor->get_data('vendor_id');
    $refer_vendor['vendor_name'] = $vendor->get_data('vendor_name');
    $refer_vendor['code_name'] = $vendor->get_data('code_name');
}

$selected_tag_id = get_input('tag_id', null);
if (!$selected_tag_id && is_logged_in()) {
    $selected_tag_id = Utils::userData('autopost_tag_id');
}

$subcategories = Utils::$tags_list_vendor;
$sorted_categories = Utils::getCategoriesInCustomOrder();

// removing unused data to not show all the data on page
$subcategories_short = array();
foreach ($subcategories as $key=>$subcategory) {
    $subcategories_short[$key]['tag_id'] = $subcategory['tag_id'];
    $subcategories_short[$key]['tag_name'] = $subcategory['tag_name'];
}

$template->assign('refer_vendor', json_encode($refer_vendor));
$template->assign('selected_tag_id', $selected_tag_id);
$template->assign('categories', $sorted_categories);
$template->assign('subcategories', $subcategories_short);
$template->assign('categories_str', json_encode($sorted_categories));
$template->assign('subcategories_str', json_encode($subcategories_short));
$template->assign('type', $type);

require_once('class.Notification.php');

if ($type=='contest') {
    if (is_admin()) {
        $template->assign('experts', Utils::getContestExperts());
        // Utils::populateFullCompanyAndUsersList();
    }

    $template->assign('contest_votes_left', VoteContest::getVotesLeft());
    $template->assign('contest_categories', Utils::getContestCategories());
    $template->assign('use_contest_vote', 1);
    $template->display('pages/post_contest.tpl');
} else {
    $template->display('pages/post.tpl');
}