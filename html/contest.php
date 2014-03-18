<?php

require_once('../includes/global.inc.php');
require_once('class.FrontStream.php');
require_once('class.VoteContest.php');

if (get_input('redirect_to_get')) {
   // all data posted is already processed in included FrontStream.php
   redirect($_SERVER['REQUEST_URI']);
}

$FIRST_POSTS_ON_PAGE = 50;

if (Utils::reqParam('type')==='confirm_votes') {
    if (is_logged_in()) {
        $message = 'Sorry, this confirmation link can not be used by signed in users.<br>Please <a href="mailto:support@sharebloc.com">contact us</a> if you are trying to confirm votes and cannot.';
        $template->assign('message', $message);
        $template->display('pages/message.tpl');
        exit();
    }
    if (!User::confirmEmail()) {
        $message = 'Sorry, this confirmation link is in error.<br>Please <a href="mailto:support@sharebloc.com">contact us</a> if you are trying to confirm votes and cannot.';
        $template->assign('message', $message);
        $template->display('pages/message.tpl');
        exit();
    }
    Vote::confirmVotes();
    $_SESSION['just_confirmed_votes'] = true;
    redirect('/'.Utils::$contest_urls[Utils::CONTEST_MARKETO_ID]);
}

$just_confirmed_votes = Utils::unsetSVar('just_confirmed_votes');

$contest_id = get_input('contest_id');
if (!$contest_id) {
    redirect(Utils::getDefaultPage());
}

$contest_url = Utils::$contest_urls[$contest_id];


$content = FrontStream::getContent($FIRST_POSTS_ON_PAGE,
                                    0,
                                    array('type'=>'contest',
                                        'contest_id'=>$contest_id)
                                );
FrontStream::setCommonSmartyParams(true);

$scroll_to_post = intval(Utils::reqParam('post', 0));

$smarty_params = array(
    'content' => $content,
    'experts' => getExperts(),
    'active_submenu' => 'contest',
    'init_clipboard_copy' => true,
    'contest_votes_left' => VoteContest::getVotesLeft(),
    'just_confirmed_votes' => $just_confirmed_votes,
    'use_contest_vote' => 1,
    'twitter_symbols_left' => Utils::countTwitterSymbolsLeft(),
    'show_thank_you_contest_popup' => Utils::sVar("show_thank_you_contest_popup", true),
    'show_join_welcome_popup' => Utils::unsetSVar('show_join_welcome_popup'),
    'contest_open_nomin_popup' => Utils::unsetSVar('contest_open_nomin_popup') ? 1 : 0,
    'posts_on_contest_page' => $FIRST_POSTS_ON_PAGE,
    'scroll_to_post' => $scroll_to_post,
    'show_join_widget' => 1,
    'contest_id' => $contest_id,
    'contest_url' => $contest_url,
);

$_SESSION['show_thank_you_contest_popup'] = false;

$template->assign($smarty_params);

if ($contest_id == 1) {
    $template->display('pages/contest.tpl');
} else {
    $template->display('contest_marketo/contest.tpl');
}


