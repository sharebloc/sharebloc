<?php

/**
 * Gate functions container
 */
class ALParser {

    const ENTERPRISE_TAG = 12;

    private static $max_vendor_id = 0;

    public static function getEmptyVendor() {
        $vendor = array();
        $vendor['id'] = '';
        $vendor['hidden'] = '';
        $vendor['name'] = '';
        $vendor['angellist_url'] = '';
        $vendor['logo_url'] = '';
        $vendor['thumb_url'] = '';
        $vendor['quality'] = '';
        $vendor['product_desc'] = '';
        $vendor['high_concept'] = '';
        $vendor['follower_count'] = '';
        $vendor['company_url'] = '';
        $vendor['created_at'] = '';
        $vendor['twitter_url'] = '';
        $vendor['blog_url'] = '';
        $vendor['video_url'] = '';
        $vendor['markets'] = '';
        $vendor['locations'] = '';
        $vendor['crunchbase_url'] = '';
        $vendor['company_type'] = '';
        $vendor['follower_count'] = '';
        return $vendor;

    }

    public function toArray($object) {
        $vars = get_object_vars($object);
        $array = array ();
        foreach ( $vars as $key => $value ) {
            $array [ltrim ( $key, '_' )] = $value;
        }
        return $array;
    }

    // https://angel.co/api/spec/GET/startups/GET/startups/search
    public static function getVendorsByTag($tag_id, $page=0, $order = "desc") {
        $temp_file = "/opt/dslabs/trillium/vendorstack/data_entry/log/bear_$page.txt";
        $vendors = array();
        $url = sprintf("https://api.angel.co/1/tags/%d/startups?page=%d&order=%s",
                            $tag_id,
                            $page,
                            $order);
        //$url = $temp_file;

        $json_data = file_get_contents($url);
        // file_put_contents($temp_file, $json_data);
        if ($json_data) {
            $vendors_raw = json_decode($json_data);
        }

        foreach ($vendors_raw->startups as $data) {
            $data = (array)$data;
            $vendor = self::getEmptyVendor();
            $array_fields = array('markets', 'locations');

            if ($data['hidden']) {
                $vendor['id'] = $data['id'];
                $vendor['hidden'] = 1;
                $vendors[] = $vendor;
                continue;
            }

            foreach ($vendor as $key=>$value) {
                if (in_array($key, $array_fields)) {
                    continue;
                }
                $vendor[$key] = $data[$key];
            }

            foreach ($data['markets'] as $temp) {
                $temp = (array)$temp;
                $market = array();
                $market['id'] = $temp['id'];
                $market['tag_type'] = $temp['tag_type'];
                $market['name'] = $temp['name'];
                $market['display_name'] = $temp['display_name'];
                $vendor['markets'][] = $market;
            }
            foreach ($data['locations'] as $temp) {
                $temp = (array)$temp;
                $vendor['locations'][] = $temp['display_name'];
            }

            $vendors[] = $vendor;
        }
        return $vendors;
    }

    public static function saveVendor($vendor) {
        $sql = sprintf("INSERT INTO data_al_vendors (al_id, hidden, name, created_at, raw_data)
                    VALUES (%d, %d, %s, %s, %s)",
                    $vendor['id'],
                    $vendor['hidden'] ? 1 : 0,
                    $vendor['hidden'] ? DBUtils::sqlSafe('') : DBUtils::sqlSafe($vendor['name']),
                    $vendor['hidden'] ? DBUtils::sqlSafe('') : DBUtils::sqlSafe($vendor['created_at']),
                    DBUtils::sqlSafe(serialize($vendor)));
        return DBUtils::executeQuery($sql);
    }
    public static function getVendorById($id) {
        $sql = sprintf("SELECT * FROM data_al_vendors
                    WHERE al_id = %d",
                    $id);
        return DBUtils::executeQueryArray($sql, true);
    }
    public static function getMaxVendorId() {
        $max_id = 0;
        $sql = sprintf("SELECT max(al_id) max_id FROM data_al_vendors");
        $result = DBUtils::executeQueryArray($sql, true);
        if ($result) {
            $max_id = $result['max_id'];
        }
        return $max_id;
    }

    public static function saveNewVendor($vendor) {
        $db_vendor = ALParser::getVendorById($vendor['id']);
        if ($db_vendor) {
            if (!$db_vendor['hidden']) {
                echo(sprintf("Skipping vendor (not hidden) %d %s - we just have it<br>",
                                $db_vendor['al_id'],
                                $db_vendor['name']));
                return;
            }
            if ($db_vendor['hidden'] && !$vendor['hidden']) {
                // todo should update
                echo(sprintf("Skipping vendor (hidden) %d %s - we just have it<br>",
                                $db_vendor['al_id'],
                                $db_vendor['name']));
                return;
            }
        }

        ALParser::saveVendor($vendor);
        echo(sprintf("Vendor %d %s saved.<br>",
                        $vendor['id'],
                        $vendor['hidden'] ? '(hidden)' : $vendor['name']));
    }

    public static function parseNewVendors() {
        self::$max_vendor_id = ALParser::getMaxVendorId();
        $start_page = 1;
        $end_page = 2;
        $current_page = $start_page;
        while (true) {
            if ($current_page > $start_page) {
                Utils::sleep(Settings::SLEEP_TIMEOUT);
            }

            echo(sprintf("Will parse page #%d<br>", $current_page));
            $vendors = ALParser::getVendorsByTag(ALParser::ENTERPRISE_TAG, $current_page, 'desc');

            foreach ($vendors  as $vendor) {
                if ($vendor['id'] <= self::$max_vendor_id) {
                    echo("We got all of the newest AL vendors, exiting.<br>");
                    // should stop after this data portion
                    $current_page = $end_page;
                    break;
                }
                $save_result = self::saveNewVendor($vendor);
            }
            $current_page++;

            if ($save_result=='got_old') {
                break;
            }
            if (!$vendors) {
                break;
            }
            if ($current_page > $end_page) {
                break;
            }
        }
        return;
    }
}




?>