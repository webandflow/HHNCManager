<?php
// Grab the hhncmanager package
$path = MODX_CORE_PATH . 'components/hhncmanager/';
$result = $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 		= new HHNCManager(&$modx);
$user			= $manager->getMemberCurrentInfo();

if (!empty($_POST)) { 
	$add			= array();
	foreach($_POST as $key => $value) {
		$value		= filter_var($value,FILTER_SANITIZE_STRING);		
		$add[$key]	= $value;
	}
	
	if($add['type'] == 1) {
		// this is a home delivery address and needs to be handled a little more rigidly than
		// the regular pickup location addresses.... they've been sanitized.... should we validate?
		if ($manager->validateAddress($add) == true) {
		
			// $removed		= $manager->deactivateDeliveryAddress($user['userid']);
			$added			= $manager->addDefaultDeliveryAddress($user['userid'],$add);
			$output .= "<p>Your Address has been successfully updated.</p>";
		      $output .= "<p><a href=\"" . $modx->makeUrl(29) . "\">Back to the main menu.</a></p>";
		
		} else {
		
			$output		.= $modx->getChunk('hhncFormUserCustomAddressTpl',$add);	
		
		}
		
	
	} else {
		// here, it's simply writing an affiliation between
		// the user and the address id in the default addresses table
		
		// we'll remove any existing address
		$removed			= $manager->removeDefaultPickupAddress($user['userid']);
		
		// and add the new one.
		$added				= $manager->addDefaultAddress($user['userid'],$add['addressid']);
		$output .= "<p>Your Address has been successfully updated.</p>";
		$output .= "<p><a href=\"" . $modx->makeUrl(29) . "\">Back to the main menu.</a></p>";
		
	}

} else {
	// is this a verification or a new entry?
	$v				= $_GET['v'];
	
	// get someother good info here
	$homeDel		= $user['canHomeDelivery'];
	$address		= $manager->getUserAddress($homeDel);
	$addressid		= $address['addressid'];
			
	if ($homeDel == 0) {
		// no matter what, we just need to present the dropdown list of pickup addresses
		$output		.= $manager->getPickUpAddressForm($addressid);
	
	} else if ($v==1) {
		// if this is a reverification we should go ahead and grab the address
		
		if ($user['canHomeDelivery'] == 1) {
			// if this is a home delivery address, we need the form
			$address['type'] = 1;
			$output		.= $modx->getChunk('hhncFormUserCustomAddressTpl',$address);		
			
		} 
		
	} else {
		// this is a new entry and needs to be created from scratch
		$address			= array();
		$address['type'] 	= 1;
		$output		.= $modx->getChunk('hhncFormUserCustomAddressTpl',$address);	
	}
}


return $output;

?>