<?php
/*
	[[!hhncAdminMemberOrderViewer]]
*/

// Grab the hhncmanager package
$path 				= MODX_CORE_PATH . 'components/hhncmanager/';
$result 			= $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 			= new HHNCManager(&$modx);
$output = '';
$userOrders = array();

/*
    In order to display an collection or orders (custom and alacarte, we need to know three things...
    1. userid
    2. seasonid
    3. week
    
*/

$get = $_GET;
if(!empty($get)) {
    foreach ($get as $key => $value) {
        $get[$key] = filter_var($get[$key], FILTER_SANITIZE_STRING);
    }
}

$info['s'] = $get['s'];
$info['w'] = $get['w'];
$info['uid'] = $get['uid'];

if($info['s'] == '' || $info['w'] == '' || $info['uid'] == '') {

    $output = "<p>Sorry there seems have been an error with finding this information.  Most likely we're missing a piece of information.  Please go back to the <a href=\"" . $modx->makeUrl(59) . "\" title=\"Order Viewer\">&quot;Order Viewer&quot;</a> page and try again.</p>";

} else {
    // if we have all the necessary information grab the orders for this user and get ready to display them
    $orders = $manager->getMembersOrders($info['uid'],$info['s'],$info['w']);
    foreach ($orders as $order) {
        if ($order->get('is_alacarte') == 1) {
            $userOrders['alacarte'][] = $order->get('data');
        } else {
            // if it's not alacarte it's their normal order for the week which is either a default basket
            // or it's a custom order.... it can't be both
            if($order->get('is_alacarte') == 0 && is_numeric($order->get('data'))) {
                
                $userOrders['regularOrder']['is_custom'] = 0;
            
            } else {
                // it's not numeric so it must be a custom order
                $userOrders['regularOrder']['is_custom'] = 1;
                
            }
            
            $userOrders['regularOrder']['data'] = $order->get('data');       
            
        }
    }    

// By this point, I shoudl have all fo the data that I need to build the tables of order information
// I just want to grab some user and season information to start off.

// Grab the user's information
$userprofile = $modx->getObject('modUserProfile',$info['uid']);
$info['fullname'] = $userprofile->get('fullname');

$seasonInfo = $manager->season->getSeasonById($info['s']);
$info['seasonName'] = $seasonInfo['name'];

$info['week'] = "Week " . $info['w'];

}
$memberinformation = $modx->getChunk('hhncMemberHeaderTpl',$info);


// First Display their Regular Order - whether default or custom

$regularOrder = '';
$regularOrder .= '<h3>Weekly Order Information</h3>';

if($userOrders['regularOrder']['is_custom'] == '1') {

    $regularOrder .= "<h4 class=\"delineate\">This user made a custom order this week containing the following items</h4>";
    $orderarray = json_decode($userOrders['regularOrder']['data'],1);
    $regularOrder .= $manager->displayOrderArrayAsList($orderarray);

} else {
    
    $regularOrder .= "<h4 class=\"delineate\">This user does not have custom ordering and will receive a &quot;classic&quot; basket as outlined below:</h4>";
    
    $regularOrder .= "<p class=\"weekly-order-announcement\">";

    if($userOrders['regularOrder']['data'] == 1) {
        $regularOrder .= 'Petite Classic Basket';    
    } elseif ($userOrders['regularOrder']['data'] == 2) {
        $regularOrder .= 'Classic Basket';
    }
    
    $regularOrder .= "</p>";

}


/* Now Deal with the A La Carte orders */
$alacarteOrders = '';
if( count($userOrders['alacarte']) > 0) {

    $alacarteOrders .= "\n<h3>A La Carte Orders";
    $alacarteOrders .= "<h4 class=\"delineate\">This user has a la carte ordering and has made made the following additional purchases:</h4>";

    $i = 1; // iterator
    foreach ($userOrders['alacarte'] as $order) {
        // Title This Section
        $alacarteOrders .= "\n<div class=\"alacarteorderreport\">";
        $alacarteOrders .= "\n<h4>Order #$i";
        
        $orderarray = json_decode($order,1);
        $alacarteOrders .= $manager->displayOrderArrayAsList($orderarray);        
        $i++;
        $alacarteOrders .= "\n</div>";        
    } // end foreach
} // end if




$output .= $memberinformation;
$output .= $regularOrder;
$output .= $alacarteOrders;

$modx->setPlaceholder('membersOrder',$output);
return null;

?>

