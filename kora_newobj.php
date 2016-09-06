<?php
    //where is close icon? or create?
	require_once( realpath( dirname(__FILE__) . "/../../../wp-includes/wp-db.php" ) );
	require_once( realpath( dirname(__FILE__) . "/../../../wp-blog-header.php"));
	require_once( realpath( dirname(__FILE__) . "/dbconfig.php" ) );

	global $wpdb;
	define('KORA_PLUGIN_RESTFUL_SUBPATH', 'api/restful.php');
    define('KORA_PLUGIN_PATHBASE', plugin_dir_url(__FILE__));
?>
 <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

    <script src="<?php echo plugins_url('kora/remodal_v1.0.6/dist/remodal.min.js'); ?>"></script>
    <script src="<?php echo plugins_url('kora/chosen_v1.4.2/chosen.jquery.min.js'); ?>"></script>
    <script src="<?php echo plugins_url('kora/chosen_v1.4.2/chosen.proto.min.js'); ?>"></script>
    <script> var url_plugin = '<?php echo KORA_PLUGIN_PATHBASE;?>'; 
             var schemeid = "<?php if (isset($_POST['kid'])) { echo $_POST['kid']; } ?>";
    </script>

    <?php $libraryUrl = admin_url('admin.php?page=Library'); ?>
    <script>
        var libraryUrl = "<?php echo $libraryUrl;?>";
     </script>
    
    <script src="<?php echo plugins_url('kora/js/addkoraobject.js'); ?>"></script>
    
    <link rel="stylesheet" href="<?php echo plugins_url('kora/remodal_v1.0.6/dist/remodal.css'); ?>" type="text/css"/>
    <link rel="stylesheet" href="<?php echo plugins_url('kora/remodal_v1.0.6/dist/remodal-default-theme.css'); ?>" type="text/css"/>
    <link rel="stylesheet" href="<?php echo plugins_url('kora/chosen_v1.4.2/chosen.css'); ?>" type="text/css"/>
