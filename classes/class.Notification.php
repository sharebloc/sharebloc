<?php

class Notification {
    const HEADER_NOTIFICATIONS_COUNT = 5;
    const SHOW_AUTHORS_COUNT = 2;
    const EMAIL_WEEK_DAYS_COUNT = 7;
    const EMAIL_ONE_DAY_COUNT = 1;

    static $notifications = array();
    static $post_views_count = array();

    /* Mailing list parameters */
    static $really_send_emails = false;
    static $mailing_users = array();
    static $test_mode = true;
    static $users_limit_str = '';
    static $mailing_list_type = '';
    static $start_user_id = 1;
    static $users_query = '';
    static $tag_id = 0;

    /* USUAL NOTIFICATIONS */
    public static function insertNotification($data) {
        global $db;
        $query = sprintf("INSERT INTO notifications
                                (post_type, post_id, comment_id, reason,
                                user_id, created_ts)
                                VALUES
                                ('%s', %d, %d, '%s',
                                %d, now())",
                                $data['post_type'],
                                $data['post_id'],
                                empty($data['comment_id']) ? 'NULL' : $data['comment_id'],
                                $data['reason'],
                                $data['user_id']);
        $db->query($query);
    }

    public static function getNotificationsFromDB($user_id) {
        global $db;
        $query = sprintf("
SELECT notifications.*, comment_user.first_name  as first_name_comment, comment_user.last_name as last_name_comment,
        comment.user_id as comment_author_id, comment_user.code_name as comment_author_code_name,
        comment.privacy as comment_privacy
FROM (
    SELECT n.comment_id, n.post_type, n.post_id, n.reason, posted_link.user_id as post_author_id,
            posted_link.title as title, posted_link.text as text, posted_link.privacy, posted_link.date_added
        FROM notifications n
        JOIN posted_link ON posted_link.post_id=n.post_id
        WHERE n.user_id=%1\$d AND n.post_type = 'posted_link'
    UNION
    SELECT n.comment_id, n.post_type, n.post_id, n.reason, question.user_id as post_author_id,
            question.question_title as title, question.question_text as text, question.privacy, question.date_added
        FROM notifications n
        JOIN question ON question.question_id=n.post_id
        WHERE n.user_id=%1\$d AND n.post_type = 'question'
) notifications
LEFT JOIN comment ON comment.comment_id = notifications.comment_id
LEFT JOIN user comment_user ON comment_user.user_id = comment.user_id
ORDER BY date_added DESC",
                        $user_id);

        $result = $db->query($query);
        if (!$result) {
            return array();
        }

        return $result;
    }

    // todo bear should be cached
    static function populateNotifications() {
        $user_id = get_user_id();
        if ($user_id) {
            $raw_data = self::getNotificationsFromDB($user_id);
            $posts = self::groupCommentsByPosts($raw_data);
            self::$notifications = self::prepareHtmlData($posts);
        }
    }

    public static function clearUserNotifications() {
        global $db;
        $query = sprintf("DELETE FROM notifications
                                WHERE user_id = %d",
                                get_user_id());
        $db->query($query);
    }

    public static function deletePostNotifications($post_type, $post_id) {
        global $db;
        $query = sprintf("DELETE FROM notifications
                                WHERE post_type='%s'
                                AND post_id = %d
                                AND user_id = %d",
                                $post_type,
                                $post_id,
                                get_user_id());
        $db->query($query);
    }

    public static function clearNotificationsIfNeeded($post_type, $post_id) {
        $post_uid = $post_type . "_" . $post_id;
        if (isset(self::$notifications[$post_uid])) {
            self::deletePostNotifications($post_type, $post_id);
            unset(self::$notifications[$post_uid]);
            Utils::$smarty->assign('header_notifications', self::getNotificationsForHeader());
        }
    }

    public static function prepareHtmlData($posts) {
        foreach($posts as &$post) {
            $autors_prefix = '';

            $post['rest_authors_text'] = '';
            $post['authors_html'] = array_slice($post['authors'], 0, self::SHOW_AUTHORS_COUNT);
            $rest_authors_count = count($post['authors']) - self::SHOW_AUTHORS_COUNT;
            if ($rest_authors_count > 0) {
                $post['rest_authors_text'] =  sprintf("%d other%s",
                                                    $rest_authors_count,
                                                    $rest_authors_count > 1 ? 's' : '');
            }

            $post['autors_prefix'] = $autors_prefix;

            // todo this should be standartized, not hardcoded here
            $post['my_url'] = sprintf("/show_post.php?type=%s&id=%d",
                                            $post['post_type'],
                                            $post['post_id']);
        }

        //e($post['authors_html']);

        return $posts;
    }

