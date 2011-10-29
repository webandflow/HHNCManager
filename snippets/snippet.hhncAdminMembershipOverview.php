<?php
/*
	* hhncAdminMembershipOverview
	* =-=-=-=-=-=-=-=-=-=-=-=-=-=
	* This code is used to populate the "Edit Memberships" view
	* for the HHNC Membership editor page.  For output, it sets a
	* a number of placeholders:
	* - hhnc.currentmembership
	* - hhnc.noncurrentusers
	* - hhnc.seasonname
	* - hhnc.seasondates
	
	* v. 0.1
*/

// Grab the hhncmanager package
$path 				= MODX_CORE_PATH . 'components/hhncmanager/';
$result 			= $modx->addPackage('hhncmanager',$path . 'model/','hhnc_');
$output				= array();
$regusers           = array();

// register some of the additional files
$modx->regClientScript('assets/js/jquery.colorbox-min.js');
$modx->regClientScript('assets/js/jquery.membershipcontrols.js');
$modx->regClientCss('css/colorbox.css');

// require some of the custom classfiles
require_once($path . 'classes/class.hhncmanager.php');
$manager 			= new HHNCManager(&$modx);

// Check to see if there are any posted variables. This will likely be a membership being
// added to the database.
if($_POST) {
    $type = $_POST['transactiontype'];
    if ($type == 'user-add') {
        $u = $_POST['userid'];
        $s = $_POST['seasonid'];
        $l = $_POST['membershiplevel'];
        $adduser = $manager->manuallyAddMembership($u,$s,$l);
    } elseif ($type == 'membership-edit') { // if
        $mid = $_POST['mid'];
        $m = $modx->getObject('Memberships',$mid);
        $hd = ($_POST['can_homedeliver'] == 'on') ? 1 : 0;
        $ac = ($_POST['can_alacarte'] == 'on') ? 1 : 0;
        $co = ($_POST['can_customorder'] == 'on') ? 1 : 0;
        $m->set('membership_status',$_POST['membershiplevel']);
        $m->set('can_homedeliver',$hd);
        $m->set('can_alacarte',$ac);
        $m->set('can_customorder',$co);
        $m->set('manual_override',1);
        $m->save();
    } // end elseif
}// end if $_POST

// we're going to pass season ids via the get variables
// if we don't have one, assume the current season
if ($_GET['id'] != '') {
    $get = array();
    foreach($_GET as $key => $value) {
        $get[$key] = filter_var($value, FILTER_SANITIZE_STRING);
    }
  $s = $get['id'];    
} else {
  // grab the current season info
  $seasonspecs    = $manager->getCurrentSeasonInfo();
  $s = $seasonspecs['id'];
  
} // end if

// Now that we have a season id we can get some detailed information about the season
// and set the first couple of place holders
$season = $manager->season->getSeasonById($s,1);
$datespan = $season['startfulldate'] ." - " . $season['endfulldate'];
$modx->setPlaceholder('hhnc.seasonname',$season['name']);
$modx->setPlaceholder('hhnc.seasondates',$datespan);

// if the season has yet to start, display a countdown
if($season['daysToStart'] > 0) {
    $countdown = "<p class=\"season-countdown\">This season starts in <strong>{$season['daysToStart']}</strong> days</p>";
    $modx->setPlaceholder('hhnc.seasoncountdown',$countdown);
}

// check the current membership
$members = $manager->getAllPaidMembers($s);
if (count($members) > 0) {

    $output['currentmembership'] .= "\n<p>There are <span class=\"membership-qty\">" . count($members) . "</span> user(s) enrolled in this season.</p>";

    $output['currentmembership'] .= "\n<div id=\"currentMembershipList\">";
    $i=1;
    foreach($members as $member) {
        // grab all of the relevant data that we'll need to build the users profile
        $mid = $member->get('id');
        $uid = $member->get('modx_user_id');
        $profile = $modx->getObject('modUserProfile',$uid);
        $level = $member->getOne('Level');
        $user = $profile->getOne('User');
        
        // use that data to fill out the userdata array
        // we'll pass this to the view to complete the data
        $userdata = array();
        $userdata['fullname'] = $profile->get('fullname');
        $userdata['userid'] = $uid;
        $userdata['username'] = $user->get('username');
        $userdata['verified'] = date('F j, Y',$member->get('membership_verified'));
        $userdata['seasonid'] = $s;
        $userdata['level_name'] = $level->get('level_name');
        $userdata['canHomeDeliver'] = ($member->get('can_homedeliver') == 1) ? 'yes' : 'no';
        $userdata['canALaCarte'] = ($member->get('can_alacarte') == 1) ? 'yes' : 'no';
        $userdata['canCustomOrder'] = ($member->get('can_customorder') == 1) ? 'yes' : 'no';
        $userdata['evenodd'] = ($i % 2 == 0) ? 'even' : 'odd' ;
        $userdata['mid'] = $mid;
    
        // grab the view and populate the fields
        $output['currentmembership'] .= $modx->getChunk('hhncAdminUserMembershipTpl',$userdata);
    
        // include this user in the reguser array so we don't include them in the "other users" list
        $regusers[] = $uid;
        
        $i++;
    }
    $output['currentmembership'] .= "\n</div>";
    $modx->setPlaceholder('hhnc.currentmembership',$output['currentmembership']);
    
    // Now get all of the other user data to display in the 
    // other users list
    $allusers = $modx->getCollection('modUser');
    $i=1;
    foreach($allusers as $otheruser) {
        $uid = $otheruser->get('id');
        if(in_array($uid,$regusers) == false) {
        // if the users isn't a registered user
        $userdata = array();
        $profile = $modx->getObject('modUserProfile',$uid);
        $user = $profile->getOne('User');
        
        $userdata = array();
        $userdata['fullname'] = $profile->get('fullname');
        $userdata['userid'] = $uid;
        $userdata['username'] = $user->get('username');
        $userdata['evenodd'] = ($i % 2 == 0) ? 'even' : 'odd' ;
        $userdata['seasonid'] = $s;
        $output['noncurrentusers'] .= $modx->getChunk('hhncAdminNonMemberUserTpl',$userdata);
        $i++;    
        } // end if

    } // end foreach

    $modx->setPlaceholder('hhnc.noncurrentusers',$output['noncurrentusers']);

} else {
    // if there are no registered members for this season
    $output['currentmembership'] .= $modx->getChunk('hhncAdminNoMembersThisSeason');
}

return null;
?>

