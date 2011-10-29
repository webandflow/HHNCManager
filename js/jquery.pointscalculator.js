$(document).ready(function(){
var pointcalculator = {
   maxpoints: 0,
   curpoints: 0,
   hasNaN: 0,
   init: function() {
     this.maxpoints	= Number($('#max-user-points').html());
     this.setTotal();
   },
   addAllItems: function() {
     this.curpoints = 0; // re-init the tracker
     var curpoints = 0;  // local tracker
     var qtys = $('.selection-editor-qty');
     var hasNaN	= 0;
     for(i=0;i<qtys.length;i++) {
       var curitem 	= qtys[i];
	   var qtyitem 	= $(curitem).val();
	   qtyitem		= Number(qtyitem);
	   // check here to see if there is problem with the type of character entered
	   if(isNaN(qtyitem)) {
	     $(curitem).addClass('fielderror');
	     hasNaN++;
	     //$(curitem).val('');
	   } else {
	     $(curitem).removeClass('fielderror');
	   }
	   var ptsitem 	= $(curitem).siblings('p').find('.points-display').html();
	   ptsitem		= Number(ptsitem)
	   var totalpts = (qtyitem)*(ptsitem);
	   
	   this.curpoints += totalpts;
	   curpoints += totalpts
     } // end for
  	this.hasNaN	= hasNaN;
    return curpoints;
   },
   setTotal: function() {
     var total = this.addAllItems()
     
     if(!isNaN(total)) {
       $('#total-points-display').html(total);
     } else {
       $('#total-points-display').html('[ERROR]');
     }
   }
};

// activate the calculator...
pointcalculator.init();

$('.selection-editor-qty').blur(function(){
	pointcalculator.setTotal();
});

$('.changeUserSelectionsForm').submit(function(){
    pointcalculator.setTotal();
	var curpoints		= pointcalculator.curpoints;
	var maxpoints		= pointcalculator.maxpoints;	
	if (curpoints > maxpoints) {
		// pop an alert box here
		alert("You've used too many points.  You're only allowed to use "+maxpoints+" but you seem to have used "+curpoints+".  Please change your selections and try again");
		return false;
	} else if (curpoints < maxpoints) {
		// pop a confirm dialog here
		var remain		= Number(maxpoints) - Number(curpoints);
		var confirmation = confirm("You haven't used all of your points. You still have "+remain+" remaining.  Are you sure you want to submit these selections?");
		if (confirmation) {
			return true;
		} else {
			return false;
		}
	} else if(pointcalculator.hasNaN > 0) {
		alert("You've got an error in your form.  Make sure that you've got all the fields filled out correctly.  Any fields with problems should be marked in red.");
		return false;
	}else {
		return true;
	}
	
});

}); // end 