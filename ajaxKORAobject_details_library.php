<?php
    require_once( realpath( dirname(__FILE__) . "/../../../wp-includes/wp-db.php" ) );
    require_once( realpath( dirname(__FILE__) . "/../../../wp-blog-header.php"));
    require_once(realpath(dirname(__FILE__) . "/dbconfig.php"));

    global $wpdb;
    if (isset($_GET['kid']) && isset($_GET['schemeid'])) {
        //get image/video/audio and display item
        $library= $wpdb->prefix . 'koralibrary';
       // var_dump($_GET['kid']);
        $query = "SELECT * FROM  $library WHERE KID = '".$_GET['kid']."'";
        if(!empty($wpdb->get_results($query))) {
           foreach( $wpdb->get_results($query) as $key => $kora_obj) {
                  $display_item = explode(",", $kora_obj -> Display);
                  $schemeid = explode("_", $display_item[0]);
                  $schemeid = $schemeid[1];
                  
                  unset($display_item[0]);
                  unset($display_item[1]);
               
           }
        }
        
            /* connect to database*/
    $mysql_hostname = kordat_dbhostname;
    $mysql_user = kordat_dbhostuser;
    $mysql_database = kordat_dbselectname;
    $mysql_password = kordat_dbhostpass;

    @$bd = new mysqli($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);
    if ($bd->connect_error) {
        echo "<p class='error'><strong>";
        echo "Please edit config.php.dist (Kora Host Database Settings) first! Save as config.php!";
        echo "</strong></p>";
    } else {
                   //get search information
                    $schemeid = $_GET['schemeid'];
                    $target_kid = $_GET['kid'];
                   // echo $target_kid." ".$schemeid;
                    $dbproj = get_option('kordat_dbproj');
                    $dbscheme = get_option('kordat_dbscheme');
                    $dbtoken = get_option('kordat_dbtoken');
                   // var_dump($controls);
                    
                    $query_sid_pid = "SELECT schemeid,pid FROM scheme WHERE schemeid = '".$schemeid."';";
                    $stmt = $bd->prepare($query_sid_pid) ;
                    $stmt->execute();
                    $stmt->bind_result($sids,$pids);
                    //$sid_pid_token = array();
                        
                    while($stmt->fetch()){
                        $sid = $sids;
                        $pid = $pids;
                        if (in_array($sid, $dbscheme) && in_array($pid, $dbproj)){
                            $pos = array_search($pid, $dbproj);
                            $sid_pid_token = array('schemeid' => $sid, 'projectid' => $pid, 'token' => $dbtoken[$pos]);
                        }
                    }
                    $stmt->close();
                    
                    $restful = get_option('kordat_dbapi');
			        $restful_url =$restful . KORA_PLUGIN_RESTFUL_SUBPATH;
			
			                   
			        $i = 0;
                     $fields = 'ALL';
                  
                    $display = 'json';
                   // $display='plugin';
                     $query = "KID,=,".$target_kid;
                    if (!empty($sid_pid_token)) {
                            $url = $restful_url.'?request=GET&pid='.$sid_pid_token['projectid'].'&sid='.$sid_pid_token['schemeid'].'&token='.$sid_pid_token['token'].'&display='.urlencode($display).'&fields='.urlencode($fields).'&query='.urlencode($query);
                    }
               
                    //initialize post request to KORA API using curl
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);

                    //capture results and display
                    $obj_json = curl_exec($ch);
                    //echo $obj_json;
                    //convert json string to php array
                    $server_output = json_decode($obj_json, true);
                   $control_fields = array();
                   
                    foreach($server_output as $record) {
                        
                         if ($record[$kora_obj -> imagefield]) {
                             $src = get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid'].'/'.$record[$kora_obj -> imagefield]['localName'];

                         } else if ($record[$kora_obj -> videofield]) {
                             $src = get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid'].'/'.$record[$kora_obj -> videofield]['localName'];

                         } else if ($record[$kora_obj -> audiofield]) {
                              $src = get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid'].'/'.$record[$kora_obj -> audiofield]['localName'];
               
                         } else {
                             $src = "http://img.photobucket.com/albums/v516/MizGrace/babyhedgehoginbubblebath.jpg";
                         }
                         
                                   $description = $record['Description'];
                                   $control_name = array_keys($record);
                                   
                                   foreach($control_name as $control) {
                                       if ($control != 'Image' && $control != 'Thumbnail' && $control != 'Audio File' && $control != 'Video File' && $control != 'Description') {
                                           if (!empty($record[$control])) {
                                              //array_push($control_fields, $control_fields[$control] = $record[$control]);
                                                $control_fields[$control] =  $record[$control];
                                           }
                                           
                                       }
                                      
                                   }
                    }
                    
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
                 
               if ($type == "imagefield") {
                   echo '<img id="objectImage" src="'.$src.'" width="667" height="495" alt="'.$target_kid.'">';
                } else if ($type == "videofield") {
                   
                    echo '<video width="667" height="495" controls><source src="'.$src.'" type="video/mp4"></video>';
                   
                } else if ($type == "audiofield"){
                    echo '<video width="667" height="495" controls><source src="'.$src.'" type="audio/mpeg"></video>';
                   
                } else {
                    echo "<img src='http://img.photobucket.com/albums/v516/MizGrace/babyhedgehoginbubblebath.jpg' alt='".$kora_obj -> KID."'>";
                }
              
                   // echo  '<form id="editObjectForm" method="post" action="">';
                    echo '  <div class= "control_show" >
                            <input type="checkbox" class = "radio" id="controlYes" name = "control_show" value="yes" checked />
                            <label for="controlYes">Control will be displayed</label>

                            <input type="checkbox" class = "radio" id="controlNo" name = "control_show" value="no" />
                            <label for="controlNo">Control will not be displayed</label>
                            <br/></div>';
                   echo     '<div class = "control_fields_checkbox">
                              <input type="checkbox" id="controlDescription" value="Description" />
                              <label for="controlDescription">Description</label>
                              <p for="controlDescription">'.$description.'</p>';      
                    foreach($control_fields as $key => $value) {
                        if (is_array($value)) {
                           $str = "";
                           foreach ($value as $val) {
                               $str.= $val." ";
                           }
                            echo '<input type="checkbox" id="'.$key.'" value="'.$str.'"'; 
                            if (in_array($key, $display_item)) {
                                echo "checked";
                            }
                            echo '/>
                                  <label for="control6">'.$key.'</label>
                                  <p for="control6">'.$str.'</p>';
                        } else {
                            echo '<input type="checkbox" id="'.$key.'" value="'.$value.'"';  
                            if (in_array($key, $display_item)) {
                                echo "checked";
                            }
                            echo '/>
                                  <label for="control6">'.$key.'</label>
                                  <p for="control6">'.$value.'</p>';
                        } 
  
                    }
                    echo "</div>";
                    echo ' <input class = "edit_details_submit" type="submit" value="Update Object Details Into KORA Library" alt = "'.$target_kid.'"/>';
                  //  echo '</form>';
                   //var_dump($control_fields);

        }
    } else {
        echo "<h1>No recrod detials!</h1>";
    }
