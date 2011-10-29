<?php
/*

	* MEMBER VERIFICATION
	* ===================
	* This script receives transaction data from FoxyCart via XML and verifies membership
	* levels in the hhncMemberships database.  Users will not be able to take advantage of
	* a season until their memberships have been verified by FoxyCart (and datestamped).
	*
	* The FoxyCart Data feed is sent within a minute of the transaction processiing and
	* so the results should be pretty quick.
	* 
	* V. 0.1

*/

// if there isn't any post data coming to this file, assume
// that the user is here in error... send 404

if(!$_POST['FoxyData']) { $modx->sendErrorPage(); }

// data from FoxyCart arrives via $_POST
$data			= $_POST;
$feed			= $data['FoxyData'];

// Grab the hhncmanager package
$path 			= MODX_CORE_PATH . 'components/hhncmanager/';
$result 		= $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
require_once($path . 'classes/class.rc4crypt.php');
$manager 		= new HHNCManager(&$modx);

// set up decryption password
$pwd			= 'xvZ5m8GXEnCYfQ31CZ1hYJDQl3QwlJ5NaYImXfewtKqCritTcn61XzGetsEI';

$feed			= urldecode($feed);
$feed			= rc4crypt::decrypt($pwd,$feed);

// first - grab the feed and dump it in our database for our records
//echo $feed;

// setup simple XML to parse through this data.
$xml			= new SimpleXMLElement($feed);

// init the info array
$info			= array();

// use xpath to find custom fields
foreach($xml->transactions->transaction->transaction_details->transaction_detail->transaction_detail_options->transaction_detail_option as $option) {

	// parse through the XML to find the important custom information
	// userid, seasonid, membership level
	$key		= (string)$option->product_option_name;
	$value		= (string)$option->product_option_value;
	$info[$key]	= $value;

} // end foreach

// add the current time to the verification array
$info['time']	= time();

// Now we want to grab the membershiplevel for which this registration is for
$q				= $modx->getObject('Membershiplevels',array('level_number' => $info['Membership-Level']));
$leveldetails	= $q->toArray();

// Now we have all the info that we need to add a new Membership to the database
$m				= $modx->newObject('Memberships',array(
	'modx_user_id' 			=> $info['user'],
	'seasonid'				=> $info['seasonid'],
	'membership_status'		=> $info['Membership-Level'],
	'membership_verified'	=> $info['time'],
	'can_alacarte'			=> $leveldetails['alacarte'],
	'can_homedeliver'		=> $leveldetails['homedelivery'],
	'can_customorder'		=> $leveldetails['custom_order'],
	'manual_override'		=> 0
));

if ($m->save()) {
	$output		= 'foxy';
}

return $output;


?>