    public static function groupCommentsByPosts($data) {
        $commented_posts = array();
        foreach ($data as $comment) {
            $post_uid = $comment['post_type'] . "_" . $comment['post_id'];
            if ($comment['comment_privacy']!=='public') {
                $comment['first_name_comment'] = "An anonymous";
                $comment['last_name_comment'] = "user";
                $comment['comment_author_code_name'] = "";
            }
            $author_uid = $comment['first_name_comment'] . "_" . $comment['last_name_comment'];
            if (!isset($commented_posts[$post_uid])) {
                $commented_posts[$post_uid] = array();
                $commented_posts[$post_uid]['comments'] = array();
                $commented_posts[$post_uid]['authors'] = array();
                if (!$comment['title']) {
                    $comment['title'] = $comment['text']; // for old reviews
                }
                $commented_posts[$post_uid]['post_title'] = $comment['title'];
                $commented_posts[$post_uid]['post_type'] = $comment['post_type'];
                $commented_posts[$post_uid]['post_id'] = $comment['post_id'];
                $commented_posts[$post_uid]['last_comment_date'] = $comment['date_added'];
                $commented_posts[$post_uid]['reason'] = $comment['reason'];
            }
            $commented_posts[$post_uid]['comments'][] = $comment;
            if (!isset($commented_posts[$post_uid]['authors'][$author_uid])) {
                $commented_posts[$post_uid]['authors'][$author_uid]['full_name'] = $comment['first_name_comment'] . " " . $comment['last_name_comment'];
                $commented_posts[$post_uid]['authors'][$author_uid]['my_url'] = User::getUrlByCodeName($comment['comment_author_code_name']);

            }
        }

        return $commented_posts;
    }

    public static function getNotificationsForHeader() {
        $header_notifications = array();
        $header_notifications['notifications'] = array_slice(self::$notifications, 0, self::HEADER_NOTIFICATIONS_COUNT);
        $header_notifications['total_count'] = count(self::$notifications);
        $header_notifications['total_count_show'] = count(self::$notifications) > 9 ? "9+" : count(self::$notifications);
        $header_notifications['show_see_all'] = count(self::$notifications) > self::HEADER_NOTIFICATIONS_COUNT;
        return $header_notifications;
    }

    /* MAILING LISTS */
    public static function processPeriodicEmails() {
        self::initPeriodicEmailParameters();
        self::validatePeriodicParameters();
        self::setUsersForPeriodicEmails();
        self::showPeriodicEmailsHeader();
        $msg = self::sendPeriodicEmails();

        return $msg;
    }

    public static function getCLIArgs() {
        global $argv;
        $params = array();

        if (count($argv)<2) {
            Log::$logger->warn("No argv params, will use defaults");
        }

        for ($i=1; $i<count($argv); $i++) {
            $parts = explode("=", $argv[$i]);
            if (count($parts)<2) {
                Log::$logger->error("Wrong argv param " . $argv[$i]);
                continue;
            }

            $params[$parts[0]] = $parts[1];
        }

        return $params;
    }

    private static function initPeriodicEmailParameters() {
        if (Settings::DEV_MODE) {
            self::$users_limit_str = 'limit 10';
        }

        self::$mailing_users = array();
        self::$test_mode = true;
        self::$really_send_emails = false;
        self::$mailing_list_type = '';

        $users_string = '2,951';

        if (Utils::isConsoleCall()) {
            $cli_parameters = self::getCLIArgs();
            if (!empty($cli_parameters['users'])) {
                $users_string = $cli_parameters['users'];
            }
            if (!empty($cli_parameters['normal_work'])) {
                self::$test_mode = false;
            }
            if (!empty($cli_parameters['send_emails'])) {
                self::$really_send_emails = true;
            }
            if (!empty($cli_parameters['type'])) {
                self::$mailing_list_type = $cli_parameters['type'];
            }
            if (!empty($cli_parameters['start'])) {
                self::$start_user_id = $cli_parameters['start'];
            }
            if (!empty($cli_parameters['bloc_id'])) {
                self::$tag_id = $cli_parameters['bloc_id'];
            }
        } else {
            self::$test_mode = !Utils::reqParam('normal_work', false);
            self::$really_send_emails = Utils::reqParam('send_emails', false);
            self::$mailing_list_type = Utils::reqParam('type');
            self::$start_user_id = Utils::reqParam('start', 1);
            self::$tag_id = Utils::reqParam('bloc_id', 0);
            $users_string = Utils::reqParam('users', $users_string);
        }

        $temp_users = explode(',', $users_string);
        foreach ($temp_users as $user_id) {
            self::$mailing_users[] = $user_id;
        }
    }

