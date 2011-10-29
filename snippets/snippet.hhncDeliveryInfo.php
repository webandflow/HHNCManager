<?php
/*

	* hhncDeliveryInfo Snippet
	==========================
	This snippet is used to display the address to which
	baskets will be delivered - they are either the home addresses
	of heirloom members or the pickup locations for Classic members

*/

// Grab the hhncmanager package
$path 			= MODX_CORE_PATH . 'components/hhncmanager/';
$result 		= $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');

$manager 		= new HHNCManager(&$modx);
$user			= $manager->getMemberCurrentInfo();

// If the user is not a free member....
if($user['levelNumber'] != 0){
	
	// show introductory information depending on whether this is a 
	// heirloom or classic member.
	$addressOutput 	= ($user['canHomeDelivery'] == 1) ? $modx->getChunk('homeDeliveryInfo') : $modx->getChunk('pickupLocationInfo');
	
	// init the output
	$output			= '';
	$output			.= $addressOutput;
	$address		= $manager->getUserAddress($user['canHomeDelivery']);
	
	if (!empty($address)) {
		// if we're here, then the use has already selected a default address
		// for their level of membership.  We should display it here by passing
		// the array to the address format chunk in MODX.
		$output .= $modx->getChunk('addressDisplayTpl',$address);
		// $output .= $modx->getChunk('addressLockInInstructions');
	
	} else {
	
		$output .= "<p>show way to select/enter address</p>";
		
	}
}

return $output;

?>