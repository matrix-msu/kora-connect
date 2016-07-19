<?php
require_once("../../../../wp-blog-header.php");
global $wpdb;

$gallery_n=stripslashes($_GET['gallery']);
$gallery_nam=stripslashes(str_replace(" ", "_", $gallery_n));
		//remove apostrophe
$gal = str_replace("'","Char_39__", html_entity_decode($gallery_nam, ENT_QUOTES)); 
$gallery=$wpdb->prefix.$gal;

	echo '<h3>'.$gallery_n.'</h3>';

if(empty($wpdb->get_results("SELECT * FROM $gallery"))){
	echo "PLEASE SELECT ONE GALLERY";
}

foreach($wpdb->get_results("SELECT * FROM $gallery") as $key => $row){
	$url = preg_replace('/ /','%20',$row->url);

	echo "<div class='kora-obj' id=".$row->KID.">
		<div class='kora-obj-left'>";
		if($row->imagefield!='default'){
			echo  "	<img src=".str_replace($row->KID,"thumbs/".$row->KID,$url)." alt=".$row->KID." />";
		}
		else if($row->audiofield!='default'){
			echo '<video width="142" height="140" controls><source src="'.$url.'" type="audio/mpeg"></video>';


		}else if($row->videofield!='default'){
			echo '<video width="142" height="140" controls><source src="'.$url.'" type="video/mp4"></video>';
  
		}
		echo "</div>
		<div class='kora-obj-right'>
			<ul class='kora-obj-fields'>
				<li><span>KID: </span>".$row->KID."</li>
				<li><span>Title: </span>".$row->title."</li>
				<li><span>Direct Link: </span><a target='_blank' href='".$row->url."'>Click Here!</a></li>
			</ul>
		</div>
	</div>";

}