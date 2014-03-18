<?php
require_once('../includes/global.inc.php');

$new_users = Notification::getUsersJoinedLastDays();

$joined_people = Notification::getJoinedPeopleForUser($new_users);
$users_followed = Notification::getFollowedForUserForLastDays(get_user_id());

$joined_people = Utils::prepareFollowDataForUsers($joined_people);
$users_followed = Utils::prepareFollowDataForUsers($users_followed);

$smarty_params = array(
    'joined_people' => $joined_people,
    'users_followed' => $users_followed,
    'tab_selected' => 'followers',
    'init_clipboard_copy' => true,
);

$template->assign($smarty_params);
$template->display('pages/recent_connections.tpl');
?>
