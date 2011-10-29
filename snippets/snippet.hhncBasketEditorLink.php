<?php
/*

	This snippet determines if the current catalog can be edited.
	Shows a link if possible, otherwise indicates that this is
	an active catalog and cannot be edited.

*/

// Grab the hhncmanager package
$path = MODX_CORE_PATH . 'components/hhncmanager/';
$result = $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 		= new HHNCManager(&$modx);
$progress		= $manager->season->getDailyProgress();

// grab the timestamp for right now.  We'll need it later
$now				= time(); // current time stamp

if($_GET['s'] != '' && $_GET['w'] != '') {
	// if we're given s and w
	$s			= filter_var($_GET['s'], FILTER_SANITIZE_STRING);
	$w			= filter_var($_GET['w'], FILTER_SANITIZE_STRING);
} else {
	// if we're not given s and w we need to find the next week
	// our first choice will be to check for the current season
	// alternatively, grab the next season info
	$season			= $manager->getCurrentSeasonInfo();

	// first, check to see if there is an active season
	if ($season != false) {
		$s			= $season['id'];
		$progress	= $manager->season->getDailyProgress();
		$w			= $progress['currentWeek'];
	} else {
	// if not, grab the next season info	
		$season		= $manager->season->getNextSeason($now,0);
		$s			= $season['id'];
		$w			= 1;
		$wkStarted  = false; 
	}
}

$editable			= true; // until proven otherwise


// we want to check several things....
// 1. we can't change past seasons catalogs
// 2. we can't chnage past weeks catalogs from this season
// 3. we can't change this weeks catalog if its already been set since 
// 		so much of the rest of the system is dependant on this catalog
if (!$seasonInfo) {
	$seasonInfo		= $manager->season->getSeasonById($s,1);
}

// 1. Past Seasons
// if there is no season info... we're most likely between seeasons.
if (empty($seasonInfo)) { 
	$editable = false; 

} else {
	$end		= $seasonInfo['end'];
	if ($end < $now) {
		$editable = false;	
	}
} // end if...else seasoninfo....

// 2. Past Weeks this season and
// 3. This weeks catalog

$hasBasket			= $manager->checkBasketExists($s,$w);
$wkStarted			= (!empty($wkStarted)) ? $wkStarted : $manager->hasWeekStarted($s,$w);

if ($editable == true && (($hasBasket == true) && ($wkStarted == true))) {
	// we don't want to kill editing if the catalog has 
	// yet to be set for this week.  Check that....
	
	$q=$modx->newQuery('WeeklyDefaults');
	$q->where(array(
		'seasonid' 	=> $s,
		'week'		=> $w
	));
			
	$qty		= count($modx->getCollection('WeeklyDefaults',$q));
	
	if ($qty > 0) {
		$editable 	= false;	
	} 
	
	$editable		= false;
	
}


if($editable != false) {
	$data['seasonnumber']			= $s;
	$data['week']					= $w;
	$output			= $modx->getChunk('hhncBasketEditable',$data);
} else {
	$output			= $modx->getChunk('hhncBasketNotEditable');
}

return $output;


?>