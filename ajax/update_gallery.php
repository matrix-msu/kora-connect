<?php

	require_once("../../../../wp-blog-header.php");
	require_once( realpath( dirname(__FILE__) . "/../dbconfig.php" ) );

	global $wpdb;
	
	if(isset($_GET['chk']) && isset($_GET['schemeid']) && isset($_GET['controlfields']) && isset($_GET['gallery_name'])){
		//Check object isn't already in gallery
		$gallery= $_GET['gallery_name'];
		$kid = $_GET['chk'];
        $schemeid = $_GET['schemeid'];
        $controlfields  = $_GET['controlfields'];
	    $wpdb->update( $gallery, array( 'display' => $controlfields ), array( 'KID' => $kid ));
	   echo true;
    }else{
    	echo false;
    }
	
?>