    private static function setUsersForPeriodicEmails() {
        switch (self::$mailing_list_type) {
            case 'weekly':
                $sql = sprintf("SELECT u.user_id
                                FROM user u
                                join link l ON l.entity1_id=u.user_id AND l.entity1_type='user'
                                    AND l.entity2_type='tag' AND l.entity2_id=1
                                WHERE u.user_id>=%d
                                    AND u.notify_weekly=1
                                    AND u.f_contest_voter=0

                                ORDER BY user_id
                                %s",
                                self::$start_user_id,
                                self::$users_limit_str);
                break;
            case 'daily':
                $sql = sprintf("SELECT user_id
                                FROM user u
                                JOIN link ON entity1_type='user'
                            		AND entity1_id=u.user_id AND entity2_type='tag' AND entity2_id=1
                                WHERE user_id>=%d
                                    AND notify_daily=1
                                    AND f_contest_voter=0
                                ORDER BY user_id
                                %s",
                                self::$start_user_id,
                                self::$users_limit_str);
                break;
            case 'onetime':
                            $sql = sprintf("SELECT u.user_id
                            FROM user u
                            LEFT JOIN vote_contest vc ON vc.user_id = u.user_id
                            LEFT JOIN link ON entity1_type='user'
                            		AND entity1_id=u.user_id AND entity2_type='tag' AND entity2_id=1
                            WHERE 1=2 AND u.user_id>=%d
                                AND notify_weekly=1
                                AND notify_contest=1
                                AND (vc.user_id IS NOT NULL OR entity1_id IS NOT NULL)
                            GROUP BY user_id
                            ORDER BY user_id
                            %s",
                            self::$start_user_id,
                            self::$users_limit_str);
                break;
            case 'bloc_feed_email':
                            $sql = sprintf("SELECT id as user_id, email
                                            FROM subscriptions
                                            WHERE tag_id=%d
                                                AND confirmed_ts IS NOT NULL
                                                AND deleted_ts IS NULL
                                            %s",
                            self::$tag_id,
                            self::$users_limit_str);
                break;
            case 'friends_join_daily':
                            $sql = sprintf("SELECT user_id
                                FROM user
                                WHERE user_id>=%d
                                    AND notify_weekly=1
                                    AND f_contest_voter=0
                                ORDER BY user_id
                                %s",
                                self::$start_user_id,
                                self::$users_limit_str);
                break;
            case 'publishers_info':
                            $sql = sprintf("SELECT posted_link.user_id
                                FROM posted_link
                                JOIN user ON user.user_id = posted_link.user_id
                                WHERE posted_link.user_id >= %d
                                    AND posted_link.date_added > (NOW() - INTERVAL 1 DAY)
                                    AND f_auto = 1
                                    AND posted_link.user_id IS NOT NULL
                                GROUP BY posted_link.user_id
                                %s",
                                self::$start_user_id,
                                self::$users_limit_str);
                break;
            case 'suggestion_weekly':
                            $sql = sprintf("SELECT u.user_id
                                FROM user u
                                JOIN link ON entity1_type='user'
                                        AND entity1_id=u.user_id AND entity2_type='tag' AND entity2_id=1
                                WHERE u.user_id>=%d
                                    AND f_contest_voter=0
                                    AND notify_suggestion=1
                                ORDER BY user_id
                                %s",
                                self::$start_user_id,
                                self::$users_limit_str);
                break;
            case 'contest_launch':
                //You will get this email if you subscribe to Sales & Marketing
                //and subscribe to "Send me an email on product updates from ShareBloc
                            $sql = sprintf("SELECT u.user_id
                                FROM user u
                                JOIN link l ON l.entity1_id=u.user_id AND l.entity1_type='user'
                                        AND l.entity2_type='tag' AND l.entity2_id=1
                                WHERE u.user_id>=%d
                                AND notify_product_update=1
                                ORDER BY user_id
                                %s",
                                self::$start_user_id,
                                self::$users_limit_str);
                break;
            case 'contest_marketo_reminder':
                // for people who follow Sales & Marketing, still subscribe to product updates
                // but have NOT posted to the contest or voted on the contest.
                            $sql = sprintf("SELECT u.user_id
                                FROM user u
                                JOIN link l ON l.entity1_type='user' AND l.entity1_id=u.user_id
                                    AND l.entity2_type='tag' AND l.entity2_id=1
                                LEFT JOIN posted_link pl ON pl.user_id=u.user_id AND pl.f_contest=%d
                                LEFT JOIN vote_contest vc ON vc.user_id=u.user_id
                                LEFT JOIN posted_link pl2 ON pl2.post_id=vc.post_id AND pl2.f_contest=%d
                                WHERE u.user_id>=%d
                                    AND pl.post_id IS null
                                    AND pl2.post_id IS null
                                    AND notify_product_update=1
	                            GROUP BY u.user_id
                                ORDER BY u.user_id
                                %s",
                                Utils::CONTEST_MARKETO_ID,
                                Utils::CONTEST_MARKETO_ID,
                                self::$start_user_id,
                                self::$users_limit_str);
                break;

            case 'contest_post_reminder':
                // for people who have posted to the contest.
                            $sql = sprintf("SELECT u.user_id
                                FROM user u
                                JOIN posted_link pl ON pl.user_id=u.user_id AND pl.f_contest=%d
                                WHERE  u.user_id>=%d
                                    AND notify_product_update=1
                                GROUP BY u.user_id
                                ORDER BY u.user_id
                                %s",
                                Utils::CONTEST_MARKETO_ID,
                                self::$start_user_id,
                                self::$users_limit_str);
                break;
            case 'contest_vote_reminder':
                 //for people who have voted but NOT posted.
                            $sql = sprintf("SELECT u.user_id
                                FROM user u
                                LEFT JOIN posted_link pl ON pl.user_id=u.user_id AND pl.f_contest=%d
                                JOIN vote_contest vc ON vc.user_id=u.user_id
                                JOIN posted_link pl2 ON pl2.post_id=vc.post_id AND pl2.f_contest=%d
                                WHERE u.user_id>=%d
                                    AND pl.post_id IS null
                                    AND notify_product_update=1
                                GROUP BY u.user_id
                                ORDER BY u.user_id
                                %s",
                                Utils::CONTEST_MARKETO_ID,
                                Utils::CONTEST_MARKETO_ID,
                                self::$start_user_id,
                                self::$users_limit_str);
                break;
            case 'marketo_contest_end':
                 //same as contest launch
                            $sql = sprintf("SELECT u.user_id
                                FROM user u
                                JOIN link l ON l.entity1_id=u.user_id AND l.entity1_type='user'
                                        AND l.entity2_type='tag' AND l.entity2_id=1
                                WHERE u.user_id>=%d
                                AND notify_product_update=1
                                ORDER BY user_id
                                %s",
                                self::$start_user_id,
                                self::$users_limit_str);
                break;

            case 'funnelholic_webinar':
                 //same as contest launch
                            $sql = sprintf("SELECT u.user_id
                                FROM user u
                                JOIN link l ON l.entity1_id=u.user_id AND l.entity1_type='user'
                                        AND l.entity2_type='tag' AND l.entity2_id=1
                                WHERE u.user_id>=%d
                                AND notify_product_update=1
                                ORDER BY user_id
                                %s",
                                self::$start_user_id,
                                self::$users_limit_str);
                break;

            case 'shutdown_notice':

                            $sql = sprintf("SELECT u.user_id
                                FROM user u
                                WHERE u.user_id>=%d
                                AND notify_product_update=1
                                ORDER BY user_id
                                %s",
                                self::$start_user_id,
                                self::$users_limit_str);
                break;
    
            case 'deactivation':
                 //all who don't follow sales and marketing
                            $sql = sprintf("SELECT u.user_id
                                FROM user u
                                LEFT OUTER JOIN link l ON l.entity1_id=u.user_id AND l.entity1_type='user'
                                        AND l.entity2_type='tag' AND l.entity2_id=1
                                WHERE u.user_id>=%d
                                AND l.entity1_id is null
                                ORDER BY user_id
                                %s",
                                self::$start_user_id,
                                self::$users_limit_str);  
                    break;  


            default:
                $msg = "Unknown mailing list type: " . self::$mailing_list_type . ", will exit.";
                e($msg);
                Log::$logger->fatal($msg);
                exit;
        }

        self::$users_query = $sql;

        if (self::$test_mode) {
            // using data from init
            return;
        }

        self::$mailing_users = array();
        $result = Database::execArray(self::$users_query);
        foreach ($result as $user) {
            self::$mailing_users[] = $user['user_id'];
        }
    }

