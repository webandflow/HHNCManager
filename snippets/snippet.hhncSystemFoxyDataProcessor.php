<?php
/*

	* TRANSACTION PROCESSOR
	* =====================
	* This script receives transaction data from FoxyCart via XML and verifies membership
	* levels in the hhncMemberships database, as well as processes a la carte orders.
	* Users will not be able to take advantage of
	* a season until their memberships have been verified by FoxyCart (and datestamped).
	*
	* The FoxyCart Data feed is sent within a minute of the transaction processiing and
	* so the results should be pretty quick.
	* 
	* V. 0.2
	
	* CHANGELOG
	* =========
	* VER 0.2
	* + ADDED FEED LOGGING (TO HHNC_FOXY_CART_RESPONSES
	* + ADDED LOGIC TO PROCESS BOTH MEMBERSHIPS AND A LA CARTE ORDERS IN SINGLE PASS
	*

*/

// if there isn't any post data coming to this file, assume
// that the user is here in error... send 404

if(!$_POST['FoxyData']) { $modx->sendErrorPage(); }

// data from FoxyCart arrives via $_POST
$data			= $_POST;
$feed			= $data['FoxyData'];
$now            = time();
$errors         = array();

// Grab the hhncmanager package
$path 			= MODX_CORE_PATH . 'components/hhncmanager/';
$result 		= $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
require_once($path . 'classes/class.rc4crypt.php');
$manager 		= new HHNCManager(&$modx);

// set up decryption password
$pwd			= 'xvZ5m8GXEnCYfQ31CZ1hYJDQl3QwlJ5NaYImXfewtKqCritTcn61XzGetsEI';

// decrypt the encrypted feed
$feed			= urldecode($feed);
$feed			= rc4crypt::decrypt($pwd,$feed);

// first - grab the feed and dump it in our database for our records
$f              = $modx->newObject('FoxyCartResponses',array(
    'time' => $now,
    'data' => $feed
));
//$f->save();

// setup simple XML to parse through this data.
$xml			= new SimpleXMLElement($feed);

// init the info array for memberships information
$membership     = array();

// init the products array for a la cart orders
$products       = array();

// we need to start parsing through the xml
$qtyItems       = count($xml->transactions->transaction->transaction_details->transaction_detail);
$i              = 0;

for($i=0;$i<$qtyItems;$i++) {
    // determine (by product code) if this is a product item or a membership
    $code = (string)$xml->transactions->transaction->transaction_details->transaction_detail[$i]->product_code;
    $pieces = explode('-',$code);
    $orderuser =$pieces[1];    
    if($pieces[0] == 'membership') {
        foreach($xml->transactions->transaction->transaction_details->transaction_detail[$i]->transaction_detail_options->transaction_detail_option as $option) {
            $key = (string)$option->product_option_name;
            $value = (string)$option->product_option_value;
            $membership[$key] = $value;
        } // foreach
    } elseif($pieces[0] == 'hhnc') {
            $products[$i]['qty']    = (string)$xml->transactions->transaction->transaction_details->transaction_detail[$i]->product_quantity;
            $products[$i]['prodid'] = $pieces[2];
    } // end if
} // end for


// lets check to see if there were any memberships returned
if (count($membership) > 0) {
    // Now we want to grab the membershiplevel for which this registration is for
    $q				= $modx->getObject('Membershiplevels',array('level_number' => $membership['Membership-Level']));
    $leveldetails	= $q->toArray();

    // Now we have all the info that we need to add a new Membership to the database
    $m				= $modx->newObject('Memberships',array(
    	'modx_user_id' 			=> $membership['user'],
    	'seasonid'				=> $membership['seasonid'],
    	'membership_status'		=> $membership['Membership-Level'],
    	'membership_verified'	=> $now,
    	'can_alacarte'			=> $leveldetails['alacarte'],
    	'can_homedeliver'		=> $leveldetails['homedelivery'],
    	'can_customorder'		=> $leveldetails['custom_order'],
    	'manual_override'		=> 0
    ));

    if($m->save() == false) {
        $msg = "There was an error adding a {$membership['Membership-Level']} membership for user: {$membership['user']} in season: {$membership['seasonid']} ";
        $errors[] = $msg;
        mail('des@webandflowdesign.com','Error in adding membership',$msg);
    } // end save
} // end add membership code

if (count($products) > 0) {
 // if this transaction report includes information on a la carte orders we will want
 // to add this to the orders table.  Simple $productid => $qty array as json
    $order      = array();
    
    foreach ($products as $product) {
     $pid       = $product['prodid'];
     $qty       = $product['qty'];
     
     $order[$pid] = $qty;
    }

    if ($manager->orderSubmit($orderuser,$order,1) == false) {
        $errors[] = "There was an issue with submitted an a la carte order for $orderuser.  Here is the data: \n $feed";
    }
}

if (count($errors) > 0) {
    $msg    = implode(' :::: ',$errors);
    mail('des@webandflowdesign.com','HHNC :: ERRORS IN FOXYCART DATA PROCESSING', $msg);
}

$output = 'foxy';
return $output;
?>