function getExperts() {
    $experts = array();

    $experts[] = array('full_name'=>'Alen Mayer, CSP', 'position'=>"Mayer Sales Training", 'position_link'=>"http://www.alenmajer.com/",
                        'logo_url'=>"images/contest/alen_mayer.png",
                        'twitter'=>"https://twitter.com/mayeralen",'linkedin'=>"http://www.linkedin.com/in/alenmajer",'google_plus'=>"https://plus.google.com/+AlenMayer/posts",'my_url'=>'http://www.sharebloc.com/users/alen_mayer');
    $experts[] = array('full_name'=>'Ardath Albee', 'position'=>"Marketing Interactions", 'position_link'=>"http://www.marketinginteractions.com/",
                        'logo_url'=>"images/contest/ardath_albee.png",
                        'twitter'=>"https://twitter.com/ardath421",'linkedin'=>"http://www.linkedin.com/in/ardathalbee",'google_plus'=>"https://plus.google.com/+ArdathAlbee/posts",'my_url'=>'http://www.sharebloc.com/users/ardath_albee');
    $experts[] = array('full_name'=>'Craig Rosenberg', 'position'=>"Funnelholic.com", 'position_link'=>"http://www.funnelholic.com/",
                        'logo_url'=>"images/contest/craig_rosenberg.png",
                        'twitter'=>"https://twitter.com/funnelholic",'linkedin'=>"http://www.linkedin.com/in/craigrosenberg",'google_plus'=>"https://plus.google.com/104385539678159611943/posts",'my_url'=>'http://www.sharebloc.com/users/craig_rosenberg');
    $experts[] = array('full_name'=>'Dave Brock', 'position'=>"Partners In EXCELLENCE", 'position_link'=>"http://partnersinexcellenceblog.com/",
                        'logo_url'=>"images/contest/dave_brock.png",
                        'twitter'=>"https://twitter.com/davidabrock",'linkedin'=>"http://www.linkedin.com/in/davebrock",'google_plus'=>"https://plus.google.com/+DaveBrockPiE/posts",'my_url'=>'http://www.sharebloc.com/users/dave_brock');
//    $experts[] = array('full_name'=>'Douglas Karr', 'position'=>"Marketing Tech Blog", 'position_link'=>"http://www.marketingtechblog.com/",
//                        'logo_url'=>"images/contest/douglas_karr.png",
//                        'twitter'=>"https://twitter.com/mktgtechblog",'linkedin'=>"http://www.linkedin.com/in/douglaskarr",'google_plus'=>"https://plus.google.com/+DouglasKarr/posts",'my_url'=>'http://www.sharebloc.com/users/douglas_karr');
    $experts[] = array('full_name'=>'Bob Thompson', 'position'=>"Customer Think", 'position_link'=>"http://www.customerthink.com/",
                        'logo_url'=>"images/contest/bob_thompson.png",
                        'twitter'=>"https://twitter.com/Bob_Thompson",'linkedin'=>"http://www.linkedin.com/in/customerthink",'google_plus'=>"",'my_url'=>'http://www.sharebloc.com/users/bob_thompson');

    $experts[] = array('full_name'=>'Justin Gray', 'position'=>"LeadMD", 'position_link'=>"http://www.leadmd.com/",
                        'logo_url'=>"images/contest/justin_gray.png",
                        'twitter'=>"https://twitter.com/Jgraymatter",'linkedin'=>"http://www.linkedin.com/in/leadmd",'google_plus'=>"",'my_url'=>'http://www.sharebloc.com/users/justin_gray');
    $experts[] = array('full_name'=>'Lori Richardson', 'position'=>"Score More Sales", 'position_link'=>"http://scoremoresales.com/",
                        'logo_url'=>"images/contest/lori_richardson.png",
                        'twitter'=>"https://twitter.com/scoremoresales",'linkedin'=>"http://www.linkedin.com/in/scoremoresales",'google_plus'=>"https://plus.google.com/107207834263120513066/posts",'my_url'=>'http://www.sharebloc.com/users/lori_richardson');
    $experts[] = array('full_name'=>'Matt Heinz', 'position'=>"Heinz Marketing", 'position_link'=>"http://www.heinzmarketing.com/",
                        'logo_url'=>"images/contest/matt_heinz.png",
                        'twitter'=>"https://twitter.com/HeinzMarketing",'linkedin'=>"http://www.linkedin.com/in/mattheinz",'google_plus'=>"https://plus.google.com/+MattHeinz/posts",'my_url'=>'http://www.sharebloc.com/users/matt_heinz');
    $experts[] = array('full_name'=>'Michael Brenner', 'position'=>"SAP", 'position_link'=>"http://www.sap.com/",
                        'logo_url'=>"images/contest/michael_brenner.png",
                        'twitter'=>"https://twitter.com/BrennerMichael",'linkedin'=>"http://www.linkedin.com/in/michaelbrenner",'google_plus'=>"https://plus.google.com/110241925170552838764/posts",'my_url'=>'http://www.sharebloc.com/users/michael_brenner');
    $experts[] = array('full_name'=>'Tibor Shanto', 'position'=>"Renbor Sales Solutions", 'position_link'=>"http://www.sellbetter.ca/",
                        'logo_url'=>"images/contest/tibor_shanto.png",
                        'twitter'=>"https://twitter.com/TiborShanto",'linkedin'=>"http://www.linkedin.com/in/tiborshanto",'google_plus'=>"https://plus.google.com/111498935244970813607/posts",'my_url'=>'http://www.sharebloc.com/users/tibor_shanto');
    $experts[] = array('full_name'=>'Trish Bertuzzi', 'position'=>"The Bridge Group", 'position_link'=>"http://www.bridgegroupinc.com/",
                        'logo_url'=>"images/contest/trish_bertuzzi.png",
                        'twitter'=>"https://twitter.com/bridgegroupinc",'linkedin'=>"http://www.linkedin.com/in/trishbertuzzi",'google_plus'=>"https://plus.google.com/105794499704161065650/posts",'my_url'=>'http://www.sharebloc.com/users/trish_bertuzzi');
    return $experts;

}


