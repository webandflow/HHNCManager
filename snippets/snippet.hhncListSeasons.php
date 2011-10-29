<?php
// Grab the hhncmanager package
$path = MODX_CORE_PATH . 'components/hhncmanager/';
$result = $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
require_once($path . 'classes/class.seasonprocessor.php');
$manager 	= new HHNCManager(&$modx);
$season		= $manager->getCurrentSeasonInfo();

// the number of seasons to show if not specified in the snippet call
$q			= (isset($q)) ? $q : 3;

$seasons	= $manager->season->getFutureSeasons($q);

if ($seasons != false) {

$qty		= (count($seasons) < 3) ?  $qty : $q;

$output		= '';

$output		.= "<h3>Upcoming Seaons</h3>";
$output		.= "<p class=\"instructions\">Here are the next $qty scheduled seasons.  Click on any of them to make changes. </p>";
$output		.= "<ol class=\"seasonlist\">";

foreach ($seasons as $season) {
	$output	.= "<li><a href=\"[[~36]]?id=" . $season['id'] . "\" title=\"Click to edit this season\">" . $season['name'] ."</a>";
	$output	.= "<br /><span class=\"datespan\">" . $manager->season->intToDate($season['start'], "F j, Y") . " - " . $manager->season->intToDate($season['end'], "F j, Y") . "</span>";
	$output .= "</li>";

}

$output		.= "</ol>";

$output		.= "<p><a href=\"[[~]]\"></a></p>";

} else {

$output		.= "<p>There are no seasons currently scheduled.  You might <a href=\"" . $modx->makeUrl(33) . "\" title=\"Create a Season\"> want to create one :)</p>";

}

return $output;


?>