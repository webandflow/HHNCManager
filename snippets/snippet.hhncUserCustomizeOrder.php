<?php


/* THIS SHOULD BE CONVERTED TO USE A CHUNK AS A TEMPLATE!!! */

/*
	* hhncUserCustomizeOrder
	=-=-=-=-=-=-=-=-=-=-=-=-
	* Use this form to customize the orders for heirloom members
	* This form should only be presented to Heirloomers, or Classics
	* if have have the can_customorder permission

*/
// Grab the hhncmanager package
$path 				= MODX_CORE_PATH . 'components/hhncmanager/';
$result 			= $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

$output				= '';
$post				= $_POST;

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 			= new HHNCManager(&$modx);
$user				= $manager->getMemberCurrentInfo();
$seasonspecs		= $manager->getCurrentSeasonInfo();
$seasoninfo			= $manager->season->getSeasonById($seasonspecs['id'],1);
$progress			= $manager->season->getDailyProgress();



if(!empty($post)) {
	// now, what to do with the submissions.....
	// We need to put this items into a json array and
	// store it in the database...

	// if this passes validation, then move on

	$data					= array();
	$data['seasonid']		= $seasonspecs['id'];
	$data['week']			= $progress['currentWeek'];
	$data['userid']			= $user['userid'];
	$data['data']			= json_encode($post);

	if ($manager->updateUserSelections($data) == true) {
	
		$output				.= $modx->getChunk('hhncUserUpdateSelectionsSuccess');
	
	} else {
	
		$output				.= $modx->getChunk('hhncUserUpdateSelectionsFailed');
	
	}

} else {

if ($user['canCustomize'] != '1') {
	// if the user does not have the canCustomize permission
	// then we need to send 'em packin'....
	
	$output			.= $modx->getChunk('hhncUserCannotCustomize');
	
} else {
	// start by assuming week is still able to be altered
	// and check against weekly progress to determine
	// if the week should be closed
	$status			= $manager->weekStatus();
	if($status == true) {
		$modx->regClientScript('core/components/hhncmanager/js/jquery.pointscalculator.js');
		// init the filler array
		$filler			= array();
				
		// Since the user does have the canCustomize permission, we can present
		// the customization form for them to select their produce.  If the user
		// has already submitted a selection, we should grab that instead of the
		// default selections.
	
		$sel = $manager->getUsersSelection($user['userid'],$seasonspecs['id'],$progress['currentWeek']);
			
		// max user points
		$maxpoints			= $user['levelPoints'];
	
		// We need to grab the entire catalog this this week.... not just the selections
		$catalog			= $manager->getWeeklyCatalog($seasonspecs['id'],$progress['currentWeek']);

		$item				= array();
		
		$filler['formbody'] .= "\n<div class=\"order-headings\">";
		$filler['formbody'] .= "\n<div class=\"floatleft alignleft\">Produce Item</div>";
		$filler['formbody'] .= "\n<div class=\"floatright alignright\">Quantity</div>";
		$filler['formbody'] .= "\n<br style=\"clear: both\" />";
		$filler['formbody'] .= "\n</div>";
		
		$img				= $modx->getObject('modTemplateVar',array('name' => 'produceImg'));
		
		foreach($catalog as $item) {
			$fItem['id']				= $item['id']; // form item = fItem
			$fItem['introtext']			= $item['introtext'];
			$fItem['points']			= $item['points'];
			$fItem['name']              = $item['name'];
			$fItem['img']               = $img->getValue($fItem['id']);
        			
			foreach($sel as $s) {				
				if($s['id'] == $fItem['id']) { 
					$fItem['qty']		= $s['qty'];
				 }
			}
		  
		    $qty = ($fItem['qty'] != '') ? $fItem['qty'] : 0;
		
			$filler['formbody']		.= "\n<div class=\"selection-editor-item cf\">";
			$filler['formbody']     .= "\n<div class=\"editor-image-float\">";
			$filler['formbody']     .= "\n<img src=\"" . $fItem['img'] . "\" title=\"" . $fItem['name'] . " image\" class=\"catalog-image\" />";
			$filler['formbody']     .= "\n</div>";
			$filler['formbody']     .= "\n<div class=\"editor-text-float\">";
			$filler['formbody']		.= "\n\t<input class=\"selection-editor-qty\" type=\"text\" name=\"" . $fItem['id'] . "\" value=\"" . $qty ."\" />";
			$filler['formbody']		.= "\n<label for=\"" . $fItem['id'] . "\">" . $fItem['name'] . "</label>";
			$filler['formbody']		.= "\n\t<p>point value: <span class=\"points-display\">" . $fItem['points'] . "</span></p>";
			$filler['formbody']		.= ($fItem['introtext'] != '') ? "\n<p style=\"text-align: left\">" . $fItem['introtext'] . "</p>" : '';
			$filler['formbody']     .= "\n</div>";
			$filler['formbody']		.= "\n</div>";
		
		} // end foreach

		$filler['formbody']			.= "<div id=\"total-points\">total points: <span id=\"total-points-display\"></span>&nbsp;of&nbsp;<span id=\"max-user-points\">" . $maxpoints . "</span></div>";	
		$output				= $modx->getChunk('hhncUserChangeSelectionForm',$filler);
	
	} else {
	
		// if we're past the cut off time, make sure the user knows
		$output		.= $modx->getChunk('hhncUserCustomizeCutoffTime');
	
	} // end if-progress	

} // end if canCustomize
}

return $output;