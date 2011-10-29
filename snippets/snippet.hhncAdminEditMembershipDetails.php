<?php
// Grab the hhncmanager package
$path 				= MODX_CORE_PATH . 'components/hhncmanager/';
$result 			= $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');
$output				= '';

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 			= new HHNCManager(&$modx);

$get = array();
foreach($_GET as $key => $value) {
    $get[$key] = filter_var($value, FILTER_SANITIZE_STRING);
}

// get the user and membership ids
$uid = $get['u'];
$mid = $get['m'];
$s = $get['s'];

// get user/membership information
$user = $modx->getObject('modUser',$uid);
$profile = $user->getOne('Profile');
$membership = $modx->getObject('Memberships',$mid);
$selected = $membership->get('membership_status');

$cp = array();
$cp['can_homedeliver']['text'] = "Home Delivery";
$cp['can_homedeliver']['val'] = $membership->get('can_homedeliver');
$cp['can_alacarte']['text'] = "A La Carte";
$cp['can_alacarte']['val'] = $membership->get('can_alacarte');
$cp['can_customorder']['text'] = "Custom Ordering";
$cp['can_customorder']['val'] = $membership->get('can_customorder');

// set up the user data array
$userdata = array();
$userdata['fullname'] = $profile->get('fullname');
$userdata['username'] = $user->get('username');
$userdata['mid'] = $mid;
$userdata['userid'] = $uid;
$userdata['seasonid'] = $s;
$userdata['membership_select'] = $manager->getMembershipSelectBox($selected);
$userdata['membership_permissions'] = $manager->getMembershipPermissionsCheckboxes($cp);

$output .= $modx->getChunk('hhncAdminEditMembershipFormTpl',$userdata);

$modx->setPlaceholder('hhnc.EditMembershipDetails',$output);

return null;

?>