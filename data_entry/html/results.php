<?php

$smarty = null;
require_once ("../utils/init.php");

Utils::redirectIfNotLoggedInAsAdmin();

$smarty_params = array( 'all_entities_count' => DBUtils::getAllEntitiesCount(),
                        'in_queue_count' => DBUtils::getCompletedEntitiesCount(),
                        'exported_count'=> DBUtils::getExportedEntitiesCount(),
                        'deleted_count' => DBUtils::getDeletedEntitiesCount(),
                        'ready_for_export_count' => DBUtils::getReadyForExportEntitiesCount());

$smarty->assign($smarty_params);
$smarty->display('results.tpl');

Log::$logger->trace("Results page done");
exit();
?>
