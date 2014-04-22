<?php
/* WARN! FrontStream::init() is called at the file end. */

require_once('class.PostedLink.php');
require_once('class.Question.php');

class FrontStream {
    const POSTS_ON_PAGE = 20;
    const DECAY_TIME_CONSTANT_SECONDS = 45000; // 12.5 hours * 60 * 60

    // https://vendorstack.atlassian.net/browse/VEN-199
    const DECAY_VOTES_MULTIPLIER = 7; // should be corrected experimentally after trying on live

    static $no_more_content = 0;
    static $allowed_orders = array('date', 'rating');
    static $allowed_post_types = array('question', 'posted_link');
    static $allowed_filters = array('all', 'question', 'posted_link');
    static $allowed_feed_types = array('all', 'user', 'vendor', 'tag', 'feed', 'tag_top', 'tag_top_weekly', 'feed_weekly', 'contest', 'contest_all');

    static $saved_options = array();
    static $order = 'rating';
    static $type_filter = 'all';
    static $tags_filter_enabled = false;
    static $tags_filter = array();

    /* This vars are used as some search types can implicitly modify filters set by user.
     * So filters set by user are never changed implicitly, but this vars are changed */
    static $tags_filter_applied = array();
    static $type_filter_applied = 'all';

    static $current_vote_multiplier = self::DECAY_VOTES_MULTIPLIER;

    private static $raw_parameters = array();
    private static $feed_type = 'all';
    private static $entity_id = 0;
    private static $for_rss = false;

    static function init($options = array()) {
        self::restoreSavedOptions($options);

        self::initOrder();
        self::initTagsFilterOn();
        self::initTypeFilter();
        self::initTagsFilter();

        if (!$options) {
            self::storeOptions();
        }
    }

    static function restoreSavedOptions($options = array()) {
        if (!$options && isset($_SESSION['front_page_params'])) {
            $options = $_SESSION['front_page_params'];
        }

        if ($options) {
            self::$order = isset($options['order']) ? $options['order'] : self::$order;
            self::$tags_filter_enabled = isset($options['tags_filter_enabled']) ? $options['tags_filter_enabled'] : self::$tags_filter_enabled;
            self::$type_filter = isset($options['type_filter']) ? $options['type_filter'] : self::$type_filter;
        }

        $feed_tags_str = User::getUserFeedTags();
        self::$tags_filter = json_decode($feed_tags_str);
    }

    static function storeOptions() {
        $_SESSION['front_page_params'] = array();
        $_SESSION['front_page_params']['order'] = self::$order;
        $_SESSION['front_page_params']['tags_filter_enabled'] = self::$tags_filter_enabled;
        $_SESSION['front_page_params']['type_filter'] = self::$type_filter;

        self::saveFeedTags();
    }

    static function saveFeedTags() {
        $user_id = get_user_id();
        if (!$user_id) {
            return;
        }
        $feed_tags_str = json_encode(self::$tags_filter);
        $current_feed_str = User::getUserFeedTags();
        if ($feed_tags_str === $current_feed_str) {
            return false;
        }
        $user = new User($user_id);
        $user->set(array('feed_tags' => $feed_tags_str));
        $user->save();
    }

    static function initOrder() {
        $order = get_input("order");
        if (!$order || !in_array($order, self::$allowed_orders)) {
            return;
        }
        self::$order = $order;
    }

    static function initTagsFilterOn() {
        $filter_enabled = get_input("tags_filter_enabled");
        if (!strlen($filter_enabled)) {
            return;
        }
        self::$tags_filter_enabled = $filter_enabled ? 1 : 0;
    }

    static function initTypeFilter() {
        $type_filter = get_input("type_filter");
        if (!$type_filter || !in_array($type_filter, self::$allowed_filters)) {
            return;
        }
        self::$type_filter = $type_filter;
    }

    static function initTagsFilter() {
        $tag_id = intval(get_input("remove_tag_from_filter"));
        if ($tag_id) {
            self::removeTag($tag_id);
            // no other actions needed
            return;
        }

        if (!get_input("tags_filter_update")) {
            return;
        }

        $tags_filter = get_input("tags_filter");
        if (!$tags_filter) {
            // filter was cleared and submitted
            $tags_filter = array();
        }

        self::$tags_filter = array_keys($tags_filter);
     }

