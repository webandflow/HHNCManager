<?php
/*
 * HHNCManager MAIN CLASS
 * version: 0.2 (08-MAR-2011)
 * =-=-=-=-=-=-=-=-=-=-=-
 * by des at webandflowdesign.com
 * 
 * The HHNCManager Main class is the heart of the HHNC operation. All of
 * of the main methods are found here and in the helper "season" class and 
 * 'produce' processor class.
 *
 */
 
// pull in the configuration file
require_once('config.inc.php');
// grab the helperclasses
require_once('class.seasonprocessor.php');
require_once('class.produceprocessor.php');

Class HHNCManager {
	private $user;
	private $userInfo;
	public	$modx;
	public 	$season;
	public	$purchaseWindowLow; // number of days before start of season when purchasing membership closes
	public	$purchaseWindowHigh;
	
	function __construct(&$modx) {
		$this->modx					= $modx;
		$this->user					= $this->modx->user->get('id');
		$this->userInfo				= '';
		$this->season				= '';
		$this->purchaseWindowLow	= 6;
		$this->purchaseWindowHigh	= 1000;
		// Setup the Season Object
		$this->setSeason();
		// Setup the Produce Object
		$this->produce              = new ProduceProcessor(&$modx);
			
	} // end __construct()
	
	public function getMemberId() {
		// get the ID of the currently logged in user
		return $this->user;
	
	} // end getMemberId
	
	public function getMemberCurrentInfo($user='') {
	
		// grab a couple of important variables here
		$modx			= $this->modx;
		$user			= ($user != '') ? $user : $this->user;
		$season			= $this->getCurrentSeasonInfo();
		
		$query = $this->modx->newQuery('Memberships');
		$query->innerJoin('Seasons','Season');
		$query->innerJoin('Membershiplevels','Level');
		$query->where(array('Season.id' => $season['id'], 'Memberships.modx_user_id' => $user)); // change this to reflect current season
		$query->limit(1);
		$query->select(array('Level.*','Memberships.*'));
				
		// Grab the memberships from the collection above... there should be just one		
		$memberships	= $this->modx->getCollection('Memberships',$query);
		
		// init the array we'll use for output
		$info					= array();

		// populate with general information
		$info['userid']			= $this->user;
		
		if( count($memberships) == 1 ) {	
			foreach($memberships as $membership) {

				$info['isCurrentMember']= 1;													
				$info['levelName']		= $membership->get('level_name');
				$info['levelNumber']	= $membership->get('level_number');
				$info['levelRank']		= $membership->get('level_points');
				$info['canCustomize']	= $membership->get('can_customorder');
				$info['canALaCarte']	= $membership->get('can_alacarte');
				$info['canHomeDelivery']= $membership->get('can_homedeliver');
				
				//$info['levelPoints']	= $membership->get('level_points');
	
				$q				= $this->modx->newQuery('PointLevels');
				$q->where(array('id' => $membership->get('level_points')));
				$coll			= $this->modx->getCollection('PointLevels',$q);
				
				if ($coll) {
					foreach ($coll as $item) {
						$info['levelPoints']	= $item->get('points');
			
					}
					
				} else {
					
					$info['levelPoints'] = 0;
					
				} // end if
	
			}	// end foreach
		} else {
				$info['isCurrentMember']= 0;
				$info['levelNumber']	= 0;
				$info['levelName']		= "Free Member";

		}
		
		// set this to the object scope for other methods
		$this->userInfo					= $info;
		return $info;
		
	} // end getMemberLevel()
	
	private function setSeason() {
	
		$seasoninfo			= $this->getCurrentSeasonInfo(); 
		$this->season		= new SeasonProcessor($this->modx,$seasoninfo['start'],$seasoninfo['end']);
		$this->season->setSeasonInfo($seasoninfo);
		
	}
	
	public function getCurrentSeasonInfo() {
	
		// this function returns an array of
		// infomration about the current season 
	
		// grab a couple of useful variables
		$modx			= $this->modx;
		
		// current timestamp to compare
		$nowtime		= time();
		
		$query			= $this->modx->newQuery('Seasons');
		$query->sortby('start','ASC');
		$query->where(array(
			"start:<=" => $nowtime,
			"end:>=" => $nowtime
		));
		
		// We only want to return a single season
		$query->limit(1);
		$seasons		= $modx->getCollection('Seasons',$query);
		
		if (count($seasons) > 0) {			
			foreach ($seasons as $season) {
				$s		= $season->toArray();	
			}
		} else {
			$s			= false;
		}
		
		return $s;	
	} // end getCurrentSeason()

	public function getThisWeeksSelection($s='',$w='') {

		$user			= $this->userInfo;
		$seasonInfo		= $this->season->getSeasonInfo();
		$progress		= $this->season->getProgressInfo();
		
		// which week are we looking for
		$s				= ($s !='') ? $s : $seasonInfo['id'];
		$w				= ($w !='') ? $w : $progress['currentWeek'];
		
		$query			= $this->modx->newQuery('WeeklyDefaults');
		$query->where(array(
			'seasonid' 	=> $s,
			'week'		=> $w
		));
		$query->limit(1);
		$defaults		= $this->modx->getCollection('WeeklyDefaults',$query);

		if (count($defaults > 0)) {

		foreach ($defaults as $default) {
			
			$selection	= $default->toArray();		
			
		}
		
		// take the JSON-encoded data from the database and 
		// decode it to an associative array of product_id => default_qty
		$data			= $this->processJSON($selection['data']);
		// this data will allow us to display a list of items
		// that will be delivered this week depending on
		// the users membership level.
		$listItems		= $this->getProduceFromArray($data['selections']);


		} else {
		
			$listItems 	= array();
		
		}

		// return an array with the current selections
		return $listItems;
		
	
	} // end getThisWeeksSelection()
	
	public function getUsersSelection($u,$s,$w) {
		$q			= $this->modx->newQuery('UserSelections');
		$q->where(array(
		  'seasonid' => $s,
		  'week'	 => $w,
		  'userid'	 => $u 
		));
		
		$results	= $this->modx->getCollection('UserSelections',$q);
		$qty		= count($results);
		foreach($results as $r) {
			$json	= $r->get('data');
		}
		$array		= json_decode($json,true);
		$listItems	= $this->getProduceFromArray($array,0);

		if ($qty == 0) {
			$listItems = array();
		}

		return $listItems;

	} // end getUsersSelection
	
	private function processJSON($data,$array='1') {
		// takes the JSON encoded data for this weeks selections
		// and returns an array of whats' selected by default
		// for petite and regular level memebers
	
		$a			= json_decode($data,$array);
		return $a;
	
	
	} // end processJSON()
	
	private function getProduceFromArray($selections,$deep=1) {
		// takes an array of selections and returns a similar array
		// but this time with product names/info in addition to qty
		
		$selectionInfo				= array();
		if ($deep == 1){
		foreach ($selections as $level => $selection) {
			
			foreach ($selection as $id => $qty) {
			
				$item				= $this->modx->getObject('modResource',array('id' => $id ));
				$product['id']		= $id;
				$product['qty']		= $qty;
				$product['name']	= $item->get('pagetitle');
				
				$selectionInfo[$level][] = $product;
			
			} // end foreach
			
		} // end foreach
		
		} else {
		
			foreach ($selections as $id => $qty) {
			
				$item				= $this->modx->getObject('modResource',array('id' => $id ));
				$product['id']		= $id;
				$product['qty']		= $qty;
				$product['name']	= $item->get('pagetitle');
				
				$selectionInfo[] = $product;
			
			} // end foreach

		}
	
		return $selectionInfo;
	
	} // end getProduceFromArray()
	
	public function getUserAddress($homeDelivery='',$user='') {
		
		// check to see if homeDelivery is set....
		$homeDelivery	= ($homeDelivery != '') ? $homeDelivery : 0;
	
	    $user = ($user != '') ? $user : $this->userInfo['userid'];
	
		// address is the array that we will return to the delivery snippet
		$address	= array();
	
	
		// build the xPDO query
		$query		= $this->modx->newQuery('DefaultAddresses');
		$query->where(array(
			'userid' 	=> $user,
			'type'		=> $homeDelivery
		));
		$query->innerJoin('Addresses','UserAddresses');
		$query->limit(1);
		$query->select(array('DefaultAddresses.*','UserAddresses.*'));
		$address		= $this->modx->getCollection('DefaultAddresses',$query);
		
		// if there is an address, we can assume that the one
		// we're looking for is the first record (and 
		// should be the only record since we've limited to 1
		if(!empty($address)) {
			foreach($address as $add) {
			
				$result		= $add->toArray();
			
			}
		} else {
		  $result = false;
		}
		
		return $result;
		
	} // end getUserAddress
	
	public function getAllProducts($depth='10') {
		// we want to find all of the products in the MODX resource tree under
		// the parent of Products and pass them back to the calling method_exists
		$t				= $this->modx->getChildIds(9,$depth);
		
		$q				= $this->modx->newQuery('modResource');
		$q->where(array('id' => 9));
		$q->andCondition(array('isfolder' => 0));
		foreach($t as $id) {
			$q->orCondition(array('id' => $id));
			$q->andCondition(array('isfolder' => 0));
		}
		$q->sortby('pagetitle','ASC');
		
		$r				= $this->modx->getCollection('modResource',$q);
		
		return $r;
		
	} // end getAllProducts
	
	public function processCatalogSubmission($data) {
		// items passed to this method will be added to the 
		// database as available for the coming week
		
		// grab the list of items from the array
		$ids			= $data['ids'];
				
		// remove all of the items from the database from this particular week
		// and then replace with these entries passed from the form
		$remove			= $this->removeItemsFromCatalog($data['season'],$data['week']);	
		
		// now add the new items to the Catalog
		foreach ($ids as $id) {
			$add[]		= $this->addCatalogItems($id,$data['season'],$data['week']);
		}
		
		return $add;
		
	} // end processCatalogSubmission

	private function addCatalogItems($i,$s,$w) {
	
		$time			= time();
		$user			= $this->modx->user->get('id');
	
		$item		= $this->modx->newObject('Catalog',array(
			'seasonid' 		=> $s,
			'week'			=> $w,
			'productid'		=> $i,
			'available'		=> '1',
			'publishedby'	=> $user,
			'publishedon'	=> $time		
		));
		
		return $item->save();
	
	} // end AddCatalogItems()

	private function removeItemsFromCatalog($s,$w) {
	
		$result		= true;
		$q			= $this->modx->newQuery('Catalog');
		$q->where(array(
			'seasonid' 		=> $s,
			'week'			=> $w
		));
		
		$c			= $this->modx->getCollection('Catalog',$q);
		
		foreach ($c as $item) {
			if($item->remove() == false) {
				$result[]	= false;
			} // end if
		} // end foreach 
		
		return $result;		
	
	} // end removeItemsFromCatalog

	public function checkCatalogExists($s,$w) {
		// this function returns true is there is a catalog
		// set up, and false if it is not yet set up
		$q			= $this->modx->newQuery('Catalog');
		$q->where(array(
			'seasonid' 	=> $s,
			'week'		=> $w
		));
		
		$items		= $this->modx->getCollection('Catalog',$q);
		if(count($items) > 0) {		
			$state = true;			
		} else {
			$state = false;
		}
		return $state;
	} //
	
	public function getWeeklyCatalog($s,$w) {
		// check to see what's in the catalog
		// we should be able to order it by ID
		// which won't guarantee alphabetical order
		// but should be close enough
	
		$q			= $this->modx->newQuery('Catalog');
		$q->where(array(
			'seasonid' 	=> $s,
			'week'		=> $w
		));	
		$q->sortby('id','ASC');
		
		$items				= $this->modx->getCollection('Catalog',$q);
		
		// We want to grab the template variables as well
		$points				= $this->modx->getObject('modTemplateVar',array('name' => 'producePoints'));
		$price				= $this->modx->getObject('modTemplateVar',array('name' => 'producePrice'));
		$img				= $this->modx->getObject('modTemplateVar',array('name' => 'produceImg'));
		$description		= $this->modx->getObject('modTemplateVar',array('name' => 'produceDescription'));
		
		if(count($items) > 0) {
    		foreach($items as $item) {
    			
    			$resourceid		= $item->get('productid');
    			$resource		= $this->modx->getObject('modResource', $resourceid);	
    			$data			= array();
    			$data['id'] 	= $resourceid; 
    			$data['name']	= $resource->get('pagetitle');
    			$data['desc']	= $description->getValue($resourceid);
    			$data['introtext'] = $resource->get('introtext');
    			$data['points']	= $points->getValue($resourceid);
    			$data['price']	= $price->getValue($resourceid);
    			$data['img']	= $img->getValue($resourceid);
    					
    			$output[]		= $data;
    			
    		} // end foreach
        } else {
    
            $output = false;
    
        } // end if else
		
		return $output;
		
	} // end getWeeklyCatalog()

	
	public function viewWeeklyCatalog($s,$w) {
		// check to see what's in the catalog
		// we should be able to order it by ID
		// which won't guarantee alphabetical order
		// but should be close enough
	
		$q			= $this->modx->newQuery('Catalog');
		$q->where(array(
			'seasonid' 	=> $s,
			'week'		=> $w
		));	
		$q->sortby('id','ASC');
		
		$items				= $this->modx->getCollection('Catalog',$q);
		
		// We want to grab the template variables as well
		$points				= $this->modx->getObject('modTemplateVar',array('name' => 'producePoints'));
		$price				= $this->modx->getObject('modTemplateVar',array('name' => 'producePrice'));
		$points				= $this->modx->getObject('modTemplateVar',array('name' => 'producePoints'));
		$description		= $this->modx->getObject('modTemplateVar',array('name' => 'produceDescription'));
		$img				= $this->modx->getObject('modTemplateVar',array('name' => 'produceImg'));
		
		// get season information
		$seasoninfo			= $this->season->getOtherSeasonsInfo($s);
		$data['seasonname'] = $seasoninfo['seasonname'];
		$data['weeknumber']	= $w;
						
				
		foreach($items as $item) {
			
			$id				= $item->get('productid');
			$resource		= $this->modx->getObject('modResource', $id);	
			$data			= array();
			$data['name']	= $resource->get('pagetitle');
			$data['desc']	= $description->getValue($id);
			$data['img']	= $img->getValue($id);
			//$data['desc']	= $resource->get('introtext');
			
			$output[]		= $this->modx->getChunk('hhncAdminCatalogViewerItemTpl',$data);
			
			
		} // end foreach
		
		return $output;
		
	} // end viewWeeklyCatalog()
	
	public function getPointLevels() {
		// this is used to determine how many different levels there should be
		// e.g. "Petite" or "Regular"
		
		$q			= $this->modx->newQuery('PointLevels');
		$q->sortby('points','ASC');
		$results	= $this->modx->getCollection('PointLevels',$q);
		
		$i=0;
		foreach ($results as $result) {
		
			$lev[$i]['modifier']		= $result->get('title_modifier');
			$lev[$i]['points']			= $result->get('points');
			$lev[$i]['levelid']			= $result->get('id');
			$i++;
		
		}// end foreach
	
		return $lev;

	} // end getPointLevels
	
	public function processDefaultBasket($json,$s,$w) {
	
		// remove the current basket and replace it
		$removed 	= $this->removeBasket($s,$w);
		$saved		= $this->saveBasket($json,$s,$w);
		
		if ($saved) { return true; }
		
	} // end saveDefaultBasket
	
	private function removeBasket($s,$w) {

		$q		= $this->modx->newQuery('WeeklyDefaults');
		$q->where(array(
			'seasonid' 	=> $s,
			'week'		=> $w
		));
		if ($s != '' && $w!= '') {
			$status		= $this->modx->removeCollection('WeeklyDefaults',array('seasonid' => $s, 'week' => $w));
		}
		
		return $status;
		
	
	} // end removeBasket()
	
	private function saveBasket($json,$s,$w) {
	
		$basket			= $this->modx->newObject('WeeklyDefaults',array(
													'seasonid' 		=> $s,
													'week'			=> $w,
													'data'			=> $json,
													'submittedby'	=> $this->modx->user->get('id'),
													'submittedon'	=> time()
													));
													
		if ($basket->save() == false) {
			echo "There was a problem saving the default basket";
			return false; 
		} else {
			
			return true;
		
		}
	
	} // end saveBasket()
	
	public function checkBasketExists($s,$w) {
		$q		= $this->modx->newQuery('WeeklyDefaults');
		$q->where(array(
			'seasonid' 	=> $s,
			'week'		=> $w
		));
		
		$collection		= $this->modx->getCollection('WeeklyDefaults',$q);
		
		if (count($collection) > 0) {
			return true;
		} else {
			return false;
		}
	
	}
	
	public function userEnrolled($s='',$user='') {
		// the default functionality of this method is to 
		// check to see if the user is enrolled in the 
		// upcoming season.   If the season is not provided as a
		// paramter, then find out what next season is.
		
		if ($s=='') {
			$t				= time();
			$seasoninfo		= $this->season->getNextSeason($t,0);
			$s				= $seasoninfo['id'];
		}
	
	
		// Check to see if the user has already enrolled in this season
		if ($user=='') {
			$user		= $this->modx->user->get('id');
		}
	
		// now that we've got a user....
		
		$query			= $this->modx->newQuery('Memberships');
		$query->where(array(
			'modx_user_id' 	=> $user,
			'seasonid'		=> $s
		));
		$results		= $this->modx->getCollection('Memberships',$query);
		$qty			= count($results);
		
		if($qty == 1) {
			foreach ($results as $r) {
				$output							= array();
				$output['level']				= $r->get('membership_status');
				$output['membership_verified']	= $r->get('membership_verified');
				$output['id']					= $r->get('id');
				$output['season']				= $r->get('seasonid');
			}
		} elseif ($qty > 1) {
		
			$output			= -1; // error
		
		} else {
		
			$output			= false;
		}
		return $output;	
	} // end user enrolled

	public function getBasketView($levels,$catalog) {
		// the number of levels will be helpful for building out the form
		$numLevels			= count($levels);
		$currentId			= $this->modx->resource->get('id');
		
		// init		
		$output				= '';
		$output				.= "<form action=\"" . $this->modx->makeUrl($currentId) . "\" method=\"post\" id=\"defaultBasketEditorForm\">";
		$output				.= "\n<table id=\"defaultBasketEditorTable\">";
		
		// Build the header row here
		$output				.= "\n\t<tr>";
		$output				.= "<th class=\"productName\">Product Name</th>";

			for ($i=0;$i<$numLevels;$i++) {
				$output		.= "<th class=\"productQty\">" . $levels[$i]['modifier'] . "</th>";
			}
	
		$output				.= "</tr>";
		// end header row		
		
		$j=0;
		foreach ($catalog as $item) {
			$output			.= "\n\t<tr>";
			$output			.= "<td><span  class=\"productItemName\">" . $item['name'] . "</span><br /><span class=\"productItemPoints\">" . $item['points'] . " point(s)</span></td>";
			
			foreach ($levels as $level) {
				$output		.= "<td>";
				$output		.= "<input type=\"text\" name=\"". $item['id'] ."[]\" value=\"\" class=\"level-".$level['levelid']."-points\"/>";
				$output		.= "<input type=\"hidden\" class=\"item-points\" name=\"points\" value=\"" . $item['points'] . "\">";
				$output		.= "</td>";			
			} // end foreach
			$output			.= "</tr>";
		} // end foreach
		
		$output				.= "\n<tr>\n\t<td><span class=\"productItemName\">Totals:</span></td>";
		
		foreach ($levels as $level) {
		  $output             .= "\n\t<td id=\"level-" . $level['levelid'] . "-total\"></td>";
		}
		$output             .= "\n</tr>";
		$output				.= "\n</table>";
		$output				.= "\n<input type=\"hidden\" name=\"s\" value=\"{$_GET['s']}\" />";
		$output				.= "\n<input type=\"hidden\" name=\"w\" value=\"{$_GET['w']}\" />";
		foreach($levels as $level) {
		  $output				.= "\n<input type=\"hidden\" class=\"levelmaxpoints\" id=\"lv" . $level['levelid'] . "-max\" name=\"lv" . $level['levelid'] . "-max\" value=\"". $level['points'] ."\" />";		
		}
		$output				.= "\n<input type=\"submit\" value=\"Submit This Form\" />";
		$output				.= "\n</form>";
		return $output;
	} // end function getBasketView()
	
	public function weekStatus() {
		// this function determines the status of the current (or given) week			
            // grab the settings
            $closeday = HHNC_ORDER_CLOSE;
            
            // assume it's open unless we prove otherwise
			$open			= true;
			$progress		= $this->season->getDailyProgress();
            
			if ($progress['dayOfWeek'] >= $closeday || ($progress['dayOfWeek'] == ($closeday-1) && $progress['hourOfDay'] > 12)) {
				$open		= false;
			} // end cutoff check
	
		return $open;
	
	}	// end week status
	
	public function hasWeekStarted($s,$w) {
		// check to see if the week has started
		$now			= time();
		
		// find the start date of the season in question
		$season			= $this->season->getSeasonById($s);
		$wkLength		= 60*60*24*7;
		$wkCumulative	= ($w-1)*$wkLength;
		
		$wkStart		= $season['start'] + $wkCumulative;
		
		if ($now > $wkStart) {
			return true;
		} else {
			return false;
		}
		

	} // end hasWeekStarted()
	
	public function checkUserToDos($s) {
		
		// Get detailed user information for this season
		$user				= $this->getMemberCurrentInfo();
		
		// 1. Verify Address
		// We want to make sure that the user 
		// has the correct address type in the Database
		$todo['address']	= $this->verifyUserAddress($user);
		
		return $todo;
				
		
	} // end checkUserTodos
	
	private function verifyUserAddress($user) {
		
		$add_query			= $this->modx->newQuery('DefaultAddresses');
		$add_query->innerJoin('Addresses','UserAddresses');
		
		if($user['canHomeDelivery'] == 1) {
			// if the user has home delivery, check to make
			// sure that they have a home address in the database
			$add_query->where(array(
				'userid'	=> "{$user['userid']}",
				'type'		=> 1
			));			
			
		} else {
			// if the user doesn't have home delivery, then 
			// make sure they have a pick up location
			$add_query->where(array(
				'userid'	=> "{$user['userid']}",
				'type'		=> 0
			));	
			
		}
		$add_query->select(array('DefaultAddresses.*','UserAddresses.*'));
		
		// run the query
		$addresses			= $this->modx->getCollection('DefaultAddresses',$add_query);
	
		if(count($addresses) > 0 ) {
			foreach ($addresses as $address) {
		
				$info['add_name']			= $address->get('name');
				$info['add_st1']			= $address->get('st1');
				$info['add_st2']			= $address->get('st2');
				$info['add_city']			= $address->get('city');
				$info['add_st']				= $address->get('st');
				$info['add_zip']			= $address->get('zip');
				$info['verified']			= $address->get('timestamp');

			}
		} else {
			$info = false;
		}
		return $info;
		
	
	} // end function verifyUserAddress
	
	public function getPickUpAddressForm($addressid) {			
		// init output
		$output			= '';

		$addresses		= $this->getPickUpAddresses(1);
		$qty			= (count($addresses) > 20) ? 20 : count($addresses);
		
		
		
		$formbody		.= '';
		$formbody       .= '<p>Please use the list below to select a pick up location for your items.  We will be adding pick up locations as they become available.</p><p>Addresses are set on a season by season basis.  You can select a new location during the next season that you enroll.</p>';
		$formbody		.= "\n<select id=\"address-selector\" name=\"addressid\" size=\"" . $qty . "\">";
		foreach ($addresses as $address) {
			
			// is this the selected record?
			$address['selected']	= ($address['id'] == $addressid) ? ' selected' : '';
			$formbody				.= "\n" . $this->modx->getChunk('hhncFormPickupAddressItemTpl',$address);
			
		}
		
		$formbody		.= "\n</select>";
		$formbody       .= "<p>Press 'Submit This Address' to continue. </p>";
		
		$data = array();
		$data['formbody']			= $formbody;
		$output						= $this->modx->getChunk('hhncFormPickupAddressFormTpl',$data);
		
		return $output;
		
	} // end getPickUpAddressForm
	
	private function getPickUpAddresses($asArray=0) {
		
		$q				= $this->modx->newQuery('Addresses');
		$q->where(array(
			'useraddress' 	=> 0,
			'active'		=> 1
		));
		$q->sortby('name','ASC');
		
		$addresses			= $this->modx->getCollection('Addresses',$q);
		
		
		if($asArray == 1) {
			$i 		= 0;
			$add	= array();
			foreach ($addresses as $a) {
			
				$add[$i]	= $a->toArray();
				$i++;
			}
			
			return $add;	
			
		} else {
		
			return $addresses;
		
		}
	
	} // end getPickupAddresses
	
	public function removeDefaultPickupAddress($u,$type='0') {

		$add			= $this->modx->removeCollection('DefaultAddresses',array(
			'userid'		=> $u,
			'type'			=> $type
		));
		
		
	} // removeDefaultAddress()
	
	public function addDefaultAddress($u,$addressid,$type=0) {
		$time			= time();
		$a				= $this->modx->newObject('DefaultAddresses',array(
													'userid' 	=> $u,
													'addressid'	=> $addressid,
													'type'		=> $type,
													'timestamp'	=> $time
						));
						
		if ($a->save() == false) {
			
			print "<p>There was an error saving the address - please contact us and let us know</p>";
		
		}
	
	} // end addDefaultPickupAddress
	
	public function removeDefaultDeliveryAddress($u) {

		$add			= $this->modx->removeCollection('Addresses',array(
			'userid'		=> $u,
			'type'			=> 1
		));
		
	} // removeDefaultAddress()
	
	public function addDefaultDeliveryAddress($u,$add) {
		$time			= time();
		$a				= $this->modx->newObject('Addresses',array(
													'name' 			=> $add['nickname'],
													'st1'			=> $add['st1'],
													'st2'			=> $add['st2'],
													'city'			=> $add['city'],
													'st'			=> $add['st'],
													'zip'			=> $add['zip'],
													'useraddress' 	=> 1,
													'addedby'		=> $u,
													'addedon'		=> $time,
													'active'		=> 1
						));
												
		if ($a->save() == false) {
			
			print "<p>There was an error saving the address - please contact us and let us know</p>";
		
		} else {
			
			$id			= $a->get('id');
			$b['remove']= $this->removeDefaultPickupAddress($u,1);
			$b['add']	= $this->addDefaultAddress($u,$id,1);
			
		}
	
	} // end addDefaultPickupAddress
	public function validateAddress($add) {
	
	
		return true;
	} // end valiedateAddress()
	
	public function updateUserSelections(array $data) {
		// first, remove any previous
		$removed		= $this->removeUserSelections($data['userid'],$data['seasonid'],$data['week']);
		$added			= $this->addUserSelections($data);		
	
      if ($added == true) { 
        return true;
      } else {
        return false;
      }
	} // end updateUserSelections
	
	private function removeUserSelections($u,$s,$w) {
		$q				= $this->modx->removeCollection('UserSelections', array(
															'userid'	=> $u,
															'seasonid'	=> $s,
															'week'		=> $w
															));
		return true;
		
	} // end function removeUserSelections()
	
	private function addUserSelections($data) {
      $now = time();
      $q = $this->modx->newObject('UserSelections',array(
        'seasonid' 	=> $data['seasonid'],
        'week'		=> $data['week'],
        'userid'	=> $data['userid'],
        'data'		=> $data['data'],
        'updated'	=> $now	   
      ));
      if ($q->save() == true) {
        return true;
      } else {
      	return false;
      }
	} // end addUserSelections
	
	public function userHasSubmittedSelections($u,$s,$w,$get=0) {
	   // This method checks to see if a given user has submitted 
	   // any custom orders for the given season/week combination
	   // if get is toggled to yes, it will return the selection object
		$query		= $this->modx->newQuery('UserSelections');
		$query->where(array(
		  'userid' => $u,
		  'seasonid' => $s,
		  'week' => $w 
		));
		
		$results	= $this->modx->getCollection('UserSelections',$query);
		if (count($results) > 0) {
			$o = ($get==1) ? $results : true;
			return $o;
		} else {
			return false;
		}
		
	} // end userHasSubmittedSelections
	
	public function orderSubmit($u,array $data,$isALaCarte='0') {
        $json           = json_encode($data);
        $season         = $this->getCurrentSeasonInfo();
        $progress       = $this->season->getDailyProgress();
        $userinfo       = $this->getMemberCurrentInfo($u);
        $now            = time();
        $address        = $this->getUserAddress($userinfo['canHomeDelivery'],$u);
        
        $order          = $this->modx->newObject('Orders',array(
          'modx_user_id' => $u,
          'seasonid' => $season['id'],
          'week' => $progress['currentWeek'],
          'data' => $json,
          'is_alacarte' => $isALaCarte,
          'is_homedeliver' => $userinfo['canHomeDelivery'],
          'addressid' => $address['id'],
          'time' => $now
        ));
   
        if ($order->save() == false) {
            return false;
        } else {
            return true;
        } 
	} // end orderSubmit
	
	public function getAllPaidMembers($s,$sortby='',$sortdir='ASC') {
	   /*
	       Simple method to grab all of the paid members for the current season
	       by querying the membership database.  Returns an array of objects if
	       true and false if no results found
	   */
	   $query      = $this->modx->newQuery('Memberships');
	   $query->where(array(
	       'seasonid' => $s
	   ));
	   
       if($sortby!='') {
        $query->sortby($sortby,$sortdir);   
       } // end if
       
	   $users      = $this->modx->getCollection('Memberships',$query);
	   // check to make sure these members still exist in the modx database
	   foreach ($users as $user) {
	       $id = $user->get('modx_user_id');
	       $mUser = $this->modx->getObject('modUser',$id);
	       if ($mUser != null) {
	           $validUsers[] = $user;
	       }
	       
	   }
	   $result     = (count($validUsers) > 0) ? $validUsers : false;
	   return $result;
	} // end getAllPaidMembers
	
	public function getLevelDefaultSelections($s,$w,$membership_status) {
	/*
	   
	   In the initial design of this system, default selections allowed staff to set
	   specific default baskets.  However, before launch, it was determined that these
	   default baskets were not needed.
	   
	   The original method is found in the OLD folder
	   
	   Instead we now just simply return a generic default "Classic Basket"
	   
	*/
	   $memType = $this->modx->getObject('Membershiplevels',array('level_number' => $membership_status));
	   $memLevel= $memType->get('level_points');
	   $memName = $memType->get('level_name');
	   
	   $isPetite = stripos($memName,'petite');
	   $output = '';
	   
	   if ($isPetite === FALSE) {
	       $output = '2'; // This is a Classic Basket
	   } else {
	       $output = '1'; // This is a Petite Classic Basket
	   }
	   
	   return $output;
	   
	} // end getLevelDefaultSelections
	
	public function getOrders($s,$w='') {
	   // gets all of the orders for the given season
	   $query = $this->modx->newQuery('Orders');
       $where = array();
       $where['seasonid'] = $s;
       if ($w != '') {
        $where['week'] = $w;
       }
       $query->where($where);
       $query->sortby('seasonid','ASC');
       $orders = $this->modx->getCollection('Orders',$query);
       if(count($orders) > 0) {
        return $orders;
       } else {
        return false;
       }
	} // end function getOrders
	
	public function getOrderBreakdownByWeek($orders) {
	   // this function takes a modx collection of orders and 
	   // parses through it to determine the breakdown of information
	   $orderinfo = array();
	   foreach ($orders as $order) {
	       $week = $order->get('week');
	       if ($week != '') {
	         $orderinfo[$week]++;
	       }
	         
	   }
	   if(is_array($orderinfo)) { ksort($orderinfo); }
	   $results = (!empty($orderinfo)) ? $orderinfo : false;

	   return $results; 
	}
	
	public function checkOrderForItem($data,$itemid) {
	   if ($array = json_decode($data,1)) {
            $qty = $array[$itemid];
            if ($qty > 0) {
                $result = $qty;
            } else {
                $result = 0;
            }
	   } else {
	       $result = false;
	   }
	   
	   return $result;
	   
	} // end checkOrderForItem()
 	
 	public function getMembershipSelectBox($sl='') {
 	  $output = '';
 	  
 	  // get the membership levels
        $membershiplevels = $this->modx->getCollection('Membershiplevels');
        $ml = array();
        foreach ($membershiplevels as $level) {
            $level_number = $level->get('level_number');
            if($level_number != 0) {
                $ml[$level_number]['level_number'] = $level_number;
                $ml[$level_number]['level_name'] = $level->get('level_name');
            }
        } // end foreach
        ksort($ml);
        
        $output .= "\n<select name=\"membershiplevel\">";
        foreach($ml as $memlev){
            $selected = ($memlev['level_number'] == $sl) ? ' selected' : '';
            $output .= "\n\t<option value=\"{$memlev['level_number']}\"".$selected.">{$memlev['level_name']}</option>";
        } // end foreach
        $output .= "\n</select>";
        
        return $output;
        
 	} // end getMembershipSelectBox
 	
 	public function manuallyAddMembership($u,$s,$l) {
 	  
 	  $now = time();
 	  
 	  $oldmmem = $this->modx->removeCollection('Memberships',array(
 	      'seasonid' => $s,
 	      'modx_user_id' => $u
 	  ));
 	  
 	  $defaults = $this->modx->getObject('Membershiplevels',array('level_number' => $l));
 	  
 	  $membership = $this->modx->newObject('Memberships',array(
 	      'modx_user_id' => $u,
 	      'seasonid' => $s,
 	      'membership_status' => $l,
 	      'membership_verified' => $now,
 	      'can_alacarte' => $defaults->get('alacarte'),
 	      'can_customorder' => $defaults->get('custom_order'),
 	      'can_homedeliver' => $defaults->get('homedelivery'),
 	      'manual_override' => 1
 	  ));
 	  
 	  if ($membership->save() == true) {
 	      return true;
 	  } else {
 	      return false;
 	  }
 	
 	} // end manuallyAddUser()
 	
 	public function getMembershipPermissionsCheckboxes(array $currentPermissions) {
 	  $output = '';
 	  foreach($currentPermissions as $code => $p) {
 	      $checked = ($p['val'] == 1) ? ' checked' : '';
 	      $output .= "\n\t<div class=\"membership-checkbox\"><input type=\"checkbox\" name=\"$code\"".$checked."/> {$p['text']}</div>"; 
 	  
 	  } // end foreach
 	
 	  return $output;
 	
 	} // end getMembershipPermissionsCheckboxes
 	
 	public function getMembersOrders($uid,$s='',$w='') {
 	
 	  $q = $this->modx->newQuery('Orders');
 	  $q->where(array(
 	      'seasonid' => $s,
 	      'week' => $w,
 	      'modx_user_id' => $uid
 	  ));
 	  $orders = $this->modx->getCollection('Orders',$q);
 	  if( count($orders) > 0 ) {
 	      return $orders;
 	  } else {
 	      return false;
 	  }
 	
 	} // end getMembersOrder
 	
 	public function displayOrderArrayAsList(array $orderArray, $includeQty='1',$showEmpties='0') {
 	
 	  $output ='';
 	
 	  if ( count($orderArray) > 0 ) {
 	  
 	      $output .= "\n<ul class=\"order-listing\">";
 	      
 	      foreach ($orderArray as $item => $qty) {
 	      /*
 	          08/23/2011
 	          =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
 	          display each item once per order (e.g, not example - x orders, but rather, example, example)
 	      */
 	      
 	          if ($qty > 0) {
                $produce = $this->modx->getObject('modResource',$item);
                $name = $produce->get('pagetitle');
                
                for($i=0;$i<$qty;$i++) {
                
     	          $output .= "\n\t<li>" . $name . "</li>";

                } // end for
     	      
 	          }
 	      }
 	      
 	      $output .= "\n</ul>";
 	  
 	  }
 	  return $output;
 	
 	} // end displayOrderArrayAsList()
 		
} // end class HHNCManager
?>