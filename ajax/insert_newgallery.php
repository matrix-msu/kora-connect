<?php
	require_once("../../../../wp-blog-header.php");
	require_once( realpath( dirname(__FILE__) . "/../dbconfig.php" ) );
	global $wpdb;
	if(isset($_GET['chk']) && isset($_GET['schemeid']) && isset($_GET['controlfileds']) ){
		$schemeid = $_GET['schemeid'];
		$controlfileds  = $_GET['controlfileds'];
        $chkarr = $_GET['chk'];
	    $kid = $chkarr[0];
	    $gallery = $chkarr[1];
		$description = $chkarr[2];
		//image/video/audio
		$image_control = $_GET['image_control'];
        $video_control = $_GET['video_control'];
        $audio_control = $_GET['audio_control'];
		
		//insert record into gallery information db.
		$gallery_n = $wpdb->prefix . $gallery;
		//gallery name without space to create the table
		$gallery_nam=stripslashes(str_replace(" ", "_", $gallery_n));
		//remove apostrophe
		$gallery_name = str_replace("'","Char_39__", html_entity_decode($gallery_nam, ENT_QUOTES)); 
		echo $gallery_name. "\n";
		$gallert_infodb = $wpdb->dbname;
		$query_galleryinfo = "SHOW TABLES $gallert_infodb LIKE $gallery_name";
		if($wpdb->get_var($query_galleryinfo) != $gallery_name) {
		 	$query_create_table = "CREATE TABLE $gallery_name (
					id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					KID VARCHAR(45) NOT NULL,
					schemeid VARCHAR(45) NOT NULL,
					url VARCHAR(10000) DEFAULT '' NOT NULL,
					thumb VARCHAR(10000) DEFAULT '' NOT NULL,
					title VARCHAR(999) NOT NULL,
					display VARCHAR(10000) DEFAULT '' NOT NULL,
					imagefield  VARCHAR(45) DEFAULT '' NOT NULL,
					videofield  VARCHAR(45) DEFAULT '' NOT NULL,
					audiofield  VARCHAR(45) DEFAULT '' NOT NULL
					);";
			$wpdb->query($query_create_table);
			$galleryinfo = $wpdb->prefix . "koragallery";
			   //insert galery information into wp_koragallery
                if ($description) {
                    $gallery_description = $description;
                } else {
                    $gallery_description = "No description";
                }
			if (!$wpdb->query("select description from $galleryinfo")) {
				$wpdb->query("ALTER TABLE $galleryinfo ADD COLUMN `description` VARCHAR(1000)");
			}
			$query_galleryinfo_check = "SELECT * FROM $galleryinfo where title LIKE '$gallery'";
		    if(empty($wpdb->get_results($query_galleryinfo_check))){
			    $wpdb->insert(
					$wpdb->prefix . "koragallery",
					array(
						"title" => stripslashes_deep($gallery),
						"description" => stripslashes_deep($gallery_description),
                    	"imagefield" => $image_control,
                    	"videofield" => $video_control,
                    	"audiofield" => $audio_control
					),
					array(
						'%s',
						'%s',
						'%s',
						'%s',
						'%s'
					) 
				);
			}

		 }
		 
		 //check if object is already in gallery
		$query = "SELECT * FROM $gallery_name where KID= '$kid'";
	    if(empty($wpdb->get_results($query))){
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
                    //$sid_pid_token = array();
                        
                    while($stmt->fetch()){
                        $sid = $sids;
                        $pid = $pids;
                        //echo $sid." ".$pid."<br>";
                        if (in_array($sid, $dbscheme) && in_array($pid, $dbproj)){
                            $pos = array_search($pid, $dbproj);
                            $sid_pid_token = array('schemeid' => $sid, 'projectid' => $pid, 'token' => $dbtoken[$pos]);
                          
                        }
                    }
                    $stmt->close();
        
			$user = kordat_dbuser;
			//$pass = get_option('kordat_dbpass');
			$pass = kordat_dbpass;
			//$query = "KID,=,".$kid;
			if ($kid == "ALL") {
				$query = "Display,=,"."True";
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
			  echo $server_output;
             
			  $control_save = array();
              $control_format = array();
              foreach($server_output as $record){
				       if ($record[$image_control]) {
                             $thumb_src = get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid'].'/thumbs/'.$record[$image_control]['localName'];
                             $src = str_replace("thumbs/", "", $thumb_src);

                        } else if ($record[$video_control]) {
	                         $src = get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid'].$record[$video_control]['localName'];
                                
                        } else if ($record[$audio_control]) {
                             $src = get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid'].$record[$audio_control]['localName'];
                             
                        } else {
                             $thumb_src = "http://img.photobucket.com/albums/v516/MizGrace/babyhedgehoginbubblebath.jpg";
                             $src = "http://img.photobucket.com/albums/v516/MizGrace/babyhedgehoginbubblebath.jpg";   
                         }
				  
                
				    //Get KID from HTML
                    $kid = $record['kid'];
                    $title = $record['Title'];
                   
                    //get all controls need to save in db.
              }
			  $control_save .= implode(",", $controlfileds);
    			
		 $wpdb->insert(
				$gallery_name,
				array(
					'KID' =>$kid,
					'schemeid' => $schemeid,
					'url' => $src,
					'title' => stripslashes_deep($title),
					'thumb' => $thumb_src,
                    'display' => $control_save,
                    'imagefield' => $image_control,
                    'videofield' => $video_control,
                    'audiofield' => $audio_control
				),
				array(
					'%s',
					'%s',
					'%s',
					'%s',
                    '%s',
					'%s',
					'%s',
					'%s',
					'%s'
				)
			);

			
				echo json_encode (true);
			}
			else{
				header('HTTP/1.1 200 OK');
				echo json_encode(false);

			}
			
		
	}
?>

