<?php

$smarty = null;
require_once ("../utils/init.php");

Utils::redirectIfNotLoggedInAsAdmin();
$err_msg = '';
$filter = Utils::reqParam('filter', '');

$ids_to_delete = Utils::reqParam('ids_to_delete');
if ($ids_to_delete) {
    DBUtils::deleteVendorsFromDB($ids_to_delete);
}

$dont_show_more = Utils::reqParam('dont_show_more', 'true');
if ($dont_show_more === 'false') {
    $entities = DBUtils::getAllEntities();
} else {
    $entities = DBUtils::getAllEntitiesWithoutDeletedAndExported();
}

if (!$entities) {
    $err_msg = "No entities in the DB";
}

$smarty_params = array('entities' => $entities,
                        'err_msg'=> $err_msg,
                        'dont_show_more' => $dont_show_more,
                        'filter' => $filter);

$smarty->assign($smarty_params);

$smarty->display('all_entities.tpl');

Log::$logger->trace("All entities page done");
exit();
?>
