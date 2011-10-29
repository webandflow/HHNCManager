<?php
// Grab the hhncmanager package
$path = MODX_CORE_PATH . 'components/hhncmanager/';
$result = $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 		= new HHNCManager(&$modx);
$user			= $manager->getMemberCurrentInfo();
$isEnrolled		= $manager->userEnrolled($s,$user['']);

// init the output
$output		= '';
if($user['levelNumber'] > 0) {
$output		.= $modx->getChunk('subscriptionLevelTpl',$user);
}

//$output		.= ($user['levelNumber']==0  && $isEnrolled == false ) ? $modx->getChunk('membershipPitch') : '';

return $output;
?>