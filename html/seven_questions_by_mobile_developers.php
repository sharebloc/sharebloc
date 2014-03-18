<?php

require_once('../includes/global.inc.php');
require_once('class.Vendor.php');
require_once('class.SiteCategory.php');

/* * *************  CONFIG **************** */
// see getQuestions()
/* * *************  END OF CONFIG **************** */

$questions = getQuestions();

foreach ($questions as $key => &$question) {
    foreach ($question['vendors'] as &$vendor) {
        $temp_data = array();

        // WARN - please note that we get each vendor with separate DB query. There is about 30 vendors.
        $temp_vendor = new Vendor(null, $vendor['vendor_code']);
        if ($temp_vendor->is_loaded()) {
            $temp_data = $temp_vendor->get();

            $temp_data['local'] = true;

            $first_tag             = reset($temp_data['tag_list_details']);
            $temp_data['category'] = $first_tag;
        } else {
            // WARN! used for vendors which are not present in DB. We should not be here on production.
            $temp_data['local'] = false;

            $temp_data['code_name']   = $vendor['vendor_code'];
            $temp_data['vendor_name'] = $vendor['vendor_code'];
            $temp_data['logo_hash']   = $vendor['icon'];

            $temp_data['category']['tag_name']             = "";
            $temp_data['category']['tag_id']               = "";
            $temp_data['category']['parent_tag_name']      = "";
            $temp_data['category']['parent_tag_id']        = "";
            $temp_data['category']['parent_tag_code_name'] = "";

            $temp_data['rating']                = Array("total"  => 0, "count"  => 0, "rating" => 0);
            $temp_data['rating']                = Array("total"  => 0, "count"  => 0, "rating" => 0);
            $temp_data['description']              = "Sorry, the vendor is not present in our database anymore.";
        }
        $temp_data['border'] = $vendor['border'];
        $vendor              = $temp_data;
    }
}

$template->assign('is_ie8', is_ie8());
$template->assign('questions', $questions);
$template->display('pages/splash-mobile.tpl');
exit;

/* Functions */

function is_ie8() {
    if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'compatible; MSIE ') !== false && isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'sorrynoie') === false) {
        $matches = array();
        preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $matches);
        if (count($matches) > 1 && $matches[1] == 8) {
            return true;
        }
    }
    return false;
}

