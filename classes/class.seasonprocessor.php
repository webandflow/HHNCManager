<?php
/*
    * CLASS SeasonProcessor
    * -=-=-=-=-=-=-=-=-=-=-
    * The Season Processor Class is used to handle most of 
    * the time-related methods for the HHNC manager.
    *
    * Version 0.2.1
    * =-=-=-=-=-=-=
    * - further tweaks to getNextWeek()
    * 
    * Version 0.2
    * =-=-=-=-=-=
    * - Changed DailyProgress to return false if we are 
    *   not current in a season
    * - Changed getNextWeek to process results correctly
    *   when we're not currently enrolled in season. Now,
    *   handles 'next week' methods correctly

*/

date_default_timezone_set('America/New_York');

class SeasonProcessor {
	
	public $length;
	private $modx;
	private $start;
	private $stop;
	public	$startDate;
	public	$stopDate;
	private $seasonName;
	private	$progress;
	private $seasonInfo;
	
	function __construct(&$modx,$start,$stop) {

		$this->modx			= $modx; 
		$this->start		= $start;
		$this->stop			= $stop;
		
		// init some variables here
		$this->seasonid		= '';
		$this->startDate	= '';
		$this->stopDate		= '';
		$this->seasonName	= '';
		$this->length		= '';
		$this->progress		= '';
		$this->seasonInfo	= '';

		// populate the variables for this season
		// when this object is constructed
		$this->populateTimeDetails();
		
	} // end function
	
	private function populateTimeDetails() {
	
		$startDate			= $this->intToDate($this->start);
		$this->startDate	= $startDate;
		$stopDate			= $this->intToDate($this->stop);
		$this->stopDate		= $stopDate;
		
		$this->getDailyProgress();
		
	} // emd getTimeSpan()

	public function intToDate($timestamp, $format="l, F j, Y") {
	
		$date		= date($format,$timestamp);
		return $date;
	
	} // end intToDate

	public function getTotalDays() {
	
		$days			= (((($this->stop - $this->start)/60)/60)/24);
		$days			= ceil($days);
		return $days;
	
	} // end percentComplete
	
	public function getCurrentWeek() {
	
		$currentWeek = ($this->progress['currentWeek'] != '') ? $this->progress['currentWeek'] : FALSE;
		return $currentWeek;
		
	
	} // end getCurrentWeek
	
	public function getDailyProgress() {
		// grab the current timestamp from the server
		// and find timestamp for 00:00:00 on this date
		// We'll use this to figure out which day we're in
		$now					= getDate();
		$today					= mktime(0,0,0,$now['mon'],$now['mday'],$now['year']);
		$totaldays              = $this->getTotalDays();
		
		
		// in the progress array that we'll use to return the data
		$progress					= array();
		$progress['totalDays']		= $totaldays;
		$progress['daysPassed']		= ceil(((($today - $this->start)/60)/60)/24);		
		$progress['daysRemain']		= $progress['totalDays'] - $progress['daysPassed'];
		
		// week information
		$progress['totalWeeks']		= ceil(($progress['totalDays'])/7);
		$progress['sysWeek']		= floor($progress['daysPassed']/7 );
		$progress['currentWeek']	= $progress['sysWeek'] + 1;
		$progress['dayOfWeek']		= $progress['daysPassed'] % 7;
		$progress['hourOfDay']		= date("H");
		
		// used for determine plurality of terms
		$dayOrDays					= ($progress['daysRemain'] > 1) ? "days" : "day";
		$progress['remmsg']	= "<p>We have " . $progress['daysRemain'] . " " . $dayOrDays .  " remaining in this season</p>";
		
		$this->progress		= $progress;
		return $progress;
		
	} // end getDailyProgress
	
	public function getProgressDetail($detail='totalWeeks') {
		// this function is used to grab a single detail from the progress array
		
		$result			= $this->progress[$detail];
		return $result;
		
	} // function getProgressDetail
	 
	public function setSeasonInfo($seasoninfo) {
		// this is used to keep a copy of the source material
		// used to generate this season in case we need
		// access to it later... and we will :)
		if (is_array($seasoninfo)) {
			$this->seasonInfo		= $seasoninfo;
		}
	
	} // end setSeasonInfo()
	
	public function getSeasonInfo() {
		
		return $this->seasonInfo;
		
	}
	
	public function getProgressInfo() {
		return $this->progress;
	}
	
