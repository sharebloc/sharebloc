<?php
/**
* Global system settings container
* @since 28 May 2013
* @author bear@vendorstack.com
*/

class Settings {
    const DB_HOST = "10.55.31.194";
    const DB_USER = "web";
    const DB_PASSWORD = "af8ajf81jd9";
    const DB_DBNAME = "vendorstack";

    const CACHE_MEMCACHE_PORT = 11211;
    const CACHE_MEMCACHE_LIMIT = 1000;

    const LINKEDIN_API_KEY = "u75uhmcou7zp";

    /* Weekly values for the front page */
    // you can use both IDs and codenames for selecting companies and vendors
    public static $FRONT_VENDOR_IDS_REAL = array('LeanData', 'ActiveCampaign', 'GoodData');
    const FRONT_THIS_WEEK_BLOG_TAG = "Lead Farming Vendors";
    const FRONT_THIS_WEEK_BLOG_URL = "http://blog.vendorstack.com/2013/06/18/vendorstack-presents-lead-farming-three-steps-to-grow-leads/";

	public static $HYBRYD_AUTH_CONFIG = array(
        "base_url" => "http://cws.tril2.trillium.lan:8989/ext_auth.php?hybrid=1",

		"providers" => array (
			"Google" => array (
				"enabled" => true,
				"keys"    => array ( "id" => "", "secret" => "" ),
			),

            "Facebook" => array (
                "enabled" => true,
                "keys" => array ( "id" => "", "secret" => "" )
             ),

            "Twitter" => array (
             "enabled" => true,
             "keys" => array ( "key" => "", "secret" => "" )
            ),

			"LinkedIn" => array (
				"enabled" => true,
				"keys"    => array ( "key" => "", "secret" => "" )
			),
		),

		// if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
		"debug_mode" => false,

		"debug_file" => "",
	);

    /* Long queries logging settings */
    const REQUEST_TIMEOUT_INFO = 500;
    const REQUEST_TIMEOUT_WARN = 3000;
    const REQUEST_TIMEOUT_ERROR = 5000;

    const SHOW_BETA_BORDER = false;

    const DEV_MODE = false;
}
?>