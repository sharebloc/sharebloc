<?php
/* Todo remove verbose logging after this will be stable */
/* Todo review and simplify */

require_once("class.NetUtils.php");

class Feed {
    const FEED_POSTS_COUNT = 40;
    const FEED_NOT_OLDER = 10;

    private static $test_mode = false;
    private static $crawl_limit = 3;
    private static $crawl_offset = 0;
    private static $autopost_feeds = array();

    public static $publish_posts = false;

    public static $BLOCS_WITH_RSS = array('Sales & Marketing', 'Technology', 'Real Estate');

    private static function prepareChannelData($channel_entity) {

        $channel_data = array();
        $channel_data['title'] = "ShareBloc - " . $channel_entity['name'];
        $channel_data['url'] = $channel_entity['my_url'];
        $channel_data['description'] = "The top news from ShareBloc on " . $channel_entity['name'];
        $channel_data['img_url'] = $channel_entity['logo']['my_url'];
        return $channel_data;
    }

    public static function getPostHTMLDescription($post) {
        Utils::$smarty->assign('post', $post);
        $html = Utils::$smarty->fetch('components/front/rss_post.tpl');
        return $html;
    }

    public static function outputRSS($channel_entity, $posts) {
        require_once("../includes/feedwriter/FeedTypes.php");

        $channel_data = self::prepareChannelData($channel_entity);

        $feed_writer = new RSS2FeedWriter();
        $feed_writer->setTitle($channel_data['title']);
        $feed_writer->setLink(Utils::getBaseUrl() . $channel_data['url']);
        $feed_writer->setDescription($channel_data['description']);

        //Image title and link must match with the 'title' and 'link' channel elements for valid RSS 2.0
        $feed_writer->setImage($channel_data['title'],
                                Utils::getBaseUrl() . $channel_data['url'],
                                Utils::getBaseUrl() . $channel_data['img_url']);

        foreach ($posts as $post) {
            $item = $feed_writer->createNewItem();
            $item->setTitle($post['title']);
            $item->setLink(Utils::getBaseUrl() . $post['my_url']);
            $item->setDate(strtotime($post['date_added']));
            // $item->setDescription($post['text']);
            $item->setDescription(self::getPostHTMLDescription($post));
            $feed_writer->addItem($item);
        }

        $feed_writer->generateFeed();
    }

    public static function getFeed($url) {
        $posts = array();

        require_once("../includes/SimplePie/autoloader.php");
        $feed = new SimplePie();
        $feed->set_feed_url($url);
        $feed->enable_cache(false);
        $success = $feed->init();

        if (!$success || $feed->error()) {
            return $posts;
        }

        $items = $feed->get_items();
        foreach ($items as $item) {
            $post = array();
            $post['title'] = $item->get_title();
            $post['raw_url'] = $item->get_permalink();
            $post['pubDate'] = $item->get_date('j M Y, g:i a');
            $post['text'] = $item->get_content();
            $post['image'] = NetUtils::getBestImageFromPage($post['text']);
            $posts[] = $post;
        }

        return $posts;
    }

    public static function getFeedsToParseFromRequest() {
        $feed = array();
        $feed['entity_type'] = 'user';
        if (Utils::reqParam('entity_type')=='vendor') {
            $feed['entity_type'] = 'vendor';
        }

        $feed['entity_id'] = Utils::reqParam('entity_id', 951);
        $feed['tag_id'] = '1';
        $feed['url'] = Utils::reqParam('rss_to_test', 'http://blog.sharebloc.com/rss');

        // defaults
        $feed['name'] = 'test_feed';
        $feed['allowed'] = 1;
        $feed['errors'] = array();
        $feed['published'] = array();
        $feed['skipped'] = array();
        $feed['posts'] = array();
        $feed['is_first_run'] = false;
        return array($feed);
    }

    private static function isFirstScriptRunForEntity($feed) {
        $author_id_field = 'user_id';
        if ($feed['entity_type']=='vendor') {
            $author_id_field = 'author_vendor_id';
        }

        $sql = sprintf("SELECT 1 FROM posted_link
                        WHERE f_auto=1 AND %s = %d",
                        $author_id_field,
                        $feed['entity_id']);

        $result = Database::execArray($sql, true);
        if ($result) {
            return false;
        } else {
            return true;
        }
    }

    public static function initAutopostFeeds() {
        if (self::$test_mode) {
            self::$autopost_feeds = self::getFeedsToParseFromRequest();
            return;
        }

        $sql = "SELECT 'user' as entity_type, user_id as entity_id, f_auto_allowed as allowed,
                autopost_tag_id as tag_id, rss as url, CONCAT(first_name, ' ', last_name) as name
                FROM user WHERE f_autopost=1 AND autopost_tag_id!=0 AND rss!=''

                UNION

                SELECT 'vendor' as entity_type, vendor_id as entity_id, 1 as allowed,
                autopost_tag_id as tag_id, rss as url, vendor_name as name
                FROM vendor WHERE f_autopost=1 AND autopost_tag_id!=0 AND rss!=''";

        self::$autopost_feeds = Database::execArray($sql);

        foreach (self::$autopost_feeds as &$feed) {
            $feed['errors'] = array();
            $feed['published'] = array();
            $feed['skipped'] = array();
            $feed['posts'] = array();
            $feed['is_first_run'] = self::isFirstScriptRunForEntity($feed);
        }
    }

