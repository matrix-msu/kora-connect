<div class="wrap" id="galleries">
    <h1>Galleries</h1>

    <button class = "addNewGallery" id="addNewGallery">Add New Gallery</button>



    <h2>Your Galleries</h2>
<?php
	require_once( realpath( dirname(__FILE__) . "/../../../wp-includes/wp-db.php" ) );
	require_once( realpath( dirname(__FILE__) . "/../../../wp-blog-header.php"));
    require_once( realpath( dirname(__FILE__) . "/dbconfig.php" ) );
    define('KORA_PLUGIN_RESTFUL_SUBPATH', 'api/restful.php');
    define('KORA_PLUGIN_PATHBASE', plugin_dir_url(__FILE__));
    //need to interact with script page
    global $wpdb;
    $galleries = $wpdb->prefix . "koragallery";
    if(empty($wpdb->get_results("SELECT * FROM $galleries"))){
    ?>
	
	<p>
		You have no galleries. Add them via the "Add New Gallery" button above in order for 
		them to appear here!
	</p>
	
	<p>
		Galleries are sequences of objects grouped together due to content similarities. A 
		proper description of what galleries are will appear here. This text will be gone 
		when a gallery is made.
	</p>


    <?php
    }
	
	//if galleries exist, display
	else {
    $gal_num = 0;
    echo "<div class='gal_accordion'>";

    foreach( $wpdb->get_results("SELECT * FROM $galleries") as $key => $row){
        $id = $row->id;
        $gallery_n = $wpdb->prefix .$row->title;
      //gallery name without space to search the table
       $gallery_nam=str_replace(" ", "_", $gallery_n);
        $gallery_name = str_replace("'","Char_39__", html_entity_decode($gallery_nam, ENT_QUOTES)); 
        $gallery_name_only = $row->title;
        $gallery_desc = $row->description;
        echo "<div class='gal_gallery' >";
        echo "<div class='gal_title'>";
        echo "<p class='galleryTitle' alt = '".$row->title."'>".$row->title."&nbsp;"."</p>";
        echo "<p class='objectNum'>"." ".count($wpdb->get_results("SELECT * FROM $gallery_name"))." Objects </p>";
        echo "<p class='addToGallery' id = '$id'><a>ADD TO / EDIT GALLERY</a></p>";
        echo "<span class='close' id='$gallery_name' alt = '$id'><img src='../wp-content/plugins/kora/images/Close.svg'></span>";
        echo "</div>";

        echo "<div class='gal_body'>";
        echo "<div class='gal_koraInfo'>";
        echo "<p>".$gallery_desc."</p>";
        echo "</div>";


        echo "<div class='kora-objs'>";
        foreach( $wpdb->get_results("SELECT * FROM $gallery_name") as $key => $row){
            $kid = $row -> KID;
            $schemeid = $row -> schemeid;
            $display_item = explode(",", $row -> display);
            unset($display_item[0]);
            unset($display_item[1]);
            $display_item = array_filter($display_item);
     
			// connect to database
            $mysql_hostname = kordat_dbhostname;
           // var_dump($mysql_hostname);
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
                    //$sid_pid_token = array();
                        
                    while($stmt->fetch()){
                        
                        $sid = $sids;
                        $pid = $pids;
                        //var_dump($sid." ".$pid."<br>");
                        if (in_array($sid, $dbscheme) && in_array($pid, $dbproj)){
                            $pos = array_search($pid, $dbproj);
                            $sid_pid_token = array('schemeid' => $sid, 'projectid' => $pid, 'token' => $dbtoken[$pos]);
          
                        }
                    }
           $stmt->close();             
           
			$user = kordat_dbuser;
			$pass = kordat_dbpass;
			if ($kid == "ALL") {
				$query = "Display,=,"."True";
			} else {
				$query = "KID,eq,".$kid;
			}
			$restful_url = get_option('kordat_dbapi') . KORA_PLUGIN_RESTFUL_SUBPATH;
			$fields = 'ALL';
			$display='json';

	
             if (!empty($sid_pid_token)) {
                  $url = $restful_url.'?request=GET&pid='.$sid_pid_token['projectid'].'&sid='.$sid_pid_token['schemeid'].'&token='.$sid_pid_token['token'].'&display='.urlencode($display).'&fields='.urlencode($fields).'&query='.urlencode($query);
             }
             //var_dump($url); 
              //initialize post request to KORA API using curl
              $ch = curl_init($url);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
              curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);

              //capture results and display
              $obj_json = curl_exec($ch);
              //convert json string to php array
              $server_output = json_decode($obj_json, true);  

      //check image or video or audio
      $type = "";
     
      if ($row -> imagefield != 'default' && $row -> imagefield != '') {
          $type = 'imagefield';
         
      } else if ($row -> videofield != 'default' && $row -> videofield != '') {
          $type = 'videofield';
         
      } else if ($row -> audiofield != 'default' && $row -> audiofield != '') {
          $type = 'audiofield';
         
      } else {
          $type = 'null';
      
      }
    
            echo "<div class='kora-obj'>";
		        echo "<div class='kora-obj-left' id = ".$row->KID." >";
            //image/video/audio
               if ($type == "imagefield" && $server_output[$row -> KID][$row -> imagefield]) {
                    $thumb_src = get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid'].'/'.$server_output[$row -> KID][$row -> imagefield]['localName'];
                    echo "<img src='".$thumb_src."' alt='".$row -> KID."'>";
                } else if ($type == "videofield") {
                    $videoFile = get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid'].'/'.$server_output[$row -> KID][$row -> videofield]['localName'];
                    echo '<video width="142" height="110" controls><source src="'.$videoFile.'" type="video/mp4"></video>';
                    
                    
                } else if ($type == "audiofield"){
                  $videoFile = get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid'].'/'.$server_output[$row -> KID][$row -> videofield]['localName'];
                    echo '<video width="142" height="110" controls><source src="'.$videoFile.'" type="audio/mpeg"></video>';
                    
                    
                } else {?>
                     <img src="<?php echo KORA_PLUGIN_PATHBASE.'images/placeholder_plugin.svg'?>" alt="<?php $kora_obj -> KID?>"> 
             <?php
                }
                    echo   "<input type='button' class = 'edit_detials' id = '".$row -> KID."' value='edit details' gal_name='".$gallery_name."' alt = '".$sid_pid_token['schemeid']."'>"."<span class = 'gallery_name_".$row -> KID."' style = 'display:none'>".$gallery_name_only."</span>";

            echo "</div>";

           echo "<div class='kora-obj-right'>";
               echo  "<div class='kora-obj-close' id = ".$row->KID." alt = ".$gallery_name.">
                        <img src='../wp-content/plugins/kora/images/Close - Tiny.svg' class='closePic'>
                    </div>";
                 echo    "<ul class='kora-obj-fields'>";
                    echo "<li><span>KID:</span> ".$row -> KID."</li>";
                      if (is_array($display_item)) {
                            foreach ($display_item as $key ) {
                                if ($key != "KID" ) {
                                    if (is_array($server_output[$row -> KID][$key])) {
                                        echo    "<li><span>".$key."</span>: ";
                            
                                        foreach($server_output[$row -> KID][$key] as $k) {
                                           echo  $k." ";
                                        }
                                        echo "</li>";
                                    } else {
                                          echo  "<li><span>".$key."</span>: ".$server_output[$row -> KID][$key]."</li>";
                                    }
                                }
                            }
                        } else {
                             if ($display_item != "KID" ) {
                                    if (is_array($server_output[$row -> KID][$display_item])) {
                                        echo    "<li><span>".$display_item."</span>: ";
                                        foreach($server_output[$row -> KID][$display_item] as $k) {
                                           echo  $k." ";
                                        }
                                        echo "</li>";
                                    } else {
                                          echo  "<li><span>".$display_item."</span>: ".$server_output[$row -> KID][$display_item]."</li>";
                                    }                             
                             }
                        }
     
                            

            echo " </ul></div></div>";
        }
        echo "</div>";
        echo "</div>";
        echo "</div>";
        $gal_num ++;
    }
    echo "</div>";
	}

    ?>
