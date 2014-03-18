<?php

require_once("../include/simplehtmldom_1_5/simple_html_dom.php");

Class Parser {
    private static $html = null;

    public static function init($page_code) {
        self::$html = str_get_html($page_code);
    }

    public static function parseProfileData($profile, $body, $json=false) {
        $parsed_data = array();
        if (!$json) {
            self::init($body);
        }

        switch ($profile) {
            case 'linkedin':
                $parsed_data = self::parseLinkedIn();
                break;
            case 'facebook':
                if ($json) {
                    $parsed_data = self::parseFacebookFromJson($body);
                } else {
                    $parsed_data = self::parseFacebook();
                }
                break;
            case 'twitter':
                $parsed_data = self::parseTwitter();
                break;
            case 'crunchbase':
                $parsed_data = self::parseCrunchbase();
                break;
            case 'angel':
                $parsed_data = self::parseAngel();
                break;
        }
        $parsed_data['city'] = preg_replace("/\s+/", " ", $parsed_data['city']);
        $parsed_data['country'] = preg_replace("/\s+/", " ", $parsed_data['country']);

        return $parsed_data;
    }

    private static function getTextValueBySelector($selector) {
        $element = self::$html->find($selector, 0);
        if (!$element) {
            Log::$logger->warn("Parse error. Element doesn't exist. Selector - $selector");
            return '';
        }
        return html_entity_decode(trim($element->plaintext), ENT_QUOTES, 'UTF-8');
    }

    private static function getElementBySelector($selector, $id=0) {
        $element = self::$html->find($selector, $id);
        if (!$element) {
            Log::$logger->warn("Parse error. Element doesn't exist. Selector - $selector");
            return '';
        }
        return $element;
    }

    private static function getImgSourceBySelector($selector) {
        $img = self::$html->find($selector, 0);
        if (!$img) {
            Log::$logger->warn("Parse error. Element doesn't exist. Selector - $selector");
            return '';
        }
        return trim($img->src);
    }

    private static function parseFirstSearchedLink($body) {
        Log::$logger->debug("Google search results parsing (simple_html_dom)");
        Log::$logger->trace("body presents");
        self::init($body);
        $first_link = self::getTextValueBySelector('#ires ol li.g div.s div.kv cite');

        if ($first_link) {
            $crawled_url = Utils::addHttpPrefixIfNeeded($first_link);
            return $crawled_url;
        }

        return '';
    }

    private static function simpleParseFromHttpByTags($body) {
        $open = "<cite>";
        $close = "</cite>";
        $from = strpos($body, $open);
        $to = strpos($body, $close);
        if ($from === false || $to === false) {
            return '';
        }
        $website_start = $from + strlen($open);
        $website = trim(strip_tags(substr($body, $website_start, $to - $website_start)));

        if ($website) {
            $crawled_url = Utils::addHttpPrefixIfNeeded($website);
            return $crawled_url;
        }

        return '';
    }

    public static function parseUrlFromJson($body) {

        Log::$logger->trace("body presents");
        $crawled_url = null;

        $json_data = json_decode($body);

        if ($json_data->responseStatus == 200) {
            if (count($json_data->responseData->results)) {
                $crawled_url = trim($json_data->responseData->results[0]->url);
            } else {
                Log::$logger->warn("Response status=" . $json_data->responseStatus . " No results in the google answer");
                Log::$logger->debug($json_data);
            }
        } else {
            Log::$logger->warn("Response status=" . $json_data->responseStatus . " Maybe banned by Google. Retry after sleep");
            Log::$logger->debug($json_data);
        }

        return $crawled_url;
    }

    public static function parseUrlFromHtml($body) {

        $website = self::parseFirstSearchedLink($body);

        if (!$website) {
            $website = self::simpleParseFromHttpByTags($body);
        }

        return $website;
    }

    private static function parseLinkedIn() {
        Log::$logger->trace("start parseLinkedIn");
        $parsed_data = Utils::getEmptyData();

        $parsed_data['logo'] = self::getImgSourceBySelector('img.logo');
        $locality = self::getTextValueBySelector('span.locality');
        $locality = rtrim($locality, ',');  // to remove "," after the city name
        $region = self::getTextValueBySelector('abbr.region');
        $country = self::getTextValueBySelector('span.country-name');
        $parsed_data['city'] = $locality;
        $parsed_data['country'] = ($country=="United States") ? $region : $country;
        $parsed_data['description'] = self::getTextValueBySelector('div.text-logo p');

        $basic_info_element = self::getElementBySelector('div.basic-info div[class=content inner-mod] dl');
        if (!$basic_info_element) {
            return $parsed_data;
        }
        $info = $basic_info_element->children();
        if (!$info) {
            return $parsed_data;
        }
        foreach ($info as $element) {
            $prev_element = $element->prev_sibling();
            if (!$prev_element) {
                continue;
            }
            if (trim($prev_element->plaintext) == 'Company Size') {
                $company_size = html_entity_decode(trim($element->plaintext), ENT_QUOTES, 'UTF-8');
                if ($company_size) {
                    $parsed_data['size'] = trim(str_replace('employees', '', $company_size));
                }
            }
            if (trim($prev_element->plaintext) == 'Industry') {
                $parsed_data['industry'] = html_entity_decode(trim($element->plaintext), ENT_QUOTES, 'UTF-8');
            }
            if ($parsed_data['size'] && $parsed_data['industry']) {
                break;
            }
        }

        return $parsed_data;
    }

    private static function parseFacebook() {
        Log::$logger->trace("start parseFacebook");
        $parsed_data = Utils::getEmptyData();
        //$parsed_data['logo'] = self::getImgSourceBySelector('img[class=profilePic img]');
        $parsed_data['logo'] = self::getImgSourceBySelector('img[class=imgCrop img]');
        $parsed_data['city'] = self::getTextValueBySelector('div[class=acw apm] table tr td div.mfsm span.mfsm');

        $details = self::getElementBySelector('div[class=acw apm]', 1);
        if ($details) {
            $description = $details->find('table tr td div.mfsm', 1);
            if ($description) {
                $parsed_data['description'] = html_entity_decode(trim($description->plaintext), ENT_QUOTES, 'UTF-8');
            }
        }
        return $parsed_data;
    }

    private static function parseTwitter() {
        Log::$logger->trace("start parseTwitter");
        $parsed_data = Utils::getEmptyData();

        $parsed_data['logo'] = self::getImgSourceBySelector('img[class=avatar size73]');

        $location = self::getTextValueBySelector('span[class=location profile-field]');
        if ($location) {
            $city_country = explode(",", $location);
            if (count($city_country)==2) {
            $parsed_data['city'] = trim($city_country[0]);
            $parsed_data['country'] = trim($city_country[1]);
            } else {
                $parsed_data['city'] = $location;
            }
        }
        $parsed_data['description'] = self::getTextValueBySelector('p[class=bio profile-field]');

        return $parsed_data;
    }

    private static function parseCrunchbase() {
        Log::$logger->trace("start parseCrunchbase");
        $parsed_data = Utils::getEmptyData();

        $parsed_data['logo'] = self::getImgSourceBySelector('#company_logo a img');
        $parsed_data['city'] = self::getTextValueBySelector('div.col1_office_address');

        $whole_descr  = '';
        $first = true;
        $descr_div = self::getElementBySelector('#col2_internal');
        if (!$descr_div) {
            $parsed_data['description'] = $whole_descr;
            return $parsed_data;
        }

        $all_blocks = $descr_div->children();
        if (!$all_blocks) {
            $parsed_data['description'] = $whole_descr;
            return $parsed_data;
        }
        foreach ($all_blocks as $element) {
            if($element->tag == 'p') {
                if (!$first) {
                    $whole_descr .= "\n" . trim($element->plaintext);
                } else {
                    $whole_descr .= trim($element->plaintext);
                }
                $first = false;
            } elseif ($element->tag == 'h1') {
                continue;
            } else {
                break;
            }
        }
        $parsed_data['description'] = html_entity_decode($whole_descr, ENT_QUOTES, 'UTF-8');

        return $parsed_data;
    }

    private static function parseAngel() {
        Log::$logger->trace("start parseAngel");
        $parsed_data = Utils::getEmptyData();

        $parsed_data['logo'] = self::getImgSourceBySelector('div[class=logo g-profile_avatar] img');
        $location = self::getTextValueBySelector('a.location-tag');
        $parsed_data['city'] = $location;
        $parsed_data['description'] = self::getTextValueBySelector('#product div[class=section content]');

        return $parsed_data;
    }

    private static function parseFacebookFromJson($body) {
        Log::$logger->trace("start parseFacebookFromJson");
        $parsed_data = Utils::getEmptyData();

        $data_array = json_decode($body);
        Log::$logger->info($data_array);

        if (!$data_array) {
            return $parsed_data;
        }

        if (!empty($data_array->company_overview)) {
            $parsed_data['description'] = $data_array->company_overview;
        } elseif (!empty($data_array->about)) {
            $parsed_data['description'] = $data_array->about;
        }

        if ($data_array->location) {
            if (!empty($data_array->location->city)) {
                $parsed_data['city'] = $data_array->location->city;
            }

            if (!empty($data_array->location->country)) {
                if ($data_array->location->country == 'United States' && !empty($data_array->location->state)) {
                    $parsed_data['country'] = $data_array->location->state;
                } else {
                    $parsed_data['country'] = $data_array->location->country;
                }
            }
        }

        return $parsed_data;
    }

    public static function parseImgSourceFromHtml($html) {
        if (!$html) {
            Log::$logger->debug("Empty html");
            return '';
        }
        $profile_img_code = '<img class="profilePic img" src="';
        $img_code_str_len = strlen($profile_img_code);

        $start_img_position = strpos($html, $profile_img_code);
        if($start_img_position === false) {
            Log::$logger->debug("No image in the html");
            return '';
        }

        $img_src_and_page_end = substr($html, $start_img_position+$img_code_str_len);
        $img_source = substr($img_src_and_page_end, 0, strpos($img_src_and_page_end, '"'));

        Log::$logger->debug("Found link: $img_source");

        if ($img_source && get_headers($img_source)) {
            return $img_source;
        } else {
            Log::$logger->warn("get_headers error for image URL: $img_source");
            return '';
        }
    }
}
?>
