<?php

require_once('../includes/global.inc.php');
require_once('class.Vendor.php');
require_once('class.Screenshot.php');

$code_name = get_input('code');
$size      = "full";

if (!$code_name) {
    display_404();
}

$parts     = explode(".", $code_name);
$code_name = $parts[0];

if (strpos($code_name, "_thumb") !== false) {
    $size      = "thumb";
    $code_name = str_replace("_thumb", "", $code_name);
} else if (strpos($code_name, "_full") !== false) {
    $size      = "full";
    $code_name = str_replace("_full", "", $code_name);
}

$screenshot = null;

preg_match('/(.*)_(\d+)$/', $code_name, $matches);

$vendor_code_name = '';
$screenshot_id    = 0;

if (isset($matches[1]))
    $vendor_code_name = $matches[1];

if (isset($matches[1]))
    $screenshot_id = $matches[2];

if ($screenshot == null && strlen($vendor_code_name) > 0 && is_numeric($screenshot_id) && $screenshot_id > 0) {

    $vendor     = new Vendor(null, $vendor_code_name);
    $screenshot = new Screenshot($screenshot_id);

    if (!$vendor->is_loaded() || !$screenshot->is_loaded() || $vendor->get_data('vendor_id') != $screenshot->get_data('entity_id')) {
        $screenshot = null;
    }
}

if ($screenshot == null && strlen($code_name) == 32) {
    $screenshot = new Screenshot(null, $code_name);
}

if (isset($screenshot) && $size == 'thumb') {
    $screenshot_file = $screenshot->get_data('thumb_file');
} else if (isset($screenshot)) {
    $screenshot_file = $screenshot->get_data('full_file');
}

if (!isset($screenshot_file)) {
    display_404();
}

if (file_exists($screenshot_file)) {
    $source_image = imageCreateFromJpeg($screenshot_file);

    header('Content-Type: image/jpeg');
    imagejpeg($source_image);
} else {

    $blank_image = imageCreateTrueColor(100, 30);
    $color_gray  = imagecolorallocate($blank_image, 200, 200, 200);
    imagefill($blank_image, 0, 0, $color_gray);

    header('Content-Type: image/jpeg');
    imagejpeg($blank_image);
}

exit();
?>
