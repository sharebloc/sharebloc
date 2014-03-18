<?php

define('DISPLAY_XPM4_ERRORS', false);
require_once('class.BaseObject.php');
require_once('class.User.php');
require_once('../includes/xpertmail/MAIL5.php');

class Mailer {
    // just to not scroll down each time to enable email sending
    const DO_NOT_SEND_IN_DEV_MODE = 1;

    private $template_type;
    private $recipient_email;
    private $recipient_name;
    private $body_html;
    private $body_text;
    private $subject;
    private $email_code;
    private $template_type_name;

    private $smarty_params = array();
    private $data = array();

    private $emails_for_users = array('welcome', 'password_changed', 'notification', 'weekly_email');

    function Mailer($mailer_type) {
        $this->email_code = User::generateRandomKey();
        $template_file = DOCUMENT_ROOT . "/templates/mailers/" . $mailer_type . ".tpl";

        if (!file_exists($template_file)) {
            Log::$logger->fatal("No template file for mailer $mailer_type");
            return false;
        }

        $this->template_type = $mailer_type;
        $this->template_type_name = $mailer_type;
    }

    function initCommonSmartyParameters() {
        $this->smarty_params = array();
        $this->smarty_params['email_for_user'] = in_array($this->template_type, $this->emails_for_users);
        $this->smarty_params['test_mode'] = false;

        if (is_logged_in()) {
            $this->smarty_params['sender_full_name'] = Utils::userData('full_name');
            $this->smarty_params['user_code_name'] = get_user_code_name();
            $this->smarty_params['user_url'] = Utils::userData('my_url');
        }

        if (empty($this->data['addressee']['email'])) {
            Log::$logger->fatal("No addressee email set in mailer. Type = " . $this->template_type);
            return false;
        }

        $target = $this->data['addressee'];
        if (empty($target['first_name'])) {
            $target['first_name'] = '';
        }
        if (empty($target['last_name'])) {
            $target['last_name'] = '';
        }

        $this->smarty_params['addressee_first_name'] = $target['first_name'];
        $this->smarty_params['addressee_email'] = $target['email'];

        $this->recipient_email = $target['email'];
        $this->recipient_name  = $target['first_name'];
    }

    function prepareBody() {
        $this->smarty_params['email_code'] = $this->email_code;
        Utils::$smarty->assign($this->smarty_params);
        $this->body_html = Utils::$smarty->fetch('mailers/' . $this->template_type . '.tpl');
        $this->body_text = $this->convert_html_to_text($this->body_html);
    }

    function sendResetPasswordEmail($user) {
        $data = array();
        $data['addressee']['first_name'] = $user->get_data('first_name');
        $data['addressee']['email'] = $user->get_data('email');
        $this->data = $data;

        $this->initCommonSmartyParameters();

        $password_reset_key = $user->getPasswordResetKey();

        $this->smarty_params['password_reset_link'] = Utils::getBaseUrl().'/pw_rst/'.$password_reset_key;

        return $this->send_new();
    }

    function sendNotification($data) {
        $this->data = $data;
        $this->initCommonSmartyParameters();

        $this->smarty_params['author_full_name'] = $data['author_full_name'];
        $this->smarty_params['post_url'] = $data['post_url'];
        $this->smarty_params['post_title'] = $data['post_title'];
        $this->smarty_params['reason'] = $data['reason'];
        $this->smarty_params['post_type_name'] = $data['post_type_name'];
        $this->smarty_params['comment_text'] = $data['comment_text'];
        $this->smarty_params['addressee_code_name'] = $data['addressee']['code_name'];
        $this->smarty_params['addressee_user_url'] = $data['addressee']['user_url'];

        return $this->send_new();
    }

    function sendInvite($data) {
        $this->data = $data;
        $this->initCommonSmartyParameters();

        $this->smarty_params['confirm_key'] = $data['confirm_key'];
        $this->smarty_params['invite_front_text'] = $data['text'];

        return $this->send_new();
    }

    function sendShareLink($data) {
        $this->data = $data;
        $this->initCommonSmartyParameters();
        $this->smarty_params['share_link_text'] = $data['share_link_text'];
        return $this->send_new();
    }

