<?php

require_once('../includes/global.inc.php');

$sale_space_info = SaleSpaceRace();

foreach ($sale_space_info as &$group) {
    foreach ($group['vendor_groups'] as &$vendor_group) {
        foreach ($vendor_group['vendors'] as &$vendor) {

            $temp_data   = array();
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
                $temp_data['logo_hash']   = "";

                $temp_data['category']['tag_name']             = "";
                $temp_data['category']['tag_id']               = "";
                $temp_data['category']['parent_tag_name']      = "";
                $temp_data['category']['parent_tag_id']        = "";
                $temp_data['category']['parent_tag_code_name'] = "";

                $temp_data['rating']                = Array("total"  => 0, "count"  => 0, "rating" => 0);
                $temp_data['rating']                = Array("total"  => 0, "count"  => 0, "rating" => 0);
                $temp_data['description']              = "Sorry, the vendor is not present in our database anymore.";
            }
            $vendor = $temp_data;
        }
    }
}


$template->assign('is_ie8', is_ie8());
$template->assign('sale_space_info', $sale_space_info);
$template->display('pages/splash-space.tpl');
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

function SaleSpaceRace() {

    $sales_space = array();

    /*ANALYTICS*/

    $vendor_groups = array();

    /*GENERAL-PURPOSE ANALITYCS*/
    $vendor_group = array();
    $vendors = array();
    $vendors[0] = array("vendor_code" => "anaplan");
    $vendors[1] = array("vendor_code" => "birst");
    $vendors[2] = array("vendor_code" => "domo");
    $vendors[3] = array("vendor_code" => "tableau");

    $vendor_group['title'] = 'GENERAL-PURPOSE ANALITYCS';
    $vendor_group['vendors'] = $vendors;
    $vendor_group['scroll_id'] = 'gen_purp_analytics';

    $vendor_groups[] = $vendor_group;

    /*SALES-SPECIFIC*/
    $vendor_group = array();
    $vendors = array();
    $vendors[0] = array("vendor_code" => "bloomfire");
    $vendors[1] = array("vendor_code" => "brightfunnel");
    $vendors[2] = array("vendor_code" => "cloud9_analytics");
    $vendors[3] = array("vendor_code" => "cloudamp");
    $vendors[4] = array("vendor_code" => "ensighten");
    $vendors[5] = array("vendor_code" => "funnelfire");
    $vendors[6] = array("vendor_code" => "insightsquared");
    $vendors[7] = array("vendor_code" => "leadledger");
    $vendors[8] = array("vendor_code" => "woopra");

    $vendor_group['title'] = 'SALES-SPECIFIC';
    $vendor_group['vendors'] = $vendors;
    $vendor_group['scroll_id'] = 'sales_spec_analytics';

    $vendor_groups[] = $vendor_group;

    $sales_space[0] = array();
    $sales_space[0]['title'] = 'ANALYTICS';
    $sales_space[0]['text_title'] = '';
    $sales_space[0]['text_pro_tip'] = '';
    $sales_space[0]['vendor_groups'] = $vendor_groups;

    /*PRESENTATION*/

    $vendor_groups = array();

    /*GENERAL-PURPOSE PRESENTATION*/
    $vendor_group = array();
    $vendors = array();
    $vendors[0] = array("vendor_code" => "gotomeeting");
    $vendors[1] = array("vendor_code" => "joinme");
    $vendors[2] = array("vendor_code" => "prezi");

    $vendor_group['title'] = 'GENERAL-PURPOSE PRESENTATION';
    $vendor_group['vendors'] = $vendors;
    $vendor_group['scroll_id'] = 'gen_purp_present';

    $vendor_groups[] = $vendor_group;

    /*SALES_SPECIFIC*/
    $vendor_group = array();
    $vendors = array();
    $vendors[0] = array("vendor_code" => "9slides");
    $vendors[1] = array("vendor_code" => "anymeeting");
    $vendors[2] = array("vendor_code" => "clearslide");
    $vendors[3] = array("vendor_code" => "fileboard");
    $vendors[4] = array("vendor_code" => "meetingsio");
    $vendors[5] = array("vendor_code" => "storydesk");

    $vendor_group['title'] = 'SALES-SPECIFIC';
    $vendor_group['vendors'] = $vendors;
    $vendor_group['scroll_id'] = 'sales_spec_present';

    $vendor_groups[] = $vendor_group;

    $sales_space[1] = array();
    $sales_space[1]['title'] = 'PRESENTATION';
    $sales_space[1]['text_title'] = '';
    $sales_space[1]['text_pro_tip'] = '';
    $sales_space[1]['vendor_groups'] = $vendor_groups;

    /*CLOSING*/

    $vendor_groups = array();

    /*e-SIGNATURE*/
    $vendor_group = array();
    $vendors = array();
    $vendors[0] = array("vendor_code" => "conga");
    $vendors[1] = array("vendor_code" => "docusign");
    $vendors[2] = array("vendor_code" => "echosign");
    $vendors[3] = array("vendor_code" => "hellosign");
    $vendors[4] = array("vendor_code" => "rightsignature");

    $vendor_group['title'] = 'e-SIGNATURE';
    $vendor_group['vendors'] = $vendors;
    $vendor_group['scroll_id'] = 'esign';

    $vendor_groups[] = $vendor_group;

    $sales_space[2] = array();
    $sales_space[2]['title'] = 'CLOSING';
    $sales_space[2]['text_title'] = '';
    $sales_space[2]['text_pro_tip'] = '';
    $sales_space[2]['vendor_groups'] = $vendor_groups;

    /*CRM*/

    $vendor_groups = array();

    /*TRADITIONAL*/
    $vendor_group = array();
    $vendors = array();
    $vendors[0] = array("vendor_code" => "base_crm");
    $vendors[1] = array("vendor_code" => "highrise");
    $vendors[2] = array("vendor_code" => "infusionsoft");
    $vendors[3] = array("vendor_code" => "insightly");
    $vendors[4] = array("vendor_code" => "salesforcecom");
    $vendors[5] = array("vendor_code" => "sugarcrm");
    $vendors[6] = array("vendor_code" => "zoho_crm");

    $vendor_group['title'] = 'TRADITIONAL';
    $vendor_group['vendors'] = $vendors;
    $vendor_group['scroll_id'] = 'traditional';

    $vendor_groups[] = $vendor_group;

    /*DATA-DRIVEN/UPCOMING*/
    $vendor_group = array();
    $vendors = array();
    $vendors[0] = array("vendor_code" => "handshakez");
    $vendors[1] = array("vendor_code" => "mingly");
    $vendors[2] = array("vendor_code" => "nimble");
    $vendors[3] = array("vendor_code" => "pipedrive");
    $vendors[4] = array("vendor_code" => "relateiq");
    $vendors[5] = array("vendor_code" => "streak");

    $vendor_group['title'] = 'DATA-DRIVEN/UPCOMING';
    $vendor_group['vendors'] = $vendors;
    $vendor_group['scroll_id'] = 'data_dr_up';

    $vendor_groups[] = $vendor_group;

    $sales_space[3] = array();
    $sales_space[3]['title'] = 'CRM';
    $sales_space[3]['text_title'] = '';
    $sales_space[3]['text_pro_tip'] = '';
    $sales_space[3]['vendor_groups'] = $vendor_groups;

    //$vendors[0] = array("vendor_code" => "");

    return $sales_space;
}

?>
