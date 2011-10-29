<?php
// Grab the hhncmanager package
$path = MODX_CORE_PATH . 'components/hhncmanager/';
$result = $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 	= new HHNCManager(&$modx);

// grab/sanitize the get varibles from the URL

if ($_POST['submit'] != '') {
	// everything submitted here should be included in next
	// week's catalog.  Add the id to the database 
	
	// The entire POST variable can be passed along to the
	// processor method after a quick sanitze just in case
	
	$data 		= $_POST;
	$result 	= $manager->processCatalogSubmission($data);
	
	$output		.= $modx->getChunk('hhncAdminCatalogSubmission');

} else {



	$s			= filter_var($_GET['s'], FILTER_SANITIZE_STRING);
	$w			= filter_var($_GET['w'], FILTER_SANITIZE_STRING);


$output		= '';

/*
	This Snippet is designed to allow HHNC Admins to indicate which
	Products are avaialble for order for the coming week.  This step
	will have to be completed  prior to the default selections being
	entered for classic and heirloom members
*/


// Double - check to see that user is admin
$user		= $modx->user;
$isAdmin	= $user->isMember('Website Administrators');

if (!$isAdmin) {
	$modx->sendUnauthorizedPage();
} // send them away....



// First we should check to see if this week can be edited...
// If it has already started, or it's in the past, it can't 
// be edited and we can just show the catalog without
// any editing capabilities

$now				= time();
$inProgress			= $manager->season->checkWeekHasPassed($s,$w,$now);

// it could be that the week has started, but the catalog hasn't been added
// in that case we would still want to allow HHNC to edit the catalog
$catalogExists		= $manager->checkCatalogExists($s,$w);

if($inProgress == -1) {
	// a status of -1 means that we couldn't find the season in the database. 
	// This should only happen if someone tried to enter a season id
	// directly into the url

	$output			.= "<h3>Error</h3>";
	$output			.= "<p>Sorry - we couldn't find a record of that season in the database</p>";

} elseif($inProgress == 1 && $catalogExists) {

	// if the week has started and a catalog has been registered then we 
	// need to make sure that the catalog can't be edited.  Instead,
	// we should be able to display the current catalog below

	$output			.= "<h3>Sorry</h3>";
	$output			.= "<p>This week has already started and there is already a catalog in the database so you can't edit it any longer. Below you can see the list of items available for this week.</p>";
	$output			.= "";

} else {

	$products		= $manager->getAllProducts(); // returns an array of MODX objects
	$seasonInfo		= $manager->season->getOtherSeasonsInfo($s);
	$d['seasonname']= $seasonInfo['name'];
			
	if (count($products) > 0) {
	
		$description		= $modx->getObject('modTemplateVar',array('name' => 'produceDescription'));
		$img				= $modx->getObject('modTemplateVar',array('name' => 'produceImg'));
	
	
		foreach ($products as $product) {
		
			$data			= array();
			$data['id']		= $product->get('id');
			$data['name']	= $product->get('pagetitle');
			$data['desc']	= $description->getValue($data['id']);
			$data['img']	= $img->getValue($data['id']);
			
			
			$formbody			.= $modx->getChunk('hhncAdminCatalogItemTpl',$data);
			$d['data']			= $formbody;
			$d['seasonid']		= $s;
			$d['weeknumber']	= $w;
		
		}
		
		$output			.= $modx->getChunk('hhncAdminCatalogForm',$d);
	
	} else {
	
		$output		.= "<p>No Products Found</p>";
	
	}

	
	
	
}
}

return $output;


?>