<link rel="stylesheet" type="text/css" href="CSS/fullrecord.css">
<?php
	//define('WP_USE_THEMES', true);
require_once( realpath( dirname(__FILE__) . "/../../../wp-blog-header.php"));
require_once( realpath( dirname(__FILE__) . "/dbconfig.php" ) );

define('WP_USE_THEMES', true);
/** Loads the WordPress Environment and Template */
	global $wp, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header;
//	$user = kordat_dbuser;
//	$pass = kordat_dbpass;
	$display = 'json';
	$k = $_GET["kid"];
	$restful_url=$_POST['restful'];
	$query = "KID,=,".$k;
	if(isset($_POST['fields'])){
		$fields = $_POST['fields'];
	} else {
		$fields = 'ALL';
	}
	$url = $restful_url.urlencode($display).'&fields='.urlencode($fields).'&query='.urlencode($query);
	///initialize post request to KORA API using curl
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);

	///capture results and display
	$server_output = curl_exec($ch);
	
    echo "<a id='backArrow' href='javascript:history.go(-1)'><img src='images/Arrow-Left.svg'/></a>";
	
	$server_output = json_decode($server_output, true);


		//preview of media if present 
		/*foreach ($server_output as $kid => $koraobj) {	
				
			$decKID = explode("-", $k);
			
			if ($koraobj["Video File"] != '') {
				$prevHTML = '<div class="control_full_value">';
				$prevHTML .= '<div class="kc_file_tn">';
				$prevHTML .= '<video controls><source src="' . kora_url . 'files/' . hexdec($decKID[0]) . '/' . hexdec($decKID[1]) . '/' .  $koraobj["Video File"]['localName'] . '"></video>';
				$prevHTML .= '</div></div>';
				echo($prevHTML);
			} else if ($koraobj["Audio File"] != '') {
				$prevHTML .= '<div class="control_full_value">';
				$prevHTML .= '<div class="kc_file_tn">';
				$prevHTML .= '<audio controls><source src="' . kora_url . 'files/' . hexdec($decKID[0]) . '/' . hexdec($decKID[1]) . '/' .  $koraobj["Audio File"]['localName'] . '"></audio>';
				$prevHTML .= '</div></div>';
				echo($prevHTML);
			} else if ($koraobj["Image"] != '') {
				$prevHTML = '<div class="control_full_value">';
				$prevHTML .= '<div class="kc_file_tn">';
				$prevHTML .= '<img src="' . kora_url . 'files/' . hexdec($decKID[0]) . '/' . hexdec($decKID[1]) . '/' .  $koraobj["Image"]['localName'] . '">';
				$prevHTML .= '</div></div>';
				echo($prevHTML);
			}	
		}*/
		$prevHTML = '<div class="control_full_value">';
		$prevHTML .= '<div class="kc_file_tn">';
		$prevHTML .= stripslashes($_POST['media']);

		$prevHTML .= '</div></div>';

		echo($prevHTML);

		?>		
	
	<?php //metadata display  ?>
	<div class="container_full">
	<?php 
	foreach ($server_output as $kid => $koraobj) {
		$htmlout .= "<div class='koraobj_container'>\n";				
		foreach ($koraobj as $dfield => $dvalue) {	
			if (is_array($dvalue)) {
				
				foreach( $dvalue as $key => $value ) {
					if ($value != '') {
						
						
						$htmlout .= "\t<div class='koraobj_control control_full koraobj_control_".$key."' ><div class='koraobj_control_label'>".$key."</div>";
						$pos = strpos($value, "http");
						
					if(is_int($pos)){
						$htmlout .= "<div class='koraobj_control_value'><a href='" . $value.  "'>".$value."</a></div></div>\n";	
					}else{
						$htmlout .= "<div class='koraobj_control_value'>" . $value.  "</div></div>\n";
						
					}					}
				}
			} else {
				if ($dvalue != '') {
					
					$htmlout .= "\t<div class='koraobj_control control_full koraobj_control_".$dfield."' ><div class='koraobj_control_label control_full_label'>".$dfield."</div>";
					$pos = strpos($dvalue, "http");
				if(is_int($pos)){
						$htmlout .= "<div class='koraobj_control_value'><a href='" . $dvalue.  "'>".$dvalue."</a></div></div>\n";	
					}else{
						$htmlout .= "<div class='koraobj_control_value'>" . $dvalue.  "</div></div>\n";
						
					}
				}
			}
		}
		$htmlout .= "</div>\n";
		echo($htmlout);
	}
	?>
	
	</div>