?>

<script> var schemeid = '<?php echo $schemeid;?>'; </script>
 <script> var url_plugin = '<?php echo KORA_PLUGIN_PATHBASE;?>'; </script>
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
 <script>
   //  alert("1111");
              $("div.control_show input:checkbox").on('click', function() {
            // in the handler, 'this' refers to the box clicked on
            var $box = $(this);
            if ($box.is(":checked")) {
                // the name of the box is retrieved using the .attr() method
                // as it is assumed and expected to be immutable
                var group = "input:checkbox[name='" + $box.attr("name") + "']";
                // the checked state of the group/box on the other hand will change
                // and the current value is retrieved using .prop() method
                $(group).prop("checked", false);
                $box.prop("checked", true);
            } else {
                $box.prop("checked", false);
            }
        });
        $('.edit_details_submit').click(function(){
           var editObjectModal = $('.edit_details_submit').parent().parent();
          // alert(editObjectModal);
           // alert("11111"); 
            var chk =  $(this).attr('alt');
           // alert(kid);
            var controlfileds = "schemeid_"+ schemeid + "," + chk + ",";
            //var checked_fileds_value = [];
         /*   $("div.control_fields_checkbox input:checkbox:checked").each(function(){
                controlfileds.push($(this).attr('id'));
               // checked_fileds_value.push($(this).val());
               // alert($(this).val());
            });*/
           //alert(checked_fileds_name);
           
        if( document.getElementById("controlYes").checked) {
              //alert("yes");
            $("div.control_fields_checkbox input:checkbox:checked").each(function(){
                controlfileds += $(this).attr('id') + ",";
               // controlfileds.push($(this).attr('id'));
               // checked_fileds_value.push($(this).val());
               // alert($(this).val());
            });
          } else {
             // alert("no");
              $("div.control_fields_checkbox input:checkbox").each(function(){
                  if (!$(this).is(':checked'))  {
                      controlfileds += $(this).attr('id') + ",";
                        //controlfileds.push($(this).attr('id'));
                        //checked_fileds_value.push($(this).val());
                  }
            });
          }
                $.ajax({
                    type: "GET",
                    async: false,
                    url: url_plugin+"ajax/update_library.php",
                    data: {"chk": chk, "schemeid" : schemeid, "controlfileds" : controlfileds},
                   
                    success: function(data){
                       // alert(chk);
                      //  alert(controlfileds);
                        //alert(schemeid);      
                    
                       // parent.location.reload();
                        if(data=='false'){
                              //  alert("Not found!");
                                // editObjectModal.remodal().close();
                                location.reload();

                        } else if (data == false) {
                            //alert("Not found!");
                            
                           // editObjectModal.remodal().close();
                             //$('#editObjectModal.remodal').remodal().close();
                           location.reload();
                        }
                        else{
                               
                             //   alert(data);
                                alert("Object has been updated in the library");
                                
                                // alert(data);
                                 location.reload();
                                //var obj = "<div class=\"lib_obj\"><span class='close_lib'>&times;</span><div class='lib_image'><a class = 'popupImage' href = '+data.url+'><img src="+data.url+" alt="+data.KID+"></a></div><div class='lib_title'>"+data.title+"</div></div>";
                                //$("#wpbody-content").append(obj);
                               // parent.location.reload();
                             // window.location = libraryUrl;
                        }
                          //  $('.koraobj_container').hide();
				
                    }
                });   
           // $('#editObjectModal.remodal').remodal().close();
             //parent.location.reload();
         });
   /*  jQuery(document).ready(function($){
            alert("1111");
         $("div.control_show input:checkbox").on('click', function() {
            // in the handler, 'this' refers to the box clicked on
            var $box = $(this);
            if ($box.is(":checked")) {
                // the name of the box is retrieved using the .attr() method
                // as it is assumed and expected to be immutable
                var group = "input:checkbox[name='" + $box.attr("name") + "']";
                // the checked state of the group/box on the other hand will change
                // and the current value is retrieved using .prop() method
                $(group).prop("checked", false);
                $box.prop("checked", true);
            } else {
                $box.prop("checked", false);
            }
        });

     });*/
</script>
