<?php

/**
 * This is a container for functions related to track
 * @copyright (C) 2013 ShareBloc
 * @author dima@vendorstack.com
 * @since 29 may 2013
 */
// todo bear - should replace error_log with normal logging
class Track {

    static $email_types = array('all'=>array('title'=>'All', 'types'=>array()),
                                'weekly'=>array('title'=>'Weekly Email', 'types'=>array('weekly_email')),
                                'subscriptions' => array('title'=>'Subscription Email', 'types'=>array('weekly_real_estate', 'weekly_sales__marketing', 'weekly_technology')),
                                'notify' => array('title'=>'Comment Email', 'types'=>array('notification')),
                                'Admin' => array('title'=>'Admin Email', 'types'=>array('confirm_subscription', 'invite_custom_sent','lost_password','password_changed','share_link', 'welcome', 'admin_message')));

    static $query_used = '';
    static $params = array();
    static $return_csv = false;

    public static $types      = array(
    array('display_name' => 'Page views', 'function'     => 'getPageViews', 'descr'        => 'Total page views for the period given', 'old'=>true),
    array('display_name' => 'Visits', 'function'     => 'getVisits', 'descr'        => 'Unique sessions for the period given. If one visitor will visit 3 pages, then closes his browser and opens it again on VS, this will be counted as 2 visits.', 'old'=>true),
    array('display_name' => 'Visits stats', 'function'     => 'getVisitsStats', 'descr'        => 'Page views distribution for visits. How many sessions have this page views count.', 'old'=>true),
    array('display_name' => 'Uniques', 'function'     => 'getUniques', 'descr'        => 'Unique visitors', 'old'=>true),
    array('display_name' => 'Uniques stats', 'function'     => 'getUniquesStats', 'descr'        => 'Page views distribution for visitors. How many sessions have this page views count.', 'old'=>true),
    array('display_name' => 'Outbound', 'function'     => 'getOutbounds', 'descr'        => 'Outbound links stats', 'old'=>true),
    array('display_name' => 'Outbound for last 2 weeks', 'function'     => 'getLastOutbounds', 'descr'        => 'Outbound links for last 2 weeks', 'old'=>true),
    array('display_name' => 'Users activity', 'function'     => 'getUsersActivity', 'descr'        => 'Count of pages each user visited totally for the top 50 users.', 'old'=>true),
    array('display_name' => 'Popular pages', 'function'     => 'getPopularPages', 'descr'        => 'Top 50 popuplar pages.', 'old'=>true),
    array('display_name' => 'Popular companies', 'function'     => 'getPopularCompanies', 'descr'        => 'Top 50 popuplar companies.', 'old'=>true),
    array('display_name' => 'Bots page viewes', 'function'     => 'getBotsPageViews', 'descr'        => 'Total page views by bots for the period given', 'old'=>true),
    array('display_name' => 'Bots stats', 'function'     => 'getBotsStats', 'descr'        => 'Pages visited by each bot type', 'old'=>true),
    array('display_name' => 'Unsubscribe calls', 'function'     => 'getUnsubscribers', 'descr'        => 'Calls of unsubscribe script', 'old'=>true),
    array('display_name' => 'Last 200 signups', 'function'     => 'lastSignUps', 'descr'        => 'Last 200 signups ordered by date desc', 'old'=>true),
    array('display_name' => 'Last 200 opened emails', 'function'     => 'getLastOpenedEmails', 'descr'        => 'Last 200 opened emails', 'old'=>true),

    array('display_name' => 'ShareBloc join requests', 'function'     => 'getSBJoins', 'descr'        => 'Emails entered on SB launch page', 'old'=>true),
    array('display_name' => 'Signups stats for last 2 weeks', 'function'     => 'signupsStats', 'descr'        => 'Users signups per day', 'old'=>true),
    array('display_name' => 'Disable users', 'function'     => 'getAllUsersForDisable', 'descr'        => 'All users', 'old'=>true),
    array('display_name' => 'Disabled users', 'function'     => 'getDisabledUsers', 'descr'        => 'Disabled users', 'old'=>true),
    array('display_name' => 'Landing pages for join (slow!)', 'function'     => 'getJoinLandingPages', 'descr'        => 'Landing pages for last week which were followed with sign ups, groupped by popularity. Can be slow.', 'old'=>true, 'params'=>array('from', 'to')),
    array('display_name' => 'Suspended', 'function'     => 'getSuspendedUsers', 'descr'        => 'Suspended users', 'old'=>true, 'params'=>array()),

    array('display_name' => 'All users', 'function'     => 'getAllUsers', 'descr'        => 'All users', 'old'=>false),
    array('display_name' => 'All Subscriptions', 'function'     => 'getSubscriptions', 'descr'        => 'Non-user subscriptions for weekly bloc emails', 'old'=>false, 'params'=>array()),
    array('display_name' => 'Daily Email', 'function'     => 'getEmailOpenRates', 'descr'        => 'Emails open rates per day', 'old'=>false),
    array('display_name' => 'Weekly Summary of Top Posts', 'function'     => 'getWeeksLinks', 'descr'        => 'Weekly Summary of Top Posts', 'old'=>false),

    'getWeekTopPostStats' => array('display_name' => 'Posts stats', 'function' => 'getWeekTopPostStats', 'descr' => 'Week posts stats', 'old'=>false),
    );



