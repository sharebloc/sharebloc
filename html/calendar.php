<?php

require_once('../includes/global.inc.php');
require_once('class.Event.php');

$tag_code_name  = get_input('tag');
if ($tag_code_name !== 'sales__marketing') {
    redirect(Utils::getDefaultPage());
}
$main_tag_id = Utils::getCategoryIdByCodeName($tag_code_name);

if (!$main_tag_id) {
    redirect(Utils::getDefaultPage());
}
$page_tag = new Tag($main_tag_id);

$content = Event::getEvents($main_tag_id);

$subcategories = Utils::$tags_list_vendor;
$sorted_categories = Utils::getCategoriesInCustomOrder();
// removing unused data to not show all the data on page
$subcategories_short = array();
foreach ($subcategories as $key=>$subcategory) {
    $subcategories_short[$key]['tag_id'] = $subcategory['tag_id'];
    $subcategories_short[$key]['tag_name'] = $subcategory['tag_name'];
    $subcategories_short[$key]['parent_tag_id'] = $subcategory['parent_tag_id'];
}

$smarty_params = array(
    'page_tag' => $page_tag->get(),
    'content' => $content,
    'events_on_page' => Event::EVENTS_ON_PAGE,
    'no_more_content' => Event::$no_more_content,
    'categories' => $sorted_categories[$main_tag_id],
    'subcategories' => $subcategories_short,
    'init_clipboard_copy' => true,
    'date_format' => "%b %d",
    'only_month_date_format' => "%b",
);

$template->assign($smarty_params);
$template->display('pages/calendar.tpl');