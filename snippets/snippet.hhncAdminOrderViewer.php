<?php
/*
	* hhncAdminOrderViewer
	* =-=-=-=-=-=-=-=-=-=-
    * v. 0.2
    *
    * Changes:
    * ========
    * - Added script to handle case that no catalog is present for a given week
    *
*/

// Grab the hhncmanager package
$path 				= MODX_CORE_PATH . 'components/hhncmanager/';
$result 			= $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');
$output				= array();

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 			= new HHNCManager(&$modx);

// we're going to pass season ids via the get variables
// if we don't have one, assume the current season
if ($_GET['s'] != '' && $_GET['w'] != '') {
    $get = array();
    foreach($_GET as $key => $value) {
        $get[$key] = filter_var($value, FILTER_SANITIZE_STRING);
    }
  $s = $get['s'];
  $w = $get['w'];    
} else {
  // grab the current season info
  $seasonspecs    = $manager->getCurrentSeasonInfo();
  $s = $seasonspecs['id'];
  $progress = $manager->season->getDailyProgress();
  $w = $progress['currentWeek'];
} // end if

$orders = $manager->getOrders($s,$w);
if ($orders != false) {

    $defOrdArray = array();
    $defOrdArray['0']['name'] = 'Custom Order Basket';
    $defOrdArray['1']['name'] = 'Petite Classic Basket';
    $defOrdArray['1']['qty'] = 0;
    $defOrdArray['2']['name'] = 'Classic Basket';
    $defOrdArray['2']['qty'] = 0;
    $defOrdArray['3']['name'] = 'A La Carte Orders';
    $defOrdArray['3']['qty'] = 0;
    
    // basket orders array
    $basketOrders = array();
    $customOrders = array();
    $alacarteOrders = array();
    
    foreach($orders as $order) {
        $data = $order->get('data');
        $alacarte = $order->get('is_alacarte');
        // parse through and figure the order type
        if (is_numeric($data)) {
            $defOrdArray[$data]['qty'] = $defOrdArray[$data]['qty'] + 1;
            $basketOrders[$data][] = $order->get('modx_user_id');    
        } elseif ($alacarte == 1) {
            $defOrdArray['3']['qty'] = $defOrdArray['3']['qty'] + 1;
            $userid = $order->get('modx_user_id');
            $alacarteOrders[$userid] = $data;
        } else {
            $defOrdArray['0']['qty'] = $defOrdArray['0']['qty'] + 1;
            $userid = $order->get('modx_user_id');
            $customOrders[$userid] = $data;
        }
        
    } // end foreach

    $output['orderlist'] .= "\n<h2>Order Details</h2>";
    $output['orderlist'] .= "\n<div class=\"menu-widget green\" id=\"weekly-order-summary\">";
    $output['orderlist'] .= "\n<h4 class=\"center\"> There were " . count($orders) . " orders this week.</h4>";    
    
    $output['orderlist'] .= "\n\t<ul>";
    $output['orderlist'] .= "\n\t\t<li>".$defOrdArray['1']['qty'] . " " . $defOrdArray['1']['name'] . "(s) </li>";
    $output['orderlist'] .= "\n\t\t<li>".$defOrdArray['2']['qty'] . " " . $defOrdArray['2']['name'] . "(s) </li>";
    $output['orderlist'] .= "\n\t\t<li>".$defOrdArray['0']['qty'] . " " . $defOrdArray['0']['name'] . "(s) (details below)</li>";

    $output['orderlist'] .= "\n\t</ul>";
    
    $output['orderlist'] .= "\n</div>";
    
    $fromfarms = $modx->getObject('modTemplateVar',array('name' => 'fromFarms'));
    // get this weeks catalog...
    $catalog = $manager->getWeeklyCatalog($s,$w);
    // if there is a catalog, use it to grab the info 
    if(count($catalog) > 0) {

        foreach ($catalog as $item) {
           // Get the resource that represents this produce item
            $resource = $modx->getObject('modResource',$item['id']);
            $name = $resource->get('pagetitle');
            $output['orderlist'] .= "\n<h3>".$name."</h3>";
            
            // Display Farms that provide this produce
            $farms = $fromfarms->getValue($item['id']);
            $farmlist = explode('||',$farms);
            $output['orderlist'] .= "<p class=\"instructions\">";
            foreach ($farmlist as $farmid) {
                if ($farmid != '') {
                    $farminfo = $modx->getObject('modResource',$farmid);
                    $farm['name'] = $farminfo->get('pagetitle');
                    $output['orderlist'] .= "\n\t&bull; {$farm['name']}";
                }
            }
            $output['orderlist'] .= "</p>";
            
            
            $orderdetails = array();
            // display the list of orderers here
            foreach($orders as $order) {
                // check order for item
                $data = $order->get('data');
                $qty = $manager->checkOrderForItem($data,$item['id']);
                if($qty > 0) {
                    $orderer = $order->get('modx_user_id');
                    $orderdetails[$orderer] += $qty;
                } // end if                
        
            } // end foreach order
            
            if (!empty($orderdetails)) {
            $output['orderlist'] .= "\n<table class=\"orderlisttable\">";
            $output['orderlist'] .= "\n<thead>";
            $output['orderlist'] .= "\n\t<tr><th class=\"ordername\">Name</th><th class=\"orderqty\">Quantity</th></tr>";
            $output['orderlist'] .= "\n</thead>";
            $output['orderlist'] .= "\n<tbody>";
            $total = 0;
            $i = 1;
            
                foreach ($orderdetails as $okey => $oqty) {
                    $class= ($i % 2 == 0 ) ? 'even' : 'odd';
                    $userinfo = $modx->getObject('modUserProfile',$okey);
                    if ($userinfo != null) {
                        $user_obj = $userinfo->getOne('User');
                        $userid = $user_obj->get('id');
                        $user['name'] = $userinfo->get('fullname');
                        $output['orderlist'] .= "\n<tr class=\"".$class."\"><td>{$user['name']} ($userid)</td><td>$oqty</td>";
                        $total += $oqty;
                    }
                    $i++; 
                } 
            $output['orderlist'] .= "\n</tbody>";
            $output['orderlist'] .= "\n<tfoot>";
            $output['orderlist'] .= "\n<tr><td>TOTAL</td><td>$total</td></tr>";
            $output['orderlist'] .= "\n</tfoot>";
            $output['orderlist'] .= "\n</table>";        
            
            } else {
            
            $output['orderlist'] .= "\n<p>There were no orders for this item</p>";
            
            }// end if
        } // end foreach item
    } else { // end if
        $data['s'] = $s;
        $data['w'] = $w;
        $output['orderlist'] .= $modx->getChunk('hhncErrorWeeklyNoCatalog',$data);
    
    }
// We also want to grab the members that have placed orders for this week
// and display them here so we can print their "basket" information
 
    /* CUSTOM ORDERS */
    $output['membersorders'] .= "\n<h4>Custom Orders</h4>";   
    if ( count($customOrders > 0)) {
    
        $output['membersorders'] .= "\n<p>The following members made custom orders this week:</p>";
        $output['membersorders'] .= "\n\t<ul>";
        $i = 0; // iterator
        foreach($customOrders as $user => $order) {
            $userprofile = $modx->getObject('modUserProfile',$user);
            if($userprofile != null) {
                $i++;
            }
            
            if ($i > 0) {
              $i =0;
              $output['membersorders'] .= "\n\t<li>";
              $output['membersorders'] .= "<a href=\"" . $modx->makeUrl(102) . "?uid=$user&s=$s&w=$w\">" . $userprofile->get('fullname') . "</a>";
              $output['membersorders'] .= "</li>";
            }
        
        }
        $output['membersorders'] .= "\n\t</ul>";
    
    } else {
    
        $output['membersorders'] .= "\n<p>There were <strong>0 custom orders</strong> this week.</p>";
    
    }
 
  
    /* CLASSIC BASKETS */ 
 
    $output['membersorders'] .= "\n<h4>Classic Baskets</h4>";
    if( count($basketOrders[2]) > 0 ) {
        
        $output['membersorders'] .= "\n<p>There were <strong>" . count($basketOrders[2]) . " classic baskets</strong> ordered this week:</p>";
        $output['membersorders'] .= "\n\t<ul>";
        foreach($basketOrders[2] as $memberid) {
             $user = array();
             $userinfo = $modx->getObject('modUserProfile',$memberid);
             $user['name'] = $userinfo->get('fullname');
             
             $output['membersorders'] .= "<li><a href=\"" . $modx->makeUrl(102) . "?uid=$memberid&s=$s&w=$w\" title=\"view orders\">" . $user['name'] . " (ID: " . $memberid . ")</a></li>";
             
        }
        $output['membersorders'] .= "\n\t</ul>";
        
    } else {
        
        $output['membersorders'] .= "<p>There were <strong>0 Classic Baskets</strong> ordered this week.</p>";
        
    }
    

    /* PETITE CLASSIC BASKETS */ 
 
    $output['membersorders'] .= "<h4>Petite Classic Baskets</h4>";
    if( count($basketOrders[1]) > 0 ) {
        
        $output['membersorders'] .= "\n<p>There were <strong>" . count($basketOrders[1]) . " petite classic baskets</strong> ordered this week:</p>";
        $output['membersorders'] .= "\n\t<ul>";


        foreach($basketOrders[1] as $memberid) {
             $user = array();
             $userinfo = $modx->getObject('modUserProfile',$memberid);
             $user['name'] = $userinfo->get('fullname');
            
             $output['membersorders'] .= "<li><a href=\"" . $modx->makeUrl(102) . "?uid=$memberid&s=$s&w=$w\" title=\"view orders\">" . $user['name'] . " (ID: " . $memberid . ")</a></li>";
             
        }
        $output['membersorders'] .= "\n\t</ul>";
        
    } else {
        
        $output['membersorders'] .= "<p>There were <strong>0 Petite Classic Baskets</strong> ordered this week.</p>";
        
    }

     /* A LA CARTE ORDERS */ 
 
    $output['membersorders'] .= "<h4>A La Carte Orders</h4>";
    if( count($alacarteOrders) > 0 ) {
        
        $output['membersorders'] .= "\n<p>There were <strong>" . count($alacarteOrders) . " a la carte</strong> orders this week:</p>";

        $output['membersorders'] .= "\n\t<ul>";
        $i = 0; // iterator
        foreach($alacarteOrders as $user => $order) {
            $userprofile = $modx->getObject('modUserProfile',$user);
            if($userprofile != null) {
                $i++;
            }
            
            if ($i > 0) {
              $i =0;
              $output['membersorders'] .= "\n\t<li>";
              $output['membersorders'] .= "<a href=\"" . $modx->makeUrl(102) . "?uid=$user&s=$s&w=$w\">" . $userprofile->get('fullname') . "</a>";
              $output['membersorders'] .= "</li>";
            }
        
        }
        $output['membersorders'] .= "\n\t</ul>";
        
    } else {
        
        $output['membersorders'] .= "<p>There were <strong>0 A La Carte Orders</strong> this week.</p>";
        
    }




    // set placeholders on thew viewer page
    $modx->setPlaceholder('hhnc.membersorders',$output['membersorders']);
    $modx->setPlaceholder('hhnc.orderlist',$output['orderlist']);


} else {
    // set placeholders on thew viewer page
    $output['orderlist'] .= "<p class=\"error\">There are no orders recorded for this week.  The system automatically generates orders on Monday at 1:00pm eastern for all users.</p>" ;
    $modx->setPlaceholder('hhnc.orderlist',$output['orderlist']);
    $modx->setPlaceholder('hhnc.membersorders','<p>No Orders yet recorded.  Please check back.</p>');
    
}

return '';
?>