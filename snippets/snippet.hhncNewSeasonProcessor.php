<?php
// Grab the hhncmanager package
$path = MODX_CORE_PATH . 'components/hhncmanager/';
$result = $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');
$manager		= new HHNCManager(&$modx);

$output			= '';
$post			= $_POST;
$sani			= array();

if (is_array($post)) {

	foreach($post as $key => $data) {
		$sani[$key]		= filter_var($data, FILTER_SANITIZE_STRING); 	
	} // end foreach

}




if($post['confirm'] != '') {

	// if we're here, then we need to save this record 
	// and present the thank you chunk
	
if (!$manager->season->createNewSeason($sani)) {
		$output			.= $modx->getChunk('CreateSeasonFailed');
	} else {
		$output			.= $modx->getChunk('SeasonCreatedMsg');
	}
	
} elseif($post['submit'] != '') {
	
	// if we're here, the first submission has happened
	$start				= $sani['date'];
	$length				= $sani['length'];

	// we need to process the starttime/length a little more 
	// and get them into the form of timestamps
	$seaInfo			= $manager->season->getProposedSeasonData($start,$length);
	
	// call an array of start/end times to pass to the next method
	$ts					= array();
	$ts['start']		= $seaInfo['startts'];
	$ts['end']			= $seaInfo['endts'];

	// check to see if there are any conflicts with the 
	// pre-exisiting seasons in the db
	$conflict			= $manager->season->checkDateConflicts($ts);
	
	if ($conflict == 1) {
	
		// if there was a conflict, we need to present
		// the original form with some different data
		$data					= array();
		$data['errors']			= "<div class=\"errors\"><h3>Oops! There were some problems:</h3><p>There was a conflict with another season.  Please adjust your start date and/or length and try again.</p></div>";
		$data['seasonname']		= $sani['name'];
		$data['length']			= $sani['length'];
		$data['startdate']		= $sani['date'];
	
		$output 				.= $modx->getChunk('NewSeasonForm',$data);
	
	} else {
	
		// otherwise, present the season for confirmation
		$data					= array();
		$data['name']			= $sani['name'];
		$data['startday']		= $seaInfo['startfulldate'];
		$data['startts']		= $seaInfo['startts'];
		$data['endday']			= $seaInfo['endfulldate'];
		$data['endts']			= $seaInfo['endts'];
		
		
		$output					.= $modx->getChunk('verifySeasonCreation',$data);		
	
	} // end if/else
	
} else {
	// if nothing is submitted we can show the orignal form

	$data			= array();
	$data['length']	= '10';
	$output			.= $modx->getChunk('NewSeasonForm',$data);
	
}

//finally, return the output
return $output;







?>