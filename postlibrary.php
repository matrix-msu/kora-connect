<link rel="stylesheet" type="text/css" href="chosen_v1.4.2/chosen.css">
<link rel="stylesheet" type="text/css" href="kora.css">

<?php
	require_once('../../../wp-load.php');
	require_once( realpath( dirname(__FILE__) . "/dbconfig.php" ) );

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
	$query_control.="ORDER BY name ASC;";
	// prepare query
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
	//  var_dump($proj_name);
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
	// var_dump($schemeInfo);
//?>
<!--==============================================================-->
<!--              BELOW IS THE START OF THE FORM-->
<!--==============================================================-->
<div class="form_upload">
	<form action="" id="postgalleryform" method="post">
	<?php
		//var_dump($scheme_id);
		echo "<select id = 'id_scheme' name = 'id_scheme' data-placeholder='Scheme: Search and Select Scheme for New Object(s)' onchange='this.form.submit()'>";
		echo "<option value='default' selected></option>";
		foreach ($scheme_id as $value) {
			$id_scheme = $value;
			echo '<option value="' . $id_scheme . '"' . ($_POST['id_scheme'] == $id_scheme ? ' selected="selected"' : '') . '>' . $proj_name[$schemeInfo[$id_scheme]['pid']] .' - '. $id_scheme.' - '.$schemeInfo[$id_scheme]['schemeName'].' - '.$schemeInfo[$id_scheme]['description'].'</option>';
		}
		echo "</select>";
  ?>
	<select id="img_control" name="img_c" data-placeholder="Image Control: Search and Select the Image Control" >
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
			<option value="default" disabled selected></option>
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
			<option value="default" disabled selected></option>
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
<!--	<select class="array_control" name="array_control[]" multiple data-placeholder='Field(s): Search and Select Field(s) for New Object(s)' >-->
<!--		<option value disabled></option>-->
<!--			--><?php
//				$stmt->execute();
//			 	$stmt->bind_result($name,$schemeid);
//			 	$options = array();
//				while($stmt->fetch()){
//					$select_title_value=$name;
//					$title_value = $_POST['array_control'];
//					$title_value = $title_value[0];
//					if ( $schemeid == $_POST['id_scheme']) {
//						array_push($options, $name);
//						echo '<option value="' . $select_title_value . '"' . ($title_value == $select_title_value ? ' selected="selected"' : '') . '>' . $select_title_value . '</option>';
//					}
//				/* if ($title_value == $select_title_value) {
//						$select_scheme_id = $schemeid;
//					}*/
//				}
//			?>
<!--	</select>-->
	<!-- Infinite scroll and pagination -->
	<div class="split_control">
			<input id="infscroll" class="radio" type="radio" name="type" value="infscroll" checked />
			<label for='infscroll' class="button_radio">Infinite Scroll</label>
            <input id="pagination" type="radio" class=radio name="type" value="pagination" <?php if($_POST['type']=="pagination"){echo 'checked="checked"';}?>/>
			<label for="pagination" class="button_radio">Pagination</label>
	</div>
	<input id='pic_pagesize' type='text' name='pic_pagesize' value="<?php echo $_POST['pic_pagesize'];?>" placeholder='ENTER PAGE SIZE'/>
	<div class="split_control">
            <input type="radio" name="details" id="add_details" class=radio value="add_details" <?php if($_POST['details']=="add_details"){ echo 'checked="checked"'; }?>  />
			<label for="add_details" class="button_radio">Add Detail Page</label>
			<input id="no_details" class="radio" type="radio" name="details" value="no_details" checked />
			<label for="no_details" class="button_radio">No Detail Page</label>
	</div>
	<div class="split_control">
		<select id="num_per_page_lib" class="num_per_page" onchange="this.form.submit()" name="num_per_page" required>
			<option value="10" selected>10 Objects Per Page</option>
			<option value="20"<?php if($_POST['num_per_page']=="20"){echo 'selected';}?>>20 Objects Per Page</option>
			<option value="40"<?php if($_POST['num_per_page']=="40"){echo 'selected';}?>>40 Objects Per Page</option>
			<option value="80"<?php if($_POST['num_per_page']=="80"){echo 'selected';}?>>80 Objects Per Page</option>
		</select>
		<input type="checkbox" id="select_all_lib" name="select_all_lib" class="select_all_checkbox">
		<label for="select_all_lib" class="select_all_btn">Select All [] Object(s)</label> <!-- [] is set by .js file -->
	</div>
