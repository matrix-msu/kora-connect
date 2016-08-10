<?php
	define('WP_USE_THEMES', true);
	global $wp, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header;
	require('../../../wp-load.php');
	require_once( realpath( dirname(__FILE__) . "/confi.php" ) );
	/*$files = glob("../../../wp-includes" . '/*.php');
	var_dump($files);
	foreach ($files as $file) {
    	require_once($file);   
	}*/
	//require_once("../../../wp-includes/general-template.php");
	$theme = wp_get_theme();
	get_header($theme);
	get_sidebar($theme);
	show_admin_bar(true);	
	/*$themeDir = $_POST['theme'];	
	$token = $_POST['token'];
	$pid = $_POST["pid"];
	$sid = $_POST['sid'];*/
	$user = $_POST['user'];  
	$pass = $_POST['pass'];
	
	
	$restful_url = $_POST['restful'];
	$display = 'html';
	$k = $_GET["kid"];
	$query = "KID,=,".$k;
	$fields = 'ALL';
	$url = $restful_url.urlencode($display).'&fields='.urlencode($fields).'&query='.urlencode($query);
			
	///initialize post request to KORA API using curl
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);

	///capture results and display
	$server_output = curl_exec($ch);
	echo $server_output;
	get_sidebar($theme);
	get_footer($theme);
	?>