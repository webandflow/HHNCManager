<?php
// Grab the hhncmanager package
$path 			= MODX_CORE_PATH . 'components/hhncmanager/';
$result 		= $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 		= new HHNCManager(&$modx);
$output			= '';


$output .= "<p class=\"version-info\"><a href=\"mailto:". HHNC_VER_DEV_EMAIL ."\" title=\"Email " . HHNC_VER_DEV . "\">" . HHNC_VER_NAME . ' ' . HHNC_VER_NUM . ' (' . HHNC_VER_DATE . ')</a>';
$output .= "<a href=\"http://www.webandflowdesign.com\" title=\"Web Design and MODX Development in Charlotte, NC\" target=\"_blank\">Web and Flow Design</a></p>";


return $output;

?>