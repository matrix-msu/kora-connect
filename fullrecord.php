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
	$display = 'detail';
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
	echo $replace2;
	
	?>
