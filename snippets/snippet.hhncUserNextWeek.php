<?php
// Grab the hhncmanager package
$path = MODX_CORE_PATH . 'components/hhncmanager/';
$result = $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 		= new HHNCManager(&$modx);
$output			= '';

// we want to see if there is a next week to display.
// If so, call the hhncAdminNextWeekTpl chunk and fill
// in the blanks.  Otherwise, call the hhncAdminNoNextWeekTpl
// chunk which suggests some possible next things to do.			
$nw					= $manager->season->getNextWeek(); // returns an array of $x['season'], $x['week']

if ($nw != false) {
	$nw['seasonclass']	= ($nw['currentseason'] == 1) ? "this-season" : "attention";
	
	$output				.= $modx->getChunk('hhncAdminNextWeekTpl',$nw);
} else {
	$output				.= "<h3>There are future seasons scheduled.</h3>";
}

return $output;

?>