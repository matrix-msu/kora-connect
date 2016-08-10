$(document).ready( function() {

    //// Apply Chosen plugin functionality ////
    $("select#koraProject").chosen();
    $("select#koraScheme").chosen();
    

                $('#projectBox').children().each(function() {
                    var childPlist = $(this).find('#koraProject');
                    if (childPlist.attr('id') == 'koraProject') {
                         if (childPlist.val() != 'default') {
                             var dataString =   childPlist.val();
                             //var childSlist = $(this).find('#koraScheme');
                            var nextSlist = childPlist.closest("select").nextAll("select[id]").first();
                             //alert(nextSlist.attr('id'));
                             $.ajax({
                                type: "GET",
                                async: false,
                                url: url_plugin  + "ajaxConnectSID.php",
                                data: {"pid" : dataString },
                                success: function(data){	
                                nextSlist.html(data);  
                                nextSlist.trigger("chosen:updated"); 
                                }
                            }); 
                            
                             
                         }
                    }

                });
             
    $('#lstSchemes').multiselect({
        includeSelectAllOption: true
    });
    

                   $('#projectBox #koraProject').change(function(){
                        var dataString =  $(this).val();
                       // var alt =  $(this).attr("alt");
                        //alert(dataString);
                        $.ajax({
                            type: "GET",
                            async: false,
                            url: url_plugin  + "ajaxConnectSID.php",
                            data: {"pid" : dataString },
                            success: function(data){	
                               $('#projectBox select#koraScheme').html(data);  
                               $('#projectBox select#koraScheme').trigger("chosen:updated");                      
                            }
                        });       
                   });


    var max_fields      = 10; //maximum input boxes allowed
    //var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID
    var edit_button     = $(".edit_project_scheme");


    $(edit_button).click(function(){
        $(".project_id").removeAttr("readonly");
        $(".tokens").removeAttr("readonly");
    });

    $(".edit_server").click(function(){
        $('#kordat_dbpass_edit').removeAttr("readonly");
        $('#kordat_dbuser_edit').removeAttr("readonly");
    });

    $(".edit_host").click(function(){
        $('#kordat_dbhostname_edit').removeAttr("readonly");
        $('#kordat_dbhostuser_edit').removeAttr("readonly");
        $('#kordat_dbselectname_edit').removeAttr("readonly");
        $('#kordat_dbhostpass_edit').removeAttr("readonly");

    });


    var x = 1; //initial text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
        }
    });

  

    $('.delete_project_scheme').click(function(){
        $(this).parent('p').remove();
    });


    //// Every second, check if 'Project...' section needs to be disabled ////
    setInterval( function() {
        // Disable section, if no URL
        if( $('input#koraUrl').val() == "" ) {
            $('input#koraToken, input#addNewButton').attr(
                'disabled', 'disabled'
            );
			
			$('div.chosen-container').addClass( 'chosen-disabled' );

            $('div#questions, h2#ptsHeader, span, div.chosen-container, input#koraToken, ' +
              'img.deleteBox, input#addNewButton').css(
                'opacity', 0.2
            );

            $('span, img.deleteBox, input#addNewButton').css(
                'cursor', 'default'
            );
      }

        // Enable section, if URL
        else {
            $('input#koraToken, input#addNewButton').removeAttr(
                'disabled'
            );
			
			$('div.chosen-container').removeClass( 'chosen-disabled' );

            $('div#questions, h2#ptsHeader, span, div.chosen-container, input#koraToken, ' +
              'img.deleteBox, input#addNewButton').css(
                'opacity', 1
            );

            $('span, img.deleteBox, input#addNewButton').css(
                'cursor', 'pointer'
            );
        }

        // Functionality for Delete Boxes
        $('img.deleteBox').click( function(event) {
            event.stopImmediatePropagation();
            var deleteBox = $(this);
            var pid=deleteBox.attr("pid");
            // Only delete, if section is enabled
            if( $('input#koraUrl').val() != "" ) {

                // Open modal
                $('#deleteModal').remodal().open();
                
                console.log($(this).parent().next(".buttonCont"));
                
                $(this).parent().next(".buttonCont").append("<input id='updateConnection' type='submit' value='Update Connection' onchange='' />");

                // Modal remove button
                $('#deleteModal button.remove').click( function(e) {
                     event.stopImmediatePropagation();
                     $.ajax({
                         type: "GET",
                         data: {"pid" : pid },
                        url: url_plugin  + "ajax/removeProjectGallLib.php",
                        success: function(data){

                            $("#updateConnection").trigger( "click" );
                        }
                       
                    });
                    deleteBox.parent().remove();
                   $('#deleteModal').remodal().close();

                });

                // Modal cancel button
                $('#deleteModal button.cancel').click( function() {
                    $('#deleteModal').remodal().close();
                });
            }
        });

        // Adjust height of Delete Boxes to match Project Box
        $('img.deleteBox').height( function(){
		//	return $(this).parent().height() - 1.8;
        });
    }, 1000);

    var addMore = 0;

 $('input#addNewButton').click( function() {
        addMore = addMore + 1;
        var newBox = "newProjectBox"  + addMore;
        $('div#appendNewProjects').append(
            "<div class = 'newProjectBox' id='"+newBox+"'>" +
                "<select class = 'koraProject' id='koraProject' name='kordat_dbproj[]' data-placeholder='Project: Search and select a project' >" +
                    "<option value='default'></option>" +
                "</select>" +
				
				"<img class='deleteBox' src='../wp-content/plugins/kora/images/Close.svg' " +
                "width='19' height='18' alt='Delete Button' />" +

                "<input class = 'koraToken' id='koraToken' type='text' name='kordat_dbtoken[]' value='' " +
                "placeholder='Token: Enter Token associated with the project' />" +

                "<select class = 'koraScheme' id='koraScheme' name='kordat_dbscheme[]' data-placeholder='Scheme(s): Search and select by scheme name or ID' multiple >" +
                    "<option value='default'></option>" +
                    "<option value='Select All'>SELECT ALL</option>" +
                "</select>" +
            "</div><div class='buttonCont'><input id='updateConnection' type='submit' value='Update Connection' onchange='' /></div>"
        );
       var newPlist = "div#"+newBox+" select#koraProject";
       var newSlist = "div#"+newBox+" select#koraScheme";
        // Apply Chosen plugin functionality to new select boxes
        $(newPlist).chosen();
        $(newSlist).chosen();
        
        var ht = $("#"+newBox).height() - 1.8;
		console.log(ht);
        $("#"+newBox+" img.deleteBox").height(ht);
			   
        $.ajax({
             type: "GET",
             async: false,
             url: url_plugin  + "ajaxConnectPID.php",
             success: function(data){	
              $(newPlist).html(data);  
              $(newPlist).trigger("chosen:updated");               
           }
        });  
    });
    
    
			//$("img.deleteBox").height(ht);
    
    $(document).one('change', 'select.koraScheme', function(e) { 
        $(this).parent().next(".buttonCont").append("<input id='updateConnection' type='submit' value='Update Connection' onchange='' />");
    });
              
    $(document).on('change', '.newProjectBox select#koraProject', function(e) { 
        
      
            var dataString =  $(this).val();
            var nextSlist =  $(this).closest("select").nextAll("select[id]").first();
           $.ajax({
                     type: "GET",
                     async: false,
                     url: url_plugin  + "ajaxConnectSID.php",
                     data: {"pid" : dataString },
                     success: function(data){	
                               nextSlist.html(data);  
                               nextSlist.trigger("chosen:updated");      
                
                     }
               });  
    });
    

	
    //// Question Modals - Project, Token, Scheme ////
    $('span#projectQuestion').click( function() {
        $('#projectModal').remodal().open();
    });

    $('span#tokenQuestion').click( function() {
        $('#tokenModal').remodal().open();
    });

    $('span#schemeQuestion').click( function() {
        $('#schemeModal').remodal().open();
    });

});


