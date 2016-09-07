<link rel="stylesheet" type="text/css" href="koraModal.css">
<link rel="stylesheet" type="text/css" href="chosen_v1.4.2/chosen.css">

<?php
	global $wpdb;

	require_once( realpath( dirname(__FILE__) . "/../../../wp-includes/wp-db.php" ) );
  	require_once( realpath( dirname(__FILE__) . "/../../../wp-blog-header.php"));
	require_once( realpath( dirname(__FILE__) . "/dbconfig.php" ) );

	define('KORA_PLUGIN_PATHBASE', plugin_dir_url(__FILE__));
	define('KORA_PLUGIN_RESTFUL_SUBPATH', 'api/restful.php');

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
			//echo $value;
			$table[$n]="p".$value."Control";
			//echo $table[$n];
			$n=$n+1;
		}
	 }
	}
     $bd = new mysqli($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);
     if ($bd->connect_error) {
      die("Connection failed: " . $bd->connect_error);
     }
	 $i=0;
	 $query_control='';

	if (is_array($table)) {
	 foreach($table as $value){
		if($i!=0){
			$query_control.= " UNION ALL ";
		}
		$query_control .= "SELECT name,schemeid FROM $value WHERE  showInResults = 1 AND schemeid in(";
		 if (!$scheme_id) {
			 die("Scheme not set in the connect tab");
		 }
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
	$query_control.="ORDER BY name ASC;";
	// prepare query
	$stmt = $bd->prepare($query_control);
	//get project name
	$query_name .= "SELECT name, pid FROM project WHERE pid in(";
	if (is_array($projector_id)) {
			//remove duplicates
			$projector_id = array_unique($projector_id);
			$lastScheme = end($projector_id);
			foreach($projector_id as $value){
					if($value == $lastScheme){
							$query_name .=$value;
					}
					else{
							$query_name .= $value.",";
					}
			}
	 }
	 $query_name.=")";
	 $query_name .= " ORDER BY pid ASC;";
	 $name_stmt = $bd->prepare($query_name) ;
	 $name_stmt->execute();
	 $name_stmt->bind_result($name, $pid);
	 $proj_name = array();
	 while($name_stmt->fetch()){
			 $proj_name[$pid] = $name;
	 }
     $name_stmt->close();
    // create query for finding scheme info
    $query_scheme .= "SELECT schemeid, pid, schemeName, description FROM scheme WHERE schemeid in(";
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
    //get info from db
    $scheme_stmt = $bd->prepare($query_scheme) ;
    $scheme_stmt->execute();
    $scheme_stmt->bind_result($sid, $pid, $sname, $sdesc);
    $schemeInfo = array();

    while($scheme_stmt->fetch()){
         $schemeInfo[$sid] = array('sid' => $sid, 'pid' => $pid, 'schemeName' => $sname, 'description' => $sdesc);
    }
	//var_dump($schemeInfo);
    $scheme_stmt->close();
    
?>
<!--==============================================================
              BELOW IS THE START OF THE FORM
==============================================================-->
<div class="form_upload">

  <form action="" method="post" id="form_search">
		<?php
			echo "<select id = 'id_scheme' name = 'id_scheme' data-placeholder='Scheme: Search and Select Scheme for New Object(s)' onchange='this.form.submit()'>";
			echo "<option value='default' selected></option>";
			foreach ($scheme_id as $value) {
				$id_scheme = $value;
				echo '<option value="' . $id_scheme . '"' . ($_POST['id_scheme'] == $id_scheme ? ' selected="selected"' : '') . '>' . $proj_name[$schemeInfo[$id_scheme]['pid']] .' - '. $id_scheme.' - '.$schemeInfo[$id_scheme]['schemeName'].' - '.$schemeInfo[$id_scheme]['description'].'</option>';
			}
			echo "</select>";
	  ?>
		<select id="img_control" name="img_c" data-placeholder="Image Control: Search and Select the Image Control" required>
			<option value="default" disabled selected>  </option>
			<?php
				$stmt->execute();
			 	$stmt->bind_result($name,$schemeid);
			 	while($stmt->fetch()){
					$select_file_value=$name;
					if ( $schemeid == $_POST['id_scheme']) {
						echo '<option value="' . $select_file_value . '"' . ($_POST['img_c'] == $select_file_value ? ' selected="selected"' : '') . '>' . $select_file_value . '</option>';
					}
				}
			?>
		</select>
    <div class="split_control">
        <select id="audio_control" class="half_width_chosen_container" name="audio_c" data-placeholder="Audio Control: Search and Select the Audio Control" required>
    	<option value="default" disabled selected>  </option>
    	<?php
    		$stmt->execute();
    		 $stmt->bind_result($name,$schemeid);
    		 $options = array();
    		while($stmt->fetch()){
    				$select_file_value=$name;
    				 if ( $schemeid == $_POST['id_scheme']) {
    					 array_push($options, $name);
    				echo '<option value="' . $select_file_value . '"' . ($_POST['audio_c'] == $select_file_value ? ' selected="selected"' : '') . '>' . $select_file_value . '</option>';
                 }
    		}
    	?>
    	</select>
        <select id="video_control" class="half_width_chosen_container" name="video_c" data-placeholder="Video Control: Search and Select the Video Control" required>
    	<option value="default" disabled selected>  </option>
    	<?php
    		$stmt->execute();
    		 $stmt->bind_result($name,$schemeid);
    		 $options = array();
    		while($stmt->fetch()){
    				$select_file_value=$name;
    				 if ( $schemeid == $_POST['id_scheme']) {
    					 array_push($options, $name);
    				echo '<option value="' . $select_file_value . '"' . ($_POST['video_c'] == $select_file_value ? ' selected="selected"' : '') . '>' . $select_file_value . '</option>';
                 }
    		}
    	?>
    	</select>
    </div>
    <div class="split_control">
		<select id="title_control" class="half_width_chosen_container" name="title" data-placeholder="Title Control: Search and Select the Title Control" required>
			<option value="default" disabled selected>  </option>
			<?php
				//$result_control=$bd->query($query_control);
		    /* execute statement */
				$stmt->execute();
				/* bind result variables */
    		$stmt->bind_result($name,$schemeid);
				while($stmt->fetch()){
					$select_title_value=$name;
					if ( $schemeid == $_POST['id_scheme']) {
						echo '<option value="' . $select_title_value . '"' . ($_POST['title'] == $select_title_value ? ' selected="selected"' : '') . '>' . $select_title_value . '</option>';
						if ($_POST['title'] == $select_title_value) {
							$select_scheme_id = $schemeid;
						}
		   		}
				}
			?>
		</select>
		<select id="desc_control" class="half_width_chosen_container" name="description" data-placeholder="Description Control: Search and Select the Description Control" required>
			<option value="default" disabled selected>  </option>
			<?php
				//$result_control=$bd->query($query_control);
        // $result_control=$stmt->get_result();
		    /* execute statement */
    		$stmt->execute();
    		/* bind result variables */
				$stmt->bind_result($name,$schemeid);
		 		while($stmt->fetch()){
        	$select_description_value=$name;
			    if ( $schemeid == $_POST['id_scheme']) {
 						echo '<option value="' . $select_description_value . '"' . ($_POST['description'] == $select_description_value ? ' selected="selected"' : '') . '>' . $select_description_value . '</option>';
            if ($_POST['description'] == $select_description_value) {
							if (!$select_scheme_id){
								$select_scheme_id = $schemeid;
							}
						}
					}
				}
			?>
		</select>
	</div>
	<select class="array_control" name="array_control[]" multiple data-placeholder='Field(s): Search and Select Field(s) for New Object(s)' required>
	<option value disabled></option>
	<?php
		$stmt->execute();
		 $stmt->bind_result($name,$schemeid);
		 $options = array();
		while($stmt->fetch()){
				$select_title_value=$name;
				 $title_value = $_POST['array_control'];
				 $title_value = $title_value[0];
				 if ( $schemeid == $_POST['id_scheme']) {
					 array_push($options, $name);
				echo '<option value="' . $select_title_value . '"' . ($title_value == $select_title_value ? ' selected="selected"' : '') . '>' . $select_title_value . '</option>';
			 }
		}
	?>
	</select>
    <!-- Infinite scroll and pagination -->
    <div class="split_control">
        <input id="infscroll" type="radio" class=radio name="type" value="infscroll" checked />
        <label for='infscroll' class="button_radio">Infinite Scroll</label>
    	<input id="pagination" type="radio" class=radio name="type" value="pagination" <?php if($_POST['type']=="pagination"){echo 'checked="checked"';}?>/>
        <label for="pagination" class="button_radio">Pagination</label>
    </div>
    <input type = 'text' id = 'pic_pagesize' placeholder="ENTER PAGE SIZE" name = 'pic_pagesize' size='20' value = "<?php echo $_POST['pic_pagesize'];?>" />
    <br>
    <div class="split_control">
        <input type="radio" name="details" id="add_details" class=radio value="add_details" <?php if($_POST['details']=="add_details"){ echo 'checked="checked"'; }?>  />
        <label for="add_details" class="button_radio">Add Detail Page</label>
        <input type="radio" name="details" id="no_details" class=radio value="no_details" checked />
        <label for="no_details" class="button_radio">No Detail Page</label>
    </div>
    <div class="search-field">
        <input type="search" class="searchObjects" placeholder="Search Objects" name="kid" value="<?php echo $_POST['kid'];?>" />
        <input type="submit" class="search_button" value="search" />
    </div>
		<div class="split_control">
<!-- 'new' is short for add new kora object -->
        <select id="num_per_page_new" class="num_per_page" onchange="this.form.submit()" name="num_per_page" required>
            <option value="10" selected>10 Objects Per Page</option>
            <option value="20"<?php if($_POST['num_per_page']=="20"){echo 'selected';}?>>20 Objects Per Page</option>
            <option value="40"<?php if($_POST['num_per_page']=="40"){echo 'selected';}?>>40 Objects Per Page</option>
            <option value="80"<?php if($_POST['num_per_page']=="80"){echo 'selected';}?>>80 Objects Per Page</option>
        </select>
			<input type="checkbox" id="select_all_search" name="select_all_search" class="select_all_checkbox">
			
		</div>
</form>
  <?php
        $obj_per_page = $_POST['num_per_page']; //get the number of objects per page to load
        if ($obj_per_page == null) {
            $obj_per_page = 10; //set default
        }
		$token = $_GET['token'];
		$pid = $_GET['pid'];
		$sid = $_GET['sid'];
		$user = kordat_dbuser;
		$pass = kordat_dbpass;
		$restful=$_GET['restful'];
		$url_plugin=$_GET['url'];
		$dbproj = get_option('kordat_dbproj');
		$dbscheme = get_option('kordat_dbscheme');
		$dbtoken = get_option('kordat_dbtoken');
		//if (count(sids) > 0) {
		$query_sid_pid = "SELECT schemeid,pid FROM scheme";
		$stmt = $bd->prepare($query_sid_pid) ;
    $stmt->execute();
    $stmt->bind_result($sids,$pids);
    $sid_pid_token = array();
    while($stmt->fetch()){
        $sid = $sids;
        $pid = $pids;
        //echo $sid." ".$pid."<br>";
         if (in_array($sid, $dbscheme) && in_array($pid, $dbproj)){
            $pos = array_search($pid, $dbproj);
             $val = $sid."-".$pid."-".$dbtoken[$pos];
             array_push($sid_pid_token,$val);
        }
    }
		$stmt->close();
		if(isset($_POST['kid'])) { // THIS IS NOT ACTUALLY A KID. ITS A SEARCH TERM
			$title_form=$_POST['title'];
			$k = $_POST['kid'];
			$image_control=$_POST['img_c'];
			$audio_control=$_POST['audio_c'];
			$video_control=$_POST['video_c'];
			$type=$_POST['type'];
			$desc= $_POST['description'];
			$array_control = $_POST['array_control'];
			if (strlen($k) >= 1) {
				if($title_form or $desc) {
					$query = "";
					if (count($array_control) == 1) {
						$query .= $array_control[0].",LIKE,".$k;
					}
					else if (count($array_control) > 1){
						for ($i = 0; $i < count($array_control); $i++) {
							if ($i == 0) {
								$query .= "(".$array_control[$i].",LIKE,".$k.")";
							}
							else {
								$query .= ",OR,"."(".$array_control[$i].",LIKE,".$k.")";
							}
						}
					}
					$restful_url =$restful . KORA_PLUGIN_RESTFUL_SUBPATH;
					$fields = "";
					$num_option = count($options);
					$i = 0;
					foreach($options as $option_value) { // comma separated list of array controls
						if ($i < $num_option - 1) {
							$fields .= $option_value.',';
						} else {
							$fields .= $option_value;
						}
						$i += 1;
					}
					$fields.=','.$title_form;

					if($desc!=''){
						$fields.=','.$desc;
					}
					$display='json';
					foreach($sid_pid_token as $vals){
						$val = explode("-",$vals);
            if ($val[0] == $_POST['id_scheme']){
            	$url = $restful_url.'?request=GET&pid='.$val[1].'&sid='.$val[0].'&token='.$val[2].'&display='.urlencode($display).'&fields='.urlencode($fields).'&query='.urlencode($query);
            }
          }

///initialize post request to KORA API using curl
$ch = curl_init($url);
// because CURLOPT_RETURNTRANSFER is set, the curl_exec will return the result or the boolean FALSE.
// if it were not set, the return would be boolean TRUE or FALSE.
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);
$curl_result = curl_exec($ch);
// second parameter of json_decode is boolean to indeicate if the return should be php associative array
$kora_objects = json_decode($curl_result, true); // json_decode can handle the boolean false and invalid kora searches. in both cases it returns null
if (!empty($kora_objects)) { // check is the php array made from JSON is empty. If not, there were results from search so we'll print those
	 $total_obj = count($kora_objects);
      
       $objectsPerPage = $_POST['num_per_page'];
       $totalPages = ceil($total_obj / $objectsPerPage); ?>
    <div id="pagination" alt = "<?php echo $total_obj; ?>">
    <label for="select_all_search"   class="select_all_btn" style = "height:54px; width: 300px">Select All <?php echo $total_obj; ?> From Search</label>
<?php	echo "<div class='kora_results_container kora-objs'>";
	
	foreach ($kora_objects as $kora_object_kid => $kora_object) {
<<<<<<< HEAD
		//var_dump($kora_object);
=======
>>>>>>> 0715e1f39e10e6278fc264912e125ae5bfe73842
		if ($kora_object[$image_control]) {
           $thumb_src = get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid'].'/thumbs/'.$kora_object[$image_control]['localName'];
           $media_type = "image";
       } else if ($kora_object[$video_control]) {
           $videoFile = mysql_escape_string(get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid']."/".$record['Video File']['localName']);
          //var_dump($videoFile);
           $media_type = "video";
           
       } else if ($kora_object[$audio_control]) {
           $audioFile = mysql_escape_string(get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid']."/".$record['Audio File']['localName']);
          // var_dump($audioFile);
           $media_type = "audio";
       } else {
           $thumb_src = KORA_PLUGIN_PATHBASE.'images/placeholder_plugin.svg';
           $media_type = "NULL";
       }	
       
    echo "<div class='kora-obj' id = '".$kora_object_kid."'>
            <div class='kora-obj-left'>";
         
              if($media_type == "image") {
              echo "<img src='".$thumb_src."' alt='".$kora_object_kid."'>";
          } else if ($media_type == "video") {
              echo '<video width="142" height="140" controls><source src="'.$videoFile.'" type="video/mp4"></video>';
          } else if ($media_type == "audio") {
              echo '<video width="142" height="140" controls><source src="'.$audioFile.'" type="audio/mpeg"></video>';
      
          } else {
              echo "<img src='".$thumb_src."' alt='".$kora_object_kid."'>";
          }


        //  echo "<input type='button' class = 'edit_detials' id = '".$kora_object_kid."' value='edit details'  alt = '".$sid_pid_token['schemeid']."'>
        echo "</div>
            <div class='kora-obj-right'>";
              
                echo '<div id = "edit_details_'.$kora_object_kid.'">';
                       echo "<ul class='kora-obj-fields'>";
                   echo "<li><span>KID</span>: ".$kora_object_kid."</li>";
                   echo "<li><span>Title</span>: ".$kora_object[$title_form]."</li>";
                   
                           foreach($array_control as $field) {
                               if (is_array($kora_object[$field])) {
                                    echo "<li><span>".$field."</span>: ".implode(" ",$kora_object[$field])."</li>";
                               } else {
                                   echo "<li><span>".$field."</span>: ".$kora_object[$field]."</li>";
                               }
                           }
                    //<li><span>Field Name:</span> Field Input</li>
                          echo '</ul>';
                echo "</div>
           </div>

   </div>";  
	}?>
	<button id="prevPage" align="center" class = "prev" style = "height:54px; width: 300px; color:white; background-color: #57abce; border: 1px solid #57abce">PREV</button>
	<button id="nextPage" align="center" class = "next" style = "height:54px; width: 300px; color:white; background-color: #57abce; border: 1px solid #57abce">NEXT</button>

               
  <?php    } else {
                  echo "<h1>No matched object records!</h1>";
             }
                        
           } else {
               echo "<h1>Please choosing Scheme and Controls first!</h1>";
          }
       ?>

<!--<div class="pagination_footer"></div>-->
<div id='selected-kora-objs'>
    <input id="insert_shortcode_new" type="submit" value='Add New Object(s)' />
</div>

</div>	
<?php 
        }
     }  
?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>
	var url_plugin = "<?php echo $url_plugin;?>";
	var title='<?php echo $title_form;?>';
	var img_c='<?php echo $image_control;?>';
	var audio_c='<?php echo $audio_control;?>';
	var video_c='<?php echo $video_control;?>';
	var desc='<?php echo $desc;?>';
	var type='<?php echo $type;?>';
	var controls = '<?php echo $array_control;?>';
    var obj_per_page = <?php echo $obj_per_page;?>;
</script>
<script src="<?php echo KORA_PLUGIN_PATHBASE.'chosen_v1.4.2/chosen.jquery.min.js';?>"></script>
<script>
	var detailslink = "<?php echo 'wp-content/plugins/kora/fullrecord.php';?>";
	//=========CHOSEN=========
	$("#id_scheme").chosen();
	$("#img_control").chosen();
	$("#audio_control").chosen();
	$("#video_control").chosen();
	$("#title_control").chosen();
	$("#desc_control").chosen();
	$(".array_control").chosen();
	$(".num_per_page").chosen();
	//=========/CHOSEN=========
</script>
<script src="<?php echo KORA_PLUGIN_PATHBASE.'js/koraupload.js';?>"></script>
<script src="<?php echo KORA_PLUGIN_PATHBASE.'js/spin.js';?>"></script>

