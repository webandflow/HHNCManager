<?php
// ver: 0.1


// Grab the hhncmanager package
$path 				= MODX_CORE_PATH . 'components/hhncmanager/';
$result 			= $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 			= new HHNCManager(&$modx);
$user				= $manager->getMemberCurrentInfo();
//$isEnrolled			= $manager->userEnrolled();


// determine if this is a petite user
$isRegular = ($user['levelNumber'] >= 3) ? 2 : 1;

// init the output
$output				= '';

// we need to get this weeks selection
// the logic will need to go here....
$selections			= $manager->getThisWeeksSelection();

// we'll use $list to hold each of the list items that we retrieve
$list				= '';
if (!empty($selections)  && $user['canCustomize'] == 1) {
	
	foreach($selections[$isRegular] as $item) {
	
		if($item['qty'] > 0) {
			$list			.= "\n\t<li class=\"selectionItem\">" . $item['name'] . " <span class=\"selectionQty\">(x " . $item['qty'] . ")</span></li>";
		}
	
	} // end foreach
	$output				.= "\n<h3>This Week's Selections:</h3>";
	$output				.= "\n<ul>";
	$output				.= $list;
	$output				.= "\n</ul>";
	
} elseif (empty($selections) && $user['canCustomize'] == 1) {

	$output 		.= "<p>This weeks catalog hasn't been finalized.  Please check back later</p>";
	
} elseif ($user['levelNumber'] != 0) {
	$output 		.= $modx->getChunk('hhncUpsellClassic');
	
}

return $output;

?>