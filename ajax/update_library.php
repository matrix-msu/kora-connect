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
		//$query="UPDATE '$library' SET Display = '"$controlfileds"' WHERE KID = '".$kid."'";
        //$wpdb->query($query);
        $wpdb->update( $library, array( 'Display' => $controlfileds ), array( 'KID' => $kid ));
    }
	//var_dump($wpdb->get_results("SELECT * FROM  $library"));
?>
