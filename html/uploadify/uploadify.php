<?php
require_once('../../includes/global.inc.php');
require_once('class.Logo.php');
require_once('class.PostedLink.php');

require_once('class.Screenshot.php');

$allowed_file_types = array(IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_PNG);
$allowed_entities = array('vendor', 'company', 'user', 'tag', 'posted_link');

$image_type = get_input( 'image_type' );
$entity_id = get_input('entity_id');
$entity_type = get_input('entity_type');
$logo_id = null;

if (!empty($_FILES))
{
    $tmp_name = trim($_FILES["Filedata"]["tmp_name"]);
    if (file_exists($tmp_name)) {
        list($image_width, $image_height, $image_type) = getimagesize($tmp_name);
    }

    $debug_msg = '';
    if (!file_exists($tmp_name)) {
        $debug_msg = "Error during file upload.";
    } elseif (!in_array($entity_type, $allowed_entities) || ($entity_id && !is_numeric($entity_id))) {
        $debug_msg = "Invalid parameters.";
    } elseif ( !in_array( $image_type, Logo::$allowed_image_types ) ) {
        $debug_msg = "Invalid file type.";
    } elseif ( $image_width < 100 || $image_height < 100 ) {
        $debug_msg = "Image must be at least 100x100 pixels in size.";
    } elseif ( $_FILES['Filedata']['size'] < 10 ) {
        $debug_msg = "Image is too small.";
    } elseif ( $_FILES['Filedata']['size'] > 5000000 ){
        $debug_msg = "Image is too large.  Must be less than 5 MB.";
    } elseif (!is_uploaded_file($tmp_name)) {
        // as logo does not use move_uploaded_file, should check this
       $debug_msg = "Invalid file parameter.";
       Log::$logger->error('Possible file upload attack' . $tmp_name);
    }

    if ($debug_msg) {
        Log::$logger->warn($debug_msg);
        $data = array('err_msg'=>$debug_msg);
        echo json_encode($data);
        exit();
    }

    $entity_obj = null;

    if ($entity_id) {
        switch ($entity_type) {
            case 'user':
                $entity_obj = new User($entity_id);
                break;
            case 'vendor':
                $entity_obj = new Vendor($entity_id);
                break;
            case 'tag':
                $entity_obj = new Tag($entity_id);
                break;
            case 'posted_link':
                $entity_obj = new PostedLink($entity_id);
                break;
        }
    }

    if ($entity_id && $entity_obj && $entity_obj->is_loaded()) {
        $entity_code_name = $entity_obj->get_data('code_name');
    } else {
        $entity_code_name = md5(uniqid());
    }

    if( $image_type == "screenshot" ) {
        $size = array( 270, 180 );
        $screenshot = new Screenshot( null, null, $size[0], $size[1] );
        $code_name = $screenshot->generate_code_name($entity_code_name);

        $temp_file = Utils::getScreenshotsDir() . "/" . $code_name;
        move_uploaded_file($tmp_name, $temp_file );

        // todo for backward compatibility, as screenshot class was ot updated as logo class
        if (IMAGETYPE_JPEG === $image_type) {
            $extension = 'jpg';
        } elseif (IMAGETYPE_PNG === $image_type) {
            $extension = 'png';
        } elseif (IMAGETYPE_GIF === $image_type) {
            $extension = 'gif';
        }

        $data = array( 'screenshot_hash' => $code_name,
                       'width' => $image_width,
                       'height' => $image_height,
                       'entity_id' => $entity_id,
                       'entity_type' => $entity_type,
                       'image_suffix' => $extension
                     );

        $screenshot->set( $data );
        $screenshot->save();

        unlink( $temp_file );
    } else {
        if ($entity_id) {
            $code_name = $entity_code_name . $entity_obj::$logo_hash_suffix;
        } else {
            $code_name = Utils::sVar('not_finished_upload_logo', "_temp_" . md5(uniqid()));
            $_SESSION['not_finished_upload_logo'] = $code_name;
        }

        // if it's logo update, existing logo object will be loaded and updated
        $logo = new Logo(null, $code_name);

        $logo->set(array('logo_hash'=>$code_name, 'temp_file'=>$tmp_name));

        $logo->save();

        unlink($tmp_name);

        $logo_id = $logo->get_data('logo_id');

        if ($entity_id) {
            $entity_obj_data = $entity_obj->get();
            $entity_obj_data["logo_id"] = $logo_id;
            $entity_obj_data["image_upload"] = true;
            $entity_obj->set($entity_obj_data);
            $entity_obj->save_data();
        }
    }

    if ($entity_id) {
        $entity_obj->recache();
    }

    $data = array('code_name'=>$code_name, 'logo_id'=>$logo_id, 'my_url'=>$logo->get_data('url_thumb'), 'err_msg'=>'');

    echo json_encode($data);
}
?>