    private static function validatePeriodicParameters() {
        if (!self::$test_mode) {
            if (self::$really_send_emails && Settings::SHOW_BETA_BORDER) {
                e("<b>WARN</b>: You can't use send_emails=1 with normal work on BETA. Will finish.");
                exit;
            }

            if (self::$really_send_emails && !Utils::isConsoleCall() && !Settings::DEV_MODE) {
                e("<b>WARN</b>: You can't use send_emails=1 with normal work when using HTTP script call. Will finish.");
                exit;
            }
        }
    }

    private static function showPeriodicEmailsHeader() {
        $msg = "<b>Mailing script started, type:</b>: " . self::$mailing_list_type;
        if (self::$mailing_list_type=='bloc_feed_email') {
            $msg .= ", for bloc_id = " . Utils::$tags_list_vendor[self::$tag_id]['tag_name'];
        }

        Log::$logger->warn($msg);
        e($msg);

        if (self::$test_mode) {
            e("<b>WARN</b>: TEST MODE. Only specified users are processed. To test specific users modify parameter users.");
            e("Processed users are: " . implode(',', self::$mailing_users));
            Log::$logger->info('Test mode');
        } else {
            e("<b>WARN</b>: Normal work: all users are processed.");
            Log::$logger->info('Normal mode');
        }

        if (self::$really_send_emails) {
            e("<b>WARN</b>: emails will be sent to real users. DO NOT interrupt script execution OR restart it. Normal execution finishes with 'Script finished successfully.' message.");
            Log::$logger->info('Will send emails');
        } else {
            e("<b>WARN</b>: No emails will be sent. Add '&send_emails=1' parameter to really send emails.");
            Log::$logger->info('Will NOT send emails');
        }

        e("Query for getting users for this mailing list type is: \n\n" . self::$users_query);

        $msg = sprintf("There are <b>%d</b> users to send email to.\n", count(self::$mailing_users));
        Log::$logger->info($msg);
        e($msg);
    }