    public static function publishPostsOnBehalfOfEntity(&$feed) {
        $limit = null;
        if (self::$crawl_limit) {
            $limit = self::$crawl_limit;
        }

        if ($feed['is_first_run']) {
            $limit = 1;
        }

        if (self::$crawl_offset || $limit) {
            e("<b>Will process ". ($limit ? $limit : "all") . " posts starting from " . self::$crawl_offset . "</b><br>");
        }

        $posts_to_process = array_slice($feed['posts'], self::$crawl_offset, $limit);

        foreach ($posts_to_process as $post) {
            $post['url'] = NetUtils::getUrlAfterRedirects($post['raw_url']);

            if ($post['url'] != $post['raw_url']) {
                Log::$logger->info("Url '".$post['raw_url']."' was changed to '".$post['url']."' due to redirects");
            }

            $existing_post = PostedLink::getPostedLinkByUrl($post['url']);
            if ($existing_post) {
                $msg = "DUPLICATE - skipping post by " . $feed['name']. " '" . $post['title'] . "', url: " . $post['url'];
                e($msg); Log::$logger->info($msg);
                $post['my_url'] = PostedLink::getUrlByCodeName($existing_post['code_name']);
                $post['post_id'] = $existing_post['post_id'];
                $feed['skipped'][] = $post;
                continue;
            }

            if (!self::$publish_posts) {
                $msg = "TEST mode - skipping post by " . $feed['name']. " '" . $post['title'] . "', url: " . $post['url'];
                e($msg); Log::$logger->info($msg);
                $feed['published'][] = $post;
                continue;
            }

            $post_obj = new PostedLink();
            $post_obj->publishPostFromRSS($feed, $post);

            if ($post_obj->get_data('post_id')) {
                $msg = "PUBLISHED - post id=" . $post_obj->get_data('post_id') . ", title: " . $post_obj->get_data('title');
                e($msg); Log::$logger->info($msg);
                echo("<a href='". $post_obj->get_data('my_url') ."' target='_blank'>". $post_obj->get_data('my_url') ."</a><br><br><br>");
                $post['my_url'] = $post_obj->get_data('my_url');
                $post['post_id'] = $post_obj->get_data('post_id');
                $post['logo_url_thumb'] = $post_obj->get_data('logo_url_thumb');
                $feed['published'][] = $post;
            } else {
                $msg = "Can't publish post, title " . $post['title'] . ", url: " . $post['url'] . ", feed name: " . $feed['name'];
                e($msg); Log::$logger->warn($msg);
                $feed['errors'][] = $msg;
            }
        }
    }

    public static function testRSSCrawler() {
        $rss_to_test = Utils::sVar('rss_to_test', 'http://blog.sharebloc.com/rss');
        $rss_to_test = Utils::reqParam('rss_to_test', $rss_to_test);
        $_SESSION['rss_to_test'] = $rss_to_test;

        $posts = array();
        if (Utils::reqParam('do_test')) {
            self::$test_mode = true;
            self::$crawl_limit = Utils::reqParam('crawl_limit', null);
            self::$crawl_offset = Utils::reqParam('crawl_offset', 0);
            if (Utils::reqParam('test_type')=='only_show') {
                $posts = self::getFeed($rss_to_test);
                $posts = array_slice($posts, self::$crawl_offset, self::$crawl_limit);
            }
        }

        Utils::$smarty->assign('rss_to_test', $rss_to_test);
        Utils::$smarty->assign('posts', $posts);
        Utils::$smarty->assign('is_live', Utils::isLive());

        Utils::$smarty->display('components/admin/rss_test.tpl');

        if (Utils::reqParam('do_test') && Utils::reqParam('test_type')=='post_on_behalf') {
            if (!Utils::isLive()) {
                self::$publish_posts = true;
            }
            self::crawlRSSFeeds();
        }
    }

    public static function autopostFeedsToText() {
        $text_arr = array();
        foreach (self::$autopost_feeds as $feed) {
            $text_arr[] = sprintf("Feed for %s %s (%d): %s%s",
                                $feed['entity_type'],
                                $feed['name'],
                                $feed['entity_id'],
                                $feed['url'],
                                $feed['allowed'] ? '' : ' (no rights, will skip)');
        }
        return implode("\n", $text_arr);
    }

    public static function crawlRSSFeeds() {
        $msg = "Will crawl RSS feeds. Will publish posts: " . (self::$publish_posts ? 'yes' : 'no');
        e($msg); Log::$logger->warn($msg);

        self::initAutopostFeeds();

        $msg = "Feeds to crawl: \n\n" . self::autopostFeedsToText();
        e($msg); Log::$logger->info($msg);
        echo("<hr><br>");

        $counter = 0;
        foreach (self::$autopost_feeds as &$feed) {
            if (!$feed['allowed']) {
                continue;
            }

            $counter++;
            $feed['posts'] = self::getFeed($feed['url']);

            $msg = "There are " .count($feed['posts']). " posts for feed " . $feed['url'];
            e($msg); Log::$logger->info($msg);

            self::publishPostsOnBehalfOfEntity($feed);

            $msg = "Parsed $counter/" . count(self::$autopost_feeds) . " feeds";
            e($msg); Log::$logger->info($msg);
            echo("<hr><br>");
        }
    }

    public static function getAutopostReport() {
        Utils::$smarty->assign('feeds', self::$autopost_feeds);
        Utils::$smarty->assign('publish_posts', self::$publish_posts);
        Utils::$smarty->assign('crawl_limit', self::$crawl_limit);
        $html = Utils::$smarty->fetch('components/admin/rss_autopost_report.tpl');
        return $html;
    }

    public static function validateRSSUrl($url) {
        if (!Utils::validate_url($url)) {
            return false;
        }

        if (!self::getFeed($url)) {
            return false;
        }

        return true;
    }
}

