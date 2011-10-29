<?php
/* 
    HHNCUserHomepage Generator
    =-=-=-=-=-=-=-=-=-=-=-=-=-
    v: 0.2 - Mar 14 2011
    
    This snippet is used to generate the user profile homepage, including grabbing 
    views for next season information, etc.
    
    
    // changelog //
    =-=-=-=-=-=-=-=
    v: 0.2
    - added view for next season info when user isn't enrolled for it    



*/

// Grab the hhncmanager package
$path = MODX_CORE_PATH . 'components/hhncmanager/';
$result = $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 		= new HHNCManager(&$modx);
$currentWeek	= $manager->season->getCurrentWeek();

// some global data...
$user			= $modx->user->get('id');
$userInfo		= $manager->getMemberCurrentInfo();
$now            = time();
 	 
// init
$output		= '';

// get current season info
$currentInfo= $manager->getCurrentSeasonInfo();

if (!empty($currentInfo)) {

	// First Check the ToDos
	$todo		= $manager->checkUserToDos($user);

	$userEnrolled		= $manager->userEnrolled($currentInfo['id'],$user);
	$progress			= $manager->season->getDailyProgress();
	
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
	
	$nextseason    = $manager->season->getNextSeason($now,1);
    $user          = $modx->user->get('id');
	$enr           = $manager->userEnrolled($nextseason['id'],$user);

    $data = array();
    $data['seasonname'] = $nextseason['name'];
    $data['datespan'] = $nextseason['startfulldate'] . " - " . $nextseason['endfulldate'];
    
    if (is_array($enr)) {
        // if the user is enrolled for the next season
        $data['enrolled-class'] = " is-enrolled";
        $data['enrollmentmsg'] = "<p>Thanks. You're already enrolled for this season.  Come back once the season has started for more information.</p>";
    
    } else {
        // if the user isn't enrolled
        $data['enrolled-class'] = " not-enrolled";
        $data['enrollmentmsg'] = "<a href=\"" . $modx->makeUrl(45) . "\">Click here to continue the enrollment process.</a>";
    }
    
	$output        .= $modx->getChunk('hhncUserSeasonPitchTpl',$data);
	
}

// Now, we want to deliver the "this week" widget
// This should only be delivered if we're in a "valid" week
// as determined by an integer response from SeasonProcessor::getCurrentWeek();
// it returns FALSE if we are not in a valid week


if ($todo['address'] == 9999999999999 && $userInfo['levelNumber'] > 0) {
	// if there is no address yet, present the "need your address" chunk
	$output				.= $modx->getChunk('hhncUserNeedYourAddress');



/*

THIS SNIPPET OF CODE WAS COMMENTED OUT ON APRIL 4TH 2011 TO ELIMINATE CHECKING FOR 
DELIVERY/PICKUP ADDRESSES.... TO REENABLE, UNCOMMENT THIS SECTION and replace the 99999999999 above with false;

} elseif ($todo['address']['verified'] <  $currentInfo['start'] && $userInfo['levelNumber'] > 0) {
	// if the address hasn't been reverified for this season
	// present the address reverification chunk
	$output				.= $modx->getChunk('hhncUserVerifyAddress');
*/



} else {	
	if (($currentWeek != FALSE && $currentWeek <= $data['totalweeks']) && $userInfo['levelNumber'] > 0) {
		$output			.= $modx->runSnippet('hhncUserThisWeek');
	} else {
        // we want to indicate that the user isn't enrolled, but first
        // we want to check to see if we're even in a season
        
		if ($currentSeason == false && $currentInfo != false) {
            $output				.= $modx->getChunk('hhncUserNotEnrolledThisSeason');
        }
	}

}


// Let's show a don't forget widget here
$output .= $modx->getChunk('hhncUserDoNotForget');

return $output;

?>