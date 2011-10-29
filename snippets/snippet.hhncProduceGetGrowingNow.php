<?php
/* 
    HHNCProduceGetGrowingNow
    =-=-=-=-=-=-=-=-=-=-=-=-=-

*/

// Grab the hhncmanager package
$path = MODX_CORE_PATH . 'components/hhncmanager/';
$result = $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

$output = '';

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 		= new HHNCManager(&$modx);
$produce = $manager->produce;

$category = (isset($category)) ? $category : false;

if ($category) {

    $grownow = $produce->getGrowingNow($category);
    if (count($grownow) > 0) {
        $output .= "\n<ul class=\"in-season-list\">"; 

        foreach($grownow as $id => $name) {
            $data['id'] = $id;
            $data['name'] = $name;
            $data['url'] = $modx->makeUrl($id);

            $output .= $modx->getChunk('produceGrowingNowItemTpl',$data);

        } // end foreach        

        $output .= "\n</ul>";
    } // end if
    
} else {

    $output .= $modx->getChunk('errorNoCategoryProvidedForGrowingNow');

}

return $output;

?>