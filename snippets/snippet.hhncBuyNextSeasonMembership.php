<?php
/*

	* BuyNextSeasonMembership
	=========================
	* v: 0.2.1
	* =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	* - edited error page to point at resource 469 in MODX (line 52)
	*
	* v: 0.2
	* =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    * - edited to include main config setting for enrollment close days
    *
	* v: 0.1
	* =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	* Used by HHNCManager to determine if user is eligible 
	* to purchase an enrollment for the upcoming season
	* If so, it presents the grid of information from the 
	* membership brochure with links to foxycart.
	* This page will require links to the foxycart api

*/

// Grab the hhncmanager package
$path 			= MODX_CORE_PATH . 'components/hhncmanager/';
$result 		= $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');


// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
require_once($path . 'classes/class.rc4crypt.php');
$manager 		= new HHNCManager(&$modx);
$output			- '';

// some global data...
$user			= $modx->user->get('id');

// We need to establish a few things:
// What time is it?
$now			= time();
	// If there's a problem here, send an error....
	if ($now == '') { $modx->sendErrorPage(); }

// get the next season details.
$nextseason		= $manager->season->getNextSeason($now);

if($now > $nextseason['start']) {
	// if we're here, somehow the system got confused and tried
	// to let users purchase a membership for a past season
	// so let's take to of that.
		
	$modx->sendForward(469); // send to error page

}

// Check to see if the user already has purcashed a membership for this season?
$enrollment		= $manager->userEnrolled($nextseason['id'],$user);

if(is_array($enrollment)) {
	
	$seasonInfo						= $manager->season->getSeasonById($enrollment['season'],1);
	$seasonInfo['verification_date']= $manager->season->intToDate($enrollment['membership_verified']);
	
		
	// if there is an array returned, it means the user has already enrolled
	$output		= $modx->getChunk('hhncUserAlreadyEnrolled',$seasonInfo);

	} elseif($enrollment == -1) {
		// if there is a -1 returned, there's an error
		$modx->sendForward(1); // send to a "contact us" error page


} else {

// if we're still here.... then the user doesn't already have a membership purchased for this season


// Now, is the season open for enrollment based on days?
if(($nextseason['daysToStart'] > HHNC_ENR_CLOSE_LIMIT_DAYS) && ($nextseason['daysToStart'] < $manager->purchaseWindowHigh)) {
	$open		= true;
	} else {
		$open		= false;
		$modx->sendForward(46);
}

if ($open == true) {


	// now that we're here, everything checks out and the users
	// can be presented with the various options for purchasing 
	// an enrollment for the upcoming season. 
	
	// we'll need foxy cart, so we can register the api calls.
	$modx->regClientStartupScript('https://cdn.foxycart.com/homeharvestnc/foxycart.complete.js');
	$modx->regClientCss('https://static.foxycart.com/scripts/colorbox/1.3.9/style1/colorbox.css');	
	
	// How Many Weeks long is this season?
	$length = round(($nextseason['end'] - $nextseason['start'])/604800);
	
	// Compile Data to pass to the membership table chunk...
	$content					= array();
	$content['userid']			= $user;
	$content['seasonname']		= $nextseason['name'];
	$content['seasonid']		= $nextseason['id'];
	$content['startfulldate']	= $nextseason['startfulldate'];
	$content['endfulldate']		= $nextseason['endfulldate'];
	
  $content['price-lvl1'] = 25*$length;
  $content['price-lvl2'] = 30*$length;
  $content['price-lvl3'] = 35*$length;
  $content['price-lvl4'] = 40*$length;
  
	// Now we can present the chunk with the various purcase options	
	$output					= $modx->getChunk('purchaseMembershipsTable',$content);
	
	// NEW IN VERSION 0.5.0 ----
	// We're also going to need to register a jquery script to handle zip filtering
	$modx->regClientScript('assets/js/jquery.zipcodefilter.js');
	
}

}

return $output;



?>