</div>

<div class="remodal" id="editObjectModal" data-remodal-id="editObjectModal">
	<button data-remodal-action="close" class="remodal-close"></button>

    <img id="backArrow" src="../wp-content/plugins/kora/images/Arrow%20-%20Left.svg" width="12" height="20" alt="Back Arrow" />
	<h2>Edit Details for Object Name</h2>
    <div class = "object_details">

    </div>
</div>

<link rel="stylesheet" href= "<?php echo "../wp-content/plugins/kora/colorbox.css";?>" type="text/css">

<script src="<?php echo "../wp-content/plugins/kora/js/jquery.colorbox-min.js";?>" type="text/javascript"></script>
<script src="<?php echo "../wp-content/plugins/kora/js/jquery.js";?>" type="text/javascript"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="<?php echo plugins_url('kora/remodal_v1.0.6/dist/remodal.min.js'); ?>"></script>
<link rel="stylesheet" href="<?php echo plugins_url('kora/remodal_v1.0.6/dist/remodal.css'); ?>" type="text/css"/>
<link rel="stylesheet" href="<?php echo plugins_url('kora/remodal_v1.0.6/dist/remodal-default-theme.css'); ?>" type="text/css"/>


<script> var url_plugin = '<?php echo KORA_PLUGIN_PATHBASE;?>'; </script>
<script>
    $('.gal_accordion .gal_title').click(function(e) {

        //Close all <div> but the <div> right after the clicked <a>
        $(e.target).parent().siblings().children('.gal_body').slideUp('fast');

        //Toggle open/close on the <div> after the <h3>, opening it if not open.
        $(e.target).next('.gal_body').slideToggle('fast');
    });
    
 jQuery(document).ready(function($){
     
  
    var editObjectModal = $('#editObjectModal.remodal');
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
    
     $('.edit_detials').click(function( ){
        $("#editObjectModal").show();
        var kid_open  = $(this).attr('id');
        var schemeID = $(this).attr('alt');
        var gallery_name_class = "gallery_name_" + kid_open;
        var gallery_name = $(this).attr('gal_name');
       
        $.ajax({
                  type: "GET",
                  async: false,
                  url: plugin.url  + "ajaxKORAobject_details_gallery.php",
                  data: {"kid" : kid_open, "schemeid": schemeID, "gallery_name": gallery_name },
                  success: function(data){	
                    $('.object_details').html(data);  
                       
                  }
        });  
          editObjectModal.remodal().open();
    });
    


       $('.close').click(function(){
           var gallery_name = $(this).attr('id');
           var id = $(this).attr('alt');
                        jQuery.ajax({
                            type: "GET",
                            async: false,
                            url: plugin.url+"ajax/delete_gallery.php",
                            data: {"gallery": gallery_name, 'id': id},
                            success: function(data){
                                location.reload(true);
                            }
                        });
         
        });

         $('.kora-obj-close').click(function(){
               var kid = $(this).attr('id');
               var gallery_name = $(this).attr('alt');
               if (confirm("Are you sure?")) {
                    jQuery.ajax({
                        type: "GET",
                        async: false,
                        url: plugin.url+"ajax/delete_gallery.php",
                        data: {"kid": kid, "gallery_name": gallery_name },
                        success: function(data){
                            location.reload(true);
                        }
                    });
                } else {
                    location.reload(true);
                }
        }); 
        
        
        
        jQuery('.gal_image').on('click', function(){
        $('.popupImage').colorbox({ opacity:0.5 , rel:'group1', Width:'800px', Height:'800px' });

        $('.gal_accordion').children().first('.gal_body').slideToggle('fast');

        });

    });
</script>

