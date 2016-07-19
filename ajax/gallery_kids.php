<?php
require_once("../../../../wp-blog-header.php");
global $wpdb;

$gallery_n = $_GET['gallery'];
$gallery_nam=stripslashes(str_replace(" ", "_", $gallery_n));
		//remove apostrophe
$gall = str_replace("'","Char_39__", html_entity_decode($gallery_nam, ENT_QUOTES)); 

$gallery= $wpdb->prefix.$gall;

$out = array();
if(sizeof($wpdb->get_results("SELECT * FROM $gallery")) == 1)
{
	foreach($wpdb->get_results("SELECT * FROM $gallery") as $key => $row)
	{
		$kid = "kid,eq,".$row->KID;
		array_push($out, $kid);
	}
}

else
{
	foreach($wpdb->get_results("SELECT * FROM $gallery") as $key => $row)
	{
		$kid = "(kid,eq,".$row->KID.")";
		array_push($out, $kid);
	}
}

echo implode(',or,', $out);
?>