function getQuestions() {
    $questions = array();

    /* q1 OPTIMIZATION */
    $vendors    = array();
    $vendors[1] = array("vendor_code" => "apptamin", "border"      => true, "icon"        => "bba6a5b68c1d7d5e43db89911528728a");
    $vendors[2] = array("vendor_code" => "searchman", "border"      => false, "icon"        => "201ccb2abde5414512f568390686e277");
    $vendors[3] = array("vendor_code" => "appfigures", "border"      => true, "icon"        => "77a33ae60171b8213756cb1c5b924c9e");
    $vendors[4] = array("vendor_code" => "mobiledevhq", "border"      => true, "icon"        => "d8236d45b4402f3553a882d846db3b18");
    $vendors[5] = array("vendor_code" => "app_annie", "border"      => false, "icon"        => "2bb00fbf18a97905bb448073445f68c6");
    $vendors[6] = array("vendor_code" => "mopapp", "border"      => true, "icon"        => "9ec330027654fa0c5bd08c1a1c084957");


    $questions[1] ['vendors']    = $vendors;
    $questions[1] ['text_title'] = "OPTIMIZATION";
    $questions[1] ['text_line1'] = "HOW DO I GET MY APP TO RANK HIGHER";
    $questions[1] ['text_line2'] = "ON THE APP STORES?";
    $questions[1] ['pdf_number'] = 1;

    /* q2 CUSTOMER SERVICE */
    $vendors    = array();
    $vendors[1] = array("vendor_code" => "apptentive", "border"      => false, "icon"        => "91e9ecc4fc3e9dc36905e09b8820edc8");
    $vendors[2] = array("vendor_code" => "uservoice", "border"      => true, "icon"        => "258b7e48575a0351c12fd892263b1551");
    $vendors[3] = array("vendor_code" => "helpshift", "border"      => false, "icon"        => "fe161ed87a0e854e3dd6370e8da2f1ce");

    $questions[2] ['vendors']    = $vendors;
    $questions[2] ['text_title'] = "CUSTOMER SERVICE";
    $questions[2] ['text_line1'] = "HOW DO I GET OUR USERS";
    $questions[2] ['text_line2'] = "TO RATE MY APP?";
    $questions[2] ['pdf_number'] = 4;

    /* q3 APP MONETIZATION */
    $vendors    = array();
    $vendors[1] = array("vendor_code" => "admob", "border"      => true, "icon"        => "01f2afb6e726a9b8f3f37620789c6c2c");
    $vendors[2] = array("vendor_code" => "fiksu", "border"      => false, "icon"        => "c933edd12eccab2053d8e3b2b7ddfd89");
    $vendors[3] = array("vendor_code" => "tapjoy", "border"      => false, "icon"        => "d177050f4652635618f7d9246a02e5cc");
    $vendors[4] = array("vendor_code" => "iad", "border"      => false, "icon"        => "f94bf4c2d84de10436e6fff2f5387f87");
    $vendors[5] = array("vendor_code" => "appia", "border"      => false, "icon"        => "ce46cfc56da14decb81502d6994f8770");

    $questions[3] ['vendors']    = $vendors;
    $questions[3] ['text_title'] = "APP MONETIZATION";
    $questions[3] ['text_line1'] = "WHAT IS THE EFFECTIVENESS OF";
    $questions[3] ['text_line2'] = "INCENTIVIZED INSTALLS?";
    $questions[3] ['pdf_number'] = 5;

    /* q4 ANALYTICS */
    $vendors    = array();
    $vendors[1] = array("vendor_code" => "google_analytics", "border"      => false, "icon"        => "b63868263af3ee2c5760ea4e018bd713");
    $vendors[2] = array("vendor_code" => "kontagent", "border"      => true, "icon"        => "a814d6c05ceac48d1fb53b59558dd938");
    $vendors[3] = array("vendor_code" => "gameanalytics", "border"      => true, "icon"        => "f247eeaf8b3b51235ac99c70881c3f37");
    $vendors[4] = array("vendor_code" => "keenio", "border"      => true, "icon"        => "7c473dc15fff7a3ff42f82f980b4485c");
    $vendors[5] = array("vendor_code" => "flurry", "border"      => true, "icon"        => "ff60116bbb05f1f60a16f89c6764b760");
    $vendors[6] = array("vendor_code" => "mixpanel", "border"      => true, "icon"        => "b8c14f32d5e495780e00708784a2799a");
    $vendors[7] = array("vendor_code" => "yozio", "border"      => true, "icon"        => "1dae63b6da78bb67cd724c3aeafd3430");

    $questions[4] ['vendors']    = $vendors;
    $questions[4] ['text_title'] = "ANALYTICS";
    $questions[4] ['text_line1'] = "WHAT METRICS SHOULD I BE";
    $questions[4] ['text_line2'] = "MEASURING ON MY APP?";
    $questions[4] ['pdf_number'] = 2;

    /* q5 CRASH REPORTING */
    $vendors    = array();
    $vendors[1] = array("vendor_code" => "bugsense", "border"      => true, "icon"        => "58c0c5e491c523a25ac86a9f6271212c");
    $vendors[2] = array("vendor_code" => "crittercism", "border"      => true, "icon"        => "b384898ce44fc43e59d4306f040c5549");
    $vendors[3] = array("vendor_code" => "crashlytics", "border"      => false, "icon"        => "741bbff0e4491434dc499588461bbb39");
    $vendors[4] = array("vendor_code" => "testflight", "border"      => false, "icon"        => "7f0a9b50e6235021500fe5b804bb7545");

    $questions[5] ['vendors']    = $vendors;
    $questions[5] ['text_title'] = "APP PERFORMANCE";
    $questions[5] ['text_line1'] = "WHO DO YOU USE FOR";
    $questions[5] ['text_line2'] = "CRASH REPORTING?";
    $questions[5] ['pdf_number'] = 6;

    /* q6 QA TESTING */
    $vendors    = array();
    $vendors[1] = array("vendor_code" => "testdroid", "border"      => true, "icon"        => "16520fc4bae3644daba942c4aff4c409");
    $vendors[2] = array("vendor_code" => "perfecto_mobile", "border"      => true, "icon"        => "a4adaea0eb7d354dc80e6ed15e9fee54");
    $vendors[3] = array("vendor_code" => "deviceanywhere", "border"      => true, "icon"        => "c1b9ac22ecb2635f2d1477eac46db850");

    $questions[6] ['vendors']    = $vendors;
    $questions[6] ['text_title'] = "QA TESTING";
    $questions[6] ['text_line1'] = "WHAT TOOLS CAN HELP ME";
    $questions[6] ['text_line2'] = "QA TEST QUICKLY ON ANDROID?";
    $questions[6] ['pdf_number'] = 3;

    /* q7 BACKEND-AS-A-SERVICE */
    $vendors    = array();
    $vendors[1] = array("vendor_code" => "stackmob", "border"      => true, "icon"        => "d958160fdd3919f37048b53a9608fb3e");
    $vendors[2] = array("vendor_code" => "parse", "border"      => false, "icon"        => "dacd41978fe7f84083b62e5150ec85c9");
    $vendors[3] = array("vendor_code" => "urban_airship", "border"      => true, "icon"        => "be0f2673ae1a8f8dafb18619cd5b01e3");

    $questions[7] ['vendors']    = $vendors;
    $questions[7] ['text_title'] = "BACKEND-AS-A-SERVICE";
    $questions[7] ['text_line1'] = "WHAT SHOULD I KNOW BEFORE I DEVELOP";
    $questions[7] ['text_line2'] = "ON A BACKEND-AS-A-SERVICE?";
    $questions[7] ['pdf_number'] = 7;

    return $questions;
}

?>