    private static function sendPeriodicEmails() {
        require_once('class.Mailer.php');
        $new_users = array();
        $bloc_feed_posts = array();
        $counter = 0;
        $succeed = 0;
        $users_count = count(self::$mailing_users);

        if (self::$mailing_list_type=='weekly') {
            $new_users = self::getUsersJoinedLastDays();
        } elseif (self::$mailing_list_type=='bloc_feed_email') {
            $bloc_feed_posts = self::getBlocFeedPosts();
            if (!$bloc_feed_posts) {
                $msg = sprintf("No posts for bloc feed for bloc %s",
                                Utils::$tags_list_vendor[self::$tag_id]['tag_name']);
                e($msg);
                Log::$logger->error($msg);
                return $msg;
            }

            self::addRandomViewsCount($bloc_feed_posts);
        } elseif (self::$mailing_list_type=='friends_join_daily') {
            $new_users = self::getUsersJoinedLastDays(self::EMAIL_ONE_DAY_COUNT);
        } elseif (self::$mailing_list_type=='suggestion_weekly') {
            $new_users = self::getUsersToSuggestWeekly();
        }

        foreach(self::$mailing_users as $user_id) {
            $msg = sprintf("Will prepare and send email to user with user_id=%d",
                            $user_id);
            e($msg);
            Log::$logger->info($msg);

            switch (self::$mailing_list_type) {
                case 'weekly':
                    $user = new User($user_id);
                    user_login($user);
                    $result = self::prepareAndSendWeeklyEmail($new_users);
                    // logging user out
                    $_SESSION = array();
                    break;
                case 'daily':
                    $user = new User($user_id);
                    user_login($user);
                    $result = self::prepareAndSendDailyEmail();
                    // logging user out
                    $_SESSION = array();
                    break;
                case 'onetime':
                    $user = new User($user_id);
                    user_login($user);
                    $result = self::prepareAndSendOnetimeEmail();
                    // logging user out
                    $_SESSION = array();
                    break;
                case 'bloc_feed_email':
                    $result = self::prepareAndSendBlocFeedEmail($user_id, $bloc_feed_posts);
                    break;
                case 'friends_join_daily':
                    $result = self::prepareAndSendFriendsJoinEmail($user_id, $new_users);
                    break;
                case 'publishers_info':
                    $result = self::prepareAndSendPublishersInfoEmail($user_id);
                    break;
                case 'suggestion_weekly':
                    $result = self::prepareAndSendSuggestionWeeklyEmail($user_id, $new_users);
                    break;
                case 'contest_launch':
                    $result = self::prepareAndSendContestLaunchEmail($user_id);
                    break;
                case 'contest_marketo_reminder':
                    $result = self::prepareAndSendContestReminderEmail($user_id);
                    break;
                case 'contest_post_reminder':
                    $result = self::prepareAndSendContestReminderEmail($user_id);
                    break;
                case 'contest_vote_reminder':
                    $result = self::prepareAndSendContestReminderEmail($user_id);
                    break;
                case 'marketo_contest_end':
                    $result = self::prepareAndSendContestEndingEmail($user_id);
                    break;
                case 'funnelholic_webinar':
                    $result = self::prepareAndSendFunnelholicWebinarEmail($user_id);
                    break;
                case 'shutdown_notice':
                    $result = self::prepareAndSendShutdownNoticeEmail($user_id);
                    break;                
                case 'deactivation':
                    $result = self::prepareAndSendDeactivationEmail($user_id);
                    break;       

            }

            if ($result) {
                $succeed++;
                if (self::$really_send_emails) {
                    $msg = sprintf("Email was successfully sent for user %d.", $user_id);
                    e($msg);
                    Log::$logger->info($msg);
                }
            } else {
                Log::$logger->info("Periodic email was not sent. User_id = $user_id");
            }


            $counter++;
            $msg = sprintf("user_id=%d processed (%d of %d).\n---------------------------------------------",
                        $user_id, $counter, $users_count);
            Log::$logger->info($msg);
            e($msg);
        }
        $msg = "Emails were sent to $succeed users, total processed users count is $users_count";
        return $msg;
    }
    /* END OF COMMON MAILING LISTS METHODS */

    private static function prepareAndSendDailyEmail() {
        $feed_posts = self::getTopPostsForUser(true);

        if (!$feed_posts) {
            $msg = sprintf("WARN: Will NOT send email to user with user_id=%d (%s, %s) as it has empty feed.",
                            get_user_id(),
                            htmlentities(Utils::userData('full_name')),
                            htmlentities(Utils::userData('email'))
                            );
            e($msg);
            Log::$logger->error($msg);
            return false;
        }

        self::addRandomViewsCount($feed_posts);

        $followed_authors = self::getFollowedAuthors($feed_posts);

        e( sprintf("%d of posts authors are followed by user",
                    count($followed_authors)) );

        $email_data = array();
        $email_data['subject'] = self::getEmailSubject($followed_authors, count($feed_posts), false);
        $email_data['posts'] = $feed_posts;

        $mailer = new Mailer('daily_email');
        $send_result = $mailer->sendDailyEmail($email_data, !self::$really_send_emails);

        return $send_result;
    }


