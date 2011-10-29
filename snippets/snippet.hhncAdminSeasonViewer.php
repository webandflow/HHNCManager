<?php
/*
	* hhncAdminSeasonViewer
	* =-=-=-=-=-=-=-=-=-=-=-=-
	* This code is used to show the invidual season profile
	* for admin users.  Includes memberships, orders, etc.
	* This snippet outputs to placeholders
	*  - [[+hhnc.orderoutput]]
	*  - [[+hhnc.useroutput]]
	*  - [[+hhnc.seasonname]]
	*  - [[+hhnc.seasondates]]

    * v. 0.2 (March 28, 2011)
    * =-=-=-=-=-=-=-=-=-=-=-=-=-
    * - added ability to add memberships to seasons when
    * no other member has yet been added
    
    
    
*/

// Grab the hhncmanager package
$path 				= MODX_CORE_PATH . 'components/hhncmanager/';
$result 			= $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');
$output				= array();

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 			= new HHNCManager(&$modx);
$user				= $manager->getMemberCurrentInfo();

// we're going to pass season ids via the get variables
// if we don't have one, assume the current season
if ($_GET['id'] != '') {
    $get = array();
    foreach($_GET as $key => $value) {
        $get[$key] = filter_var($value, FILTER_SANITIZE_STRING);
    }
  $s = $get['id'];    
} else {
  // grab the current season info
  $seasonspecs    = $manager->getCurrentSeasonInfo();
  $s = $seasonspecs['id'];
  
} // end if

// Now that we have a season id we can get some detailed information about the season
// and set the first couple of place holders
$season = $manager->season->getSeasonById($s,1);
$datespan = $season['startfulldate'] ." - " . $season['endfulldate'];
$modx->setPlaceholder('hhnc.seasonname',$season['name']);
$modx->setPlaceholder('hhnc.seasondates',$datespan);

// if the season has yet to start, display a countdown
if($season['daysToStart'] > 0) {
    $countdown = "<p class=\"season-countdown\">This season starts in <strong>{$season['daysToStart']}</strong> days</p>";
    $modx->setPlaceholder('hhnc.seasoncountdown',$countdown);
}

// Now let's start figuring out what we want to put in the orders view
$orders = $manager->getOrders($s);
if ($orders != false) {
    $qty = count($orders);
    $output['orders'] .= "\n\t<h3>Orders</h3>";
    $output['orders'] .= "\n\t<p>This season there have been " . $qty . " orders.  Here's the breakdown.  You can also click on individual weeks for more information.</p>"; 
    
    // get the order breakdown
    $orderbreakdown = $manager->getOrderBreakdownByWeek($orders,1);
    $output['orders'] .= "\n\t<ul>";
    foreach($orderbreakdown as $key=>$value) {
        $output['orders'] .= "\n\t\t<li><a href=\"".$modx->makeUrl(59)."?s=$s&w=$key\">Week $key ($value orders)</a></li>";
    
    } // end foreach
    $output['orders'] .= "\n\t</ul>";
} else {
    $output['orders'] .= "\n\t<p>We have not had any orders yet this season.  Please check back soon.</p>";
    
} // end if $orders...

// Now let's start to look at some of the user information
$users = $manager->getAllPaidMembers($s);
if($users != false) {
    $user_qty = count($users);
    $output['users'] .= "\n\t<h3>Memberships</h3>";
    $output['users'] .= "\n\t<p class=\"no-margin-bottom\">Therea are $user_qty users enrolled in this season.</p>";
    $output['users'] .= "\n\t<p class=\"instructions\"><a href=\"".$modx->makeUrl(60)."?id=".$s."\">Edit Memberships</a></p>";
    $output['users'] .= "\n\t<ul>";
    foreach ($users as $s_user) {
        $s_id = $s_user->get('modx_user_id');
        $sprofile = $modx->getObject('modUserProfile',$s_id);
        
        if($sprofile != null) {
        $fullname = $sprofile->get('fullname');
        $u = $sprofile->getOne('User');
        $username = $u->get('username');
        $output['users'] .= "\n\t\t<li>".$fullname." (". $username .")</li>";
        }
    } 
    $output['users'] .= "\n\t</ul>";

} else {
    $output['users'] .= "\n\t<p>There are no users enrolled for this season</p>";
    $output['users'] .= "\n\t<p class=\"instructions\"><a href=\"".$modx->makeUrl(60)."?id=".$s."\">Edit Memberships</a></p>";
}

$modx->setPlaceholder('hhnc.orderoutput', $output['orders']);
$modx->setPlaceholder('hhnc.useroutput', $output['users']);

return '';



