<?php

require_once('../includes/global.inc.php');

redirect(Utils::getDefaultPage());

$team = getTeam();
$template->assign('team', $team);
$template->display('pages/team.tpl');


function getTeam() {
    $team = array();

    $member = array();
    $member['name'] = 'David Cheng';
    $member['title'] = 'CEO & Co-Founder';
    $member['portrait'] = 'david.png';
    $member['twitter'] = 'https://twitter.com/DavidPCheng';
    $member['linkedin'] = 'https://www.linkedin.com/in/davidpcheng/';
    $team[] = $member;

    $member = array();
    $member['name'] = 'Andrew Koller';
    $member['title'] = 'Community & Co-Founder';
    $member['portrait'] = 'andrew.png';
    $member['twitter'] = 'https://twitter.com/AndrewKoller';
    $member['linkedin'] = 'http://www.linkedin.com/profile/view?id=18050617';
    $team[] = $member;

    $member = array();
    $member['name'] = 'Igor Kryltsov';
    $member['title'] = 'Operations & Back-End';
    $member['portrait'] = 'igor.png';
    $member['twitter'] = 'https://twitter.com/kryltsov';
    $member['linkedin'] = 'http://www.linkedin.com/in/igorkryltsov';
    $team[] = $member;

    $member = array();
    $member['name'] = 'Dmitriy Yakubovskiy';
    $member['title'] = 'Senior Developer';
    $member['portrait'] = 'dima.png';
    $member['twitter'] = '';
    $member['linkedin'] = 'http://www.linkedin.com/profile/view?id=38638798';
    $team[] = $member;

    $member = array();
    $member['name'] = 'Katya Yakubovskiy';
    $member['title'] = 'Developer';
    $member['portrait'] = 'katya.png';
    $member['twitter'] = '';
    $member['linkedin'] = 'http://www.linkedin.com/profile/view?id=38239490';
    $team[] = $member;


    $member = array();
    $member['name'] = 'Misu';
    $member['title'] = 'Our inspiration';
    $member['portrait'] = 'misu.png';
    $member['twitter'] = '';
    $member['linkedin'] = '';
    $team[] = $member;

    $member = array();
    $member['name'] = 'Josh Becker';
    $member['title'] = 'Advisor';
    $member['portrait'] = 'josh.jpg';
    $member['twitter'] = 'https://twitter.com/JoshBeckerSV';
    $member['linkedin'] = 'http://www.linkedin.com/profile/view?id=12577';
    $team[] = $member;

    $member = array();
    $member['name'] = 'Rahul Prakash';
    $member['title'] = 'Advisor';
    $member['portrait'] = 'rahul.jpg';
    $member['twitter'] = 'https://twitter.com/rahulprakash';
    $member['linkedin'] = 'http://www.linkedin.com/profile/view?id=8689586';
    $team[] = $member;

    $member = array();
    $member['name'] = 'Satyam Priyadarshy, Ph.D';
    $member['title'] = 'Advisor';
    $member['portrait'] = 'satyam.jpg';
    $member['twitter'] = 'https://twitter.com/priyadarshy';
    $member['linkedin'] = 'http://www.linkedin.com/profile/view?id=3657999';
    $team[] = $member;

    return $team;
}
?>