<?php 
	 /* connect to database*/
	 $mysql_hostname = kordat_dbhostname;
     $mysql_user = kordat_dbhostuser;
     $mysql_database = kordat_dbselectname;
	 $mysql_password = kordat_dbhostpass;

	 $projector_id=get_option('kordat_dbproj');
       // var_dump( $projector_id);
   
	 $scheme_id=get_option('kordat_dbscheme');
	 $n=0;
	// var_dump($scheme_id);
	if (is_array($projector_id)) {
	 foreach($projector_id as $value){
		if($value !==""){
			//echo $value;
			$table[$n]="p".$value."Control";
			//echo $table[$n];
			$n=$n+1;
		}
	 }
	}
	
	// $table="p".$projector_id."Control";
     $bd = new mysqli($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);
     if ($bd->connect_error) {
      die("Connection failed: " . $bd->connect_error);
     }


    if (!$scheme_id) {
        die("Scheme not set in the connect tab");
    }
	 $i=0;
	 $query_control='';
	if (is_array($table)) {
	 foreach($table as $value){
		if($i!=0){
			$query_control.= " UNION ALL ";
		}
		
		$query_control .= "SELECT name,schemeid FROM $value WHERE showInResults = 1 AND schemeid in(";
       
		$lastScheme = end($scheme_id);
	  if (is_array($scheme_id)) {	
		foreach($scheme_id as $value){
		 if($value == $lastScheme){
			 $query_control.=$value;  
		 }
		 else{
			 $query_control.=$value.",";
		 }
		 
	   }
	  }
		$query_control.=")";
			$i=$i+1;
	 }
	}

	 $query_control.="  ORDER BY name ASC;";
     //create scheme information query
     $query_scheme .= "SELECT schemeid, sch.pid, sch.schemeName,sch.description,name FROM scheme as sch LEFT JOIN project as pj on pj.pid=sch.pid WHERE schemeid in(";
     if (is_array($scheme_id)) {	
         $lastScheme = end($scheme_id);
		foreach($scheme_id as $value){
		 if($value == $lastScheme){
             $query_scheme .=$value;
		 }
		 else{
             $query_scheme .= $value.",";
         }
	   }
	  }
     $query_scheme.=")";
     $query_scheme .= " ORDER BY schemeid ASC;";
 //Get all scheme infromation from db.
   $stmt = $bd->prepare($query_scheme) ;
   $stmt->execute();
   $stmt->bind_result($sid, $pid, $sname, $sdesc,$namepj);
   $schemeInfo = array();
   
   while($stmt->fetch()){  
        $schemeInfo[] = array('sid' => $sid,'project_name'=>$namepj, 'pid' => $pid, 'schemeName' => $sname, 'description' => $sdesc);                     
   }
   $stmt->close(); 
  

    ?>


   


    <div class='wrap' id="addNewObject">
        <h1>Add New Object(s)</h1>

        <p>Adding a new object(s) will add it to your library. Select a Scheme for the new object(s) to get started.</p>

        <form action="" method="post">
            <select id='newObjectScheme' name="kid" data-placeholder="Scheme: Search and select scheme for new object(s)">
                 <option value="default"></option>
				<?php 
                  	foreach ($schemeInfo as $value) {
                        $id_scheme = $value['sid'];
                        $name_scheme = $value['schemeName'];
                        $desc_scheme = $value['description'];
                        $pid_scheme = $value['pid'];
                        $pid_name = $value['project_name'];
                        echo '<option value="' . $id_scheme . '"' . ($_POST['kid'] == $id_scheme ? ' selected="selected"' : '') . '>' . $pid_scheme.'---'.$pid_name.'---'.$id_scheme.'---'.$name_scheme.'---'.$desc_scheme.'</option>';
                    }
                ?>
          </select>
            <select id='newObjectFields1' class = 'newObjectFields1' name = "controlsImage" data-placeholder="Select Image Field Name">
               <option value="default"></option>
               <?php if(isset($_POST['controlsImage'])){?>
               <option value="<?php echo $_POST['controlsImage']?>" selected><?php echo $_POST['controlsImage']?></option>
               <?php } ?>
           </select>
           <select id='newObjectFields1' class = 'newObjectFields1' name = "controlsVideo" data-placeholder="Select Video Field Name">
               <option value="default"></option>
                <?php if(isset($_POST['controlsVideo'])){?>
               <option value="<?php echo $_POST['controlsVideo']?>" selected><?php echo $_POST['controlsVideo']?></option>
               <?php } ?>
           </select>
           <select id='newObjectFields1' class = 'newObjectFields1' name = "controlsAudio" data-placeholder="Select Audio Field Name">
               <option value="default"></option>
                <?php if(isset($_POST['controlsAudio'])){?>
               <option value="<?php echo $_POST['controlsAudio']?>" selected><?php echo $_POST['controlsAudio']?></option>
               <?php } ?>
           </select>

            <select id='newObjectFields2' class = 'newObjectFields2' name = "controlsName[]" data-placeholder="Field(s): Search and select field(s) for new object(s)" multiple>
