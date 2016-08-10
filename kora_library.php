
<?php
require_once( realpath( dirname(__FILE__) . "/dbconfig.php" ) );

		global $wpdb;
	define('KORA_PLUGIN_RESTFUL_SUBPATH', 'api/restful.php');
    define('KORA_PLUGIN_PATHBASE', plugin_dir_url(__FILE__));

?>

<div class='wrap' id='library'>
	<h1>Library</h1>
	<input type='button' value='add new object(s)' class='blue-btn'>
	<h2>Your Library</h2>
    <?php
        $library= $wpdb->prefix . 'koralibrary';
        $query = "SELECT * FROM  $library";
        //Only displayed when library is empty
        if(empty($wpdb->get_results("SELECT * FROM  $library"))){
     ?>
            <p>Your library is empty. Add new Kora objects above in order for them to appear here!</p>
	        <p>Your library is where all the objects you’ve added, either individually or within a gallery will exist in a gridded format. Within your libary, you will be able to search for objects, view/edit individual object details and delete objects as necessary. A proper description of what the library is will appear here. This text will be gone when an object is added.</p>  
     <?php
        }
    ?>

	<input id='' type='search' name='' value='' placeholder='Search Library'/>
	<div class='kora-objs'>
    <?php 
     //   var_dump($wpdb->get_results("SELECT * FROM  $library"));
        foreach( $wpdb->get_results("SELECT * FROM  $library") as $key => $kora_obj) {
            $kid = $kora_obj -> KID;
            $display_item = explode(",", $kora_obj -> Display);
            $schemeid = explode("_", $display_item[0]);
            $schemeid = $schemeid[1];
            unset($display_item[0]);
            unset($display_item[1]);

			// connect to database
            $mysql_hostname = kordat_dbhostname;
            $mysql_user = kordat_dbhostuser;
            $mysql_database = kordat_dbselectname;
            $mysql_password = kordat_dbhostpass;
	        $bd = new mysqli($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);

            if ($bd->connect_error) {
                die("Connection failed: " . $bd->connect_error);
            }
		            $dbproj = get_option('kordat_dbproj');
                    $dbscheme = get_option('kordat_dbscheme');
                    $dbtoken = get_option('kordat_dbtoken');
                    
                    $query_sid_pid = "SELECT schemeid,pid FROM scheme WHERE schemeid = '".$schemeid."';";
                    $stmt = $bd->prepare($query_sid_pid) ;
                    $stmt->execute();
                    $stmt->bind_result($sids,$pids);
                        
                    while($stmt->fetch()){
                        $sid = $sids;
                        $pid = $pids;
                        if (in_array($sid, $dbscheme) && in_array($pid, $dbproj)){
                            $pos = array_search($pid, $dbproj);
                            $sid_pid_token = array('schemeid' => $sid, 'projectid' => $pid, 'token' => $dbtoken[$pos]);
                        }
                    }
           $stmt->close();             
           
			$user = kordat_dbuser;
			$pass = kordat_dbpass;
			if ($kid == "ALL") {
				$query = "KID,!=,''";
			} else {
				$query = "KID,=,".$kid;
			}
			$restful_url = get_option('kordat_dbapi') . KORA_PLUGIN_RESTFUL_SUBPATH;
			$fields = 'ALL';
			$display='json';
  
             if (!empty($sid_pid_token)) {
                  $url = $restful_url.'?request=GET&pid='.$sid_pid_token['projectid'].'&sid='.$sid_pid_token['schemeid'].'&token='.$sid_pid_token['token'].'&display='.urlencode($display).'&fields='.urlencode($fields).'&query='.urlencode($query);
             }
              //initialize post request to KORA API using curl
              $ch = curl_init($url);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
              curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);

              //capture results and display
              $obj_json = curl_exec($ch);
              //convert json string to php array
              $server_output = json_decode($obj_json, true);            
           /**/
           if ($kora_obj -> thumb) {
               $pic = $kora_obj -> thumb;
           } else if ($kora_obj -> url) {
               $pic = $kora_obj -> url;
           }
      
      //check image or video or audio
      $type = "";
     
      if ($kora_obj -> imagefield != 'default' && $kora_obj -> imagefield != '') {
          $type = 'imagefield';
         
      } else if ($kora_obj -> videofield != 'default' && $kora_obj -> videofield != '') {
          $type = 'videofield';
         
      } else if ($kora_obj -> audiofield != 'default' && $kora_obj -> audiofield != '') {
          $type = 'audiofield';
         
      } else {
          $type = 'null';
      
      }
      
      echo "<div class='kora-obj' id ='".$kora_obj -> KID."'>
                <div class='kora-obj-left'>";
               
                if ($type = "imagefield" && $server_output[$kora_obj -> KID][$kora_obj -> imagefield]) {
                    $thumb_src = get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid'].'/'.$server_output[$kora_obj -> KID][$kora_obj -> imagefield]['localName'];
                    echo "<img src='".$thumb_src."' alt='".$kora_obj -> KID."'>";
                } else if ($type = "videofield" && $server_output[$kora_obj -> KID][$kora_obj -> videofield]) {
                    $videoFile = get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid'].'/'.$server_output[$kora_obj -> KID][$kora_obj -> videofield]['localName'];
                    echo '<video width="142" height="110" controls><source src="'.$videoFile.'" type="video/mp4"></video>';
                                         
                    
                } else if ($type = "audiofield" && $server_output[$kora_obj -> KID][$kora_obj -> audiofield]){
                    $audioFile = get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid'].'/'.$server_output[$kora_obj -> KID][$kora_obj -> audiofield]['localName'];
                    echo '<video width="142" height="110" controls><source src="'.$audioFile.'" type="audio/mpeg"></video>';
                    
                } else {?>
                    <img src="<?php echo KORA_PLUGIN_PATHBASE.'images/placeholder_plugin.svg'?>" alt="<?php $kora_obj -> KID?>"> 
                <?php
                }
                    
                echo   "<input type='button' class = 'edit_detials' id = '".$kora_obj -> KID."' value='edit details'  alt = '".$sid_pid_token['schemeid']."'>
                    
                </div>
                <div class='kora-obj-right'>
                    <div class='kora-obj-close'>
                        <img src='../wp-content/plugins/kora/images/Close - Tiny.svg' class='closePic'>
                    </div>
                    <ul class='kora-obj-fields'>
                        <li><span>KID:</span> ".$kora_obj -> KID."</li>";
                        if (is_array($display_item)) {
                            foreach ($display_item as $key ) {
                                if ($key != "KID" ) {
                                    if (is_array($server_output[$kora_obj -> KID][$key])) {
                                        echo    "<li><span>".$key."</span>: ";
                                        foreach($server_output[$kora_obj -> KID][$key] as $k) {
                                           echo  $k." ";
                                        }
                                        echo "</li>";
                                    } else {
                                          echo  "<li><span>".$key."</span>: ".$server_output[$kora_obj -> KID][$key]."</li>";
                                    }
                                }
                            }
                        } else {
                             if ($display_item != "KID" ) {
                                    if (is_array($server_output[$kora_obj -> KID][$display_item])) {
                                        echo    "<li><span>".$display_item."</span>: ";
                                        foreach($server_output[$kora_obj -> KID][$display_item] as $k) {
                                           echo  $k." ";
                                        }
                                        echo "</li>";
                                    } else {
                                          echo  "<li><span>".$display_item."</span>: ".$server_output[$kora_obj -> KID][$display_item]."</li>";
                                    }                             
                             }
                        }

                     
                       
                   echo " </ul>
                </div>
            </div>";
        }
    ?>
    </div>

	
	<div id='remove-kora-objs'>
		<input type='button' />
	</div>