	public function getProposedSeasonData($start,$length) {
	
		// we'll use t to return the info
		$t				= array();
		
		// process the incoming variables
		// first the date string in mm/dd/year
		// we'll bust into an array
		$s				= explode("/",$start);
		
		$startTS		= mktime(0,0,0,$s[0],$s[1],$s[2]);
		
		$t['startdate']		= $start;
		$t['startfulldate']	= date("F j, Y",$startTS);
		$t['startts']		= $startTS;
		
		
		// we want to add the length in seconds.
		// this should be exactly the number of
		// weeks in length - same time of day
		$lengthInSeconds	= $length*7*24*60*60;
	
		$e					= $startTS + $lengthInSeconds;
		$t['enddate']		= date("n/j/Y",$e);
		$t['endts']			= $e;
		$t['endfulldate']	= date("F j, Y",$e);
		
		return $t;
		
	} // end getSeasonEndDate()
	
	public function checkDateConflicts($timestamps) {
		// this function returns an empty array if there are no 
		// conflicts found with any other seasons
		// or a string containing an error message
		// if TRUE
		
		// first check for basic dates within an existing season
		
		$i 		= 0;
		$trip	= 0;
		
		foreach($timestamps as $key => $ts) {
		
			$query		= $this->modx->newQuery('Seasons');
			$query->where(array(
				'start:<' => $ts,
				'end:>' => $ts
			));

			$results	= $this->modx->getCollection('Seasons',$query);
			$conflicts	= array();
			
			if(count($results) > 0) {
			$trip 		= 1;
				foreach($results as $conflict) {
					$conflicts[$i]['seasonid']	= $conflict->get('id');		
					$conflicts[$i]['name']		= $conflict->get('name');
					$i++;
				} // end foreach
			} // end if
		
		}
		
		// then we need to check to see if this season spans an
		// entire existing season
		
			$query		= $this->modx->newQuery('Seasons');
			$query->where(array(
				'start:>' 	=> $timestamps['start'],
				'end:<'		=> $timestamps['end']
			));
			$results		= $this->modx->getCollection('Seasons',$query);
			
			if(count($results) > 0) {
			$trip =1;
				foreach($results as $conflict) {
					$conflicts[$i]['seasonid']		= $conflict->get('id');
					$conflicts[$i]['name']			= $conflict->get('name');
	
					$i++;
				} // end foreach
			
			} // end if

		return $trip;
		
	} // end checkDateConflicts()
	
	public function getOtherSeasonsInfo($id) {
		
		$query		= $this->modx->newQuery('Seasons');
		$query->where(array('id' => $id));
		
		$season		= $this->modx->getCollection('Seasons',$query);
		
		foreach($season as $s) {
			$info	= $s->toArray();
		}
		
		return $info;
		
	} // end getOtherSeasonsInfo()
	
	public function createNewSeason($data) {
		$season		= $this->modx->newObject('Seasons',array(
			'start' => $data['start'],
			'end'	=> $data['end'],
			'name'	=> $data['name']
		));
		
		return $season->save();
		
	} // end createNewSeason
	
	public function getFutureSeasons($q=3,$current=1) {
		// get the next $q seasons by selecting the 
		// next $q end times (this includes the current
		// season as well by default).
		
		$now		= time();
		
		$query		= $this->modx->newQuery('Seasons');
		$query->where(array('end:>' => $now));
		$query->sortby('end','ASC');
		
		// we we don't want the current season, add to the limit
		// so we can still grab the next $q seasons 
		if ($current == 0) {$q++;}
		$query->limit($q);
		
		$seasons	= $this->modx->getCollection('Seasons',$query);
		if (count($seasons) > 0) {
			foreach ($seasons as $season) {
				$results[]		= $season->toArray();
			} // end foreach
			
			// if we don't want the current season, shift it off the array
			if ($current == 0) {array_shift($results);}
			
		} else {
		
			$results = false;
		}		
		return $results;
		
	} // end function getSeasons
	