    function sendInviteJoin($data) {
        $data['addressee'] = $data;
        $this->data = $data;
        $this->initCommonSmartyParameters();
        $this->smarty_params['confirm_key'] = $data['confirm_key'];
        $this->smarty_params['addressee_first_name'] = $data['first_name'];
        return $this->send_new();
    }

    function sendPasswordChanged($user) {
        $data = array();
        $data['addressee']['first_name'] = $user->get_data('first_name');
        $data['addressee']['last_name'] = $user->get_data('last_name');
        $data['addressee']['email'] = $user->get_data('email');
        $this->data = $data;

        $this->initCommonSmartyParameters();

        return $this->send_new();
    }

    function sendWelcomeEmail($data) {
        $user = $data['user_obj'];
        $data['addressee']['email'] = $user->get_data('email');
        $data['addressee']['first_name'] = $user->get_data('first_name');
        $data['addressee']['last_name'] = $user->get_data('last_name');
        $this->data = $data;
        $this->initCommonSmartyParameters();

        $confirm_link = '/confirm/email/'.$user->get_data('confirm_email_key');

        $this->smarty_params['confirm_link'] = $confirm_link;
        $this->smarty_params['generated_password'] = $data['generated_password'];
        $this->smarty_params['use_email_confirmation'] = $data['use_email_confirmation'];

        return $this->send_new();
    }

    function sendMessageToAdmin($message, $email, $subject = '') {
        if (!$email) {
            return true;
        }

        $data = array();
        $data['addressee']['email'] = $email;
        $this->data = $data;
        $this->initCommonSmartyParameters();

        $this->smarty_params['subject'] = 'Message for SB admin: ' . $subject;
        $this->smarty_params['message'] = $message;
        return $this->send_new();
    }

    function sendWeeklyEmail($weekly_data, $test = false) {
        $data = array();
        $data['addressee']['first_name'] = Utils::userData('first_name');
        $data['addressee']['last_name'] = Utils::userData('last_name');
        $data['addressee']['email'] = Utils::userData('email');
        $this->data = $data;

        $this->initCommonSmartyParameters();

        $this->smarty_params['unsubscribe_key'] = Utils::userData('unsubscribe_key');
        $this->smarty_params['subject'] = $weekly_data['subject'];
        $this->smarty_params['posts'] = $weekly_data['posts'];
        $this->smarty_params['combined_people'] = $weekly_data['combined_people'];

        return $this->send_new($test);
    }

    function sendDailyEmail($daily_data, $test = false) {
        $data = array();
        $data['addressee']['first_name'] = Utils::userData('first_name');
        $data['addressee']['last_name'] = Utils::userData('last_name');
        $data['addressee']['email'] = Utils::userData('email');
        $this->data = $data;

        $this->initCommonSmartyParameters();

        $this->smarty_params['unsubscribe_key'] = Utils::userData('unsubscribe_key');
        $this->smarty_params['subject'] = $daily_data['subject'];
        $this->smarty_params['posts'] = $daily_data['posts'];

        return $this->send_new($test);
    }

    function sendFriendsDailyEmail($user_id, $combined_people, $subject, $test = false) {
        $user = new User($user_id);

        $data = array();
        $data['addressee']['first_name'] = $user->get_data('first_name');
        $data['addressee']['last_name'] = $user->get_data('last_name');
        $data['addressee']['email'] = $user->get_data('email');
        $this->data = $data;

        $this->initCommonSmartyParameters();

        $this->smarty_params['user_url'] = $user->get_data('my_url');
        $this->smarty_params['unsubscribe_key'] = $user->get_data('unsubscribe_key');
        $this->smarty_params['subject'] = $subject;
        $this->smarty_params['combined_people'] = $combined_people;

        return $this->send_new($test);
    }

    function sendPublishersInfoEmail($user_id, $posts, $test = false) {
        $user = new User($user_id);

        $data = array();
        $data['addressee']['first_name'] = $user->get_data('first_name');
        $data['addressee']['last_name'] = $user->get_data('last_name');
        $data['addressee']['email'] = $user->get_data('email');
        $this->data = $data;

        $this->initCommonSmartyParameters();

        $this->smarty_params['user_url'] = $user->get_data('my_url');
        $this->smarty_params['unsubscribe_key'] = $user->get_data('unsubscribe_key');
        $this->smarty_params['subject'] = "Share on Twitter your auto-published posts";
        $this->smarty_params['posts'] = $posts;
        $this->smarty_params['twitter_symbols_left'] = Utils::countTwitterSymbolsLeft(" via @ShareBloc  ");

        return $this->send_new($test);
    }

