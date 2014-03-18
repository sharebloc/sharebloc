<?php
/**
* Includes common files, defines some constants
*/
error_reporting(E_ALL);
ini_set('display_errors', '0');

define('DATA_ENTRY_ROOT_PATH', realpath(dirname(dirname(__FILE__))) );
set_include_path(get_include_path(). PATH_SEPARATOR . DATA_ENTRY_ROOT_PATH);

set_time_limit(120);

require_once "Settings.class.php";
require_once 'include/log4php/Logger.php';
require_once 'include/adodb/adodb.inc.php';
require_once 'include/adodb/adodb-exceptions.inc.php';
require_once 'include/adodb/adodb-errorhandler.inc.php';
require_once 'utils/Log.class.php';
require_once 'utils/Utils.class.php';
require_once 'utils/DBUtils.class.php';
require_once "include/smarty/Smarty.class.php";
?>
