<?php
// Grab the hhncmanager package
$path = MODX_CORE_PATH . 'components/hhncmanager/';
$result = $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 		= new HHNCManager(&$modx);
$output			= '';

// first check to see if the catalog exists for this week.  If it does we can proceed.
// if not, we'll present a chunk that says, "Sorry, enter the catalog first"

// get the GETs
	$s					= $_GET['s'];
	$w					= $_GET['w'];

	$data				= array();
	$data['s']			= $s;
	$data['w']			= $w;


if(!empty($_POST)) {

	$submissions		= $_POST;
	$selections			= array();
	$s					= $submissions['s'];
	$w					= $submissions['w'];
	
	$qtySub				= count($submissions);
	$i=0;
	foreach($submissions as $key => $sub) {
	
		if(is_array($sub)) {
			
			$selections[0][$i]		= "\"$key\" : \"{$sub[0]}\"";
			$selections[1][$i]		= "\"$key\" : \"{$sub[1]}\"";		
			
		} // end if
	
		$i++;
	
	} // end foreach
	

	foreach ($selections as $key => $info) {
		
		$j[]			= implode(',',$info);
		
	}
	
	foreach ($j as $key => $info) {
		$key++;
		$json_inside[] = "\"$key\" : { " . $info . " }";
	
	}
	
	$json_contents		= implode(',',$json_inside);
	$json_output		= "{\"selections\" : { " . $json_contents . " } } ";

	if(json_decode($json_output)) {
	  
		// if this is valid json we should be able to save it
		// in the database 
		$saved			= $manager->processDefaultBasket($json_output,$s,$w);
		
		if ($saved) {
		
			print "<p>Get the saved basket success chunk</p>";
		
		} else {
		
			print "<p>Get the basket save error chunk</p>";
		}
	 	  
	} else {
	
		print "<p>Bad JSON Value</p>";
	
	}


// Should be able to store this json now



	
} elseif($_GET['s'] == '' || $_GET['w'] =='' || !is_numeric($_GET['w']) || !is_numeric($_GET['w'])) {
	// if there are problems with the GET variables, throw an error
	$modx->sendForward(41);

} else {

	// check to see if the catalog exists for this week
	$catalogExists		= $manager->checkCatalogExists($s,$w);
	
	if (!$catalogExists) {
		// if the catalog doesn't exist, then we will need to 
		// do that first.  Send a message to let the user know
		$output			.= $modx->getChunk('hhncCatalogNotSet',$data);
	
	} else {
		// otherwise, we can assume that we can go ahead and 
		// create new defaults.
		
		// we need to present the available items in a table
		// format where hhnc admins can set the quantity for 
		// each item.  We should also calculate the number 
		// of points being tallied, etc - that'll be
		// the work of jQuery.
	
		// How many different point levels are there?
	
		$levels			= $manager->getPointLevels();		
		// We want to create a table that has at least three
		// columns - the name and the petite and regular fields
		
		$catalog		= $manager->getWeeklyCatalog($s,$w);
		
		// Call the view method
		$modx->regClientScript('core/components/hhncmanager/js/jquery.basketpointscalculator.js');
		$editorview		= $manager->getBasketView($levels,$catalog);
		$output			.= $editorview;
			
	}


}


return $output;


?>