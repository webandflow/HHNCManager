<?php
/*
    * hhncUserALaCarteOrdering
    * =-=-=-=-=-=-=-=-=-=-=-=-
    * This snippet presents a la carte ordering options to the user
    * and handles including foxy carte scripts to process the order
*/

// Grab the hhncmanager package
$path 			= MODX_CORE_PATH . 'components/hhncmanager/';
$result 		= $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
//require_once($path . 'classes/class.rc4crypt.php');
$manager 		= new HHNCManager(&$modx);
$output			= '';
$season         = $manager->getCurrentSeasonInfo();
$progress       = $manager->season->getDailyProgress();

// reassign some variables for ease of use later
$s              = $season['id'];
$w              = $progress['currentWeek'];

// determine if the user is eligible for a la carte ordering
$user           = $manager->getMemberCurrentInfo();
$uid            = $user['userid'];

if ($user['canALaCarte'] != 1) {
    // the user can't a la carte order
    $output     .= $modx->getChunk('hhncUserCannotALaCarte');
} else {
    // the user CAN a la carte order
   	// we'll need foxy cart, so we can register the api calls.
    // start by assuming week is still able to be altered
	// and check against weekly progress to determine
	// if the week should be closed
	$status			= $manager->weekStatus();
    if($status == true) {
    	$modx->regClientStartupScript('http://cdn.foxycart.com/homeharvestnc/foxycart.complete.js');
    	$modx->regClientCss('http://static.foxycart.com/scripts/colorbox/1.3.9/style1/colorbox.css');
    	
    	// grab this weeks catalog
    	$catalog       = $manager->getWeeklyCatalog($s,$w);
    	// grab the images
    	$img				= $modx->getObject('modTemplateVar',array('name' => 'produceImg'));
    	$availableALaCarte  = $modx->getObject('modTemplateVar',array('name' => 'produceCanALACarte'));
    	
    	
		$output .= "\n<div class=\"order-headings\">";
		$output .= "\n<div class=\"floatleft alignleft\">Produce Item</div>";
		$output .= "\n<div class=\"floatright alignright\">Quantity</div>";
		$output .= "\n<br style=\"clear: both\" />";
		$output .= "\n</div>";
    	
    	foreach ($catalog as $p) {
    	
    	   $data = array();
    	   $data['name'] = $p['name'];
    	   $data['id'] = $p['id'];
    	   $data['price'] = ($p['price'] != '') ? $p['price'] : '5.00';
    	   $data['productid'] = $p['id'];
    	   $data['userid'] = $uid;
    	   $data['desc'] = $p['introtext'];
    	   $data['img'] = $img->getValue($p['id']);
    
           if ($availableALaCarte->getValue($data['id']) == 'yes') {
            $output     .= $modx->getChunk('hhncALaCarteItemTpl',$data);	
            }
    	
    	} // end foreach
    } else { // if the week is closed to ordering
        // if the week is closed, alert the user
        $output		.= $modx->getChunk('hhncUserCustomizeCutoffTime');
    
    }
}

return $output;

?>