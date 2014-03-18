<?php

/*
    deletes from the db links to not existed files and wise versa
*/
require_once('../includes/global.inc.php');
require_once('../classes/class.Question.php');
require_once('../classes/class.LinkUse.php');
require_once('../classes/class.Screenshot.php');
require_once('../classes/class.Logo.php');

if (!is_admin()) {
    return;
}

/* Backing up */

/* Copying table */
global $db;
global $last_vendor_id;

function msg($arr) {
    $t = '';
    if (is_array($arr)) {
        $t = print_r($arr, true);
    } else {
        $t = $arr;
    }

    $t = str_replace(" ", "&nbsp;", $t);
    $t = nl2br($t);
    $t .= "<br>";
    echo($t);
}


function removeDBRecords($test_only) {
    $logos = Database::execArray("select * from logo");
    foreach ($logos as $logo) {
        $logo_obj = new Logo($logo['logo_id']);

        $logo_file_t = $logo_obj->get_data('thumb_file');
        $logo_file_f = $logo_obj->get_data('full_file');

        $full_exists = file_exists($logo_file_f);
        $thumb_exists = file_exists($logo_file_t);

        if ($full_exists !==$thumb_exists) {
            $msg = " full_exists $full_exists !== $thumb_exists thumb_exists";
            Log::$logger->warn($msg);
            msg($msg);
        }

        if (!$full_exists || !$thumb_exists) {
            $msg = " will delete logo $logo_file_t and $logo_file_f";
            Log::$logger->warn($msg);
            msg($msg);
            if (!$test_only) {
                $logo_obj->delete();
            }
        }
    }
}

function getFiles($logo_dir) {
    $dh = opendir($logo_dir);
    $files = array();
    while (false !== ($file = readdir($dh))) {
        if ($file !== '.' && $file !== '..') {
            if ( is_dir($logo_dir.$file) ) {
                continue;
            } else {
                $files[] = $file;
            }
        } // <-- if
    } // <-- while
    closedir($dh);
    return $files;
}

function removeFiles($test_only) {
    $logo_dir = Utils::getLogosDir()."/";
    $files = getFiles($logo_dir);
    foreach ($files as $file) {
        $parts = explode('_thumb.jpg', $file);
        if (count($parts)<2) {
            $parts = explode('_full.jpg', $file);
        }
        if (count($parts)<2) {
            $msg = " will delete file $file as it has no standard suffixes";
            Log::$logger->warn($msg);
            msg($msg);
            if (!$test_only) {
                unlink($logo_dir.$file);
            }
            continue;
        }
        $code_name = $parts[0];
        $logo = new Logo(null, $code_name);
        if (!$logo->is_loaded()) {
            $msg = " will delete file $file as we can't load logo from DB by codename $code_name";
            Log::$logger->warn($msg);
            msg($msg);
            if (!$test_only) {
                unlink($logo_dir.$file);
            }
            continue;
        } else {
            $msg = " Found $file by codename $code_name";
            //msg($msg);
        }
    }
}

$test_only = true;
msg("Logo cleaner script started.");
set_time_limit(3000);
msg("Time limit changed to 3000 seconds.");

if (Utils::reqParam('apply')) {
    $test_only = false;
}
msg("TEST ONLY = $test_only");
removeDBRecords($test_only);
removeFiles($test_only);
msg("Done.");

exit();

?>