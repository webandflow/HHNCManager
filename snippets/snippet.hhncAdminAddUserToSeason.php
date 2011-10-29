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

// pull out the user information
$profile = $modx->getObject('modUserProfile',$get['u']);
$userinfo = $profile->getOne('User');
$userdata = array();
$userdata['fullname'] = $profile->get('fullname');
$userdata['username'] = $userinfo->get('username');
$userdata['userid'] = $get['u'];
$userdata['seasonid'] = $get['s'];
$seasoninfo = $manager->season->getSeasonById($userdata['seasonid']);
$userdata['seasonname'] = $seasoninfo['name'];
$userdata['membership_select'] = $manager->getMembershipSelectBox();

// grab the form template
$output .= $modx->getChunk('hhncAdminAddMemberFormTpl',$userdata);
$modx->setPlaceholder('hhnc.AddUserToSeason',$output);

return null;

?>