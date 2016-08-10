

<?php
require_once("../../../../wp-blog-header.php");
global $wpdb;
if(isset($_GET['kid'])){
	if(isset($_GET['library'])){
		$kid = $_GET['kid'];
		$library = $_GET['library'];
		$library = $wpdb -> prefix.$library;
		var_dump($kid);
		$wpdb->show_errors();
		var_dump($kid);
		var_dump($library);
		$wpdb->query(
			"
			DELETE FROM $library
			 WHERE KID = '$kid'
			"
		);
		$wpdb->print_error();
	}
}

?>