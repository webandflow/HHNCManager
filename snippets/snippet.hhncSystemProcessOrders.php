<?php
/*

    * ORDER PROCESSOR (CRON JOB)
	* ==========================
	* This script should only be called via CRON job.  It locks in user-selections as 
	* orders and alerts administrators that the orders have been finalized.
	*
	* V. 0.2 (Mar 29, 2011)
	* =-=-=-=-=-=-=-=-=-=-=
	* - reconfigured script to correctly email under any circumstance

*/
// Grab the hhncmanager package
$path 			= MODX_CORE_PATH . 'components/hhncmanager/';
$result 		= $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');
$output         = '';
// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 		= new HHNCManager(&$modx);
$season         = $manager->getCurrentSeasonInfo();
$progress       = $manager->season->getDailyProgress();

// set up reporting email address
$emailTo = 'des@webandflowdesign.com';

if ($season != false) {
    
    // set a password as an additional layer of security
    // to prevent unwanted executions of this script
    $password       = 'xvZ5m8GXEnCYfQ';
    $p              = filter_var($_GET['p'], FILTER_SANITIZE_STRING);
    
    // set up and iterator to report how many orders were added....
    $i = 0;
    
    // get the timestamp
    $now = time();
        
    if ($p != $password) {
    print "nope";
        $message        = "The cron job was attempted but authentication via the GET password failed.";
        mail($emailTo,'HHNC - FAILED CRON JOB',$message);
        return false;
    } else {
        // assign progress variables
        $s = $season['id'];
        $w = $progress['currentWeek'];
        
        // grab all of the paid users for this week
        $members = $manager->getAllPaidMembers($s);
        if ($members != false) {
            // if we're here, then we have some memberships for this
            // season to process.  Check through and see what we need to do
            foreach($members as $m) {
                // set up the order array for storing relevant details
                $order = array();
                $user = array();
                
                // get some membership information
                $user = $m->toArray();
            
                /*  
                 *  =========================
                 *  ORDER CUSTOMIZATION CHECK
                 *  =========================
                 */
                
                // can this user customize their order?
                $customize = $user['can_customorder'];
                if($customize == 1) {
                    // if they can custom order, check to see if they've
                    // submitted a custom order this week and grab it if they have
                    $cstmOrder = $manager->userHasSubmittedSelections($user['modx_user_id'],$s,$w,1);
                    /*
                    GET THE DEFAULT SELECTIONS FOR THIS USERS LEVEL.....
                    */
                    if (is_array($cstmOrder)) {
                      foreach ($cstmOrder as $o){
                        $userSelection = $o->toArray();
                        $order['data'] = $userSelection['data'];
                      } // end foreach
                    } else {
                    // if a user has not submitted a custom order, then grab the default for their level
                        $order['data'] = $manager->getLevelDefaultSelections($s,$w,$user['membership_status']);
                    } // end if(is_array())
                } else {
                // if the user can't custom order just grab the default info from the
                // weekly defaults table - this is exactly like the previous call
                    $order['data'] = $manager->getLevelDefaultSelections($s,$w,$user['membership_status']);
                } // end order customization
                
                 /*  
                 *  =========================
                 *  ADDRESS CHECK
                 *  =========================
                 */
                // Here we need to grab the users address information to store in the database
                $address = $manager->getUserAddress($user['can_homedeliver'],$user['modx_user_id']);
    
                // if the user doesn't have an address listed, what should we do?
                // we should at least send an email to someone.....
               
                if ($address == false) {
                  /*
      // get user name
                    $userprofile = $modx->getObject('modUserProfile',$user['modx_user_id']);
                    $name = $userprofile->get('fullname');
                
                    $msg = "There was a problem with order processing.  User " . $name . " (id: ". $user['modx_user_id'] . ") doesn't seem to have a default address for their account.  It may be a good idea to contact them.  It's mostly likely that they haven't logged in into the website.  Once they do that, they'll be prompted to either input an address (for users with home delivery) or select from one of the drop-off locations (for users without home delivery). The order was still added to the weekly orders database, with a generic address. If this problem persists for this user, contact Desmond at des@webandflowdesign.com.  ";
                    
                    $subject = "PROBLEM WITH ORDER FOR USER: " . $name;            
                    mail($emailTo,$subject,$msg); */
                    $order['addressid'] = 1;
    
                } else {
                    // grab the default address for the users level
                    $order['addressid'] = $address['id'];
                }  // end address check
                
                /*
                *
                *  We should have everything we need to submit this order to the database
                *  form the xpdo query and make it happ'n cap'n (Darryl - The Office)
                *
                */
                
                $q = $modx->newObject('Orders',array(
                    'modx_user_id' => $user['modx_user_id'],
                    'seasonid' => $s,
                    'week' => $w,
                    'data' => $order['data'],
                    'is_alacarte' => 0, // explicitly no... this has been automatic
                    'is_homedeliver' => $user['can_homedeliver'],
                    'addressid' => $order['addressid'],
                    'time' => $now
                ));
                
                if ($q->save() == false) {
                    $debug = $q->toArray();
                    $msg = "There was a problem saving a record to the orders database.  A debug message follows: \n\n" . $debug . "\n\n.";
                    $subject = "HHNC - PROBLEM SAVING ORDER";
                    mail($emailTo,$subject,$msg);
                }
            $i++;
            } // end foreach($members as $m)
        } else {
            // we don't have any users so simply return false
            $output =  false;
        }
    } // end else


$msg = "The system has just processed " . $i . " orders and added them to the orders database.  They are available for viewing in the HHNC Web Administration System.";
$subject = "HHNC: " . $i ." Orders Successfully Processed";
mail($emailTo,$subject,$msg);

} else {

    $msg = "It appears as though HHNC is not currently in an active season.  No orders were processed.";
    $subject = "HHNC: No Active Season Detected";
    mail($emailTo,$subject,$msg);

} // season != false

return $output;
?>