    function sendSuggestionWeeklyEmail($user_id, $users_to_suggest, $test = false) {
        $user = new User($user_id);

        $data = array();
        $data['addressee']['first_name'] = $user->get_data('first_name');
        $data['addressee']['last_name'] = $user->get_data('last_name');
        $data['addressee']['email'] = $user->get_data('email');
        $this->data = $data;

        $this->initCommonSmartyParameters();

        $this->smarty_params['user_url'] = $user->get_data('my_url');
        $this->smarty_params['unsubscribe_key'] = $user->get_data('unsubscribe_key');
        $this->smarty_params['subject'] = "We Found Some People You Should Follow on ShareBloc";
        $this->smarty_params['users_to_suggest'] = $users_to_suggest;

        return $this->send_new($test);
    }

    function sendContestLaunchEmail($user_id, $test = false) {
        $user = new User($user_id);

        $data = array();
        $data['addressee']['first_name'] = $user->get_data('first_name');
        $data['addressee']['last_name'] = $user->get_data('last_name');
        $data['addressee']['email'] = $user->get_data('email');
        $this->data = $data;

        $this->initCommonSmartyParameters();

        $this->smarty_params['unsubscribe_key'] = $user->get_data('unsubscribe_key');
        $this->smarty_params['subject'] = "Win a free ticket to Marketo's upcoming conference with the Content Marketing Nation Contest";
        $this->smarty_params['contest_url'] = Utils::$contest_urls[Utils::CONTEST_MARKETO_ID];

        return $this->send_new($test);
    }

    function sendContestReminderEmail($user_id, $reminder_type, $test = false) {
        $user = new User($user_id);

        $data = array();
        $data['addressee']['first_name'] = $user->get_data('first_name');
        $data['addressee']['last_name'] = $user->get_data('last_name');
        $data['addressee']['email'] = $user->get_data('email');
        $this->data = $data;

        $this->initCommonSmartyParameters();

        $this->smarty_params['unsubscribe_key'] = $user->get_data('unsubscribe_key');
        $this->smarty_params['reminder_type'] = $reminder_type;
        $this->smarty_params['contest_url'] = Utils::$contest_urls[Utils::CONTEST_MARKETO_ID];

        $this->smarty_params['subject'] = "Still time to win a free ticket to Marketo's upcoming conference with the Content Marketing Nation Contest";

        return $this->send_new($test);
    }

    function sendOnetimeEmail($test = false) {
        $this->template_type_name = "contest_finished";

        $data = array();
        $data['addressee']['first_name'] = Utils::userData('first_name');
        $data['addressee']['last_name'] = Utils::userData('last_name');
        $data['addressee']['email'] = Utils::userData('email');
        $this->data = $data;

        $this->initCommonSmartyParameters();

        $this->smarty_params['unsubscribe_key'] = Utils::userData('unsubscribe_key');
        $this->smarty_params['subject'] = "Congratulations to the Top 50 Content Marketing Posts of 2013 Winners";

        return $this->send_new($test);
    }

    function sendBlocFeedEmail($test, $email_data) {
        $this->template_type_name = "weekly_" . Utils::$tags_list_vendor[$email_data['tag_id']]['code_name'];

        $data = array();
        $data['addressee']['email'] = $email_data['email'];
        $this->data = $data;

        $this->initCommonSmartyParameters();

        $this->smarty_params['subject'] = "Last Week's Top Posts for " . Utils::$tags_list_vendor[$email_data['tag_id']]['tag_name'];
        $this->smarty_params['posts'] = $email_data['posts'];
        $this->smarty_params['tag_id'] = $email_data['tag_id'];

        $this->smarty_params['unsubscribe_key'] = $email_data['confirm_key'];
        return $this->send_new($test);
    }