<!--	<!---->
<!--	<div id=="exsistobj_select" >-->
<!--		<button id="shortcode"> Insert Shortcode</button>-->
<!--	</div>-->
<!--	<div id="loading" align="center"></div> -->
	</form>
</div>

<!-- <div class="kora_results_container_library">-->
<div class="kora_results_container kora-objs">
	<?php
		require_once( realpath( dirname(__FILE__) . "/../../../wp-includes/wp-db.php" ) );
		require_once( realpath( dirname(__FILE__) . "/../../../wp-blog-header.php"));
		define('KORA_PLUGIN_PATHBASE', plugin_dir_url(__FILE__));
		define('KORA_PLUGIN_RESTFUL_SUBPATH', 'api/restful.php');
		global $wpdb;
		$library= $wpdb->prefix . 'koralibrary';

        $obj_per_page = $_POST['num_per_page']; //get the number of objects per page to load
        if ($obj_per_page == null) {
            $obj_per_page = 10; //set default
        }
		//var_dump($wpdb->get_results("SELECT * FROM  $library"));
		foreach( $wpdb->get_results("SELECT * FROM  $library") as $key => $row) {
			$url = preg_replace('/ /','%20',$row->url);

			echo "<div class='kora-obj' id=".$row->KID.">
				<div class='kora-obj-left'>";
				if($row->imagefield!='default'){
				    $new_url = str_replace($row->KID,"thumbs/".$row->KID,$url)." alt=".$row->KID;
                    $new_url = 'http://' . $new_url;
					echo  "	<img src=".$new_url." />";
				}
				else if($row->audiofield!='default'){
					echo '<video width="142" height="140" controls><source src="'.$url.'" type="audio/mpeg"></video>';
      

				}else if($row->videofield!='default'){
					echo '<video width="142" height="140" controls><source src="'.$url.'" type="video/mp4"></video>';
          
				}
				echo "</div>
				<div class='kora-obj-right'>
					<ul class='kora-obj-fields'>
						<li><span>KID: </span>".$row->KID."</li>
						<li><span>Title: </span>".$row->title."</li>
						<li><span>Direct Link: </span><a target='_blank' href='".$row->url."'>Click Here!</a></li>
					</ul>
				</div>
			</div>";
		}
//	foreach($wpdb->get_results("SELECT * FROM  $library") as $key => $row) {
	//		$img_field = $row->imagefield;
	//		$audio_field = $row->audiofield;
	//		$video_field = $row->videofield;
	//		//$sid = $row->
	//		$title_field = 'Title';
	//		$description_control = 'Description';
	//		break;
	//	}
	//	foreach ($scheme_id as $scheme) {
	//		$sid = $scheme;
	//		break;
	//	}
	?>
</div>
<div class="pagination_footer"></div>

<!-- BELOW IS THE 'ADD SELECTED KORA OBJS' BOX -->
<div id='selected-kora-objs'>
	<input id="insert_shortcode_lib" type="submit" value='Add Object(s) to Library' />
</div>

<!-- SCRIPTS -->
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="<?php echo KORA_PLUGIN_PATHBASE.'chosen_v1.4.2/chosen.jquery.min.js';?>"></script>
<script>
	//=========CHOSEN=========
	$("#id_scheme").chosen();
	$("#img_control").chosen();
	$("#audio_control").chosen();
	$("#video_control").chosen();
	$("#title_control").chosen();
	$("#desc_control").chosen();
	$(".array_control").chosen();
	$(".num_per_page").chosen();
	//=========/CHOSEN=======

	// Global vars used in postlibrary.js
	var url_plugin = "<?php echo $url_plugin;?>"; // currently, this is not used in postlibrary.js
	var detailslink = "<?php echo 'wp-content/plugins/kora/fullrecord.php';?>";
    var obj_per_page = <?php echo $obj_per_page;?>;


</script>
<script src="<?php echo KORA_PLUGIN_PATHBASE.'js/postlibrary.js';?>"></script>
<script src="<?php echo KORA_PLUGIN_PATHBASE.'js/spin.js';?>"></script> <!-- <-- THIS IS LOAD ICON -->