    static function initAppliedFilters() {
        self::$tags_filter_applied = self::$tags_filter;
        self::$type_filter_applied = self::$type_filter;

        if (self::$feed_type=='tag' || self::$feed_type=='tag_top' || self::$feed_type=='tag_top_weekly') {
            self::$tags_filter_applied[] = self::$entity_id;

            if (self::$feed_type=='tag_top' || self::$feed_type=='tag_top_weekly') {
                self::$type_filter_applied = 'all';
            }
        } elseif (self::$feed_type=='contest' || self::$feed_type=='contest_all') {
            self::$type_filter_applied = 'contest';
        }
     }

    static function removeTag($tag_id) {
        $key = array_search($tag_id, self::$tags_filter);
        if ($key!==false) {
            array_splice(self::$tags_filter, $key, 1);
        }
    }

    static function getContent($limit = self::POSTS_ON_PAGE, $offset = 0, $feed_parameters=array()) {
        global $db;
        $content = array();

        self::$raw_parameters = $feed_parameters;

        if (is_admin() && Utils::reqParam('vote_multiplier')) {
            self::$current_vote_multiplier = Utils::reqParam('vote_multiplier');
        }

        if ($feed_parameters && in_array($feed_parameters['type'], FrontStream::$allowed_feed_types)) {
            self::$feed_type = $feed_parameters['type'];
            if (!empty($feed_parameters['id'])) {
                self::$entity_id = $feed_parameters['id'];
            }
            if (!empty($feed_parameters['for_rss'])) {
                self::$for_rss = $feed_parameters['for_rss'];
            }
        }

        if (self::$feed_type=='feed' && Utils::sVar('no_feed')) {
            self::$feed_type = 'all';
        }

        self::initAppliedFilters();

        if (self::$feed_type=='feed_weekly' || self::$feed_type=='tag_top_weekly') {
            $sql_select_part = self::getSqlForTopLikeReddit();
            $order_sql = "ORDER BY vote DESC, posts.date_added DESC";
        } elseif (self::$feed_type=='contest_all' || self::$feed_type=='contest') {
            if (self::$order == 'date') {
                $sql_select_part = self::getSqlForRecent(true);
                $order_sql = "ORDER BY posts.date_added DESC";
            } else {
                $sql_select_part = self::getSqlForSimpleTop();
                $order_sql = "ORDER BY vote DESC, posts.date_added ASC";
            }
        } elseif (self::$order == 'rating' || self::$feed_type=='tag_top' || self::$for_rss) {
            $sql_select_part = self::getSqlForTopLikeReddit();
            $order_sql = sprintf("ORDER BY ((time_passed*vote_sign)/%d + LOG(10, vote_abs)*%d) DESC, posts.date_added DESC",
                                    self::DECAY_TIME_CONSTANT_SECONDS,
                                    self::$current_vote_multiplier);
        } elseif (self::$order == 'date') {
            $sql_select_part = self::getSqlForRecent(true);
            $order_sql = "ORDER BY posts.date_added DESC";
        } else {
            $sql_select_part = self::getSqlForRecent(true);
            $order_sql = "ORDER BY posts.date_added DESC";
        }

        $where_part = self::getWherePart();

        $joins_ar = array();
        $joins_part = 'LEFT JOIN user ON user.user_id = posts.user_id';
        if ($joins_ar) {
            $joins_part = sprintf("%s", implode(" \n", $joins_ar));
        }

        // to determine if we have shown all the content
        $limit = $limit + 1;
        $sql = sprintf("%s
                        %s
                        %s
                        %s
                        LIMIT %d,%d",
                        $sql_select_part,
                        $joins_part,
                        $where_part,
                        $order_sql,
                        $offset,
                        $limit);

        if (Settings::DEV_MODE && false) {
            /* to debug rating algorithm */
            $debug_sql = sprintf("select ((time_passed*vote_sign)/45000 + LOG(10, vote_abs)*5) as rating,
                            (time_passed*vote_sign)/45000 as a, LOG(10, vote_abs) as b, vote_abs, %d as vm,
                            date_added FROM (%s) as zz",
                            self::$current_vote_multiplier,
                            $sql);
            e($debug_sql);
            //exit;
        }

        if (get_user_id()===951 && Utils::reqParam('show_query')) {
            echo("<pre>" . $sql . "</pre>");
        }

        $results = $db->query($sql);

        if (count($results) < $limit) {
            self::$no_more_content = 1;
        } else {
            // as we got limit+1 rows
            array_pop($results);
        }

        $counter = $offset;
        foreach ($results as $result) {
            $entity = self::prepareOnePost($result['post_id'], $result['post_type']);
            $entity['entity_number'] = ++$counter;
            if ($entity) {
                $content[] = $entity;
            }
        }

        return $content;
    }

