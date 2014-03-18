<?php
/**
*
* Global system settings container
*/

class Settings {
    //*******************************************************//
    //****** Next options have to be set for proper work ****//
    //*******************************************************//

    //  Database IP or hostname (use colon, if runs on non-standard port: 127.0.0.1:563)
    const DB_HOST = "127.0.0.1";
    //  Username to connect to DB
    const DB_USER = "root";
    //  Password to connect to DB
    const DB_PASSWORD = "123456";
    //  Name of DB
    const DB_DBNAME = "vendorstack";
    const VS_HOST = "http://www.vendorstack.com";

    const POPUP_WIDTH = 500;
    const POPUP_HEIGHT = 700;
    const POPUP_LEFT = 700;
    const POPUP_TOP = 0;

    const CLEAN_DATA_TIMEOUT = 10800; // in sec
    const AJAX_REFRESH_PAGE_TIMEOUT = 5; // in sec

    //  Database type. Supported values are "pgsql" and "mysql"
    const DB_TYPE = "mysql";

    const DEV_MODE = true;
    const USE_SESSION_CACHE = true;

    const CSS_REFRESH_IP = '192.168.56.101';

    public static $DELIMITER_CSV = ',';

    public static $ERASE_STRINGS = array(", Incorporated", "Incorporated",
                                        "Company", ".com",
                                        ", Co.", "Co.", ", Co ", " Co ",
                                        ", The", "The ",
                                        ", Inc.", ", Inc", "Inc.", "Inc",
                                        ", GmbH", "GmbH",
                                        ".net", ".org",
                                        ", Ltd.", ", Ltd", "Ltd.", "Ltd",
                                        ", LLP", "LLP",
                                        ", LLC", "LLC",
                                        ", plc.", ", plc", "plc.", "plc",
                                        ", S.A.", ", SA", "SA", "S.A.",
                                        " .");

    const CURL_TIMEOUT = 10;

    const GOOGLE_AJAX_SEARCH_URL = "http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=%s";
    const GOOGLE_SEARCH_URL_SIMPLE = "http://www.google.com/search?q=%s";

    const SLEEP_TIMEOUT = 5;
    const MAX_ATTEMPTS_COUNT = 1;

    const FIRST_SEARCH_METHOD = 'html';
    const SECOND_SEARCH_METHOD = 'ajax';

}
?>