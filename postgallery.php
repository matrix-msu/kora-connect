<link rel="stylesheet" type="text/css" href="kora.css">
<link rel="stylesheet" type="text/css" href="chosen_v1.4.2/chosen.css">
  <?php
require_once( realpath( dirname(__FILE__) . "/../../../wp-includes/wp-db.php" ) );
require_once( realpath( dirname(__FILE__) . "/../../../wp-blog-header.php"));
define('KORA_PLUGIN_PATHBASE', plugin_dir_url(__FILE__));
define('KORA_PLUGIN_RESTFUL_SUBPATH', 'api/restful.php');
require_once( realpath( dirname(__FILE__) . "/dbconfig.php" ) );

global $wpdb;

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
	// $table="p".$projector_id."Control";
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

		$query_control .= "SELECT name,schemeid FROM $value WHERE name not in ('systimestamp', 'recordowner') AND schemeid in(";
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
	
	 $query_control.="  ORDER BY name ASC;";
	$stmt = $bd->prepare($query_control) ;

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
	 //var_dump($query_name);
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
	$scheme_stmt->close();
?>

<div class="form_upload">
<form action="" id="postgalleryform" method="post">
  <?php
	echo "<select id = 'id_scheme' name = 'id_scheme' data-placeholder='Scheme: Narrow Galleries by Scheme' onchange='this.form.submit()'>";
	echo "<option value='default' selected>  </option>";
	foreach ($scheme_id as $value) {
		$id_scheme = $value;
		echo '<option value="' . $id_scheme . '"' . ($_POST['id_scheme'] == $id_scheme ? ' selected="selected"' : '') . '>' . $proj_name[$schemeInfo[$id_scheme]['pid']] .' - '. $id_scheme.' - '.$schemeInfo[$id_scheme]['schemeName'].' - '.$schemeInfo[$id_scheme]['description'].'</option>';
	}
	echo "</select>";
  ?>
    <select id="img_control" data-placeholder="Image Control: Search and Select the Image Control" name="img_c" required>
	<option value="default" disabled selected>  </option>
	<?php
		$stmt->execute();
		 	 $stmt->bind_result($name,$schemeid);

        // $result_control=$bd->query($query_control);
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
    <div class="split_control">
        <input id="infscroll" type="radio" class=radio name="type" value="infscroll" checked />
        <label for='infscroll' class="button_radio">Infinite Scroll</label>
    	<input id="pagination" type="radio" class=radio name="type" value="pagination" <?php if($_POST['type']=="pagination"){echo 'checked="checked"';}?>/>
        <label for="pagination" class="button_radio">Pagination</label>
    </div>

  <select id="Galleries" name="select_gallery" onchange="chooseGallery()" data-placeholder="Select a Gallery">
  <option disabled selected></option>

<?php
//Get list of galleries from database
global $wpdb;
$gallery= $wpdb->prefix . 'koragallery';
$query = "SELECT * FROM  $gallery";
$wpdb->get_results("SELECT * FROM  $gallery");
foreach( $wpdb->get_results("SELECT * FROM  $gallery") as $key => $row){
	echo '<option value='.$row->title.'>'.$row->title.'</option>';
}
echo '</select>';

?>

    <input type = 'text' id = 'pic_pagesize' placeholder="ENTER PAGE SIZE" name = 'pic_pagesize' size='20' value = "<?php echo $_POST['pic_pagesize'];?>" />
    <br>
    <div class="split_control">
        <input type="radio" name="details" id="add_details" class=radio value="add_details" <?php if($_POST['details']=="add_details"){ echo 'checked="checked"'; }?>  />
        <label for="add_details" class="button_radio">Add Detail Page</label>
        <input type="radio" name="details" id="no_details" class=radio value="no_details" checked />
        <label for="no_details" class="button_radio">No Detail Page</label>
    </div>
<div class="gal_display kora-objs"></div>
  <input id="insert_shortcode_gallery" type="submit" value="Insert Shortcode" />
</form>


<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="<?php echo KORA_PLUGIN_PATHBASE.'chosen_v1.4.2/chosen.jquery.min.js';?>"></script>
<script>
	$("#id_scheme").chosen();
	$("#img_control").chosen();
	$("#audio_control").chosen();
	$("#video_control").chosen();
	$("#title_control").chosen();
	$("#desc_control").chosen();
	$("#Galleries").chosen();
</script>
<script> var url_plugin = "<?php echo $url_plugin;?>"; </script>
<script src="<?php echo KORA_PLUGIN_PATHBASE.'js/postgallery.js';?>"></script>
<script src="<?php echo KORA_PLUGIN_PATHBASE.'js/chosen.jquery.min.js';?>"></script>
<script src="<?php echo KORA_PLUGIN_PATHBASE.'js/spin.js';?>"></script>
<script> var detailslink = "<?php echo 'wp-content/plugins/kora/fullrecord.php';?>"; </script>
</div>
