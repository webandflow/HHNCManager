<?php

// Grab the hhncmanager package
$path 			= MODX_CORE_PATH . 'components/hhncmanager/';
$result 		= $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 		= new HHNCManager(&$modx);
$output			- '';

$cSeason = $manager->getCurrentSeasonInfo();

if ($cSeason != false) {
    // if we're currently in season, we'll want to check for the most recent catalog
    // in order to display these items to site visitors
    
    $r = true;
    
    $progress = $manager->season->getDailyProgress();
    $catalog = $manager->getWeeklyCatalog($cSeason['id'],$progress['currentWeek']);
    
    if ($catalog == false && $progress['currentWeek'] > 1) {
        $r = false; // so far, we can assume that we don't
        for($i=$progress['currentWeek'];$i>1;$i--) {
            if ($r == false) {
              $catalog = $manager->getWeeklyCatalog($cSeason['id'],$i);
              if ($catalog != false) {
                $r = true;
              }
            }
        
        } // end for

    } // end if
    
    if ($catalog != false) {
        // now we should be able to parse through this catalog and display the items accordingly.
        $produce = array();
        $produce[10] = array(); // Leafy Greens
        $produce[11] = array(); // Vegetables and Herbs
        $produce[12] = array(); // Root Vegetables
        $produce[13] = array(); // Fruit and Specialty
        
        foreach ($catalog as $item) {
          // loop through each item in the catalog and determine which of the four
          // produce families it belongs too.
            $resource = $modx->getObject('modResource',$item['id']);
            $parent = $resource->getOne('Parent');
            $parentid = $parent->get('id');
            
            $produce[$parentid][] = $item['id'];
            
        } // end foreach        

    $output .= "\n<div id=\"currently-in-season\">";
    $output .= $modx->getChunk('hhncInSeasonTopIntro');

    // Leafy Greens
    $output .= "\n<section id=\"topcol-1\" class=\"twenty-five multi-col\">";
        // get the name
    $resource = $modx->getObject('modResource',10);
    $output .= "\n<h4>" . $resource->get('pagetitle') . "</h4>";

    $output .= "\n<ul class=\"in-season-list\">";
    
    if (count($produce[10]) > 0) {
        foreach($produce[10] as $prod) {
            $resource = $modx->getObject('modResource',$prod);
            $name = $resource->get('pagetitle');
            $output .= "\n<li><a href=\"" . $modx->makeUrl($prod) . "\">" . $name . "</a></li>";
        }
    } else {
        $output .= "\n<li>No Items to Display</li>";
    }    
    $output .= "\n</ul>\n</section>";
    
    // Vegetables and Herbs
    $output .= "\n<section id=\"topcol-1\" class=\"twenty-five multi-col\">";
        // get the name
    $resource = $modx->getObject('modResource',11);
    $output .= "\n<h4>" . $resource->get('pagetitle') . "</h4>";

    $output .= "\n<ul class=\"in-season-list\">";
    
    if (count($produce[11]) > 0) {
        foreach($produce[11] as $prod) {
            $resource = $modx->getObject('modResource',$prod);
            $name = $resource->get('pagetitle');
            $output .= "\n<li><a href=\"" . $modx->makeUrl($prod) . "\">" . $name . "</a></li>";
        }
    } else {
        $output .= "\n<li>No Items to Display</li>";
    }    
    $output .= "\n</ul>\n</section>";
    $output .= "\n<section id=\"topcol-1\" class=\"twenty-five multi-col\">";
        // get the name
    $resource = $modx->getObject('modResource',12);
    $output .= "\n<h4>" . $resource->get('pagetitle') . "</h4>";

    $output .= "\n<ul class=\"in-season-list\">";
    
    // Root Vegetables
    if (count($produce[12]) > 0) {
        foreach($produce[12] as $prod) {
            $resource = $modx->getObject('modResource',$prod);
            $name = $resource->get('pagetitle');
            $output .= "\n<li><a href=\"" . $modx->makeUrl($prod) . "\">" . $name . "</a></li>";
        }
    } else {
        $output .= "\n<li>No Items to Display</li>";
    }    
    $output .= "\n</ul>\n</section>";
    
    
    // Fruit and Specialty
    $output .= "\n<section id=\"topcol-1\" class=\"twenty-five multi-col last-col\">";
        // get the name
    $resource = $modx->getObject('modResource',13);
    $output .= "\n<h4>" . $resource->get('pagetitle') . "</h4>";
    $output .= "\n<ul class=\"in-season-list\">";
    if (count($produce[13]) > 0) {
        foreach($produce[13] as $prod) {
            $resource = $modx->getObject('modResource',$prod);
            $name = $resource->get('pagetitle');
            $output .= "\n<li><a href=\"" . $modx->makeUrl($prod) . "\">" . $name . "</a></li>";
        }
    } else {
        $output .= "\n<li>No Items to Display</li>";
    }    
    $output .= "\n</ul>\n</section>";
    $output .= "\n<br style=\"clear: both;\" />";
    $output .= "\n</div>";
        
    } else { 
        
        $output = '';
    
    }
    
} else {
    // here, we are  not currently in a season but we may want to check to see if there is a season coming up within the next week or so.

} // end if/else



return $output;



?>