</div>

<!-- Modals -->


<div class="remodal" id="editObjectModal" data-remodal-id="editObjectModal">
	<button data-remodal-action="close" class="remodal-close"></button>

    <img id="backArrow" src="../wp-content/plugins/kora/images/Arrow%20-%20Left.svg" width="12" height="20" alt="Back Arrow" />
	<h2>Edit Details for Object Name</h2>
    <div class = "object_details">

    </div>
</div>



<?php
    $addNewObjUrl = admin_url('admin.php?page=Add_New_KORA_Object');
?>
<script src="<?php echo "../wp-content/plugins/kora/js/jquery.js";?>" type="text/javascript"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script src="<?php echo plugins_url('kora/remodal_v1.0.6/dist/remodal.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo plugins_url('kora/remodal_v1.0.6/dist/remodal.css'); ?>" type="text/css"/>
    <link rel="stylesheet" href="<?php echo plugins_url('kora/remodal_v1.0.6/dist/remodal-default-theme.css'); ?>" type="text/css"/>

<script>
    var addNewObjUrl = "<?php echo $addNewObjUrl;?>";
</script>
<script>
    var divs =[];
	var selectedCount = 0;
	var deleteKoraObjs = function() {
		if (selectedCount == 0) {
			$('#remove-kora-objs > input').val('');
			$('#remove-kora-objs').removeClass('slide-remove-objs');
		}
		else {
			console.log(selectedCount + ' object(s) selected // delete selected object(s)?')
			$('#remove-kora-objs > input').val(selectedCount + ' object(s) selected // delete selected object(s)?');
			$('#remove-kora-objs').addClass('slide-remove-objs');
		}
	}

    $('.blue-btn').click(function(){
        window.location = addNewObjUrl;
    });

	$('.kora-obj-fields').click(function() {
        var koraObj = $(this).parent().parent();
       // var koraObj = $(this);
        var objKID = koraObj.attr("id");
		koraObj.toggleClass('kora-obj-active');
		if(koraObj.hasClass('kora-obj-active')) {
            divs.push(objKID);
			selectedCount++;
		}
		else {
            for (var i =0; i < divs.length; i++){
		        if (divs[i] === objKID) {
		            divs.splice(i,1);
					break;
		        }
		    }
			selectedCount--;
		}
		deleteKoraObjs();
	});
    
    $('#remove-kora-objs').click(function(){
       //  var koraObj = $(this).parent().parent();
        
       // alert(divs); 
        if (confirm("Are you sure?")) {
			//var kid = jQuery(this).closest('.lib_obj').find('img').attr('alt');
			//var kid = jQuery(this).closest('.lib_obj').attr('id').substring(1);
			//alert(title);
			var library = 'koralibrary';
			//add in prefix to library
			//jQuery(this).closest('.lib_obj').fadeOut();
            for(var i = 0; i < divs.length; i++) {
                jQuery.ajax({
                    type: "GET",
                    async: false,
                    url: plugin.url+"ajax/delete_library.php",
                    data: {"kid": divs[i], "library": library },
                    success: function(data){
                        //alert(data);
                        console.log(data);
                        parent.location.reload();
                    }
                });   
            }
		} else {
			parent.location.reload();
		}
    });
    
    
    $('.kora-obj-close').click(function(){
      //  alert("1111");
         var koraObj = $(this).parent().parent();
         var objKID = koraObj.attr("id");
        if (confirm("Are you sure?")) {
			var library = 'koralibrary';
			//add in prefix to library
	          
                jQuery.ajax({
                    type: "GET",
                    async: false,
                    url: plugin.url+"ajax/delete_library.php",
                    data: {"kid": objKID, "library": library },
                    success: function(data){
                        //alert(data);
                        console.log(data);
                        parent.location.reload();
                    }
                });   
           
		} else {
			parent.location.reload();
		}
    });


</script>
<script>
$(document).ready(function($) {
    var editObjectModal = $('#editObjectModal.remodal');
    $('.edit_detials').click(function(){
      
       var kid_open  = $(this).attr('id');
        var schemeID = $(this).attr('alt');
        $.ajax({
                  type: "GET",
                  async: false,
                  url: plugin.url  + "ajaxKORAobject_details_library.php",
                  data: {"kid" : kid_open, "schemeid": schemeID },
                  success: function(data){	
                    $('.object_details').html(data);  
                       
                  }
        });  
        editObjectModal.remodal().open();
    });
    // Add class to modal, for styling purposes, if open
    if( editObjectModal.remodal().getState() == 'opened' ||
        editObjectModal.remodal().getState() == 'opening' ) {
        editObjectModal.parent().addClass( 'largeModal' );
        
    }
    
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

});        
</script>