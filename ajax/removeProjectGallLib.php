<?php
require_once("../../../../wp-blog-header.php");
global $wpdb;
//remove all items from library with that pid
if(isset($_GET['pid'])){
	$pid = $_GET['pid'];
	$library= $wpdb->prefix . 'koralibrary';

	$wpdb->show_errors();
	$wpdb->query(
		"
		DELETE FROM $library
		 WHERE KID like '$pid%'
		"
	);
	$wpdb->print_error();
//remove in all galleries the items with that pid
	$gallery = $wpdb->prefix .'koragallery';
	$titles=$wpdb->get_results("SELECT title FROM $gallery");
	
	foreach ($titles as $t){
		$gall=str_replace(" ", "_", $t->title);
		$gal_table= $wpdb->prefix.$gall;
		$wpdb->show_errors();
		$wpdb->query(
			"
			DELETE FROM $gal_table
			 WHERE KID like '$pid%'
			"
		);	
		$wpdb->print_error();
	}
	
}
	

?>