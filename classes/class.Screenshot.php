<?php

require_once('class.BaseObject.php');

class Screenshot extends BaseObject {

    protected $data;
    protected $fields;
    protected $primary_key   = 'screenshot_id';
    protected $secondary_key = 'screenshot_hash';
    protected $table_name    = 'screenshot';
    protected $required      = array();
    private $thumb_width;
    private $thumb_height;
    private $file_path;

    function Screenshot($screenshot_id = null, $screenshot_hash = null, $thumb_width = 270, $thumb_height = 180) {
        $this->thumb_width  = $thumb_width;
        $this->thumb_height = $thumb_height;
        $this->file_path    = Utils::getScreenshotsDir();

        parent::BaseObject($screenshot_id, $screenshot_hash);
    }

    function load($primary_id = null, $secondary_id = null) {
        parent::load($primary_id, $secondary_id);

        if (isset($this->data['screenshot_id'])) {
            $this->data['full_file']  = $this->get_file_path(null, 'full');
            $this->data['thumb_file'] = $this->get_file_path(null, 'thumb');
        }
    }

    function set($data) {
        $result = parent::set($data);

        if ($result == true) {
            $this->save_file($data['screenshot_hash'], $data['image_suffix'], $this->thumb_width, $this->thumb_height, true, "thumb");
            $this->save_file($data['screenshot_hash'], $data['image_suffix'], 1280, 1024, false, "full");
        }

        return $result;
    }

    function get_hash() {
        if (isset($this->data['screenshot_hash'])) {
            return $this->data['screenshot_hash'];
        } else {
            return null;
        }
    }

    function get_file_path($screenshot_hash = null, $size = 'thumb') {
        if (!$screenshot_hash && isset($this->data['screenshot_hash']))
            $screenshot_hash = $this->data['screenshot_hash'];

        if (!$screenshot_hash)
            return null;

        return $this->file_path . "/" . $screenshot_hash . "_" . $size . ".jpg";
    }

    function save_file($screenshot_hash, $image_suffix, $target_width, $target_height, $constrain = false, $sub_name = '') {
        $file_name = $this->file_path . "/" . $screenshot_hash;
        $new_file  = $this->file_path . "/" . $screenshot_hash . ( $sub_name ? "_" . $sub_name : "" ) . ".jpg";

        if (file_exists($file_name)) {
            list( $original_width, $original_height ) = getimagesize($file_name);

            if ($original_width > $original_height) {
                $new_image_width  = $target_width;
                $new_image_height = (int) (($target_width / $original_width) * $original_height);
            } else if ($original_width < $original_height) {
                $new_image_height = $target_height;
                $new_image_width  = (int) (($target_height / $original_height) * $original_width);
            } else {
                $new_image_width  = $target_width;
                $new_image_height = $target_height;
            }

            if ($constrain == true) {
                $offset_x = (int) (($target_width - $new_image_width) / 2);
                $offset_y = (int) (($target_height - $new_image_height) / 2);

                $destination_image = imageCreateTrueColor($this->thumb_width, $this->thumb_height);
            } else {
                $offset_x = 0;
                $offset_y = 0;

                $destination_image = imageCreateTrueColor($new_image_width, $new_image_height);
            }

            $color_white = imagecolorallocate($destination_image, 255, 255, 255);
            imagefill($destination_image, 0, 0, $color_white);
            imageAntiAlias($destination_image, true);

            if ($image_suffix == "jpg") {
                $source_image = imageCreateFromJpeg($file_name);
            } else if ($image_suffix == "gif")
                $source_image = imageCreateFromGif($file_name);
            else if ($image_suffix == "png")
                $source_image = imageCreateFromPng($file_name);
            else
                $source_image = imageCreate($file_name);

            imagecopyresampled($destination_image, $source_image, $offset_x, $offset_y, 0, 0, $new_image_width, $new_image_height, $original_width, $original_height);

            imagejpeg($destination_image, $new_file, 90);
        }
    }

    function crop_image($x, $y, $w, $h) {
        $targ_w       = $this->thumb_width;
        $targ_h       = $this->thumb_height;
        $jpeg_quality = 90;

        $src  = $this->file_path . "/" . $this->data['screenshot_hash'] . "_full.jpg";
        $dest = $this->file_path . "/" . $this->data['screenshot_hash'] . "_thumb.jpg";

        $img_r = imagecreatefromjpeg($src);
        $dst_r = ImageCreateTrueColor($targ_w, $targ_h);

        imagecopyresampled($dst_r, $img_r, 0, 0, $x, $y, $targ_w, $targ_h, $w, $h);
        imagejpeg($dst_r, $dest, $jpeg_quality);
    }

    function save() {
        parent::save();
        // to clear GenericLIst cache - see recache()
        $this->recache();
    }

    /**
     * Screenshots for vendor page are loaded as GenericLIst object, which can be cached.
     * So here we should clear cache for GenericLIst for this vendor.
     * The problem is that GenericLIst to cache is not used here (when saving one screenshot).
     * So now I hardcoded cache key value to correspond with GenericLIst() call for screenshots in vendor.php.
     * 25 March 2013, bear@deepshiftlabs.com
     */
    function recache() {
        parent::recache();

        global $cache;

        // for view cache
        $screenshots_limit      = 2;
        $generic_list_cache_key = array('screenshot', null,
        array('entity_id'   => $this->get_data('entity_id'), 'entity_type' => $this->get_data('entity_type')),
        null, null, null,
        array('rand()'),
        $screenshots_limit, 0, null);
        $cache->clear('GenericList', $generic_list_cache_key);

        // for edit cache
        $screenshots_limit      = 0;
        $generic_list_cache_key = array('screenshot', null,
        array('entity_id'   => $this->get_data('entity_id'), 'entity_type' => $this->get_data('entity_type')),
        null, null, null,
        array('rand()'),
        $screenshots_limit, 0, null);

        $cache->clear('GenericList', $generic_list_cache_key);
    }

    // 25 March 2013, bear@deepshiftlabs.com
    function delete() {
        parent::delete();

        if (isset($this->data['full_file']) && file_exists($this->data['full_file'])) {
            unlink($this->data['full_file']);
        }
        if (isset($this->data['thumb_file']) && file_exists($this->data['thumb_file'])) {
            unlink($this->data['thumb_file']);
        }
    }

}

?>