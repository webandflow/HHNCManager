<?php
// Grab the hhncmanager package
$path = MODX_CORE_PATH . 'components/hhncmanager/';
$result = $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
require_once($path . 'classes/class.seasonprocessor.php');
$manager 		= new HHNCManager(&$modx);

$output			= '';

// season/week data
$s				= $_GET['s'];
$w				= $_GET['w'];
$data			= array();

if ($s && $w) {
	// if both GET variables were set, we can pull the latest catalog here
	$items		= $manager->viewWeeklyCatalog($s,$w);
	$data['seasonnumber']		= $s;
	$data['weeknumber']			= $w;

} else {
	// if not, we need to figure out what the season and week numbers are
	$season		= $manager->getCurrentSeasonInfo();
	$s			= $season['id'];
	$week		= $manager->season->getProgressInfo();
	$w			= $week['currentWeek'];

	$items		= $manager->viewWeeklyCatalog($s,$w);

}


$seasoninfo			= $manager->season->getOtherSeasonsInfo($s);

$data['seasonnumber']	= $s;
$data['seasonname']		= $seasoninfo['name'];
$data['weeknumber']		= $w;


if (count($items) > 0) { 

// Grab the header section of the page
$output				.= $modx->getChunk('hhncCatalogViewerInstructions',$data);

	foreach($items as $item) {
		$output		.= $item;	
	} // end foreach

} else {

	$output			.= $modx->getChunk('hhncCatalogNoCatalogInstructions',$data);

}

return $output;

?>