<!--                --><?php
//                $mysql_hostname = kordat_dbhostname;
//                $mysql_user = kordat_dbhostuser;
//                $mysql_database = kordat_dbselectname;
//                $mysql_password = kordat_dbhostpass;
//
//                //get selected scheme id
//                $schemeidSelected=get_option('kordat_dbscheme');
//                @$bd = new mysqli($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);
//                if ($bd->connect_error) {
//                    echo "<p class='error'><strong>";
//                            echo "Please edit config.php.dist (Kora Host Database Settings) first! Save as config.php!";
//                            echo "</strong></p>";
//                } else {
//                    // echo "<option>".$_GET['sid']."</option>";
//                    $query_scheme .= "SELECT schemeid, pid, schemeName, description FROM scheme WHERE schemeid in(".$id_scheme.")";
//                    //Get all scheme infromation from db.
//                    $stmt = $bd->prepare($query_scheme) ;
//                    $stmt->execute();
//                    $stmt->bind_result($sid, $pid, $sname, $sdesc);
//                    $schemeInfo = array();
//                    while($stmt->fetch()){
//                        $schemeInfo[] = array('sid' => $sid, 'pid' => $pid, 'schemeName' => $sname, 'description' => $sdesc);
//                    }
//                    //var_dump($schemeInfo);
//                    $stmt->close();
//
//
//
//                    // Get all scheme infromation from db.
//                    $target_table = 'p'.$schemeInfo[0]['pid'].'Control';
//                    $query_control = "SELECT name,schemeid FROM $target_table WHERE showInResults = 1 AND schemeid in(".$schemeInfo[0]['sid'].")";
//
//                    $stmt = $bd->prepare($query_control) ;
//                    $stmt->execute();
//                    $stmt->bind_result($controlName, $sid);
//                    echo '<option value="default"></option>';
//                    while($stmt->fetch()){
//                        echo '<option value = "'.$controlName.'">'.$controlName.'</option>';
//                }
//                //var_dump($schemeInfo);
//                $stmt->close();
//                } ?>
                <option value="default"></option>
                <?php if(isset($_POST['controlsName']))
                    foreach ( $_POST['controlsName'] as $control) {?>
                        <option value="<?php echo $control?>" selected><?php echo $control?></option>
                <?php } ?>

            </select>
            <?php 
            if(isset($_POST['keyword'])){ ?>
                <input type="search" id='searchObjects' name = 'keyword' value=<?php echo $_POST['keyword']?> placeholder="Search Objects" />
            <?php }else{?>
                  <input type="search" id='searchObjects' name = 'keyword' placeholder="Search Objects" />
           <?php } ?>
            <select id='objectsPerPage' name = 'objectsPerPage'>
                <option value="10" default <?php   if ($_POST['objectsPerPage'] == 10) { echo selected; } ?> >10 Objects Per Page</option>
                <option value="20" <?php   if ($_POST['objectsPerPage'] == 20) { echo selected; } ?> >20 Objects Per Page</option>
                <option value="30" <?php   if ($_POST['objectsPerPage'] == 30) { echo selected; } ?> >40 Objects Per Page</option>
                <option value="80" <?php   if ($_POST['objectsPerPage'] == 80) { echo selected; } ?> >80 Objects Per Page</option>
            </select>
            <button type="submit" name="k_search" id="lib_k_search" class='blue-btn' style = "height:54px; width:302px">Search</button>
            

        </form>
        
   

