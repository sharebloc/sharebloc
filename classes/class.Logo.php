<?php

require_once('class.BaseObject.php');

class Logo extends BaseObject {

    protected $data;
    protected $fields;
    protected $primary_key   = 'logo_id';
    protected $secondary_key = 'logo_hash';
    protected $table_name    = 'logo';
    protected $required      = array();
    private $type            = '';
    private $temp_file       = '';
    static public $allowed_image_types = array(IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_PNG);

    function Logo($logo_id = null, $logo_hash = null) {
        parent::BaseObject($logo_id, $logo_hash);
    }

    function load($primary_id = null, $secondary_id = null) {
        parent::load($primary_id, $secondary_id);

        if (isset($this->data['logo_id'])) {
            $this->data['full_file']  = $this->get_file_path(null, 'full');
            $this->data['thumb_file'] = $this->get_file_path(null, 'thumb');
            $this->data['src_file']   = '';
            $this->data['url_thumb']  = $this->get_url_thumb();
            $this->data['url_full']   = $this->get_url_full();
            $this->data['url_src']    = '';

            $src_file = $this->get_file_path(null, 'src');
            if (file_exists($src_file)) {
                // now we store src images, but old logos can be w/o src images.
                $this->data['src_file'] = $src_file;
                $this->data['url_src']  = $this->get_url_src();
            }
        } else {
            $this->data['url_thumb'] = '';
            $this->data['url_full']  = '';
            $this->data['url_src']   = '';
        }
    }

    function set($data) {
        $this->data['logo_hash'] = $data['logo_hash'];

        if (!$this->initImageFileProperties($data['temp_file'])) {
            return false;
        }

        $result = parent::set($this->data);

        if ($result == true) {
            $this->save_file("thumb");
            $this->save_file("full");
            $this->save_file("src");
        }

        return $result;
    }

    function initImageFileProperties($temp_file) {
        if (!file_exists($temp_file)) {
            Log::$logger->error("Can't find temp logo file when saving.");
            return false;
        }

        $this->temp_file = $temp_file;
        list($image_width, $image_height, $image_type) = getimagesize($temp_file);

        if (!$image_width || !$image_height) {
            Log::$logger->warn("Can't get logo dimensions when saving.");
            return false;
        }

        if (!in_array($image_type, self::$allowed_image_types)) {
            Log::$logger->error("Unsupported image type $image_type when saving");
            return false;
        }

        $this->data['width']  = $image_width;
        $this->data['height'] = $image_height;
        $this->type           = $image_type;

        return true;
    }

    function rename($new_logo_hash) {
        $result = false;

        $result = $this->rename_file($new_logo_hash, "thumb");
        $result = $result && $this->rename_file($new_logo_hash, "full");
        $this->rename_file($new_logo_hash, "src");

        if ($result) {
            $logo_data              = $this->get();
            $logo_data['logo_hash'] = $new_logo_hash;
            parent::set($logo_data);
            $this->save_data();
        } else {
            Log::$logger->error("Failed to rename logo to $new_logo_hash");
        }
        return $result;
    }

    function delete() {
        $this->delete_file("thumb");
        $this->delete_file("full");
        $this->delete_file("src");
        parent::delete();
    }

    function get_hash() {
        if (isset($this->data['logo_hash'])) {
            return $this->data['logo_hash'];
        } else {
            return null;
        }
    }

    static function getThumbUrlByCodename($code_name) {
        if (!$code_name) {
            return '';
        }
        return "/logos/" . $code_name . "_thumb.jpg";
    }

    static function getFullUrlByCodename($code_name) {
        if (!$code_name) {
            return '';
        }
        return "/logos/" . $code_name . "_full.jpg";
    }

    static function getSrcUrlByCodename($code_name) {
        if (!$code_name) {
            return '';
        }
        return "/logos/" . $code_name . "_src.jpg";
    }

    function get_url_thumb() {
        return $this::getThumbUrlByCodename($this->data['logo_hash']);
    }

    function get_url_full() {
        return $this::getFullUrlByCodename($this->data['logo_hash']);
    }

    function get_url_src() {
        return $this::getSrcUrlByCodename($this->data['logo_hash']);
    }

    function get_file_path($logo_hash = null, $size = 'thumb') {
        if (!$logo_hash && isset($this->data['logo_hash']))
            $logo_hash = $this->data['logo_hash'];

        if (!$logo_hash)
            return null;

        return Utils::getLogosDir() . "/" . $logo_hash . "_" . $size . ".jpg";
    }

