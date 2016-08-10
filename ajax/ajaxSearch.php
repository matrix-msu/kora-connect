<?php
	require_once("../../../../wp-blog-header.php");
    require_once( realpath( dirname(__FILE__) . "/../../../../wp-includes/wp-db.php" ) );
	require_once( realpath( dirname(__FILE__) . "/../../../../wp-blog-header.php"));
	require_once( realpath( dirname(__FILE__) . "/../dbconfig.php" ) );

	global $wpdb;
  
	define('KORA_PLUGIN_RESTFUL_SUBPATH', 'api/restful.php');
    define('KORA_PLUGIN_PATHBASE', plugin_dir_url(__FILE__));
    
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
                if ($_GET['sid'] && $_GET['controls']) {
                    //get search information
                    $schemeid = $_GET['sid'];
                    $controls = $_GET['controls'];
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
                        //echo $sid." ".$pid."<br>";
                        if (in_array($sid, $dbscheme) && in_array($pid, $dbproj)){
                            $pos = array_search($pid, $dbproj);
                            $sid_pid_token = array('schemeid' => $sid, 'projectid' => $pid, 'token' => $dbtoken[$pos]);
                           // $val = $sid."-".$pid."-".$dbtoken[$pos];
                            //array_push($sid_pid_token,$val);
                        }
                    }
                    $stmt->close();
                   // var_dump($sid_pid_token);
                    
                    //korasearch
                    
                    if ($_GET['keyword']) {
                        echo $_GET['keyword'];
                    } else {
			                    $query = "";
                                $restful = get_option('kordat_dbapi');
			                    $restful_url =$restful . KORA_PLUGIN_RESTFUL_SUBPATH;
			
			                   
			                    $i = 0;
			                    $fields = "";
			                    //$num_option = count($controls);
                                foreach($controls as $option_value) {
                                   $fields .= $option_value.',';
                                }
                                if (!in_array("Thumbnail", $controls)) {
                                    $fields .= "Thumbnail,";
                                }
                                if (!in_array("Multi-Part Associator", $controls)) {
                                    $fields .= "Multi-Part Associator,";
                                }
                                if (!in_array("Image", $controls)) {
                                    $fields .= "Image,";
                                }
                                if (!in_array("Audio File", $controls)) {
                                    $fields .= "Audio File,";
                                }
                                if (!in_array("Video File", $controls)) {
                                    $fields .= "Video File,";
                                }
                                $lastC = substr($fields, -1);
                                if ($lastC == ',') {
                                    $fields = trim($fields, ",");
                                } 
                                //var_dump($fields);
                               //$fields = 'ALL';
                               // $display='plugin';
                               $display = 'json';
                               if (!empty($sid_pid_token)) {
                                   $url = $restful_url.'?request=GET&pid='.$sid_pid_token['projectid'].'&sid='.$sid_pid_token['schemeid'].'&token='.$sid_pid_token['token'].'&display='.urlencode($display).'&fields='.urlencode($fields);
                               }
                /*
                	<div class='kora-obj'>
				<div class='kora-obj-left'>
					<img src='http://img.photobucket.com/albums/v516/MizGrace/babyhedgehoginbubblebath.jpg' alt='32-131-2'>
					<input type='button' value='edit details'>
				</div>
				<div class='kora-obj-right'>
					<div class='kora-obj-close'>
						<img src='../wp-content/plugins/kora/images/Close - Tiny.svg'>
					</div>
					<ul class='kora-obj-fields'>
						<li><span>KID:</span> 32-131-2</li> 
						<li><span>Field Name:</span> Field Input</li>
						<li><span>Field Name:</span> Field Input</li>
						<li><span>Field Name:</span> Field Input</li>
						<li><span>Field Name:</span> Field Input. Lorem ipsum dolor sit amet, consectetur adipiscing elit, </li>
						<li><span>Field Name:</span> Field Input</li>
					</ul>
				</div>
			</div>   http://kora.matrix.msu.edu/files/119/709/thumbs/77-2C5-0-217-NEH_SAKALY003_195801_001.jpg
                     http://kora.matrix.msu.edu/files/119/709/thumbs/77-2C5-15-217-EAP449_Cisse_0019.jpg
                */
                                
                               // var_dump($url);
                                
                                //initialize post request to KORA API using curl
                               $ch = curl_init($url);
                               curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                               curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);

                               //capture results and display
                               $obj_json = curl_exec($ch);
                               //convert json string to php array
                               $server_output = json_decode($obj_json, true);
                               //var_dump($server_output);
                               foreach($server_output as $record) {
                                   if($record['Image'] || $record['Thumbnail'] ) {
                                       if ($record['Image']) {
                                            $src = get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid'].'/thumbs/'.$record['Image']['localName'];
                                       } else if ($record['Thumbnail']['localName']) {
                                           $src = getThumbnailKORA($record['Thumbnail']['localName']);
                                       }
                                      
                                   } else {
                                       $src = "http://img.photobucket.com/albums/v516/MizGrace/babyhedgehoginbubblebath.jpg";
                                   }
                                   echo "<div class='kora-obj'>
				                            <div class='kora-obj-left'>
					                            <img src='".$src."' alt='".$record['kid']."'>
					                            <input type='button' value='edit details'>
				                            </div>
				                            <div class='kora-obj-right'>
					                            <div class='kora-obj-close'>
						                        <img src='../wp-content/plugins/kora/images/Close - Tiny.svg'>
					                            </div>
					                            <ul class='kora-obj-fields'>
						                            <li><span>KID:</span>".$record['kid']."</li>";
                                                     foreach($controls as $field) {
                                                         if (is_array($record[$field])) {
                                                              echo "<li><span>".$field.":</spn> ".implode(" ",$record[$field])."</li>";
                                                         } else {
                                                             echo "<li><span>".$field.":</spn> ".$record[$field]."</li>";
                                                         }
                                                     }
						                            //<li><span>Field Name:</span> Field Input</li>
					                            echo "</ul>
				                           </div>
			                        </div>";
                               }
                               /*        
                                       $searchResults = explode("<div class='koraobj_container'", $server_output, -1);
                                       $resultList =array();
                                       $count = 0;
              
                                       foreach($searchResults as $val) {
                                           if (!empty($val)) {
                                               $resultList[] = "<div class='koraobj_container'".$val;
                                           }
                                          
                                       }
                                //var_dump($resultList);
                                $showList =array();
                                $fieldsList = array();
                                foreach($resultList as $val){
                                     $xpath = new DOMXPath(@DOMDocument::loadHTML($val));
                                     $entries = $xpath->query("//div[@class='koraobj_container']");
                                   //  var_dump($entries);
                                     $thumb_src = $xpath->evaluate("string(//img/@src)");
                                     $video_src = $xpath->evaluate("string(//video/@src)");
                                     $audio_src = $xpath->evaluate("string(//audio/@src)");

                                      $kid = $xpath->evaluate("string(/html/body/div/div/div[2])");
                                      $title = $xpath->evaluate("string(/html/body/div/div[3]/div[2])");
                                      $showList[] = array('kid' => $kid, 'img_src' => $img_src, 'thumb_src' => $thumb_src, 'video_src' => $video_src, 'audio_src' => $audio_src);
                                      $fieldsItem = array();
                                      foreach($controls as $control) {
                                          $className = "koraobj_control koraobj_control_".$control;
                                          $query_label = '//div[@class="'.$className.'"]/div[@class="koraobj_control_label"]';
                                          $query_value = '//div[@class="'.$className.'"]/div[@class="koraobj_control_value"]';
                                          $fieldsItem[] = array($xpath->query($query_label)->item(0)->nodeValue, $xpath->query($query_value)->item(0)->nodeValue); 
                                      }
                                      $fieldsList[] = $fieldsItem;
                                     
                                     // $date = $xpath->query('//div[contains(@class,"koraobj_control koraobj_control_Date Digital")]');
                                      
                                     //var_dump($kid);
                                     //var_dump($title);
                                }  
                                //var_dump($fieldsList); 
                                $server_output_final ='';
                                $count = 0;
                                foreach($showList as $val){
                                    $item = "<div class='kora-obj'>
				                                <div class='kora-obj-left'>
					                                <img src='".$val['thumb_src']."' alt='".$val['kid']."'>
					                                <input type='button' value='edit details'>
				                                </div>
				                             <div class='kora-obj-right'>
					                            <div class='kora-obj-close'>
						                            <img src='../wp-content/plugins/kora/images/Close - Tiny.svg'>
					                            </div>
					                            <ul class='kora-obj-fields'>
						                            <li><span>KID:</span>".$val['kid']."</li> ";
                                           foreach($fieldsList[$count] as $fItem) {
                                               $item .= "<li><span>".$fItem[0].":</span>".$fItem[1]."</li>";
                                           }      
                                           $count += 1;   
                                          
					                        $item .=  "</ul>
				                            </div>
			                             </div>";
                                    $server_output_final.= $item;
                                }
                                //var_dump($controls[0]);
                                echo $server_output_final;*/
                                
                                
                                
                                
                                 //$xpath = new DOMXPath(@DOMDocument::loadHTML($server_output));
                                // $thumb_src = $xpath->evaluate("string(//img/@src)");
                                 //var_dump($thumb_src);
                              //   $server_output = implode("",$resultList);
                                

                                //  var_dump(count($searchResults));
                                 /*for($i = 0; i< count($searchResults); $i++) {
                                     if (!empty($searchResults[$i])) {
                                          $searchResults[$i] = "<div class='koraobj_container".$searchResults[$i];
                                     }
                                 }
                                 var_dump($searchResults);*/
                                // echo $server_output;
                                // var_dump($server_output);
                 				/*if(is_array($server_output)){
                                    foreach($server_output as $value){
                                        //gets url of image
                                        if($value == ''){
                                            echo "Search Returned No Results, Try Again";
                                        }
                                        else{
                                            $xpath = new DOMXPath(@DOMDocument::loadHTML($value));
                                            $thumb_src = $xpath->evaluate("string(//img/@src)");
                                            $src = str_replace("thumbs/", "", $thumb_src);
                                            
                                            //Get KID from HTML
                                            $kid = $xpath->evaluate("string(/html/body/div/div/div[2])");
                                            $title = $xpath->evaluate("string(/html/body/div/div[3]/div[2])");
                                            
                                        }
                                    }
                                }*/
		

                    }
                 } else {
                     echo "<h1>Please choosing Scheme and Controls first!</h1>";
                }
     }  


?>