    private static function getSqlForRecent($wrapper_query = true) {
        $subqueries = array();
        $subqueries['posted_link'] = "SELECT post_id, 'posted_link' AS post_type, date_added, 0 as vendor_id, user_id FROM posted_link WHERE f_contest=0\n";
        $subqueries['question'] = "SELECT question_id as post_id, 'question' AS post_type, date_added, 0 as vendor_id, user_id  FROM question\n";

        if (self::$type_filter_applied=='contest') {
            $subqueries['contest'] = "SELECT post_id, 'posted_link' AS post_type, date_added, 0 as vendor_id, user_id FROM posted_link WHERE f_contest=".self::$raw_parameters['contest_id']."\n";
        }

        // todo review and maybe simplify
        $use_subqueries = array();
        if (self::$type_filter_applied=='all') {
            $use_subqueries = $subqueries;
        } else {
            $use_subqueries[] = $subqueries[self::$type_filter_applied];
        }

        $sql_select_part = implode("UNION\n", $use_subqueries);

        if ($wrapper_query) {
            $sql_select_part = sprintf("SELECT * FROM
                                        (%s
                                         ) as posts",
                                        $sql_select_part);
        }

        return $sql_select_part;
    }

    private static function getSqlForTopLikeReddit() {
        $FIRST_POST_TS = '27.09.2012 1:11:43';
        $recent_part = self::getSqlForRecent(false);
        $votes_part = self::getSqlForVotes();

        $sql = sprintf("
        SELECT posts.*, COALESCE(user.status, 0) as user_status, COALESCE(votes_total, 0) as vote,
                        UNIX_TIMESTAMP(posts.date_added) - UNIX_TIMESTAMP('%s') as time_passed,
                        GREATEST(COALESCE(votes_total, 0), 1) as vote_abs,
                        CASE
                            WHEN (isnull(votes_total)) THEN 0
                            WHEN (votes_total>0) THEN 1
                            WHEN (votes_total<0) THEN -1
                            ELSE 0
                        END AS vote_sign
        FROM
            (
                %s
            ) as posts
            LEFT JOIN
            (
                %s
            ) as votes ON votes.votes_post_type=posts.post_type AND votes.votes_post_id = posts.post_id",
        $FIRST_POST_TS,
        $recent_part,
        $votes_part);
        return $sql;
    }

    // very similar to getSqlForTopLikeReddit(), just does not contains time_passed, vote_abs and vote_sign columns
    // todo can be simply merged with getSqlForTopLikeReddit().
    private static function getSqlForSimpleTop() {
        $recent_part = self::getSqlForRecent(false);
        $votes_part = self::getSqlForVotes();

        $sql = sprintf("
        SELECT posts.*, COALESCE(votes_total, 0) as vote, COALESCE(user.status, 0) as user_status
        FROM
            (
                %s
            ) as posts
            LEFT JOIN
            (
                %s
            ) as votes ON votes.votes_post_type=posts.post_type AND votes.votes_post_id = posts.post_id",
        $recent_part,
        $votes_part);
        return $sql;
    }

