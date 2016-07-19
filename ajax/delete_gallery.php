<?php

require_once("../../../../wp-blog-header.php");
global $wpdb;
if(isset($_REQUEST['kid']) && isset($_REQUEST['gallery_name'])){
	
		echo $_REQUEST['gallery_name'];
		$kid = $_REQUEST['kid'];
		$gallery = $_REQUEST['gallery_name'];
		echo $kid;
		echo "DELETE FROM ".$gallery." WHERE KID like '".$kid."'";
		$wpdb->show_errors();
	   $wpdb->query("DELETE FROM ".$gallery." WHERE KID like '".$kid."'");

		$wpdb->print_error();
}

if(isset($_REQUEST['gallery']) && isset($_REQUEST['id'])){
	$gallery_name = $_REQUEST['gallery'];
	$id = $_REQUEST['id'];
	$wpdb->show_errors();
	$wpdb->query("DROP TABLE ".$gallery_name);
	//$id = explode('_', $id);
	$wpdb->query("DELETE FROM ".$wpdb->prefix.koragallery." WHERE id = '".$id."'");
	$wpdb->print_error();
}

?>
