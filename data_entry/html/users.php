<?php

$smarty = null;
require_once ("../utils/init.php");

Utils::redirectIfNotLoggedInAsAdmin();

$update_user = Utils::reqParam('update_user');
if($update_user) {
    DBUtils::updateUser();
}

$users = DBUtils::getUsers();
//e($users);
$smarty->assign('users', $users);

$smarty->display('users.tpl');

Log::$logger->trace("Users page done");
exit();
?>