	public function getNextWeek() {
		// used to determine the next week for which action 
		// must be taken.  The next season could be in the next
		// season so we'll need to keep that in mind.  
		// should return an array....
		//
		// 		$x['season']
		// 		$x['week']
		//
		// We'll use this to populate the appropriate fields in
		// the hhncAdminNextWeekTpl chunk.

		$p			= $this->progress;
		$now		= time();
		
		if ($p['totalDays'] > 0) {
    		$nw			= array(); // NextWeek array init
    				
    		if ($p['currentWeek'] >= $p['totalWeeks']) {
    			// this means that we are likely in the last week
    			// of the current season.  So we want to grab a timestamp
    			// and find the next start date in the db
    			$ns					= $this->getNextSeason($now);
    			$nw['seasonname']	= $ns['name'];
    			$nw['weeknumber']	= 1;
    			$nw['seasonid']		= $ns['id'];
    			$nw['currentseason']= 0;
    			 
    		} else {
    			// this means that we're currently in a season and that
    			// the next week is also a part of this season
    			// here we should simply be able to increment the week value
    			// and have everything work out nicely
    		
    			$s					= $this->seasonInfo;	// grab this seasons info
    		
    			$nw['seasonname']	= $s['name'];
    			$nw['weeknumber']	= $p['currentWeek'] + 1;
    			$nw['seasonid']		= $s['id'];
    			$nw['currentseason']= 1;
    		
        		if ($s == false) {
        			$nw = false;
        		}
    		
    		}
    		
    		
    		
        } else {
            // if we're here, then we're not in a season so we can't use the progress
            // array to grab the upcoming week's information.  We need to get next season
            // and can assume that we just need the first week.
            $ns = $this->getNextSeason($now);
            $nw['seasonname'] = $ns['name'];
            $nw['weeknumber'] = 1;
            $nw['seasonid'] = $ns['id'];
            $nw['currentseason'] = 0;
                        
        
        } // end if...else
			
		return $nw;
			
	} // end getNextWeek()
	
	public function getNextSeason($time,$detailed=1) {
		
		$query		= $this->modx->newQuery('Seasons');
		$query->where(array('start:>' => $time));
		$query->sortby('start','ASC');
		$query->limit(1);
		
		$seasons	= $this->modx->getCollection('Seasons',$query);
		
		if(count($seasons) > 0) {
			foreach($seasons as $season) {
				$result	= $season->toArray();
			}
			$s			= ($detailed == 1) ? $this->getSeasonDetails($result,$time) : $result;
		} else {
			$s			= false;
		}
		return $s;
	} // end getNextSeason()
	
	public function getSeasonById($id,$detailed=0) {
		$now		= time();
		$query		= $this->modx->newQuery('Seasons');
		$query->where(array('id' => $id));
		$query->limit(1);
		
		$seasons	= $this->modx->getCollection('Seasons',$query);
		
		foreach($seasons as $season) {
			$result	= $season->toArray();
		}
		
		$s			= ($detailed == 1) ? $this->getSeasonDetails($result,$now) : $result;
		
		return $s;
		
	} // end getSeasonById()
	
	private function getSeasonDetails($season,$time='') { // takes an array representing a row from the seasons table in mysql
	
		// we'll use t to return the info
		$t				= array();
		
		// this should be the number of total weeks for this season
		$t['totalWeeks']= (((($season['end']-$season['start'])/60)/60)/24)/7;
		
		$t['start']			= $season['start'];				
		$t['startdate']		= date("n/j/Y", $season['start']);
		$t['starttextdate'] = date("F j, Y",$season['start']);
		$t['startfulldate']	= date("l, F j, Y",$season['start']);

		$t['end']			= $season['end'];		
		$t['enddate']		= date("n/j/Y", $season['end']);
		$t['endtextdate']   = date("F j, Y",$season['end']);
		$t['endfulldate']	= date("l, F j, Y",$season['end']);
		
		$t['id']			= $season['id'];
		$t['name']			= $season['name'];
		$t['is_detailed']	= 1;
		
		if ($time != '') {
			$t['daysToStart']	= floor(((($season['start'] - $time)/60)/60)/24);
		}
		
		return $t;

	} // end getSeasonDetails
	
	public function checkWeekHasPassed($s,$w,$now) {
		// this week takes a season and week and 
		// checks to see if the week has already
		// passed - meaning has it started or 
		// has it already finished.
		
		// we will need to know how long a week is in seconds
		$wlen			= 60*60*24*7;
		$season			= $this->getSeasonById($s);

		if (!empty($season)) {
			
			// find the start time of the week by adding 
			// $w-1 weeks in seconds to $sStart;	
			$sStart		= $season['start'];
			$wStart		= $sStart + ($wlen*($w - 1));
			
			if($wStart > $now) {
				// if the week start time is greater than the current time
				// then the week hasn't started yet
				$r		= 0;
			
			} else {
				// likewise, if $wStart < $now, then the week has already started
				$r		= 1;
			}
			
		} else {
			// if we're here, there was an error and we're not sure 
			// about what else we could do....
			$r			= -1;
		
		}
		
		return $r;
		
	} // end checkWeekHasPassed()
	
} // end SeasonManager

?>