<?php 
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
              if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  ?>

     <div class='kora-objs' id = 'kora-objs'>   
                      
    <?php
                if (isset($_POST['kid']) && isset($_POST['controlsName']) && (isset($_POST['controlsImage']) || isset($_POST['controlsVideo']) || isset($_POST['controlsAudio']))) {
                    //get search information
                    $schemeid = $_POST['kid'];
                    $controls = $_POST['controlsName'];
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
                        //echo $sid." ".$pid."<br>";
                        if (in_array($sid, $dbscheme) && in_array($pid, $dbproj)){
                            $pos = array_search($pid, $dbproj);
                            $sid_pid_token = array('schemeid' => $sid, 'projectid' => $pid, 'token' => $dbtoken[$pos]);
                           // $val = $sid."-".$pid."-".$dbtoken[$pos];
                            //array_push($sid_pid_token,$val);
                        }
                    }
                    $stmt->close();
                    
                    //korasearch
                    
                    $restful = get_option('kordat_dbapi');
			        $restful_url =$restful . KORA_PLUGIN_RESTFUL_SUBPATH;
			       
                    //GET image/video/audio filed name 
                    
                    $image_control = $_POST['controlsImage'];
                    $video_control = $_POST['controlsVideo'];
                    $audio_control = $_POST['controlsAudio'];
                    
                               
			        $i = 0;
			   
                    $fields = 'ALL';
                    $display = 'json';
                    $query = "";
                    if (isset($_POST['keyword'])) {
                        $k = $_POST['keyword'];
                        
                       
                        if (count($controls) == 1) {
                                $query .= $controls[0].",LIKE,".$k;
                         } else {
                                for ($i = 0; $i < count($controls); $i++) {
                                        if ($i == 0) {
                                            $query .= "(".$controls[$i].",LIKE,".$k.")";
                                        } else {

                                            $query .= ",OR,"."(".$controls[$i].",LIKE,".$k.")";
                                        }
                                }
                         }
                        if (!empty($sid_pid_token)) {
                            $url = $restful_url.'?request=GET&pid='.$sid_pid_token['projectid'].'&sid='.$sid_pid_token['schemeid'].'&token='.$sid_pid_token['token'].'&display='.urlencode($display).'&fields='.urlencode($fields).'&query='.urlencode($query);
                        }
                    
                    } else {
			                    
                        if (!empty($sid_pid_token)) {
                             $url = $restful_url.'?request=GET&pid='.$sid_pid_token['projectid'].'&sid='.$sid_pid_token['schemeid'].'&token='.$sid_pid_token['token'].'&display='.urlencode($display).'&fields='.urlencode($fields);
                        }
                    }
                    //initialize post request to KORA API using curl
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);

                    //capture results and display
                    $obj_json = curl_exec($ch);
                    //convert json string to php array
                    $server_output = json_decode($obj_json, true);
                   
        if (is_array($server_output)) {
                     
               $total_obj = count($server_output);
              
               $objectsPerPage = $_POST['objectsPerPage'];
               $totalPages = ceil($total_obj / $objectsPerPage); 
            
               ?>
               <button id="selectAll" align="center" class = "blue-btn" style = "height:54px; width: 500px; margin-right: 50px">Select All <?Php echo $total_obj; ?> Objects</button>
                </br>
               <div id="pagination"  alt = "<?php echo $total_obj; ?>">
               
                </div>  
                    
                       
               <?php
               $media_type = "";
               echo " <span id = 'image_control' style = 'display:none'>".$image_control."</span>
                      <span id = 'video_control' style = 'display:none'>".$video_control."</span>
                      <span id = 'audio_control' style = 'display:none'>".$audio_control."</span>";
               
                     foreach($server_output as $record) {

                         if ($record[$image_control]) {
                             $thumb_src = get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid'].'/thumbs/'.$record[$image_control]['localName'];
                             $media_type = "image";
                         } else if ($record[$video_control]) {
                             $videoFile = mysql_escape_string(get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid']."/".$record['Video File']['localName']);
                             $media_type = "video";
                             
                         } else if ($record[$audio_control]) {
                             $audioFile = mysql_escape_string(get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid']."/".$record['Audio File']['localName']);
                             $media_type = "audio";
                         } else {
                             $thumb_src = KORA_PLUGIN_PATHBASE."images/placeholder_plugin.svg";
                             $media_type = "NULL";
                         }
                         
                         
                    
        ?>		
		
		<!-- Same as Library page -->
            <?php 
                echo "<div class='kora-obj' id = '".$record['kid']."'>
				                            <div class='kora-obj-left'>";
                                            if($media_type == "image") {
                                                echo "<img src='".$thumb_src."' alt='".$record['kid']."'>";
                                            } else if ($media_type == "video") {
                                                echo '<video width="142" height="110" controls><source src="'.$videoFile.'" type="video/mp4"></video>';
                                            } else if ($media_type == "audio") {
                                                echo '<video width="142" height="110" controls><source src="'.$audioFile.'" type="audio/mpeg"></video>';
                                        
                                            } else {
                                                echo "<img src='".$thumb_src."' alt='".$record['kid']."'>";
                                            }
  					                          
					                         echo  "<input type='button' class = 'edit_detials' id = '".$record['kid']."' value='edit details'  alt = '".$sid_pid_token['schemeid']."'>
				                            </div>
				                            <div class='kora-obj-right'>";
					                          
					                            echo '<div id = "edit_details_'.$record['kid'].'">';
                                                 echo "<ul class='kora-obj-fields'>";
						                           echo "<li><span>KID</span>: ".$record['kid']."</li>";
                                                     foreach($controls as $field) {
                                                         if (is_array($record[$field])) {
                                                              echo "<li><span>".$field."</span>: ".implode(" ",$record[$field])."</li>";
                                                         } else {
                                                             echo "<li><span>".$field."</span>: ".$record[$field]."</li>";
                                                         }
                                                     }
						                            echo '</ul>';
					                            echo "</div>
				                           </div>
			   </div>";  
                     }?>
                    <button id="prevPage" align="center" class = "prev" style = "height:54px; width: 300px; color:white; background-color: #57abce; border: 1px solid #57abce;margin-right: 50px">PREV</button>
               
                    <button id="nextPage" align="center" class = "next" style = "height:54px; width: 300px; color:white; background-color: #57abce; border: 1px solid #57abce">NEXT</button>
             
               
            <?php   } else {
                        echo "<h1>No records found!</h1>";
                   }
                              
                 } else {
                     echo "<h1>Choose scheme and controls first!</h1>";
                }
             ?>


               		<div id='add-kora-objs'>
			            <input type="submit" value='Add Object(s) to Library' />
		           </div>

    </div>	
<?Php 
              }
     }  