    /* WEEKLY EMAILS METHODS*/
    private static function prepareAndSendWeeklyEmail($new_users) {
        $feed_posts = self::getTopPostsForUser();

        if (!$feed_posts) {
            $msg = sprintf("WARN: Will NOT send email to user with user_id=%d (%s, %s) as it has empty feed.",
                            get_user_id(),
                            htmlentities(Utils::userData('full_name')),
                            htmlentities(Utils::userData('email'))
                            );
            e($msg);
            Log::$logger->error($msg);
            return false;
        }

        self::addRandomViewsCount($feed_posts);

        $followed_authors = self::getFollowedAuthors($feed_posts);
        $combined_people = self::combineUsersFollowersAndJoins(get_user_id(), $new_users, self::EMAIL_WEEK_DAYS_COUNT);

        e( sprintf("%d of posts authors are followed by user",
                    count($followed_authors)) );

        $email_data = array();
        $email_data['subject'] = self::getEmailSubject($followed_authors, count($feed_posts));
        $email_data['posts'] = $feed_posts;
        $email_data['combined_people'] = $combined_people;

        $mailer = new Mailer('weekly_email');
        $send_result = $mailer->sendWeeklyEmail($email_data, !self::$really_send_emails);

        return $send_result;
    }

    private static function getEmailSubject($authors, $posts_count, $weekly=true) {
        if (!$authors) {
            if ($weekly) {
                return "Last Week's Top Posts from your ShareBloc Feed";
            } else {
                return "Today's Top Posts from your ShareBloc Feed";
            }
        }

        $names = array();
        foreach ($authors as $author) {
            $names[] = $author['name'];
        }

        if (!$names) {
            if ($weekly) {
                return "Last Week's Top Posts from your ShareBloc Feed";
            } else {
                return "Today's Top Posts from your ShareBloc Feed";
            }
        }

        $others_count = $posts_count - count($names);

        $others_str = '';
        $last_name = array_pop($names);
        $names_str = implode(', ', $names);

        if ($names_str) {
            $names_str .= (($others_count == 0) ? ' and ' : ', ') . $last_name;
        } else {
            $names_str = $last_name;
        }

        if ($others_count > 0) {
            $others_str = sprintf("and %s other%s",
                            $others_count,
                            $others_count==1 ? '' : 's'
                            );
        }

        if ($weekly) {
        $subject = sprintf("Last Week's Top Posts from %s %s",
                            $names_str,
                            $others_str);
        } else {
            $subject = sprintf("Today's Top Posts from %s %s",
                                $names_str,
                                $others_str);
        }


        return $subject;
    }

    private static function getTopPostsForUser($f_daily = false) {
        $user_following = Utils::userData('following');
        $get_top_posts_number = 5;

        // todo warn hardcoded live ids
        if (!$f_daily && (isset($user_following['tag_1']) || isset($user_following['tag_5']))) {
            $get_top_posts_number = 10;
        }

        FrontStream::init();
        $content = FrontStream::getContent($get_top_posts_number, 0, array('type'=>'feed_weekly', 'f_daily' => $f_daily));

        Log::$logger->info(sprintf("Posts count =  %d.", count($content)));

        if (count($content)<$get_top_posts_number) {
            $msg = sprintf("WARN: User user_id=%d (%s, %s) has only %d posts in feed.",
                            get_user_id(),
                            htmlentities(Utils::userData('full_name')),
                            htmlentities(Utils::userData('email')),
                            count($content));
            e($msg);
            Log::$logger->warn($msg);
        }


        return $content;
    }

    // @see second comment to https://vendorstack.atlassian.net/browse/VEN-331
    private static function addRandomViewsCount(&$posts) {
        $rand_bounds = array(
            0=>array('min'=>5, 'max'=>7),
            1=>array('min'=>5, 'max'=>6),
            2=>array('min'=>4, 'max'=>5),
            3=>array('min'=>3, 'max'=>4),
            4=>array('min'=>2, 'max'=>3),
        );

        $counter = 0;
        foreach($posts as &$post) {
            if (isset(self::$post_views_count[$post['uid']])) {
                $post['views_count'] = self::$post_views_count[$post['uid']];
                continue;
            }

            $random_multiplier = rand($rand_bounds[$counter]['min'], $rand_bounds[$counter]['max']);
            $random_add = rand(0, 5);
            $random = ($random_multiplier * $post['vote']['total']) + $random_add;

            self::$post_views_count[$post['uid']] = $random;
            $post['views_count'] = $random;

            if ($counter < count($rand_bounds)-1 ) {
                $counter++;
            }
        }
        return $posts;
    }

    private static function getFollowedAuthors($posts) {
        $following_by_entity_type = Utils::userData('following_by_entity_type');
        $following = Utils::userData('following');
        $followed = array();
        foreach ($posts as $post) {
            $user_uid = array_search($post['user_id'], $following_by_entity_type['user']);
            if ($user_uid!==false) {
                $followed[$user_uid] = $following[$user_uid];
            }
        }

        Log::$logger->info(sprintf("There are %d followed users for feed for user %d.", count($followed), get_user_id()));
        return $followed;
    }

