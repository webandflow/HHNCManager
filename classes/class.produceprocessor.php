<?php
/*
    * CLASS ProduceProcessor
    * -=-=-=-=-=-=-=-=-=-=-=
    *
*/

class ProduceProcessor {
	
	private $modx;
	
	function __construct(&$modx) {

		$this->modx			= $modx; 
				
	} // end function
	
	public function getGrowingNow($cat='') {
	   // grabs the items listed as growing now in the modx databases
	   
	   // Grab the growing now template variable object 
	   $is_growing = $this->modx->getObject('modTemplateVar',array('name' => 'produceGrowingNow'));
	   
        if ($cat != '' && is_numeric($cat)) {
            $growingArray = array();
            $parent = $this->modx->getObject('modResource',$cat);
            if ($parent != null) {
                // get the children
                $cri = $this->modx->newQuery('modResource'); // i.e. criteria
                $cri->where(array(
                    'parent' => $cat
                ));
                $cri->sortby('pagetitle','ASC');
                
                $items = $parent->getMany('Children',$cri);
        
                foreach($items as $item) {
                    // get the ID of the item
                    $id = $item->get('id');
                    $growing = $is_growing->getValue($id);

                    if ($growing == 'yes') {
                        $name = $item->get('pagetitle');
                        $growingArray[$id] = $name;
                    } // end if

                } // foreach            
            }

    	   return $growingArray;
    
        } else {
         // if the parent isn't set, set we need to fail out => return false    
         return false;        
	   }

	} // end getGrowingNow()
	
	
} // end ProduceProcessor

?>