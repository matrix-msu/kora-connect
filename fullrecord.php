<link rel="stylesheet" type="text/css" href="CSS/fullrecord.css">
<?php
	//define('WP_USE_THEMES', true);
require_once( realpath( dirname(__FILE__) . "/../../../wp-blog-header.php"));
require_once( realpath( dirname(__FILE__) . "/dbconfig.php" ) );

define('WP_USE_THEMES', true);
/** Loads the WordPress Environment and Template */
	global $wp, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header;
	$user = kordat_dbuser;
	$pass = kordat_dbpass;
	$display = 'json';
	$k = $_GET["kid"];
	$restful_url=$_POST['restful'];
	$query = "KID,=,".$k;
	//$fields = 'ALL';

	if(isset($_POST['fields'])){
		$fields = $_POST['fields'];
	} else {
		$fields = 'ALL';
	}
	$url = $restful_url.urlencode($display).'&fields='.urlencode($fields).'&query='.urlencode($query);
	///initialize post request to KORA API using curl
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);

	///capture results and display
	$server_output = curl_exec($ch);
	
	 $detail=str_replace("koraobj_container","container_full",$server_output);
	 $replace=str_replace("koraobj_control","control_full",$detail);
	 $replace1=str_replace("koraobj_control_label","control_label_full",$replace);
	 $replace2=str_replace("koraobj_control_value","control_value_full",$replace1);
     echo "<a id='backArrow' href='javascript:history.go(-1)'><img src='images/Arrow-Left.svg'/></a>";
	//echo $replace2;
	
	$server_output = json_decode($server_output, true);
	
	//var_dump($restful_url);
	
	//var_dump($k);
	
	
	foreach ($server_output as $kid => $koraobj)
			{
				$htmlout .= "<div class='koraobj_container'>\n";
				// OUTPUT THE KID ALSO
				$htmlout .= "\t<div class='koraobj_control koraobj_control_kid' ><div class='koraobj_control_label'>KID</div>";
				$htmlout .= "<div class='koraobj_control_value'>".$kid."</div></div>\n";
				
				$newArr;
				
				foreach ($koraobj as $dfield => $dvalue)
				{	
			
					if (is_array($dvalue)) {
						$arStr = '';
						foreach( $dvalue as $key => $value ) {
							if ($value != '') {
								$arStr .= $value."\n";
							}							
						}
						
						$newArr [] = $arStr;
					} else {
						$newArr [] = $dvalue;
					}
					if (!(isset($dvalue))){
						//var_dump($dvalue);
						//var_dump("empty");
					}
					
						// SKIP THIS ROW UNLESS SHOWEMPTY HAS BEEN SET TO TRUE
						//if ( $koraobj == null ||  $koraobj == '') { continue; } 
/*
						//$ctlDisplay = new $controls[$dfield]['type']($pid,$controls[$dfield]['cid'],$kid);
						if(is_a($ctlDisplay,'ImageControl')){
							$htmlout .= "\t<div class='koraobj_control koraobj_control_".htmlentities($controls[$dfield]['name'],ENT_QUOTES)."' ><div class='koraobj_control_label'>".$controls[$dfield]['name']."</div>";
							$imagecontrolstring = $ctlDisplay->showData();
							$imagepos = strrpos($imagecontrolstring,"<div class");
							$htmlout .= "<div class='koraobj_control_value'>" . 
							     substr($imagecontrolstring,0, $imagepos) . 
							     "</div></div>\n";
							$htmlpictureout .= "<div class='koraobj_control_value'>" . 
							     substr($imagecontrolstring,$imagepos) . 
							     "</div>";
						}else if(is_a($ctlDisplay,'FileControl')){
							$htmlout .= "\t<div class='koraobj_control koraobj_control_".htmlentities($controls[$dfield]['name'],ENT_QUOTES)."' ><div class='koraobj_control_label'>".$controls[$dfield]['name']."</div>";
							$htmlout .= "<div class='koraobj_control_value'>" . 
							     $ctlDisplay->showData() . 
								"</div></div>\n";
							if(strpos($htmlout,'Audio:') !== false){
								$audvidcontrolstring = $ctlDisplay->showData();
								$part1 = explode('<audio src',$audvidcontrolstring)[1];
								$part2 = explode('</audio>',$part1)[0];
								$htmlpictureout .= "<div class='koraobj_control_value'><audio src" . 
							     $part2 . 
							     "</audio></div>";
							}
							if(strpos($htmlout,'Video:') !== false){
								$audvidcontrolstring = $ctlDisplay->showData();
								$part1 = explode('<video src',$audvidcontrolstring)[1];
								$part2 = explode('</video>',$part1)[0];
								$htmlpictureout .= "<div class='koraobj_control_value'><video src" . 
							     $part2 . 
							     "</video></div>";
							}
						}
						else {
							$htmlout .= "\t<div class='koraobj_control koraobj_control_".htmlentities($controls[$dfield]['name'],ENT_QUOTES)."' ><div class='koraobj_control_label'>".$controls[$dfield]['name']."</div>";
							$htmlout .= "<div class='koraobj_control_value'>" . 
							     $ctlDisplay->showData() . 
								"</div></div>\n";
*/
						//}

					//}
				}
				
				$htmlout .= "</div>\n";
				//echo($htmlout);
				
			}
			var_dump($server_output);
	var_dump($newArr);
	?>