    function sendSubmitVotesEmail($serialized_votes, $user) {
        $data = array();
        $data['addressee']['first_name'] = $user->get_data('first_name');
        $data['addressee']['last_name'] = $user->get_data('last_name');
        $data['addressee']['email'] = $user->get_data('email');
        $this->data = $data;

        $this->initCommonSmartyParameters();

        $this->smarty_params['serialized_votes'] = $serialized_votes;
        $this->smarty_params['unsubscribe_key'] = $user->get_data('unsubscribe_key');
        $this->smarty_params['confirm_email_key'] = $user->get_data('confirm_email_key');

        return $this->send_new();
    }

    function sendConfirmSubscriptionEmail($email, $tag_id, $confirm_key) {
        $data = array();
        $data['addressee']['email'] = $email;
        $this->data = $data;

        $this->initCommonSmartyParameters();

        $this->smarty_params['confirm_email_key'] = $confirm_key;
        $this->smarty_params['tag_type_name'] = Utils::$tags_list_vendor[$tag_id]['tag_name'];
        $this->smarty_params['subject'] = "Confirm your subscription";

        return $this->send_new();
    }

    function send_new($test_mode=false) {
        $this->smarty_params['test_mode'] = $test_mode;

        $this->prepareBody();
        $this->parse_subject();

        if ($test_mode) {
            if (Utils::isConsoleCall()) {
                echo("Email was not sent as this is a test mode.");
                return true;
            }
            echo("<br><br>_____ START OF EMAIL ____<br><br>");
            echo("To: " . "$this->recipient_email");
            echo("<br>Subject: " . $this->subject);
            echo("<br><br>" . $this->body_html);
            echo("<br>_____ END OF EMAIL ____<br><br>");
            return true;
        } else {
            return $this->send();
        }
    }

    function send() {
        if (Settings::DEV_MODE && self::DO_NOT_SEND_IN_DEV_MODE) {
            Log::$logger->warn("The email was not sent because the DEV_MODE is enabled.");
            return true;
        }

        // 11 june 2013 - by bear - added email validation as MAIL5 simply exits if email address is not valid.
        if (!validate_email($this->recipient_email) || !FUNC5::is_mail($this->recipient_email)) {
            Log::$logger->error("The email was not sent as email address is invalid. Address is: " . $this->recipient_email);
            return false;
        }

        $from_name = 'ShareBloc';
        if ($this->template_type==='weekly_email') {
            $from_name = 'ShareBloc Weekly';
        }

        $m = new MAIL5; // initialize MAIL class
        $m->From('no-reply@sharebloc.com', $from_name); // set from address
        $m->AddTo($this->recipient_email, $this->recipient_name); // add to address
        $m->Subject($this->subject, 'utf-8'); // set subject
        $m->Text($this->body_text, 'utf-8'); // set text message
        $m->Html($this->body_html, 'utf-8'); // set html message
        // todo bear find and replace all these "or die".
        $c = $m->Connect('smtp.sendgrid.net', 465, 'vendorstack', 'ferrari458', 'ssl') or die(print_r($m->Result));

        $result = $m->Send($c);

        $m->Disconnect($c); // disconnect from server
        //print_r($m->History); // optional, for debugging

        $this->logEmail();

        return $result;
    }

    function logEmail() {
        $sql = sprintf("INSERT INTO email_log (email, sent_ts, email_code, type)
                        VALUES ('%s', NOW(), '%s', '%s')",
                        $this->recipient_email,
                        $this->email_code,
                        $this->template_type_name);
        Database::exec($sql);
    }

    // todo bear this is a magic
    function parse_subject() {
        if (strpos($this->body_html, "<!--TITLE:") !== false) {
            $start_pos = @strpos($this->body_html, "<!--TITLE:");
            $end_pos   = @strpos($this->body_html, "-->", $start_pos);

            if ($start_pos > 0 && $end_pos > 0) {
                $this->subject   = substr($this->body_html, $start_pos + 10, ($end_pos - $start_pos - 10));
                $this->body_html = substr($this->body_html, 0, $start_pos) . substr($this->body_html, $end_pos + 3);
            }
        }
    }

    function convert_html_to_text($html) {
        $text = trim(strip_tags($html));
        $text = trim(str_replace("&nbsp;", " ", $text));
        $text = trim(str_replace("\t", " ", $text));
        $text = preg_replace('/\s*\n\n\s*/', "\n\n", $text);

        return $text;
    }

}