    public static function getUsersJoinedLastDays($days_count = self::EMAIL_WEEK_DAYS_COUNT) {
        if (Settings::DEV_MODE) {
            $days_count = 100;
        }

        $sql = sprintf("SELECT user.user_id, email,
                        li.provider_uid AS linkedin_uid,
                        tw.provider_uid AS twitter_uid,
                        gl.provider_uid AS google_uid

                        FROM user
                        LEFT JOIN oauth li ON li.user_id=user.user_id AND li.provider='linkedin'
                        LEFT JOIN oauth tw ON tw.user_id=user.user_id AND tw.provider='twitter'
                        LEFT JOIN oauth gl ON gl.user_id=user.user_id AND gl.provider='google'

                        WHERE user.date_added > (NOW() - INTERVAL %d DAY)
                            AND f_contest_voter=0
                        ORDER BY user.date_added DESC",
                        $days_count);

        $users = Database::execArray($sql);

        $msg = sprintf("There are %d new users for last %d days.", count($users), $days_count);
        if (HELPER_SCRIPT) {
            e($msg);
        }
        Log::$logger->info($msg);
        return $users;
    }

    public static function getFollowedForUserForLastDays($user_id, $days_count = self::EMAIL_WEEK_DAYS_COUNT) {
        if (Settings::DEV_MODE) {
            $days_count = 100;
        }

        $sql = sprintf("SELECT entity1_id as user_id
                        FROM link
                        WHERE link_type='follow'
                            AND entity1_type='user'
                            AND entity2_id=%d AND entity2_type='user'
                            AND date_added > (NOW() - INTERVAL %d DAY);",
                    $user_id,
                    $days_count);

        $users = Database::execArray($sql);

        $msg = sprintf("There are %d users followed user %d for last %d days.", count($users), $user_id, $days_count);
        if (HELPER_SCRIPT) {
            e($msg);
        }

        Log::$logger->info($msg);

        $followed_this_week = array();

        foreach ($users as $user) {
            $temp_user = new User($user['user_id']);
            $followed_this_week[$user['user_id']] = $temp_user->get();
            $followed_this_week[$user['user_id']]['followed'] = true;
            $followed_this_week[$user['user_id']]['joined'] = false;
        }

        return $followed_this_week;
    }

    public static function getJoinedPeopleForUser($user_id, $new_users) {
        if (!$new_users) {
            return array();
        }

        $user_contacts = User::getOauthContactsByUserId($user_id);

        $joined_people = array();
        foreach ($user_contacts as $contact) {
            switch ($contact['provider']) {
                case 'linkedin':
                    $search_fields = array('linkedin_uid');
                    break;
                case 'twitter':
                    $search_fields = array('twitter_uid');
                    break;
                case 'google':
                    $search_fields = array('google_uid', 'email');
                    break;
            }

            foreach ($new_users as $user) {
                if ($user['user_id']== $user_id) {
                    continue;
                }
                foreach ($search_fields as $field) {
                    if ($user[$field]===$contact['id']) {
                        $temp_user = new User($user['user_id']);
                        $joined_people[$user['user_id']] = $temp_user->get();
                        $joined_people[$user['user_id']]['joined'] = true;
                        $joined_people[$user['user_id']]['followed'] = false;
                    }
                }
            }
        }

        Log::$logger->info(sprintf("there were found %d users from user %d contact list who joined last time.",
                                    count($joined_people),
                                    $user_id));
        return $joined_people;
    }

    public static function combineUsersFollowersAndJoins($user_id, $new_users, $days_count=self::EMAIL_WEEK_DAYS_COUNT) {
        $joined_people = self::getJoinedPeopleForUser($user_id, $new_users);
        $users_followed = self::getFollowedForUserForLastDays($user_id, $days_count);

        $combined_users = $users_followed;

        foreach ($joined_people as $temp_user_id => $user) {
            if (isset($combined_users[$temp_user_id])) {
                $combined_users[$temp_user_id]['joined'] = true;
            } else {
                $combined_users[$temp_user_id] = $user;
            }
        }

        return $combined_users;
    }

    /* END OF WEEKLY EMAILS METHODS*/

    private static function prepareAndSendOnetimeEmail() {
        $mailer = new Mailer('onetime_email');
        $send_result = $mailer->sendOnetimeEmail(!self::$really_send_emails);

        return $send_result;
    }

    private static function getBlocFeedPosts() {
        $limit = 10;
        if (self::$tag_id==6) {
            // real estate https://vendorstack.atlassian.net/browse/VEN-464
            $limit = 5;
        }
        FrontStream::init();
        $posts = FrontStream::getContent($limit, 0, array('type'=>'tag_top_weekly', 'id'=>self::$tag_id));
        return $posts;
    }

    private static function prepareAndSendBlocFeedEmail($id, $posts) {
        if (self::$test_mode) {
            if (Utils::isConsoleCall()) {
                $user = new User($id);
                $email = $user->get_data('email');
            } else {
                $email = Utils::userData('email');
            }

            $user = array('confirm_key'=>'unsubscribe_not_applyed_while_testing_this_list', 'email'=>$email);
        } else {
            $user = Subscription::getSubscriptionById($id);
        }

        $email_data = array();
        $email_data['posts'] = $posts;
        $email_data['email'] = $user['email'];
        $email_data['confirm_key'] = $user['confirm_key'];
        $email_data['tag_id'] = self::$tag_id;

        $mailer = new Mailer('bloc_feed_email');
        $send_result = $mailer->sendBlocFeedEmail(!self::$really_send_emails, $email_data);

        return $send_result;
    }

