jQuery(document).ready(function() {
	//// Apply Chosen plugin functionality ////
	$("select#fields").chosen();
    $("#id_scheme").chosen();
	$("select#objectsPerPage").chosen();
     $('#id_scheme').change(function(){
         
            schemeid = $(this).val();
            //alert(schemeid); 
           //  alert(url_plugin  + "ajaxControls.php");
              $.ajax({
                     type: "GET",
                     async: false,
                     url: url_plugin  + "ajaxControls.php",
                     data: {"sid" : schemeid },
                     success: function(data){	
                        
                        // alert(pathbase);
                         $('select#fields').html(data);  
                         $('select#fields').trigger("chosen:updated");      
                
                     }
               });  
        });

    $('#fields_chosen').click(function(){
         
        if(schemeid!=''){
              $.ajax({
                     type: "GET",
                     async: false,
                     url: url_plugin  + "ajaxControls.php",
                     data: {"sid" : schemeid },
                     success: function(data){	
                        
                        // alert(pathbase);
                         $('select#fields').html(data);  
                         $('select#fields').trigger("chosen:updated");      
                
                     }
               }); 
         } 
    });		
 
  // pagination part     
  var e = document.getElementById("objectsPerPage"); 
  var nb = e.options[e.selectedIndex].value;         
  /*var nb = 10;
   if (perpage > 0) {
        nb = perpage;
    }*/
    
    var start = 0;

    var end = start + nb;
    var length = $('.kora-objs .kora-obj').length;
    var list = $('.kora-objs .kora-obj');
    
    list.hide().filter(':lt('+(end)+')').show();
    
    var currentPage = 1;
    $('.prev, .next').click(function(e){
       e.preventDefault();
       console.log(start);
       if( $(this).hasClass('prev') ){
           start -= nb;
      /*     if (currentPage - 1 > 0) {
               currentPage = currentPage - 1;
           }*/
       } else {
           start += nb;
       }
        if (start < 0 ) {
            start = 0;
        } 
        if (start >= length) {
            if (length % nb ==0) {
                 start = length - nb;
            } else {
                 start = length - (length % nb);
            }
           
        }
       //if( start < 0 || start >= length ) start = 0;
       end = start + nb;        
       console.log(start);
       console.log(end);
       
       if( start == 0 ) list.hide().filter(':lt('+(end)+')').show();
       else list.hide().filter(':lt('+(end)+'):gt('+(start-1)+')').show();
    });
    
    
    
    	//// Similar to deleteKoraObjs() on Library page ////

	var selectedCount = 0;
    var divs = [];
    // Function to update 'Add Objects' button
	var addKoraObjs = function() {
		if (selectedCount == 0) {
			$('#add-kora-objs > input').val( 'Add Object(s) to Gallery' );
			$('#add-kora-objs').removeClass('slide-remove-objs');
		}
		
		else {
			$('#add-kora-objs > input').val( selectedCount + ' selected // add object(s) to gallery' );
			$('#add-kora-objs').addClass('slide-remove-objs');
		}
	};
   // Object Fields Click Functionality
	$('.kora-obj-fields').click( function(event) {
		// Stops click from registering twice
		event.stopImmediatePropagation();
	    var controldiv = [];
	    // Get associated Kora object
	    var koraObj = $(this).parent().parent().parent();
	    if( koraObj.hasClass('kora-obj-active') ) {
	      koraObj.removeClass('kora-obj-active');
	   
	    }else{
	      koraObj.addClass('kora-obj-active');  
	    }
	    
	    var objKID = koraObj.attr("id");
	    // get control fileds name for each object
	    controldiv.push(objKID);
	    $(this).find('li').each(function(){
	        var key = $(this).find('span');
	        controldiv.push(key.text());
	        //alert(key.text());
	    });
	    
		if( koraObj.hasClass('kora-obj-active') ) {
			selectedCount++;
      		divs.push(controldiv);

		}else {
			selectedCount--;
            //alert(koraObj.attr("alt"));
            for (var i =0; i < divs.length; i++){
		        if (divs[i][0] === objKID) {
		            divs.splice(i,1);
					break;
		        }
		    }
		}
	
		addKoraObjs();
	});



	//// "Select All" Button Functionality ////
	$('button#selectAll').click( function(event) {
		event.preventDefault();
		$('.kora-obj').addClass('kora-obj-active');

		// Update 'selectedCount' and 'Add Objects' button
		selectedCount = $('.kora-obj-active').length;
        addKoraObjs();
	});
	
    
    // add kora object into new gallery
       $('#add-kora-objs').click(function() {
       
    	   var image_control = $("#image_control").text();
          var video_control = $("#video_control").text();
      	  var audio_control = $("#audio_control").text();
           schemeid = $('#id_scheme').val();
           var galleryid=$("#galleryid").val();
            var gallery_name= $('#gallery_name').val();
            var gallery_description = $('#gallery_description').val();
            if(gallery_name.length>0 && gallery_description.length > 0)
            {
                
               for (var i =0; i < divs.length; i++){
				   var chk = [];
				    chk.push(divs[i][0]);
					chk.push(gallery_name);
					chk.push(gallery_description);
                    //var chk = divs[i][0];
                    var controlfileds = divs[i];
                   
				    $.ajax({
						type: "GET",
						async: false,
						url: url_plugin+"ajax/insert_existinggallery.php",
						data: {"chk": chk, "schemeid" : schemeid, "galleryid" : galleryid, "controlfileds" : controlfileds, "image_control": image_control,"video_control": video_control,"audio_control": audio_control},
					
						success: function(data){
							if(data=='false'){
									parent.location.reload();

							} else if (data == false) {
								parent.location.reload();
							}
							else{
							
								parent.location.reload();
							
							}
						
					
						}
					});  
                }
            } else {
                alert("Please Input Title and Description!");
            }


	});
	
    //// Functionality for "Edit Details" Button ////
    var editObjectModal = $('#editObjectModal.remodal');

    // Add class to modal, for styling purposes, if open
    if( editObjectModal.remodal().getState() == 'opened' ||
        editObjectModal.remodal().getState() == 'opening' ) {
        editObjectModal.parent().addClass( 'largeModal' );
        
    }

    // Open Modal
    $('div.kora-obj-left input[type=button]').click( function() {
        var kid_open  = $(this).attr('id');
        var schemeID = $(this).attr('alt');
        var koraobj = $(this).parent().parent();
       // alert(koraobj.attr('id'));
        var image_control = $("#image_control").text();
        var video_control = $("#video_control").text();
        var audio_control = $("#audio_control").text();
        var control_displayed = [];
        koraobj.find('li').each(function(){
            var key = $(this).find('span');
            control_displayed.push(key.text());
           // alert(key.text());
        });
       // alert(control_displayed);
       // alert(image_control);
        $.ajax({
                  type: "GET",
                  async: false,
                  url: url_plugin  + "ajax_gallery_object_details.php",
                  data: {"kid" : kid_open, "schemeid": schemeID,  "image_control": image_control,"video_control": video_control,"audio_control": audio_control, "control_displayed": control_displayed },
                  success: function(data){	
                    $('.object_details').html(data);  
                       
                  }
        });  
        editObjectModal.remodal().open();
        
    });


    //// "Edit Details" Modal /////////////////////////////////////////////////
    //// Back Arrow Functionality ////
    $('#editObjectModal img#backArrow').click( function() {
        editObjectModal.remodal().close();
    });

	//// Checkbox Functionality ////
	$('#editObjectModal input[type=checkbox]').click( function(event) {
        // Stops click from registering twice
        event.stopImmediatePropagation();

		var checkboxId = $(this).attr('id');
        var checkboxLabel = $('#editObjectModal label[for=' + checkboxId + ']');
        var checkboxPar = $('#editObjectModal p#' + checkboxId);

        // Toggle 'active' classes on associated label and paragraph
        checkboxLabel.toggleClass( 'activeLabel' );
        checkboxPar.toggleClass( 'activeParagraph' );
	});

    //// 'Update Object Details' Button Functionality ////
	$('#editObjectModal input[type=submit]').click( function() {
		editObjectModal.remodal().close();
	});
			/*..................*/
			// get first character
		function getFirstCh(c) {
			execScript("tmp=asc(\""+c+"\")", "vbscript");
			tmp = 65536 + tmp;
			if(tmp>=45217 && tmp<=45252) return "A";
			if(tmp>=45253 && tmp<=45760) return "B";
			if(tmp>=45761 && tmp<=46317) return "C";
			if(tmp>=46318 && tmp<=46825) return "D";
			if(tmp>=46826 && tmp<=47009) return "E";
			if(tmp>=47010 && tmp<=47296) return "F";
			if((tmp>=47297 && tmp<=47613) || (tmp == 63193)) return "G";
			if(tmp>=47614 && tmp<=48118) return "H";
			if(tmp>=48119 && tmp<=49061) return "J";
			if(tmp>=49062 && tmp<=49323) return "K";
			if(tmp>=49324 && tmp<=49895) return "L";
			if(tmp>=49896 && tmp<=50370) return "M";
			if(tmp>=50371 && tmp<=50613) return "N";
			if(tmp>=50614 && tmp<=50621) return "O";
			if(tmp>=50622 && tmp<=50905) return "P";
			if(tmp>=50906 && tmp<=51386) return "Q";
			if(tmp>=51387 && tmp<=51445) return "R";
			if(tmp>=51446 && tmp<=52217) return "S";
			if(tmp>=52218 && tmp<=52697) return "T";
			if(tmp>=52698 && tmp<=52979) return "W";
			if(tmp>=52980 && tmp<=53688) return "X";
			if(tmp>=53689 && tmp<=54480) return "Y";
			if(tmp>=54481 && tmp<=62289) return "Z";
			return c.charAt(0);
		}
		// select helper
		SelectHelper = new function() {

		this.init = function() {
		document.addEventListener("onkeypress", function() {
		var elm = event.srcElement;
		if (elm.tagName == "SELECT"
		&& elm.className.indexOf("SelectHelper") == -1) {
		elm.className += "SelectHelper";
		elm.addEventListener("onkeypress", SelectHelper.getNextKeyItem);
		elm.fireEvent("onkeypress", event);
		}
		});
		}

		function getItemKeyChar(option) {
		return option.text.charAt(0).toUpperCase();
		}

		this.getNextKeyItem = function() {
		var elm = event.srcElement;
		var index = elm.selectedIndex + 1;
		do {
		if (index == elm.length) index = 0;
		if (index == elm.selectedIndex) return false;
		} while (key2Char(event.keyCode) != getFirstCh(getItemKeyChar(elm.options[index++])));
		elm.selectedIndex = index - 1;
		return false;
		}
		};
		/*
		* a-z: 97 -> 122
		* A-Z: 65 -> 90
		* 0-9: 48 -> 57
		*/
		function key2Char(key) {
		var s = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if (key >= 97 && key <= 122) return s.charAt(key - 97);
		if (key >= 65 && key <= 90) return s.charAt(key - 65);
		if (key >= 48 && key <= 57) return "" + (key - 48);
		return null;
		}
		SelectHelper.init();


});
