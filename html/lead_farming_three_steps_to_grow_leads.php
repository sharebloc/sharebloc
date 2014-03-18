<?php

require_once('../includes/global.inc.php');

$lead_farming_info = getFarmingInfo();

foreach ($lead_farming_info as $key => &$farming_column) {
    foreach ($farming_column as &$vendor_group) {
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
$template->assign('lead_farming_info', $lead_farming_info);
$template->display('pages/splash-farming.tpl');
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

function getFarmingInfo() {
    $farming_columns = array();

    $farming_columns[0] = array();

    /* IDENTIFY LEADS */
    $vendor_group = array();
    $vendors      = array();
    $vendors[0]   = array("vendor_code" => "datacom");
    $vendors[1]   = array("vendor_code" => "hoovers");
    $vendors[2]   = array("vendor_code" => "leandata");
    $vendors[3]   = array("vendor_code" => "linkedin");
    $vendors[4]   = array("vendor_code" => "mixrank");
    $vendors[5]   = array("vendor_code" => "netprospex");
    $vendors[6]   = array("vendor_code" => "toofr");

    $vendor_group['vendors']    = $vendors;
    $vendor_group['text_title'] = "IDENTIFY LEADS";
    $vendor_group['scroll_id']  = "identify";

    $farming_columns[0][] = $vendor_group;

    /* CONTEXTUALIZE LEADS */
    $vendors    = array();
    $vendors[0] = array("vendor_code" => "insideview");
    $vendors[1] = array("vendor_code" => "mintigo");
    $vendors[2] = array("vendor_code" => "peoplelinx");
    $vendors[3] = array("vendor_code" => "radius");

    $vendor_group['vendors']    = $vendors;
    $vendor_group['text_title'] = "CONTEXTUALIZE LEADS";
    $vendor_group['scroll_id']  = "context";

    $farming_columns[0][] = $vendor_group;

    /* E-MAIL MARKETING */
    $vendor_group = array();
    $vendors      = array();
    $vendors[0]   = array("vendor_code" => "activecampaign");
    $vendors[1]   = array("vendor_code" => "aweber");
    $vendors[2]   = array("vendor_code" => "campaign_monitor");
    $vendors[3]   = array("vendor_code" => "campaigner");
    $vendors[4]   = array("vendor_code" => "constant_contact");
    $vendors[5]   = array("vendor_code" => "contactually");
    $vendors[6]   = array("vendor_code" => "exacttarget");
    $vendors[7]   = array("vendor_code" => "icontact");
    $vendors[8]   = array("vendor_code" => "mailchimp");
    $vendors[9]   = array("vendor_code" => "movable_ink");
    $vendors[10]  = array("vendor_code" => "sendicate");
    $vendors[11]  = array("vendor_code" => "userfox");
    $vendors[12]  = array("vendor_code" => "verticalresponse");
    $vendors[13]  = array("vendor_code" => "yesmail");

    $vendor_group['vendors']    = $vendors;
    $vendor_group['text_title'] = "E-MAIL MARKETING";
    $vendor_group['scroll_id']  = "email";

    $farming_columns[1][] = $vendor_group;

    /* SALES CALL SOFTWARE */
    $vendor_group = array();
    $vendors      = array();
    $vendors[0]   = array("vendor_code" => "closeio");
    $vendors[1]   = array("vendor_code" => "insidesalescom");

    $vendor_group['vendors']    = $vendors;
    $vendor_group['text_title'] = "SALES CALL SOFTWARE";
    $vendor_group['scroll_id']  = "sales";

    $farming_columns[1][] = $vendor_group;

    /* OTHER ENGAGEMENT */
    $vendor_group = array();
    $vendors      = array();
    $vendors[1]   = array("vendor_code" => "ambassador");
    $vendors[2]   = array("vendor_code" => "engajer");
    $vendors[3]   = array("vendor_code" => "hubspot");

    $vendor_group['vendors']    = $vendors;
    $vendor_group['text_title'] = "OTHER ENGAGEMENT";
    $vendor_group['scroll_id']  = "other";

    $farming_columns[1][] = $vendor_group;

    /* LEAD ANALYTICS */
    $vendor_group = array();
    $vendors      = array();
    $vendors[0]   = array("vendor_code" => "agilone");
    $vendors[1]   = array("vendor_code" => "bizible");
    $vendors[2]   = array("vendor_code" => "gooddata");

    $vendor_group['vendors']    = $vendors;
    $vendor_group['text_title'] = "LEAD ANALYTICS";
    $vendor_group['scroll_id']  = "analyt";

    $farming_columns[2][] = $vendor_group;

    /* LEAD MANAGEMENT */
    $vendor_group = array();
    $vendors      = array();
    $vendors[0]   = array("vendor_code" => "eloqua");
    $vendors[1]   = array("vendor_code" => "infusionsoft");
    $vendors[2]   = array("vendor_code" => "marketfish");
    $vendors[3]   = array("vendor_code" => "marketo");
    $vendors[4]   = array("vendor_code" => "pardot");
    $vendors[5]   = array("vendor_code" => "silverpop");
    $vendors[6]   = array("vendor_code" => "thrivehive");

    $vendor_group['vendors']    = $vendors;
    $vendor_group['text_title'] = "LEAD MANAGEMENT";
    $vendor_group['scroll_id']  = "manage";

    $farming_columns[2][] = $vendor_group;

    return $farming_columns;
}

?>
