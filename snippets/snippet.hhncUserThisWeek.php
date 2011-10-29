<?php
// Grab the hhncmanager package
$path = MODX_CORE_PATH . 'components/hhncmanager/';
$result = $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');


// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 		= new HHNCManager(&$modx);
$user			= $manager->getMemberCurrentInfo();
$season			= $manager->getCurrentSeasonInfo();
$output			= '';

// look for the data that we need
$week			= (isset($week)) ? $week : $manager->season->getCurrentWeek();

$output			.= "<div class=\"menu-widget\" id=\"thisweek\">";
$output			.= "<h3>My Home Harvest This Week</h3>";
//$output			.= "<p class=\"instructions\">Welcome to your control panel.  From here you can access the various parts of your account that you may need to change from time to time.  Heirloom members can submit orders from here as well as place a la carte orders.  Classic members can verify their address information.</p>";

if($user['canCustomize'] == 1 || $user['canALaCarte'] ==1) {

	$output		.= "<div id=\"heirloomMenu\">";
	
	if ($user['canCustomize'] == 1) {
	
		$output	.= "<a href=\"" . $modx->makeUrl(47) ."\" title=\"Customize My Order\" class=\"heirloomMenuButton\" id=\"customizeButton\"></a>";
	
	}
	
	if ($user['canALaCarte'] == 1) {
	
		$output	.= "<a href=\"" . $modx->makeUrl(51) ."\" title=\"A La Carte Ordering\" class=\"heirloomMenuButton\" id=\"alacarteButton\"></a>";
	
	}
	
	$output		.= "</div>";

	// check to see if the user has submitted an edited selection
	if($user['canCustomize'] == 1 && $manager->userHasSubmittedSelections($user['userid'],$season['id'],$week)) {
	
		$sel		= $manager->getUsersSelection($user['userid'],$season['id'],$week);
		if (!empty($sel)) {
		$output		.= "\n<h3>My Selections</h3>"; 
		$output		.= "\n<p class=\"instructions\">Here are the selections that I've made for this week.</p>";
		$output		.= "\n<ul>";
		  foreach($sel as $item) {
		  $output	.= "\n\t<li>" . $item['name'] . " - " . $item['qty'] . " order(s)</li>";
		  }
		$output		.= "\n</ul>";
		}
	
	} else {
	
		
		$output		.= "\n<h3>Make your Selections</h3>";
		$output		.= "\n<p class=\"instructions\">Your membership has the <em>custom ordering</em> feature enabled.  If you haven't already had a chance to make your selection, <a href=\"" . $modx->makeUrl(47) . "\" title=\"Click here to customize your order\">click here to do it now.</a></p>\n<p>Of course, if you like what we've selected by default, there's no need to do anything.  The order will automatically be submitted and your items will be delivered!.</p>";
	}
	
} // end if




/*

    THIS CODE WAS COMMENTED OUT ON APRIL 4 2011 TO REMOVE ADDRESS CHECKING FUNCTIONALITY


	$addressOutput 	= ($user['canHomeDelivery'] == 1) ? $modx->getChunk('homeDeliveryInfo') : $modx->getChunk('pickupLocationInfo');
	$output			.= $addressOutput;
	$address		= $manager->getUserAddress($user['canHomeDelivery']);
	if (!empty($address)) {
	
		// if we're here, then the use has already selected a default address
		// for their level of membership.  We should display it here by passing
		// the array to the address format chunk in MODX.
		
		$output .= $modx->getChunk('addressDisplayTpl2',$address);
		
	
	} else {
	
		$output .= "<p>show way to select/enter address</p>";
		
	}
*/




$output			.= "</div>";
	



return $output;

?>