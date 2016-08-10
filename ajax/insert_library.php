<?php
	require_once("../../../../wp-blog-header.php");
	require_once( realpath( dirname(__FILE__) . "/../dbconfig.php" ) );

	global $wpdb;
	
	if(isset($_GET['chk']) && isset($_GET['schemeid']) && isset($_GET['controlfileds']) ){
		//Check object isn't already in library
		$library= $wpdb->prefix . 'koralibrary';
		$kid = $_GET['chk'];
        $schemeid = $_GET['schemeid'];
        $controlfileds  = $_GET['controlfileds'];
		$query="SELECT * FROM  $library where KID='$kid'";
        $image_control = $_GET['image_control'];
        $video_control = $_GET['video_control'];
        $audio_control = $_GET['audio_control'];
        
     //   echo $video_control;
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
             
       
              
        }
         $control_save = 'schemeid_'.$schemeid.",";
               $control_save .= implode(",", $controlfileds);
         
              $libtable = $wpdb->prefix . "koralibrary";
              $wpdb->insert(
				$wpdb->prefix . "koralibrary",
				array(
					'KID' => $kid,
					'url' => $src,
					'title' => $title,
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
					'%s'
				)
			);
          $rec=array(
				'KID' => $kid,
				'url' => $src,
				'title' => $title,
				'thumb' => $thumb_src,
                  'display' => $control_save,
                  'imagefield' => $image_control,
                  'videofield' => $video_control,
                  'audiofield' => $audio_control
	    );
          echo json_encode($rec);
    }else{
      echo json_encode(false);
    }

  }else{
			echo json_encode(false);
		
  }
	
?>