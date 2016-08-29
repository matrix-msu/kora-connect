


<?php
    //where is close icon? or create?
	require_once( realpath( dirname(__FILE__) . "/../../../wp-includes/wp-db.php" ) );
	require_once( realpath( dirname(__FILE__) . "/../../../wp-blog-header.php"));
	require_once( realpath( dirname(__FILE__) . "/dbconfig.php" ) );

	global $wpdb;
	define('KORA_PLUGIN_RESTFUL_SUBPATH', 'api/restful.php');
  define('KORA_PLUGIN_PATHBASE', plugin_dir_url(__FILE__));?>

<link rel="stylesheet" type="text/css" href="kora.css">

<script> var url_plugin = '<?php echo KORA_PLUGIN_PATHBASE;?>'; 
        
</script>
<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

<script src="http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
<script src="http://cdn.rawgit.com/davidstutz/bootstrap-multiselect/master/dist/js/bootstrap-multiselect.js"></script>


<script src="<?php echo plugins_url('kora/remodal_v1.0.6/dist/remodal.min.js'); ?>"></script>
<script src="<?php echo plugins_url('kora/chosen_v1.4.2/chosen.jquery.min.js'); ?>"></script>
<script src="<?php echo plugins_url('kora/chosen_v1.4.2/chosen.proto.min.js'); ?>"></script>
<link rel="stylesheet" href="http://cdn.rawgit.com/davidstutz/bootstrap-multiselect/master/dist/css/bootstrap-multiselect.css" type="text/css"/>
<link rel="stylesheet" href="<?php echo plugins_url('kora/remodal_v1.0.6/dist/remodal.css'); ?>" type="text/css"/>
<link rel="stylesheet" href="<?php echo plugins_url('kora/remodal_v1.0.6/dist/remodal-default-theme.css'); ?>" type="text/css"/>

<link rel="stylesheet" href="<?php echo plugins_url('kora/chosen_v1.4.2/chosen.css?').time(); ?>" type="text/css"/>
<script>

var title='<?php echo $title_form;?>';
var desc='<?php echo $desc;?>';
var type='<?php echo $type;?>';
var schemeid = "<?php if (isset($_POST['id_scheme'])) { echo $_POST['id_scheme']; } ?>";
</script>

<?php
   /* connect to database*/
	 $mysql_hostname = kordat_dbhostname;
   $mysql_user = kordat_dbhostuser;
   $mysql_database = kordat_dbselectname;
	 $mysql_password = kordat_dbhostpass;
	 $projector_id=get_option('kordat_dbproj');

	 $scheme_id=get_option('kordat_dbscheme');
	 $n=0;
	if (is_array($projector_id)) {
	 foreach($projector_id as $value){
		if($value !==""){
			$table[$n]="p".$value."Control";
			$n=$n+1;
		}
	 }
	}

	$bd = new mysqli($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);
  if ($bd->connect_error) {
    die("Connection failed: " . $bd->connect_error);
  }

$url_plugin=$_GET['url'];

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
	
	?>

<div class="form_upload">
  <form action="" method="post" id="form_search">
  <?php
    //Get all scheme infromation from db.
   $stmt = $bd->prepare($query_scheme) ;
   $stmt->execute();
   $stmt->bind_result($sid, $pid, $sname, $sdesc,$namepj);
   $schemeInfo = array();

   while($stmt->fetch()){
        $schemeInfo[] = array('sid' => $sid, 'project_name'=>$namepj,'pid' => $pid, 'schemeName' => $sname, 'description' => $sdesc);
   }

   $stmt->close();

 ?>   
	<p><input type='text' name = 'gallery_name' id = 'gallery_name' value = "<?php echo stripslashes($_POST['gallery_name']);?>"placeholder='Title:'/></p>
	<p><input type='text' name = 'gallery_description' id = 'gallery_description' value = "<?php echo stripslashes($_POST['gallery_description'])?>" placeholder='Description:'/></p>
   
  <?php
	echo "<p><select class='chosen_select' id = 'id_scheme' name = 'id_scheme'>";
	echo "<option value='' selected>  </option>";

	foreach ($schemeInfo as $value) {
		$id_scheme = $value['sid'];
        $name_scheme = $value['schemeName'];
        $desc_scheme = $value['description'];
        $pid_scheme = $value['pid'];
         $pid_name = $value['project_name'];
              echo '<option value="' . $id_scheme . '"' . ($_POST['id_scheme'] == $id_scheme ? ' selected="selected"' : '') . '>' . $pid_scheme.'---'.$pid_name.'---'.$id_scheme.'---'.$name_scheme.'---'.$desc_scheme.'</option>';
      }
	echo "</select></p>"; ?>
           <p><select id='fields1' class = 'array_control chosen_select' name = "controlsImage" data-placeholder="Select Image Field Name">
               <option value="default"></option>
                <?php if(isset($_POST['controlsImage'])){?>
               <option value="<?php echo $_POST['controlsImage']?>" selected><?php echo $_POST['controlsImage']?></option>
               <?php } ?>
           </select>
           <select id='fields2' class = 'array_control chosen_select' name = "controlsVideo" data-placeholder="Select Video Field Name">
               <option value="default"></option>
                <?php if(isset($_POST['controlsVideo'])){?>
               <option value="<?php echo $_POST['controlsVideo']?>" selected><?php echo $_POST['controlsVideo']?></option>
               <?php } ?>
           </select>
           <select id='fields3' class = 'array_control chosen_select' name = "controlsAudio" data-placeholder="Select Audio Field Name">
               <option value="default"></option>
                <?php if(isset($_POST['controlsAudio'])){?>
               <option value="<?php echo $_POST['controlsAudio']?>" selected><?php echo $_POST['controlsAudio']?></option>
               <?php } ?>
           </select></p>

	<?php
    // control fields list
    echo "<p><select class='array_control chosen_select' id = 'fields4' name='array_control[]' data-placeholder='Field(s): Search and select field(s) for new object(s)'   multiple onchange = ''   required>";
    echo '<option value="default"></option>';
      if(isset($_POST['array_control']))
          foreach ( $_POST['array_control'] as $control) {?>
          <option value="<?php echo $control?>" selected><?php echo $control?></option>

       <?php }

  	echo '</select></p>';
	// # of object per page
	echo "<select id='objectsPerPage' name = 'objectsPerPage'>";