?>

		




<!-- Modals -->
<div class="remodal" id="deleteObjectModal" data-remodal-id="deleteObjectModal">
	<button data-remodal-action="close" class="remodal-close"></button>

	<h2>Remove this Object?</h2>
	<p>Doing so will remove this object from any gallery it is associated with.</p>

	<button class="remove">Remove</button>
	<button class="cancel">Cancel</button>
</div>


<div class="remodal" id="editObjectModal" data-remodal-id="editObjectModal">
	<button data-remodal-action="close" class="remodal-close"></button>

    <img id="backArrow" src="../wp-content/plugins/kora/images/Arrow%20-%20Left.svg" width="12" height="20" alt="Back Arrow" />
	<h2>Edit Details for Object Name</h2>
    <div class = "object_details">

    </div>
</div>

               		<!-- Similar to Library page -->

<?php 

?>

<script>
    var kid = "<?php echo $kid;?>";
    var pathbase = "<?php echo KORA_PLUGIN_PATHBASE;?>";
</script>


<script>
    jQuery(document).ready(function($){
    
       // check if scheme drop list is selected
      
        $('#newObjectScheme').change(function(){
            var schemeid = $(this).val();
            $.ajax({
             type: "GET",
             async: false,
             url: pathbase  + "ajaxControls.php",
             data: {"sid" : schemeid },
             success: function(data){   
                // alert(pathbase);
                 $('.newObjectFields1').html(data);
                 $('.newObjectFields1').trigger("chosen:updated");
                 $('.newObjectFields2').html(data);
                 $('.newObjectFields2').trigger("chosen:updated");

             }
   });      
              
        });
        $('#newObjectFields1_chosen').click (function(){
          if(schemeid!=''){
              $.ajax({
                 type: "GET",
                 async: false,
                 url: pathbase  + "ajaxControls.php",
                 data: {"sid" : schemeid },
                 success: function(data){   
                    // alert(pathbase);
                     $('.newObjectFields1').html(data);
                     $('.newObjectFields1').trigger("chosen:updated");
                    
                 }
   });

            }
        });
        var loaded = false;
        $('#newObjectFields2_chosen').click (function(){

            if(schemeid!='' && !loaded){
                $.ajax({
                    type: "GET",
                    async: false,
                    url: pathbase  + "ajaxControls.php",
                    data: {"sid" : schemeid,},
                    success: function(data){
                        // alert(pathbase);
                        $('.newObjectFields2').html(data);
                        $('.newObjectFields2').trigger("chosen:updated");
                        loaded = true;
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
      // if( start < 0 || start >= length ) start = 0;
       end = start + nb;        
       console.log(start);
       console.log(end);
       
       if( start == 0 ) list.hide().filter(':lt('+(end)+')').show();
       else list.hide().filter(':lt('+(end)+'):gt('+(start-1)+')').show();
    });
    });
 </script>
