$(document).ready(function(){

var pointcalculator = {
   levelinfo: new Array(),
   hasNaN: 0,
   init: function() {
     // set the level info array...
     // how many levels are there?
     var numlevels = $('.levelmaxpoints').length;
     for(i=0;i<numlevels;i++)
     {
        var j = i+1;
        var csshandle = '#lv'+j+'-max';
        this.levelinfo[i] = new Array();
        this.levelinfo[i]['max'] = Number($(csshandle).val());
        this.levelinfo[i]['cssclass'] = '.level-'+j+'-points';
        this.levelinfo[i]['totalhandle'] = '#level-'+j+'-total';
        this.levelinfo[i]['totalpoints'] = 0;
        this.levelinfo[i]['hasNaN'] = 0;
     }
    
     $('input[type=text]').val(0);
     this.setTotal();
   },
   addAllItems: function(levelnumber) {
     var levelinfo = this.levelinfo[levelnumber];
     var curpoints = 0;
     var trackerclass = levelinfo['cssclass'];
     var qtys = $(trackerclass);
     var hasNaN	= 0;
     for(j=0;j<qtys.length;j++) {
       var curitem 	= qtys[j];
	   var qtyitem 	= $(curitem).val();
	   // this gets the quantity of this particular item
	   qtyitem		= Number(qtyitem);
	   // check here to see if there is problem with the type of character entered
	   if(isNaN(qtyitem)) {
	     $(curitem).addClass('fielderror');
	     hasNaN++;
	     //$(curitem).val('');
	   } else {
	     $(curitem).removeClass('fielderror');
	   }
	   var ptsitem 	= $(curitem).siblings('.item-points').val();
	   ptsitem		= Number(ptsitem)
	   var totalpts = (qtyitem)*(ptsitem);
	   curpoints += totalpts;
     } // end for
    this.levelinfo[i]['totalpoints'] = curpoints;
  	this.levelinfo[i]['hasNaN']	= hasNaN;
  	this.hasNaN  = hasNaN;
  	
    return true;
   },
  
   setTotal: function() {
    var level = this.levelinfo;
    for(i=0;i<level.length;i++) {
    var selector = level[i]['totalhandle'];
    console.log(selector);
    // var totalholder    = $().val();
     if (this.addAllItems(i) && !isNaN(this.levelinfo[i]['totalpoints'])) {
        $(selector).html(this.levelinfo[i]['totalpoints']);
     } else {
        $(selector).html('ERROR');
     } // END ELSE
     } // END FOR
   } // END SETTOTAL()
};

// activate the calculator...
pointcalculator.init();

$('.level-1-points, .level-2-points').blur(function(){
	pointcalculator.setTotal();
});

$('#defaultBasketEditorForm').submit(function(){

    for(i=0;i<this.levelinfo.length;i++) {
        var level = this.levelinfo[i];
        if(level['totalpoints'] > level['max']) {
            alert("You've used too many points.  Please check the form and try again.");
            return false;
            break;
        } else if(level['totalpoints'] < level['max']){
            var confirmation = confirm("You haven't used all of the available points. Are you sure you want to do this?");
            if(confirmation) {
                //return true;
            } else {
                return false;
                break;
            }
        }
    }
    
    return false;
	

});
}); // end 