$(document).ready(function() {


    var divs = [];
  
       
    //// Apply Chosen plugin functionality ////
    $("select#newObjectScheme").chosen();
    $("select#newObjectFields1").chosen();
    $("select#newObjectFields2").chosen();
    $("select#objectsPerPage").chosen({
		disable_search: true
	});

    $("select#newObjectScheme, select#newObjectFields1, select#newObjectFields2, select#searchObjects, select#objectsPerPage").removeAttr('disabled');



	$('<a href="#"  id="kora-upload" class="button">Add New Kora Object</a>').insertAfter('.wp-media-buttons');   
	
   	$('#kora-upload').click(function(){
   		tb_show('Add New Kora Object',plugin.url+'/kora_upload.php?pid='+plugin.pid+
   		'&sid='+plugin.sid+'&token='+plugin.token+'&user='+plugin.user+'&pass='+plugin.pass+'&restful='+plugin.restful+'&url='+plugin.url+
   		'&height=200&width=400&TB_iframe=true');
 		
 		return false;
   	});
	

	$('<a href="#"  id="kora-gallery" class="button">Add Kora Gallery</a>').insertAfter('#kora-upload');
	
	$('#kora-gallery').click(function(){
         tb_show('Add Kora Gallery',plugin.url+'/postgallery.php?pid='+plugin.pid+
         '&sid='+plugin.sid+'&token='+plugin.token+'&user='+plugin.user+'&pass='+plugin.pass+'&restful='+plugin.restful+'&url='+plugin.url+
         '&height=200&width=400&TB_iframe=true');
      
      return false;
    });
	  

    $('<a href="#"  id="kora-library" class="button">Add Existing Kora Object</a>').insertAfter('#kora-upload');

    $("#kora-library").click(function(){
         tb_show('Add Existing Kora Object',plugin.url+'/postlibrary.php?pid='+plugin.pid+
         '&sid='+plugin.sid+'&token='+plugin.token+'&user='+plugin.user+'&pass='+plugin.pass+'&restful='+plugin.restful+'&url='+plugin.url+
         '&height=200&width=400&TB_iframe=true');
    });
	  
	
	//// Similar to deleteKoraObjs() on Library page ////

	var selectedCount = 0;

    // Function to update 'Add Objects' button
	var addKoraObjs = function() {
		if (selectedCount == 0) {
			$('#add-kora-objs > input').val( 'Add Object(s) to Library' );
			$('#add-kora-objs').removeClass('slide-remove-objs');
		}
		
		else {
			$('#add-kora-objs > input').val( selectedCount + ' selected // add object(s) to library' );
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

		}
		
		else {
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
                  url: url_plugin  + "ajaxKORAobject_details.php",
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
    
   $('#add-kora-objs').click(function() {
       
       var image_control = $("#image_control").text();
       var video_control = $("#video_control").text();
       var audio_control = $("#audio_control").text();
        var res = [];
      //When objects have been checked
      for (var i =0; i < divs.length; i++){
          var chk = divs[i][0];
          var controlfileds = divs[i];
          console.log(controlfileds);
          $.ajax({
              type: "GET",
              async: false,
              url: url_plugin+"ajax/insert_library.php",
              data: {"chk": chk, "schemeid" : schemeid, "image_control": image_control,"video_control": video_control,"audio_control": audio_control,"controlfileds" : controlfileds},
             
              success: function(data){
                 if (data == "false") {
                      res.push("false");
                  }
                  else{
                      res.push("true");   
                   
                  }
                    
              }
          });  
      }
      if (res.indexOf("false")!=-1 && res.indexOf("true")!=-1){
          alert("Some objects are already in the library");
           window.location = libraryUrl;
      }else if(res.indexOf("false")!=-1){
          alert("Objects are already in the library");
          window.location = libraryUrl;
      }else if(res.indexOf("true")!=-1){
          window.location = libraryUrl;
        
      }

	});
	
});