    static function getTracks($type, $return_csv) {
        if (!key_exists($type, self::$types)) {
            return "Wrong type.";
        }

        self::$return_csv = $return_csv;

        if (!empty(self::$types[$type]['params'])) {
            self::$params = Utils::reqParam('params');
        }

        $function_name = self::$types[$type]['function'];

        $results               = array();
        $data = self::$function_name();

        if ($return_csv) {
            self::outputAsCsv($data);
            return;
        }

        Utils::$smarty->assign('data', $data);
        Utils::$smarty->assign('type', $function_name);
        Utils::$smarty->assign('params', self::$params);
        Utils::$smarty->assign('metric_id', $type);
        Utils::$smarty->assign('email_types', self::$email_types);
        $results['html'] = Utils::$smarty->fetch('components/admin/track_table.tpl');

        $results['type']       = $type;
        $results['descr']      = nl2br(self::$types[$type]['descr']);
        $results['query']      = nl2br(self::$query_used);
        return $results;
    }

    static private function outputAsCsv($data) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="export.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
         $fp = fopen('php://output', 'w');

         fputcsv($fp, array_keys(reset($data)));
         foreach ($data as $row) {
             fputcsv($fp, $row);
         }
         fclose($fp);
    }

    static private function getPageViews() {
        global $db;
        $sql = sprintf("SELECT COUNT(1) AS page_views
                        FROM track
                        WHERE f_bot=0");
        self::$query_used = $sql;

        $result = $db->query($sql);
        if (!$result) {
            return array();
        }

        return $result;
    }

    static private function getBotsPageViews() {
        global $db;
        $sql = sprintf("SELECT COUNT(1) AS page_views
                        FROM track
                        WHERE f_bot=1");
        self::$query_used = $sql;

        $result = $db->query($sql);
        if (!$result) {
            return array();
        }

        return $result;
    }

    static private function getBotsStats() {
        global $db;
        $sql = sprintf("SELECT agent AS user_agent, COUNT(1) AS page_views
                        FROM track WHERE f_bot=1
                        GROUP BY agent
                        ORDER BY COUNT(1) DESC");
        self::$query_used = $sql;

        $result = $db->query($sql);
        if (!$result) {
            return array();
        }

        return $result;
    }

    static private function getAllUsers() {
        global $db;
        $sql = sprintf("SELECT user_id, first_name, last_name, email, date_added,
                        IF(notify_weekly=1,'yes','') as `weekly email`,
                        IF(status='inactive','yes','') as suspended
                        FROM user
                        ORDER BY user_id");
        self::$query_used = $sql;

        $result = $db->query($sql);
        if (!$result) {
            return array();
        }

        return $result;
    }

    static private function getAllUsersForDisable() {
        global $db;
        $sql = sprintf("SELECT user_id, concat(first_name, last_name) as name, email, code_name as disable
                        FROM user
                        ORDER BY user_id");
        self::$query_used = $sql;

        $result = $db->query($sql);
        if (!$result) {
            return array();
        }

        return $result;
    }

    static private function getDisabledUsers() {
        global $db;
        $sql = sprintf("SELECT user_id, concat(first_name, last_name) as name, email, date(created_ts) as date
                        FROM disabled_users
                        ORDER BY created_ts DESC");
        self::$query_used = $sql;

        $result = $db->query($sql);
        if (!$result) {
            return array();
        }

        return $result;
    }

    static private function getVisits() {
        global $db;
        $sql = sprintf("SELECT COUNT(1) AS visits
                        FROM track
                        WHERE f_visit=1
                        AND f_bot=0");
        self::$query_used = $sql;

        $result = $db->query($sql);
        if (!$result) {
            return array();
        }

        return $result;
    }

    static private function getVisitsStats() {
        global $db;
        $sql = sprintf("SELECT page_views, COUNT(1) as visits
                        FROM (
                            SELECT session_id, COUNT(1) as page_views FROM track
                            WHERE f_bot=0
                            AND session_id IS NOT NULL
                            GROUP BY visit_id
                        ) counts_per_visit
                        GROUP BY page_views
                        ORDER BY COUNT(1) DESC");
        self::$query_used = $sql;

        $result = $db->query($sql);
        if (!$result) {
            return array();
        }

        return $result;
    }

    static private function getUniques() {
        global $db;
        $sql = sprintf("SELECT COUNT(DISTINCT visitor_id) AS uniques
                        FROM track
                        WHERE f_bot=0");
        self::$query_used = $sql;

        $result = $db->query($sql);
        if (!$result) {
            return array();
        }

        return $result;
    }

    static private function getUniquesStats() {
        global $db;
        $sql = sprintf("SELECT page_views, COUNT(1) as visitors
                        FROM (
                            SELECT session_id, COUNT(1) as page_views FROM track
                            WHERE f_bot=0
                            AND session_id IS NOT NULL
                            GROUP BY visitor_id
                        ) counts_per_visitor
                        GROUP BY page_views
                        ORDER BY COUNT(1) DESC");
        self::$query_used = $sql;

        $result = $db->query($sql);
        if (!$result) {
            return array();
        }

        return $result;
    }

    static private function getUsersActivity() {
        global $db;
        $sql = sprintf("SELECT visitor_id, concat(first_name, ' ', last_name) AS User, COUNT(1) AS page_views
                        FROM track
                        LEFT JOIN user on user.user_id=track.user_id
                        WHERE f_bot=0
                        GROUP BY track.user_id, visitor_id
                        ORDER BY COUNT(1) DESC
                        LIMIT 50");
        self::$query_used = $sql;

        $result = $db->query($sql);
        if (!$result) {
            return array();
        }

        return $result;
    }

    static private function getOutbounds() {
        global $db;
        $sql = sprintf("SELECT referrer AS from_url, url AS to_url, COUNT(1) AS count
                        FROM track
                        WHERE f_bot=0
                        AND url IS NOT NULL
                        AND referrer IS NOT NULL
                        AND target = 'out'
                        GROUP BY url, referrer
                        ORDER BY COUNT(1) DESC");
        self::$query_used = $sql;

        $result = $db->query($sql);
        if (!$result) {
            return array();
        }

        return $result;
    }

    static private function getLastOutbounds() {
        global $db;

        $ts = time() - 60*60*24*14;
        $date = date("Y-m-d", $ts);

        $sql = sprintf("SELECT url AS to_url, ts as date, user.email
                        FROM track
                        LEFT JOIN user ON user.user_id=track.user_id
                        WHERE f_bot=0
                        AND url IS NOT NULL
                        AND target = 'out'
                        AND ts > '%s'
                        ORDER BY id DESC",
                        $date);
        self::$query_used = $sql;

        $result = $db->query($sql);
        if (!$result) {
            return array();
        }

        return $result;
    }

    static private function getPopularPages() {
        global $db;
        $sql = sprintf("SELECT url, COUNT(1) AS page_views
                        FROM track
                        WHERE f_bot=0
                        GROUP BY url
                        ORDER BY COUNT(1) DESC
                        LIMIT 50");
        self::$query_used = $sql;

        $result = $db->query($sql);
        if (!$result) {
            return array();
        }

        return $result;
    }

    static private function getPopularCompanies() {
        global $db;
        $sql = sprintf("SELECT url, COUNT(1) AS page_views
                        FROM track
                        WHERE f_bot=0
                        AND url LIKE '/companies%%'
                        GROUP BY url
                        ORDER BY COUNT(1) DESC
                        LIMIT 50");
        self::$query_used = $sql;

        $result = $db->query($sql);
        if (!$result) {
            return array();
        }

        return $result;
    }

    static private function getSBJoins() {
        $sql = sprintf("SELECT id, email, created_ts, info
                        FROM join_requests
                        ORDER BY created_ts DESC");
        self::$query_used = $sql;

        $result = Database::execArray($sql);
        return $result;
    }

    static private function getUnsubscribers() {
        $sql = sprintf("SELECT visitor_id, user.user_id, email, ip, ts FROM track
                        LEFT JOIN user ON user.user_id=track.user_id
                        WHERE script_name = '/unsubscribe.php'
                        ORDER BY id");
        self::$query_used = $sql;

        $result = Database::execArray($sql);
        return $result;
    }

    static private function getEmailOpenRates() {
        $email_type = Utils::reqParam('subtype', 'all');

        $where = '';
        if ($email_type != 'all' && isset(self::$email_types[$email_type])) {
            $type_filter_arr = array();
            foreach (self::$email_types[$email_type]['types'] as $type) {
                $type_filter_arr[] = "'$type'";
            }

            $where = sprintf("type IN (%s)", implode(',', $type_filter_arr));
        }

        $sql = sprintf("SELECT DATE(sent_ts) date, COUNT(1) AS sent,
                            CONCAT( CAST(COALESCE(opens, 0)/COUNT(1)*100 as UNSIGNED), '%%') AS `open rate`,
                            COALESCE(opens, 0) AS opened
                FROM email_log
                LEFT JOIN (SELECT COUNT(1) opens, DATE(sent_ts) ts
                            FROM email_log
                            WHERE opens_count>0 %s
                            GROUP BY DATE(sent_ts)
                           ) AS opens ON opens.ts = DATE(email_log.sent_ts)
                %s
                GROUP BY DATE(sent_ts)
                ORDER BY DATE(sent_ts) DESC",
                $where ? "AND " . $where : '',
                $where ? "WHERE " . $where : '');
        self::$query_used = $sql;

        $result = Database::execArray($sql);
        return $result;
    }

    static private function getLastOpenedEmails() {
        $sql = "SELECT DATE(sent_ts) Date, email as Email, type as `Email type`, opens_count as `Opens count`, last_open_ts as 'Last opened'
                FROM email_log
                WHERE opens_count>0
                ORDER BY sent_ts DESC
                LIMIT 200";
        self::$query_used = $sql;

        $result = Database::execArray($sql);
        return $result;
    }


    static private function lastSignUps() {
        $sql = "select CONCAT(first_name, ' ', last_name) as Name, email as Email, date_added as Date
                from user
                where f_contest_voter = 0
                order by date_added desc
                limit 200;";
        self::$query_used = $sql;

        $result = Database::execArray($sql);
        return $result;
    }

    static private function signupsStats() {
        $ts = time() - 60*60*24*14;
        $date = date("Y-m-d", $ts);

        $sql = sprintf("select date(date_added), count(1) from user
                        where date_added > '%s'
                        AND f_contest_voter=0
                        group by date(date_added)
                        order by date(date_added) desc",
                        $date);
        self::$query_used = $sql;
        $result = Database::execArray($sql);
        return $result;
    }

    static private function getSubscriptions() {
        $sql = sprintf("select email, tag_name, date(created_ts) as `date_added`,
                        IF (confirmed_ts IS NOT NULL,'yes','') as `confirmed`,
                        IF (deleted_ts IS NOT NULL,'yes','') as `Unsubscribed`
                        from subscriptions s
                        join tag on tag.tag_id=s.tag_id
                        order by created_ts desc");
        self::$query_used = $sql;
        $result = Database::execArray($sql);
        return $result;
    }

    static private function getJoinLandingPages() {
        if (empty(self::$params['from'])) {
            $ts_from = time() - 60*60*24*7; // 2 months
            self::$params['from'] = date("Y-m-d", $ts_from);
        }
        if (empty(self::$params['to'])) {
            $ts_to = time(); // 2 months
            self::$params['to'] = date("Y-m-d", $ts_to);
        }

        $sql = sprintf("
SELECT url, count(1)
FROM track landing_page

JOIN (SELECT session_id FROM track WHERE `script_name` = '/JOIN.php'
		AND f_bot=0 AND user_id IS NULL
        AND ts > '%s' AND ts < '%s'
        GROUP BY session_id
    ) t_join ON t_join.session_id=landing_page.session_id
JOIN (SELECT session_id, user_id FROM track WHERE
		user_id IS NOT NULL
		AND ts > '%s' AND ts < '%s'
        GROUP BY session_id, user_id
    ) t_reg ON t_reg.session_id=landing_page.session_id

JOIN user ON user.`user_id`=t_reg.user_id
WHERE
landing_page.target IN ('direct', 'in')
AND landing_page.f_bot=0
AND landing_page.user_id IS NULL
AND landing_page.ts > '%s' AND ts < '%s'
AND landing_page.ts<user.date_added
-- AND landing_page.url!='/JOIN' AND landing_page.url!='/'
GROUP BY landing_page.url
ORDER BY COUNT(1) DESC
",
                self::$params['from'], self::$params['to'],
                self::$params['from'], self::$params['to'],
                self::$params['from'], self::$params['to']);
        self::$query_used = $sql;
        $result = Database::execArray($sql);
        return $result;
    }

    static private function getSuspendedUsers() {

        $sql = sprintf("SELECT user_id, first_name, last_name, email, date_added
                        FROM user
                        WHERE status='inactive'
                        ORDER BY user_id");
        self::$query_used = $sql;
        $result = Database::execArray($sql);
        return $result;
    }

    /* START OF FUNCTIONS TO GET TOP POSTS STATS BY WEEK */
    static public function getThisWeekStart($start_day = 7) {
        $today = strtotime(date('Y-m-d', time()));
        $today_weekday = date('N', $today);

        $week_start = $today;
        if ($today_weekday !== $start_day) {
            $shift = $start_day - $today_weekday;
            if ($shift > 0) {
                $shift = $shift - 7;
            }
            $week_start = $today + 60*60*24*$shift;
        }
        return $week_start;
    }

    static public function getWeekEndByWeekStart($week_start) {
        return $week_start + 60*60*24*7 - 1; // -1 to get last second of prev week
    }

    static private function getWeeksLinks() {
        $WEEKS_IN_LIST = 52;
        $weeks = array();

        $week_start = self::getThisWeekStart();
        $week_end = self::getWeekEndByWeekStart($week_start);

        $weeks[] = array('start'=>$week_start, 'end' => $week_end);
        for ($i=1; $i<$WEEKS_IN_LIST; $i++) {
            $week_start = $weeks[count($weeks)-1]['start'] - 60*60*24*7;
            $week_end = self::getWeekEndByWeekStart($week_start);
            $weeks[] = array('start'=>$week_start, 'end'=> $week_end);
        }

        $result = array();
        foreach ($weeks as $week) {
            $link = sprintf("<a href='#' data-type='getWeekTopPostStats' class='tracks_link' data-subtype='%s'>%s - %s</a>",
                    $week['start'],
                    date('d/m/Y', $week['start']),
                    date('d/m/Y', $week['end']));
            $link_csv = sprintf("<a href='%s/cmd.php?cmd=get_tracks&type=getWeekTopPostStats&subtype=%s&csv=1' target='_blank' class='tracks_link_csv'>Download</a>",
                    Utils::getBaseUrl(),
                    $week['start']);

            $result[] = array('Show stats'=>$link, 'csv'=>$link_csv);
        }

        return $result;
    }

    static public  function getPostsByWeek($week_start) {
        $week_end = self::getWeekEndByWeekStart($week_start);
        $sql = sprintf("SELECT post_id, code_name FROM posted_link
                        WHERE f_contest=0
                              AND date_added > '%s'
                              AND date_added < '%s'",
                        date('Y-m-d H:i:s', $week_start),
                        date('Y-m-d H:i:s', $week_end));
        $result = Database::execArray($sql);
        return $result;
    }

    // top posts by pageviews
    static public  function getTopPostsForWeek($week_start, $limit=null) {
        function sorterByPageViews($first, $second) {
            return $second['page_views'] - $first['page_views'];
        }

        $posts = self::getPostsByWeek($week_start);
        foreach ($posts as $key => $post) {
            $post_obj = new PostedLink($post['post_id']);
            if (!$post_obj->is_loaded()) {
                unset ($posts[$key]);
                continue;
            }

            $posts[$key]['post_obj'] = $post_obj;
            $posts[$key]['page_views'] = $post_obj->getPageViews();
        }

        usort($posts, "sorterByPageViews");

        $top_posts = array_slice($posts, 0, $limit);

        return $top_posts;
    }

    static public  function populateStatsForPosts($posts) {
        foreach ($posts as &$post) {
            $post_obj = $post['post_obj'];

            unset($post['post_obj']);
            unset($post['code_name']);

            $post_data = $post_obj->get();
            $post['Post Title'] = $post_data['title'];
            $post['URL'] = Utils::getBaseUrl() . $post_data['my_url'];
            if (!self::$return_csv) {
                $post['URL'] = sprintf("<a href='%s'>%s</a>", $post['URL'], $post['URL']);
            }

            if (!empty($post_data['author_vendor_id'])) {
                $post_data['user'] = FrontStream::getVendorAsUser($post_data['author_vendor_id']);
            }

            $post['Posted by'] = $post_data['user']['full_name'];
            $post['Date posted'] = $post_data['date_added'];

            $post['Bloc'] = '';
            foreach($post_data['tag_list_details'] as $tag) {
                if (!$tag['parent_tag_id']) {
                    $post['Bloc'] = $tag['tag_name'];
                }
            }

            $temp = $post['page_views'];
            unset($post['page_views']);
            $post['page_views'] = $temp;

            $post['Clickthroughs from Post Page / Feed'] = $post_obj->getClicksOnPostedUrl();
            $post['Clickthroughs from Post Page'] = $post_obj->getClicksOnPostedUrlsFromPostPage();
            $post['Clicks on share buttons'] = $post_obj->getClicksOnShareButtons();
        }

        return $posts;
    }

    static public  function getWeekTopPostStats() {
        require_once('class.PostedLink.php');
        require_once('class.FrontStream.php');

        $week_start = Utils::reqParam('subtype', self::getThisWeekStart());
        if (!$week_start) {
            return array();
        }

        $top_posts = self::getTopPostsForWeek($week_start, 20);
        $top_posts = self::populateStatsForPosts($top_posts);

        $week_end = self::getWeekEndByWeekStart($week_start);
        self::$query_used = sprintf("Top posts for week %s - %s",
                            date('d/m/Y', $week_start),
                            date('d/m/Y', $week_end));

        return $top_posts;
    }

    /* END OF FUNCTIONS TO GET TOP POSTS STATS BY WEEK */

}