?>
                <option value="10" default <?php   if ($_POST['objectsPerPage'] == 10) { echo selected; } ?> >10 Objects Per Page</option>
                <option value="20" <?php   if ($_POST['objectsPerPage'] == 20) { echo selected; } ?> >20 Objects Per Page</option>
                <option value="30" <?php   if ($_POST['objectsPerPage'] == 30) { echo selected; } ?> >40 Objects Per Page</option>
                <option value="80" <?php   if ($_POST['objectsPerPage'] == 80) { echo selected; } ?> >80 Objects Per Page</option>
<?php 
    echo "</select>";
    if(isset($_POST['keyword'])){ ?>
      <input type="search" id='searchObjects' name = 'keyword' value=<?php echo $_POST['keyword']?> placeholder="Search Objects" />
  <?php }else{?>
        <input type="search" id='searchObjects' name = 'keyword' placeholder="Search Objects" />
 <?php } 

    echo "<button type='submit' id='gal_search' name='k_search' class='blue-btn' >SEARCH</button></p>";

    echo "</form>";
    echo '</div>';
	//echo "<button id='selectAll'>Select All  Objects</button>"
  ?>
  
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
    
      if (isset($_POST['id_scheme']) && isset($_POST['array_control']) && (isset($_POST['controlsImage']) || isset($_POST['controlsVideo']) || isset($_POST['controlsAudio'])) ) {
          //get search information
          $schemeid = $_POST['id_scheme'];
          $controls = $_POST['array_control'];
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
 
    //if we type a keyword to searchname = 'keyword'
   if (isset($_POST['keyword'])) {
        $k = $_POST['keyword'];
        if (count($controls) == 1) {
                $query .= $controls[0].",LIKE,".$k;
         } else {
                for ($i = 0; $i < count($controls); $i++) {
                        if ($i == 0) {
                            $query .= "(".$controls[$i].",LIKE,".$k.")";
                        } else {

                            $query .= ",AND,"."(".$controls[$i].",LIKE,".$k.")";
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
     //var_dump($server_output);   
     if (is_array($server_output)) {
     
       $total_obj = count($server_output);
      
       $objectsPerPage = $_POST['objectsPerPage'];
       $totalPages = ceil($total_obj / $objectsPerPage); 
    
       ?>
       <div class="objSpan">
       <button id="selectAll" align="center" class = "blue-btn" style = "height:54px; width: 300px">Select All <?php echo $total_obj; ?> Objects</button>
       </div>
       
       <div id="pagination" alt = "<?php echo $total_obj; ?>">
     
    </div>  
               
                       
   <?php
     echo " <span id = 'image_control' style = 'display:none'>".$image_control."</span>
          <span id = 'video_control' style = 'display:none'>".$video_control."</span>
          <span id = 'audio_control' style = 'display:none'>".$audio_control."</span>";
          
         foreach($server_output as $record) {
                   if ($record[$image_control]) {
                       $thumb_src = get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid'].'/thumbs/'.$record[$image_control]['localName'];
                       $media_type = "image";
                   } else if ($record[$video_control]) {
                       $videoFile = mysql_escape_string(get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid']."/".$record['Video File']['localName']);
                      //var_dump($videoFile);
                       $media_type = "video";
                       
                   } else if ($record[$audio_control]) {
                       $audioFile = mysql_escape_string(get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid']."/".$record['Audio File']['localName']);
                      // var_dump($audioFile);
                       $media_type = "audio";
                   } else {
                       $thumb_src = KORA_PLUGIN_PATHBASE."images/placeholder_plugin.svg";
                       $media_type = "NULL";
                   }
                   
   
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


                              echo "<input type='button' class = 'edit_detials' id = '".$record['kid']."' value='edit details'  alt = '".$sid_pid_token['schemeid']."'>
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
			                            //<li><span>Field Name:</span> Field Input</li>
                                              echo '</ul>';
		                            echo "</div>
	                           </div>

   </div>";  
}?>
<div class="objSpan" style="display: flex; justify-content: space-between;">
  <button id="prevPage" align="center" class = "prev" style = "height:54px; width: 300px; color:white; background-color: #57abce; border: 1px solid #57abce">PREV</button>
  <button id="nextPage" align="center" class = "next" style = "height:54px; width: 300px; color:white; background-color: #57abce; border: 1px solid #57abce">NEXT</button>
</div>

               
  <?php    } else {
                  echo "<h1>No matched object records!</h1>";
             }
                        
           } else {
               echo "<h1>Please choosing Scheme and Controls first!</h1>";
          }
       ?>


         		<div id='add-kora-objs'>
            <input type="submit" value='Add Object(s) to Gallery' />
         </div>

</div>	
<?php 
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
      <script src="<?php echo KORA_PLUGIN_PATHBASE.'js/gallery.js';?>"></script>
