<?php
// Grab the hhncmanager package
$path = MODX_CORE_PATH . 'components/hhncmanager/';
$result = $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 		= new HHNCManager(&$modx);
$output			= '';

// get the GETs
$s					= filter_var($_GET['s'],FILTER_SANITIZE_STRING);
$w					= filter_var($_GET['w'],FILTER_SANITIZE_STRING);

$a					= $manager->getThisWeeksSelection($s,$w);

$img				= $modx->getObject('modTemplateVar',array('name' => 'produceImg'));
$point				= $modx->getObject('modTemplateVar',array('name' => 'producePoints'));

if(count($a) > 0) {

	$output					.= "<h1>Weekly Baskets</h1>";


	foreach ($a as $level => $produce) {
		
		$q			= $modx->newQuery('PointLevels');
		$q->where(array('id' => $level));
		$l			= $modx->getCollection('PointLevels',$q);
		
		foreach ($l as $r) {	
			$modifier		= $r->get('title_modifier');
			$output			.= "\n<div class=\"basket-item-group\">";
			$output			.= "\n<h2>Default " . $modifier . " Basket</h2>";
			$output			.= "\n\t<p class=\"instructions\">The following items have been selected for the " . $modifier ." level members.  Classic members will not have the opportunity to see/edit this selections, but heirloom members may select other items from the catalog.  If they do not change anyting by the order cutoff time, their orders will be automatically generated. </p>";
			
		} // end foreach
	
		$output				.= "\n<ul class=\"basket-list\">";
		$points				= 0;
		foreach ($produce as $product) {
	
			$id				= $product['id'];
			$data			= array();
			$data['name']	= $product['name'];
			$data['qty']	= $product['qty'];
			$data['img']	= $img->getValue($id);
			$itempoints		= $point->getValue($id);
			$itemTotal		= $data['qty']*$itempoints; 
			
			$points			+= $itemTotal;
			if($data['qty'] > 0) {	
				$output			.= $modx->getChunk('hhncBasketItemTpl',$data);
			}
		
		} // 	
		$output				.= "\n</ul>";
		$output				.= "\n<p>Total Points: $points</p>";
		$output				.= "\n</div>";
	
		
	} // end foreach
} else {
	$data				= array();
	$data['season']		= $s;
	$data['week']		= $w;
	$output				.= $modx->getChunk('hhncNoBasketFound',$data);

}

return $output;
?>