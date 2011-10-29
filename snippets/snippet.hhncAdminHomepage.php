<?php
// Grab the hhncmanager package
$path = MODX_CORE_PATH . 'components/hhncmanager/';
$result = $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 	= new HHNCManager(&$modx);

$currentWeek= $manager->season->getCurrentWeek(); 

// init
$output		= '';

$currentInfo= $manager->getCurrentSeasonInfo();

if (!empty($currentInfo)) {
// First, set up the welcome message section
$data				= array();
$data['startdate']	= $manager->season->intToDate($currentInfo['start']);
$data['enddate']	= $manager->season->intToDate($currentInfo['end']);
$data['title']		= $currentInfo['name'];
$data['weeknumber']	= $currentWeek;
$data['totalweeks'] = $manager->season->getProgressDetail('totalWeeks');


// output the welcome message
$output				.= $modx->getChunk('hhncSystemHomepageWelcomeTpl',$data);

} else {

	// if we're here, we're not currently in a season so we don't need to show
	// the current season box... maybe we do a quick script to find the next
	// season and display something short about it here.
	
	$output			.= "<h3 style=\"text-align: center; margin: 15px auto 30px auto\">We are not currently in a season.  The next season will begin soon.</h3>";

}

// Now, we want to deliver the "this week" widget
// This should only be delivered if we're in a "valid" week
// as determined by an integer response from SeasonProcessor::getCurrentWeek();
// it returns FALSE if we are not in a valid week

if ($currentWeek != FALSE && $currentWeek <= $data['totalweeks']) {
	$output			.= $modx->runSnippet('hhncAdminThisWeek');
}

// Here, we present the To-Do List for Next Week where HHNC will need to
// enter which products are available and what the default "carts" will
// be - of course, only heirloom members can change their orders
// unless HHNC has granted users the ability to customize their order

$output				.= $modx->runSnippet('hhncAdminNextWeek');

return $output;