    function rename_file($new_logo_hash, $sub_name = '') {
        $old_file_name = Utils::getLogosDir() . "/" . $this->get_data('logo_hash') . ( $sub_name ? "_" . $sub_name : "" ) . ".jpg";
        $new_file      = Utils::getLogosDir() . "/" . $new_logo_hash . ( $sub_name ? "_" . $sub_name : "" ) . ".jpg";

        if (!is_file($old_file_name)) {
            return false;
        }

        $result = rename($old_file_name, $new_file);
        return $result;
    }

    function delete_file($sub_name = '') {
        $file_name = Utils::getLogosDir() . "/" . $this->get_data('logo_hash') . ( $sub_name ? "_" . $sub_name : "" ) . ".jpg";
        if (!is_file($file_name)) {
            return true;
        }
        $result = unlink($file_name);
        return $result;
    }

    private function save_file($size_type) {
        $USE_PNG = false;
        $CROP    = true;

        $target_width  = 100;
        $target_height = 100;
        $width         = $this->data['width'];
        $height        = $this->data['height'];

        switch ($size_type) {
            case 'thumb':
                break;
            case 'full':
                $target_width  = 400;
                $target_height = 400;
                break;
            case 'src':
                break;
            default:
                return;
        }

        $new_file = Utils::getLogosDir() . "/" . $this->get_data('logo_hash') . "_" . $size_type . ".jpg";

        if ($size_type === 'src') {
            copy($this->temp_file, $new_file);
            return;
        }

        $original_ratio = $width / $height;
        $target_ratio   = $target_width / $target_height;

        $new_width  = $target_width;
        $new_height = $target_height;
        if ( ($original_ratio >= $target_ratio) == $CROP ) {
            $new_width = ($target_height / $height) * $width;
        } else {
            $new_height = ($target_width / $width) * $height;
        }

        $offset_x = round(($target_width - $new_width) / 2);
        $offset_y = round(($target_height - $new_height) / 2);

        $destination_image = imageCreateTrueColor($target_width, $target_height);
        $color = imagecolorallocate($destination_image, 255, 255, 255);

        if ($USE_PNG) {
            $color = imagecolorallocatealpha($destination_image, 0, 0, 0, 127);
            imagesavealpha($destination_image, true);
        }
        imagefill($destination_image, 0, 0, $color);
        imageAntiAlias($destination_image, true);

        switch ($this->type) {
            case IMAGETYPE_JPEG:
                $source_image = imageCreateFromJpeg($this->temp_file);
                break;
            case IMAGETYPE_PNG:
                $source_image = imageCreateFromPng($this->temp_file);
                break;
            case IMAGETYPE_GIF:
                $source_image = imageCreateFromGif($this->temp_file);
                break;
        }

        imagecopyresampled($destination_image, $source_image, $offset_x, $offset_y, 0, 0, $new_width, $new_height, $width, $height);

        if ($USE_PNG) {
            imagepng($destination_image, $new_file, 9);
        } else {
            imagejpeg($destination_image, $new_file, 90);
        }
    }

    static function testResampling() {
        if (!Settings::DEV_MODE) {
            return;
        }

        $url = 'http://std3.ru/2f/c2/1382965322-2fc2b2662742d9f19978ff17c80bb6d7.jpg';

        $user = new User(951);
        $user->saveLogoByUrl($url);
        $user->recache();
        $logo = new Logo($user->get_data('logo_id'));
        $logo = $logo->get();

        e("<a href='".$logo['url_thumb']."'>".$logo['url_thumb']."</a");
        e("<a href='".$logo['url_full']."'>".$logo['url_full']."</a");
        e("<a href='".$logo['url_src']."'>".$logo['url_src']."</a");

        echo("<img src='".$logo['url_thumb']."'>");
        echo("<img src='".$logo['url_full']."'>");
        echo("<img src='".$logo['url_src']."'>");

        //e("<a href='".$logo['my_url']."'>".$logo['my_url']."</a");
        //e("<a href='".$logo['my_url_thumb']."'>".$logo['my_url_thumb']."</a");
        //e("<a href='".$logo['my_url_src']."'>".$logo['my_url_src']."</a");

        exit;
    }

}

?>