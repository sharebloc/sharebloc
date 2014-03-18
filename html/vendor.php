<?php

require_once('../includes/global.inc.php');
require_once('class.FrontStream.php');
require_once('class.AlsoViewed.php');
require_once('class.Claim.php');

$mode = "view";

$command   = get_input('cmd');
$code_name = get_input('code');

if (get_input('redirect_to_get')) {
   // all data posted is already processed in included FrontStream.php
   redirect($_SERVER['REQUEST_URI']);
}

if ($command=='create' && is_admin()) {
    $vendor = new Vendor();
    $smarty_params = array(
        "new_vendor" => true,
        "vendor" => $vendor->get(),
        'init_image_upload' => true,
    );
    Utils::populateFullCompanyAndUsersList();

    $template->assign($smarty_params);
    $template->display('pages/vendor_edit.tpl');
    exit;
}

$vendor = new Vendor(null, $code_name);
if (!$vendor->is_loaded()) {
    $template->assign('message', "This company does not exist.");
    $template->display('pages/message.tpl');
    exit();
}

$vendor_id = $vendor->get_data('vendor_id');

if ($command === 'delete' && is_admin()) {
    $vendor->delete();
    redirect(Utils::getDefaultPage());
}

$show_content_type = 'feed';
if ($command == 'connections') {
    $show_content_type = 'connections';
    $vendor->load_follows(true);
} else {
    // we need full data to show counts in feed mode
    $vendor->load_follows();
}


$seo_attributes = $vendor->get_seo_attributes();
$also_viewed = new AlsoViewed('vendor', $vendor_id);
$also_viewed->record_visit();

$vendor_data = $vendor->get();

$vendor_posts = FrontStream::getContent(FrontStream::POSTS_ON_PAGE, 0, array('type'=>'vendor', 'id'=>$vendor_id));
FrontStream::setCommonSmartyParams();

$smarty_params = array(
    "seo" => $seo_attributes,
    "vendor" => $vendor_data,
    'vendor_posts' => $vendor_posts,
    'max_follow_icons_number' => Utils::SUMMARY_FOLLOWERS_COUNT,
    'show_content_type' => $show_content_type,
    'init_image_upload' => true,
    'new_vendor' => false,
    'show_join_widget' => 1,
    'autopost_allowed' => true,
    'twitter_symbols_left' => Utils::countTwitterSymbolsLeft(' via @ShareBloc '),
);

$template->assign($smarty_params);

if (is_admin() && $command == 'edit') {
    Utils::populateFullCompanyAndUsersList();
    $template->display('pages/vendor_edit.tpl');
} else {
    $template->display('pages/vendor.tpl');
}