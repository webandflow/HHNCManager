<?php
// Grab the hhncmanager package
$path = MODX_CORE_PATH . 'components/hhncmanager/';
$result = $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');


// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 		= new HHNCManager(&$modx);
$output			= '';

// look for the data that we need
$week			= (isset($week)) ? $week : $manager->season->getCurrentWeek();

$output			.= $modx->getChunk('hhncAdminThisWeekTpl');

return $output;

?>