    // https://vendorstack.atlassian.net/browse/VEN-417
    private static function getFriendsJoinEmailSubject($users) {
        $USERS_IN_SUBJECT_COUNT = 2;

        $users_in_subject = array_slice($users, 0, $USERS_IN_SUBJECT_COUNT);
        $names = array();
        foreach ($users_in_subject as $user) {
            $names[] = $user['first_name'] . " " . $user['last_name'];
        }

        $others_count = count($users) - 2;

        $names_part = "";
        if ($others_count > 0) {
            $names_part = implode(', ', $names) . " and $others_count more";
        } else {
            $names_part = implode(' and ', $names);
        }

        $subject = sprintf("%s just followed you on ShareBloc",
                            $names_part);

        return $subject;
    }

    private static function prepareAndSendFriendsJoinEmail($user_id, $new_users) {
        $combined_people = self::combineUsersFollowersAndJoins($user_id, $new_users, self::EMAIL_ONE_DAY_COUNT);

        if (!$combined_people) {
            $msg = sprintf("Will NOT send friends joins daily email to user with user_id=%d as it has no news for today.",
                            $user_id);
            e($msg);
            Log::$logger->info($msg);
            return false;
        }

        $subject = self::getFriendsJoinEmailSubject($combined_people);

        $mailer = new Mailer('friends_join_email');
        $send_result = $mailer->sendFriendsDailyEmail($user_id, $combined_people, $subject, !self::$really_send_emails);

        return $send_result;
    }


    private static function getUsersTodayAutoposts($user_id) {
        $sql = sprintf("SELECT posted_link.post_id
                        FROM posted_link
                        WHERE posted_link.user_id = %d
                            AND posted_link.date_added > (NOW() - INTERVAL 1 DAY)
                            AND f_auto = 1
                            AND posted_link.user_id IS NOT NULL",
                        $user_id);

        $results = Database::execArray($sql);

        $posts = array();
        foreach ($results as $post) {
            $entity = FrontStream::prepareOnePost($post['post_id'], 'posted_link');
            if ($entity) {
                $posts[] = $entity;
            }
        }

        return $posts;
    }

    private static function getUsersToSuggestWeekly() {
        $users = array();
        $users_list = array('heidi_lorenzen', 'marshall_kirkpatrick', 'scott_mitchell');

        if (Settings::DEV_MODE || Settings::SHOW_BETA_BORDER) {
            $users_list = array('david_cheng', 'catherine_jhung', 'jeff_chen');
        }

        foreach ($users_list as $code_name) {
            $user = new User(null, $code_name);
            if ($user->is_loaded()) {
                $users[] = $user->get();
            }
        }
        return $users;
    }

    private static function isAlreadyFollowing($who_user_id, $whom_user_id) {
        if ($who_user_id==$whom_user_id) {
            return true;
        }

        $sql = sprintf("SELECT 1
                        FROM link
                        WHERE entity1_type='user' AND entity1_id=%d
                        AND entity2_type='user' AND entity2_id =%d",
                        $who_user_id, $whom_user_id);

        $result = Database::execArray($sql, true);
        if ($result) {
            return true;
        }
        return false;
    }

    private static function prepareAndSendSuggestionWeeklyEmail($user_id, $users_to_suggest) {
        foreach ($users_to_suggest as $key => $user) {
            if (self::isAlreadyFollowing($user_id, $user['user_id'])) {
                unset($users_to_suggest[$key]);
            }
        }

        if (!$users_to_suggest) {
            $msg = sprintf("Will NOT send suggestion email to user with user_id=%d as it has no non-followed users from the list.",
                            $user_id);
            e($msg);
            Log::$logger->info($msg);
            return false;
        }

        $mailer = new Mailer('suggestion_weekly');
        $send_result = $mailer->sendSuggestionWeeklyEmail($user_id, $users_to_suggest, !self::$really_send_emails);

        return $send_result;
    }

    private static function prepareAndSendContestLaunchEmail($user_id) {
        $mailer = new Mailer('contest_launch');
        $send_result = $mailer->sendContestLaunchEmail($user_id, !self::$really_send_emails);

        return $send_result;
    }

    private static function prepareAndSendContestReminderEmail($user_id) {
        $mailer = new Mailer('contest_reminder');
        $send_result = $mailer->sendContestReminderEmail($user_id, self::$mailing_list_type, !self::$really_send_emails);

        return $send_result;
    }


   private static function prepareAndSendContestEndingEmail($user_id) {
        $mailer = new Mailer('marketo_contest_end');
        $send_result = $mailer->sendContestEndingEmail($user_id, !self::$really_send_emails);

        return $send_result;
    }

    private static function prepareAndSendShutdownNoticeEmail($user_id) {
        $mailer = new Mailer('shutdown_notice');
        $send_result = $mailer->sendShutdownNoticeEmail($user_id, !self::$really_send_emails);

        return $send_result;
    }

    private static function prepareAndSendDeactivationEmail($user_id) {
        $mailer = new Mailer('deactivation');
        $send_result = $mailer->SendDeactivationEmail($user_id, !self::$really_send_emails);

        return $send_result;
    }


}

