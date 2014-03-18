<?php

Class NetUtils {

    private static $ch = null;

    // public functions

    public static function curlInit() {
        self::$ch = curl_init();
        if (self::$ch) {
            Log::$logger->trace('curl obj created');
            self::curlBaseConfig();
            return true;
        } else {
            Log::$logger->error('curl obj creation error');
            return false;
        }
    }

    private static function curlSSLConfig() {
        curl_setopt(self::$ch, CURLOPT_SSLVERSION, 3);
        curl_setopt(self::$ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt(self::$ch, CURLOPT_SSL_VERIFYHOST, 0);
    }

    private static function curlBaseConfig() {
        curl_setopt(self::$ch, CURLOPT_VERBOSE, 1);
        curl_setopt(self::$ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(self::$ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt(self::$ch, CURLOPT_TIMEOUT, 10);
        self::curlSetUserAgent();
        self::curlSSLConfig();

        Log::$logger->trace("curl obj inited by default");
    }

    public static function curlSetUserAgent($useragent='mozilla') {
        if ($useragent == 'opera') {
            $user_agent = "Opera/9.80 (J2ME/MIDP; Opera Mini/4.2.14912/870; U; id) Presto/2.4.15";
        } else {
            $user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 6.0; ru; rv:1.9.2.13) ' .
                    'Gecko/20101203 Firefox/3.6.13 ( .NET CLR 3.5.30729)';
        }
        curl_setopt(self::$ch, CURLOPT_USERAGENT, $user_agent);
    }

    private static function curlExecQuery($debug = false) {

        $response = '';
        Log::$logger->debug("Sending curl query. URL " .
                curl_getinfo(self::$ch, CURLINFO_EFFECTIVE_URL));
        if ($debug) {
            return '';
        }

        $response = curl_exec(self::$ch);
        if (curl_errno(self::$ch)) {
            Log::$logger->info('curl error code: ' . curl_errno(self::$ch) . ' ' . curl_error(self::$ch));

            if (curl_errno(self::$ch) == 35) {
                Log::$logger->info("This code number means that problems are on the server side.
                        Restart the script later.");
            }

            return false;
        }
        /*
          if (!$response) {
          log::$logger->error("Empty response received on curl query, url = \n" .
          curl_getinfo(self::$ch, CURLINFO_EFFECTIVE_URL).'\n\n');
          }
         */
        //log::$logger->info('URL after ' . curl_getinfo(self::$ch, CURLINFO_EFFECTIVE_URL));

        return $response;
    }

    public static function getUrlContent($page) {

        if (!self::$ch) {
            if (!self::curlInit()) {
                return null;
            }
        }

        Log::$logger->debug("(Curl) Going to link $page");
        curl_setopt(self::$ch, CURLOPT_URL, $page);
        curl_setopt(self::$ch, CURLOPT_POST, false);

        $content = self::curlExecQuery();
        $status = curl_getinfo(self::$ch, CURLINFO_HTTP_CODE);

        if (substr($status, 0, 1) == '4' || substr($status, 0, 1) == '5') {
            Log::$logger->error("Error while getting page data. CURLINFO_HTTP_CODE - $status");
            return null;
        }

        return $content;
    }

    public static function curlTearDown() {
        curl_close(self::$ch);
    }

    public static function getBestImageFromPage($string) {
        $MIN_WIDTH_TO_FIT = 200;
        $MIN_HEIGHT_TO_FIT = 100;
        $images = self::getImagesFromHtmlString($string);;
        if (!$images) {
            return '';
        }

        foreach ($images as $image_url) {
            list($image_width, $image_height, $image_type) = getimagesize($image_url);
            if (!$image_width || !$image_height || !in_array($image_type, Logo::$allowed_image_types)) {
                continue;
            }

            if ($image_width < $MIN_WIDTH_TO_FIT || $image_height < $MIN_HEIGHT_TO_FIT) {
                continue;
            }
            return $image_url;
        }
        return '';
    }

    public static function getImagesFromHtmlString($string) {
        require_once("../includes/simplehtmldom_1_5/simple_html_dom.php");

        if (!$string) {
            return array();
        }

        $dom = str_get_html($string);
        if (!$dom) {
            Log::$logger->error("Error loading the DOM stuctute for getImagesFromHtmlString()");
            return array();
        }

        return self::getImagesFromPage($dom);
    }

    public static function getImagesFromPage($dom, $url_parts = array()) {
        $img_links = array();
        Log::$logger->trace("Saving SRC values from the page images");

        $img_elements = $dom->find('img');
        if (!$img_elements) {
            Log::$logger->info("There is no pictures on the page");
            return $img_links;
        }

        Log::$logger->trace("Saving SRC values from the page images");

        foreach ($img_elements as $img) {
            $src = trim($img->src);
            if (substr($src, 0, 4) !== 'http') {
                if (substr($src, 0, 2) == '//') {
                    if ($url_parts) {
                        $src = $url_parts['scheme'].":".$src;
                    } else {
                        $src = "http:".$src;
                    }
                } else {
                    if (!$url_parts) {
                        continue;
                    }
                    $src = ltrim($src, '/');
                    $src = $url_parts['scheme'] . '://' . $url_parts['host'] . '/' . $src;
                }
            }
            if (strpos($src, '?')) {
                $parts = explode('?', $src);
                $src = $parts[0];
            }

            $img_links[] = $src;
        }
        Log::$logger->debug("Total count of images: " . count($img_links));
        $img_links_unique = array_merge(array_unique($img_links));
        Log::$logger->debug("Unique images: " . count($img_links_unique));

        return $img_links_unique;
    }

    public static function getImgLinksAndH1TitleFromPage($url) {
        require_once("../includes/simplehtmldom_1_5/simple_html_dom.php");

        Log::$logger->debug("Function getImageLinksFromPage, url: $url");
        $page_data = array();
        $page_data['img_links'] = array();
        $page_data['h1_title'] = '';
        $page_data['article_text'] = '';

        if (substr($url, 0, 4) !== 'http') {
                $url = 'http://' . $url;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            Log::$logger->error("Error while filtering URL");
            return $page_data;
        }

        Log::$logger->trace("Check for URL correctness");
        $url_parts = parse_url($url);
        if (!$url_parts || empty($url_parts['host']) || strpos($url_parts['host'], '.') === false) {
            Log::$logger->warn("Error in the URL string");
            return $page_data;
        }
        if (empty($url_parts['scheme'])) {
            $url_parts['scheme'] = 'http';
        }

        Log::$logger->trace("Loading the DOM structure from the URL");

        $content = self::getUrlContent($url);
        if (!$content) {
            return $page_data;
        }

        $dom = str_get_html($content);
        if (!$dom) {
            Log::$logger->error("Error loading the DOM stuctute");
            return $page_data;
        }

        $h1_titles = $dom->find('h1');
        if (!$h1_titles) {
            Log::$logger->info("There is no H1 tag on the page");
        }
        else {
            $title = self::getTheLongestTitle($h1_titles);
            if (!$title) {
                Log::$logger->info("There is empty H1 tag on the page");
            } else {
                $page_data['h1_title'] = $title;
            }
        }

        // $page_data['article_text'] = self::getArticleTextFromPage($dom, $url);

        $page_data['img_links'] = self::getImagesFromPage($dom, $url_parts);
        return $page_data;
    }

    private static function getTheLongestTitle($h1_titles) {
        $title = '';
        foreach ($h1_titles as $h1) {
            $title_text = html_entity_decode(trim($h1->plaintext), ENT_QUOTES, 'UTF-8');
            $title_length = strlen($title_text);
            if ($title_length > strlen($title) && $title_length > 2) {
                $title = $title_text;
            }
        }

        return $title;
    }

    static function sortTexts($texts) {
        $sort_column = array();
        foreach ($texts as $key => $text) {
            $sort_column[$key] = $text['length'];
        }
        array_multisort($sort_column, SORT_DESC, $texts);
        return $texts;
    }

    private static function getArticleTextFromPage($dom, $url='') {
        $result = '';
        $MIN_ARTICLE_LENGHT = 140;
        $MIN_ARTICLE_DOTS = 3;
        $MAX_ARTICLE_LINKS_RATIO = 0.2;

        $divs = $dom->find('div');

        if (!$divs) {
            Log::$logger->info("There is no divs on the page");
            return $result;
        }

        $texts = array();

        foreach ($divs as $div) {
            $div_text = html_entity_decode(trim($div->text('div')), ENT_QUOTES, 'UTF-8');
            $length = strlen($div_text);

            if ($length < $MIN_ARTICLE_LENGHT) {
                continue;
            }

            // todo - storing all data in array even if div will be filtered out for easier debugging
            $text = array();
            $text['text'] = $div_text;
            $text['length'] = $length;
            $text['links_count'] = count($div->find('a'));
            $text['words_count'] = str_word_count($div_text);
            $text['dots_count'] = substr_count($div_text, '.');

            $text['links_ratio'] = 10;
            if ($text['words_count']) {
                $text['links_ratio'] = $text['links_count'] / $text['words_count'];
            }

            $text['dots_ratio'] = 0;
            if ($text['words_count']) {
                $text['dots_ratio'] = $text['dots_count'] / $text['words_count'];
            }

            if (!$text['words_count'] || $text['dots_count'] < $MIN_ARTICLE_DOTS || $text['links_ratio'] > $MAX_ARTICLE_LINKS_RATIO) {
                continue;
            }

            $texts[] = $text;
        }

        if (!$texts) {
            Log::$logger->info("Can't find article text for article for url $url");
            return $result;
        }

        $sorted = self::sortTexts($texts);
        $winner = reset($sorted);

        $result = preg_replace('/[ \f\t\v]{2,}/',' ',$winner['text']);
        return $result;
        /* todo remove this after method approving  */
//        foreach($sorted as $t) {
//            e(sprintf("<b>Div with length %d, %d links (%f ratio), %d dots (%f ratio)</b><br><br>%s<br><br>",
//                        $t['length'],
//                        $t['links_count'],
//                        $t['links_ratio'],
//                        $t['dots_count'],
//                        $t['dots_ratio'],
//                        nl2br(htmlentities($t['text']))));
//        }
    }

    public static function getUrlAfterRedirects($url) {
        $content = self::getUrlContent($url);
        if (!$content) {
            return $url;
        }

        $effective_url = curl_getinfo(self::$ch, CURLINFO_EFFECTIVE_URL);
        return $effective_url;
    }
}
