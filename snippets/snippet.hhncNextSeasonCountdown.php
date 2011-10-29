<?php
/*

	NextSeasonCountdown Snippet
	Provides Links to purchase memberships for the upcoming season
	
	v. 0.4.1 ()
	
	v. 0.4 (Jun 18 2011)
	=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Updated snippet to correctly display "check back soon" message
	when no future season was available.
	
	v. 0.3 (Apr 4 2011)
	=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- edited snippet to include links to enroll if season is eligible
	- snippet includes notice of "enrollment closed" if needed
	
	v. 0.2.1 (Apr 2 2011)
	=-=-=-=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- corrected error which skipped then next season when a season 
	   was active.
	
	v. 0.2
	=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	- rewritten to take advantage of HHNCManager->season->getFutureSeasons
	
*/

// Grab the hhncmanager package
$path = MODX_CORE_PATH . 'components/hhncmanager/';
$result = $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 		= new HHNCManager(&$modx);
$output			= '';
$now			= time();

// see if we're currently in season
$current        = $manager->getCurrentSeasonInfo();
// get $user information
$user			= $manager->getMemberCurrentInfo();

// get the future seasons array
// without the current season included
if ($current != false) {
    $futureseasons = $manager->season->getFutureSeasons(3,1);
} else {
    $futureseasons = $manager->season->getFutureSeasons(3,0);
}

$prevseason = $manager->season->getSeasonById($futureseasons[0]['id'],1);
$opendate = $prevseason['startfulldate'];
$season = $manager->season->getSeasonById($futureseasons[1]['id'],1);


if ($season['id'] != NULL) {
	$data = array();
	$data['seasonname'] = $season['name'];
	$data['datespan'] = $season['starttextdate'] . " - " . $season['endtextdate'];

	// check the enrollment information and include necessary info
	$enr_close_limit = HHNC_ENR_CLOSE_LIMIT_SECONDS;
	$enr_open = ($season['start'] - $now > $enr_close_limit) ? true : false;
	
	//check to see if the logged in user is enrolled
	$user_enr = $manager->userEnrolled($season['id']);
	
	if ( $enr_open == true ) {
		
		// if enrollment is open, 
		$data['enr_message'] = ($user_enr != false) ? "<h5>You're already enrolled for this season" : "<h5><a href=\"" . $modx->makeUrl(45) . "\" title=\"Enroll for this season\">Enroll now for this season</a></h5>";
	} elseif ( $enr_open == false ) {
	
		$data['enr_message'] = ($user_enr != false) ? "<h5>You're enrolled for this season.</h5>" : "<h5>Enrollment is now closed.</h5>";
	
	}
	// prepare the output
	$output .= $modx->getChunk('hhncUserComingSoon',$data);
} else {

	$output .= $modx->getChunk('hhncUserNotComingSoon');

}


return $output;

?>