    private static function getSqlForVotes() {
        if (self::$type_filter_applied=='all') {
            // todo get from constant
            $use_types = array("'posted_link'", "'question'");
        } else {
            $use_types[] = "'".self::$type_filter_applied."'";
        }

        if (self::$type_filter_applied=='contest') {
            // we use different votes table for contest
            $sql = sprintf("SELECT 'posted_link' AS votes_post_type,  v.post_id AS votes_post_id,
                                   COUNT(v.vote_id) AS votes_count, SUM(v.value) AS votes_total
                                   FROM vote_contest v
                                   GROUP BY v.post_id");
        } else {
            $sql = sprintf("SELECT v.entity_type AS votes_post_type,  v.entity_id AS votes_post_id,
                            COUNT(v.vote_id) AS votes_count, SUM(v.value) AS votes_total
                            FROM vote v
                            WHERE entity_type IN (%s)
                            GROUP BY v.entity_type, v.entity_id",
                            implode(',', $use_types));
        }

        return $sql;
    }

    /* Can be rewritten as left join relation in select part, but this needs groupping by or distinct on post_id+post_type.
     * Do not know what is better. May be should be tested when query begin slow.
     */
    private static function getWhereForVendors($vendors=array()) {
        if (!$vendors) {
            return '';
        }

        if (!is_array($vendors)) {
            $vendors = array($vendors);
        }

        $sql = sprintf("EXISTS
                    (SELECT 1 FROM relation rel
                     WHERE rel.entity_type=posts.post_type
                           AND rel.entity_id=posts.post_id
                           AND rel.vendor_id in (%s)
                     )",
                    implode(', ', $vendors));
        return $sql;
    }

    private static function getWhereForTags($tags=array()) {
        if (!$tags) {
            return '';
        }

        if (!is_array($tags)) {
            $tags = array($tags);
        }

        $sql = sprintf("
                    EXISTS
                    (SELECT tag_id FROM tag_selection ts
                     WHERE ts.f_explicit=1
                           AND ts.entity_type=post_type
                           AND ts.entity_id=post_id
                           AND ts.tag_id IN (%s)
                    )",
                implode(', ', $tags),
                implode(', ', $tags));
        return $sql;
    }

    private static function getWhereForRepost($user_ids) {
        $sql = sprintf("
                    EXISTS
                    (SELECT user_id FROM repost
                     WHERE repost.entity_type=post_type
                           AND repost.entity_id=post_id
                           AND repost.user_id IN (%s)
                    )",
                    implode(', ', $user_ids));
        return $sql;
    }

    private static function getWhereForUser() {
        self::$entity_id;

        $sql = sprintf("posts.user_id = %d OR %s",
                    self::$entity_id,
                    self::getWhereForRepost(array(self::$entity_id)));
        return $sql;
    }

    private static function getWhereForTagsFilter() {
        $sql = '';
        if (self::$tags_filter_enabled && self::$tags_filter) {
            $sql = self::getWhereForTags(self::$tags_filter);
        }

        return $sql;
    }

    private static function getWhereForFeed() {
        $sql = '';
        if (!is_logged_in()) {
            return $sql;
        }

        $where_arr = array();
        $user_follows = Utils::userData('following_by_entity_type');
        if ($user_follows['vendor']) {
            $where_arr[] = self::getWhereForVendors($user_follows['vendor']);
        }

        // to include all user's posts
        $user_follows['user'][] = get_user_id();
        $where_arr[] = sprintf("posts.user_id in (%s)",
                                implode(', ', $user_follows['user']));

        if ($user_follows['tag']) {
            $where_arr[] = self::getWhereForTags($user_follows['tag']);
        }

        $where_arr[] = self::getWhereForRepost($user_follows['user']);

        $sql = "(" . implode(' OR ', $where_arr) . ")";
        return $sql;
    }

    private static function getWhereForPageTypeFilter() {
        $sql = '';
        switch (self::$feed_type) {
            case 'user':
                $sql = self::getWhereForUser();
                break;
            case 'vendor':
                $sql = self::getWhereForVendors(self::$entity_id);
                break;
            case 'tag':
            case 'tag_top':
            case 'tag_top_weekly':
                $sql = self::getWhereForTags(self::$entity_id);
                break;
            case 'feed':
            case 'feed_weekly':
                $sql = self::getWhereForFeed();
                break;
            case 'contest_all':
                $sql = self::getWhereForTags(self::$entity_id);
                break;
            case 'tag-top':
            case 'contest':
        }

        return $sql;
    }

    private static function getWhereForInactive() {
        if (is_admin()) {
            return '';
        }

        $where = "(user.status IS NULL OR user.status!='inactive')";
        if (is_logged_in()) {
            $where = sprintf("(%s OR user.user_id=%d)", $where, get_user_id());
        }
        return $where;
    }

    private static function getWherePart() {
        $where_ar = array();
        $skip_filter_tags = false;

        if (self::$feed_type=='tag_top' || self::$feed_type=='tag_top_weekly') {
            $skip_filter_tags = true;
        } elseif (self::$feed_type=='tag') {
            $tag = new Tag(self::$entity_id);
            if ($tag->get_data('parent_tag_id')) {
                // disabling current tag filter for subtags pages
                $skip_filter_tags = true;
            }
        }

        if (self::$feed_type=='feed_weekly' || self::$feed_type=='tag_top_weekly') {
            $days = 7;
            if (self::$feed_type=='feed_weekly' && !empty(self::$raw_parameters['f_daily'])) {
                $days = 1;
            }
            $where_ar[] = sprintf("posts.date_added > (NOW() - INTERVAL %s DAY)", $days);
        }

        if (self::$for_rss) {
            $RSS_DAYS_COUNT = 3;
            if (Settings::DEV_MODE || Settings::SHOW_BETA_BORDER) {
                $RSS_DAYS_COUNT = 100;
            }
            $where_ar[] = sprintf("posts.date_added > (NOW() - INTERVAL %s DAY)", $RSS_DAYS_COUNT);
        }

        if (!$skip_filter_tags) {
            $where_part_tags = self::getWhereForTagsFilter();
            if ($where_part_tags) {
                $where_ar[] = $where_part_tags;
            }
        }

        $where_part_page_type_filters = self::getWhereForPageTypeFilter();
        if ($where_part_page_type_filters) {
            $where_ar[] = $where_part_page_type_filters;
        }

        $where_part_for_inactive = self::getWhereForInactive();
        if ($where_part_for_inactive) {
            $where_ar[] = $where_part_for_inactive;
        }

        $where_part = '';
        if ($where_ar) {
            $where_part = sprintf("\nWHERE %s", implode("\nAND ", $where_ar));
        }

        return $where_part;
    }

    static function prepareOnePost($post_id, $post_type, $code_name = null) {
        $temp_entity = null;
        if (!$post_id && !$code_name) {
            return false;
        }

        switch ($post_type) {
            case 'posted_link':
                $temp_entity = new PostedLink($post_id, $code_name);
                if (!$post_id) {
                    $post_id = $temp_entity->get_data('post_id');
                }
                break;
            case 'question':
                $temp_entity = new Question($post_id, $code_name);
                if (!$post_id) {
                    $post_id = $temp_entity->get_data('question_id');
                }
                break;
        }

        // $temp_entity->recache();

        if (!$temp_entity->is_loaded()) {
            return false;
        }

        $temp_entity->load_comments();

        $entity_data = $temp_entity->get();

        $entity_data['post_type'] = $post_type;
        $entity_data['post_id']   = $post_id;

        $entity_data = self::preparePostData($entity_data);

        return $entity_data;
    }

    static function prepareDate($date) {
        /* Date */
        $format = "j M Y";
        $posted_ts = strtotime($date);
        if (date("Y", $posted_ts) === date("Y", time())) {
            $format = "j M";
        }
        return date($format, $posted_ts);
    }

    static function preparePostData($post_data) {
        $post_data['uid'] = $post_data['post_type']."_".$post_data['post_id'];

        $post_data['date'] = self::prepareDate($post_data['date_added']);

        if (!isset($post_data['vendor_list'])) {
            $post_data['vendor_list'] = array();
        }

        $post_data['comments_title'] = '';

        $post_data['is_sponsored'] = false;

        $post_data['f_my'] = false;
        if (is_logged_in() && $post_data['user_id'] == get_user_id() && !is_admin()) {
            $post_data['f_my'] = true;
        }

        $post_data['hide_downvote'] = false;
        if ($post_data['vote']['total'] < 1) {
            $post_data['hide_downvote'] = true;
        }

        $post_data['outer_link_full'] = '';
        $post_data['outer_link_host'] = '';

        $post_data['can_delete'] = false;
        if (is_admin() || (is_logged_in() && !empty($post_data['user']['user_id']) && $post_data['user']['user_id'] === get_user_id())) {
            $post_data['can_delete'] = true;
        }

        $post_data['title_url'] = $post_data['my_url'];

        if ($post_data['post_type']==='posted_link') {
            if (substr($post_data['url'], 0, 4) !== 'http') {
                $post_data['url'] = 'http://' . $post_data['url'];
            }

            $post_data['outer_link_full'] = $post_data['url'];
            $post_data['outer_link_host'] = Utils::getHostByUrl($post_data['url']);

            $post_data['comments_title'] = ($post_data['comment_count']==1) ? 'comment' : 'comments';
            if ($post_data['f_contest']) {
                $post_data['title_url'] = $post_data['outer_link_full'];
            } else {
                $post_data['title_url'] = $post_data['iframe_url'];
            }
        } elseif ($post_data['post_type']==='question') {
            // todo get question vendor etc
            $post_data['logo_url_thumb'] = '';
            $post_data['logo_url_full'] = '';
            $post_data['f_auto'] = 0;

            if ($post_data['question_title']) {
                // if this is a new-style question
                $post_data['text'] = $post_data['question_text'];
                $post_data['title'] = $post_data['question_title'];
            } else {
                // if this is an old-style question
                // todo make constant + review/simplify the code
                $post_data['text'] = '';
                $post_data['title'] = $post_data['question_text'];
            }

            $post_data['comments_title'] = ($post_data['comment_count']==1) ? 'answer' : 'answers';

            if (!$post_data['tag_list_details']) {
                foreach ($post_data['vendor_list'] as $question_vendor) {
                    $post_data['tag_list_details'] = array_merge($post_data['tag_list_details'], $question_vendor['tag_list_details']);
                }
            }
            //e($post_data);
        }

        $post_data['comment_list'] = self::prepareComments($post_data['comment_list']);

        // todo limit text to 500 chars
        // todo limit tiltle to 150 chars

        /* Tags */
        $post_data['categories'] = array();
        $post_data['subcategories'] = array();

        if (isset($post_data['tag_list_details'])) {
            foreach($post_data['tag_list_details'] as $tag) {
                if ($tag['parent_tag_id']) {
                    if (self::$tags_filter_enabled && self::$tags_filter_applied) {
                        if (in_array($tag['tag_id'], self::$tags_filter_applied)) {
                            $post_data['subcategories'][] = $tag;
                        }
                    } else {
                        $post_data['subcategories'][] = $tag;
                    }
                } else {
                    $post_data['categories'][] = $tag;
                }
            }
        }

        if (!empty($post_data['author_vendor_id'])) {
            $post_data['user'] = self::getVendorAsUser($post_data['author_vendor_id']);
        }

        $post_data['comments_authors'] = self::getCommentsAuthors($post_data['comment_list']);
        $post_data['comments_authors_limited'] = self::getLimitedCommentsAuthors($post_data['comments_authors']);

        if ($post_data['privacy']!='public' || !$post_data['user']) {
            if (!$post_data['user']) {
                Log::$logger->info("Post without an author, id = " . $post_data['post_type'] . $post_data['post_id']);
            }
            $post_data['user'] = BaseObject::getAnonymousUser();
        }

        $post_data['seo'] = self::getPostSeoData($post_data);

        $post_data['views'] = self::getViewsofPosts($post_data);

        return $post_data;
    }

    static function getCommentsAuthors($comments) {
        $users = array();

        if (!$comments) {
            return $users;
        }

        foreach ($comments as $comment) {
            $users[] = $comment['user'];
        }

        $users = Utils::usersArrayUnique($users);
        return $users;
    }

    static function getLimitedCommentsAuthors($users) {
        $MAX_USERS_COUNT = 14;
        $users = array_slice($users, 0, $MAX_USERS_COUNT);
        return $users;
    }

    static function getVendorAsUser($vendor_id) {
        $user = BaseObject::getAnonymousUser();

        $vendor = new Vendor($vendor_id);
        if (!$vendor->is_loaded()) {
            return $user;
        }

        $user['code_name'] = $vendor->get_data('code_name');
        $user['full_name'] = $vendor->get_data('vendor_name');
        $user['my_url'] = $vendor->get_data('my_url');
        $user['logo'] = $vendor->get_data('logo');

        $user['first_name'] = '';
        $user['last_name'] = '';
        $user['short_name'] = '';
        $user['about'] = '';
        $user['company'] = '';

        return $user;
    }

    static function getPostSeoData($post_data) {
        $seo = array();
        $seo['title'] = $post_data['title'];

        $seo['description'] = $post_data['text'];

        /* tags, vendors and vendors' tags */
        $seo['keywords'] = '';

        /* not needed for now - https://vendorstack.atlassian.net/browse/VEN-346
        $keywords = array();
        foreach ($post_data['categories'] as $category) {
            $keywords[$category['uid']] = $category['name'];
        }
        foreach ($post_data['subcategories'] as $category) {
            $keywords[$category['uid']] = $category['name'];
        }
        foreach ($post_data['vendor_list'] as $vendor) {
            $keywords[$vendor['uid']] = $vendor['vendor_name'];
            foreach ($vendor['tag_list_details'] as $tag) {
                $keywords[$tag['uid']] = $tag['name'];
            }
        }

        // recommended keywords count is < 10
        $keywords = array_slice($keywords, 0, 10);
        $seo['keywords'] = implode(', ', $keywords);
        */

        return $seo;
    }

    static function getViewsofPosts($post_data) {
        global $db;
        $views = array();
        $link_name = substr($post_data['title_url'], 0, 40);
        $sql = sprintf("
            SELECT COALESCE(count(id), 0) as views
            FROM track 
            WHERE script_name = '%s'",
            $link_name);
        $result = $db->query($sql);
        if(is_array($result)){
            $views = $result[0]['views'];
            return $views;
        }
        
        return $views;

    }

    static function prepareComments($comments) {
        foreach ($comments as &$comment) {
            $comment['date'] = self::prepareDate($comment['date_added']);

            $comment['f_my'] = false;
            if (is_logged_in() && $comment['user_id'] == get_user_id() && !is_admin()) {
                $comment['f_my'] = true;
            }

            $comment['hide_downvote'] = false;
            if ($comment['vote']['total'] < 1) {
                $comment['hide_downvote'] = true;
            }

            $comment['can_delete'] = false;
            if (is_admin() || (is_logged_in() && $comment['user']['user_id']===  get_user_id())) {
                $comment['can_delete'] = true;
            }

            if ($comment['privacy']!='public' || !$comment['user']) {
                if (!$comment['user']) {
                    Log::$logger->info("Comment without an author, id = " . $comment['comment_id']);
                }
                $comment['user'] = BaseObject::getAnonymousUser();
            }

        }
        $comments = self::sortComments($comments);
        return $comments;
    }

    static function sortComments($comments) {
        $sort_column = array();
        foreach ($comments as $key => $comment) {
            if (self::$order == 'date') {
                $sort_column[$key]  = $comment['comment_id'];
            } else {
                $sort_column[$key] = $comment['vote']['total'];
            }
        }
        array_multisort($sort_column, SORT_DESC, $comments);
        return $comments;
    }

    static function getPostsForMore($page_type, $entity_id, $offset) {
        $feed_parameters = array();
        $feed_parameters['type'] = $page_type;
        $feed_parameters['id'] = $entity_id;

        $content = FrontStream::getContent(FrontStream::POSTS_ON_PAGE, $offset, $feed_parameters);
        if (!$content) {
            return false;
        }

        if (self::$feed_type=='contest') {
            Utils::$smarty->assign('twitter_symbols_left', Utils::countTwitterSymbolsLeft());
        } else {
            Utils::$smarty->assign('twitter_symbols_left', Utils::countTwitterSymbolsLeft(" via @ShareBloc  "));
        }

        $html_divs = array();
        foreach ($content as $post) {
            Utils::$smarty->assign('post', $post);
            if (self::$feed_type=='contest') {
                $html_divs[] = Utils::$smarty->fetch('components/contest/contest_post.tpl');
            } elseif (self::$feed_type=='contest_all') {
                $html_divs[] = Utils::$smarty->fetch('components/contest/nomination_post.tpl');
            } else {
                $html_divs[] = Utils::$smarty->fetch('components/front/front_post.tpl');
            }
        }

        $result = array();
        $result['html_divs'] = $html_divs;
        $result['no_more_content'] = FrontStream::$no_more_content;
        $result['offset_for_next_query'] = $offset + FrontStream::POSTS_ON_PAGE;

        return $result;
    }

    static function setCommonSmartyParams($f_contest = false) {
        if ($f_contest) {
            $categories = Utils::getContestCategories();
        } else {
            $categories = Utils::getCategoriesInCustomOrder();
        }

        $smarty_params = array(
            'categories' => $categories,
            'all_tags' => Utils::$tags_list_vendor,
            'order' => FrontStream::$order,
            'type_filter' => FrontStream::$type_filter,
            'tags_filter' => FrontStream::$tags_filter,
            'tags_filter_enabled' => FrontStream::$tags_filter_enabled,
            'tags_filter_json' => json_encode(FrontStream::$tags_filter),
            'posts_on_page' => FrontStream::POSTS_ON_PAGE,
            'no_more_content' => FrontStream::$no_more_content,
        );

        Utils::$smarty->assign($smarty_params);
    }
}

if (!HELPER_SCRIPT) {
    FrontStream::init();
}