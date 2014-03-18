<?php
// todo remove this file

require_once('../includes/global.inc.php');
require_once('class.Vendor.php');
require_once('class.Logo.php');

$code = get_input('code');

if (!$code) {
    display_404();
}

$parts     = explode(".", $code);
$code_name_with_size = $parts[0];

$code_name_with_size_parts = explode('_', $code_name_with_size);
$size = end($code_name_with_size_parts);
$code_name = str_replace("_" . $size, '', $code_name_with_size);

if ($size!=='thumb' && $size!=='full' && $size!=='src') {
    Log::$logger->warn("Unknown image size, code = " . $code);
    display_404();
}

if (!$code_name) {
    showBlankImage($size);
    exit;
}

$logo = new Logo(null, $code_name);
if (!$logo->is_loaded()) {
    showBlankImage($size);
    exit;
}

$logo_file = $logo->get_data($size . '_file');

if (!file_exists($logo_file)) {
    showBlankImage($size);
    exit;
}

$info = getimagesize($logo_file);
header('Content-Type: ' . $info['mime']);
readfile($logo_file);
exit();

function showBlankImage($size) {
    header('Content-Type: image/jpeg');
    if ($size == 'thumb') {
        $logo_file = DOCUMENT_ROOT . "/html/images/notfound.jpg";
        readfile($logo_file);
    } else {
        $image = imageCreateTrueColor(100, 30);
        $color_gray  = imagecolorallocate($image, 200, 200, 200);
        imagefill($image, 0, 0, $color_gray);
        imagejpeg($image);
    }
}
?>
