<?php

/**
* Plugin Name: KORA Database Display
* Plugin URI: TBD
* Description: Plugin for displaying information from a KORA database.
* Author: MATRIX: The Center for Digital Humanities and Social Sciences
* Version: 2.0
* Author URI: TBD
 */
require_once( plugin_dir_path( __FILE__ ) . 'create_template.php' );
require_once( realpath( dirname(__FILE__) . "/dbconfig.php" ) );

define('KORA_PLUGIN_PATHBASE', plugin_dir_url(__FILE__));
define('KORA_PLUGIN_BLOCKUI_CDN', '//cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.66.0-2013.10.09/jquery.blockUI.min.js');
define('KORA_PLUGIN_FILES_SUBPATH', 'files/');
define('KORA_PLUGIN_RESTFUL_SUBPATH', 'api/restful.php');
define('KORA_RESTFUL_MAX_CALL', 5);
//***********************************************
// DATABASE INSTALL
//***********************************************

global $wpdb;
global $kora_db_version;
//New version now? 2.6.0 is current
$kora_db_version = "1.0";


function kora_install() {
	//Why is this repeated?
	global $wpdb;
	global $kora_db_version;

	$table_name = $wpdb->prefix . "koralibrary";

    //will need changes depending on what fields are needed
	$sql = "CREATE TABLE $table_name (
		  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
		  `KID` varchar(45) NOT NULL,
		  `url` varchar(10000) DEFAULT '',
		  `thumb` varchar(10000) DEFAULT '',
		  `title` varchar(999) DEFAULT NULL,
		  `Display` text,
		  `imagefield` varchar(10000) DEFAULT NULL,
		  `audiofield` varchar(10000) DEFAULT NULL,
		  `videofield` varchar(10000) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=349 DEFAULT CHARSET=latin1;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	//create gallery table
	$table_name = $wpdb->prefix . "koragallery";
	$sql2 = "CREATE TABLE $table_name (
		  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
		  `title` varchar(999) DEFAULT NULL,
		  `description` varchar(1000) DEFAULT NULL,
		  `imagefield` varchar(10000) DEFAULT NULL,
		  `audiofield` varchar(10000) DEFAULT NULL,
		  `videofield` varchar(10000) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=179 DEFAULT CHARSET=latin1;

	);";
	dbDelta( $sql2 );
	add_option( "kora_db_version", $kora_db_version );
	// If this file is called directly, abort.
	if (!defined( 'WPINC' )) { die;}

	add_action( 'plugins_loaded', array( 'Page_Template_Plugin', 'get_instance' ) );
	if ( ! $the_page ) {
	    // Create post object
	    $_p = array();
	    $_p['post_title'] = $the_page_title;
			$_p['post_status'] = 'publish';
			$_p['post_name']='full-record';
	    $_p['post_type'] = 'page';
	    $_p['comment_status'] = 'closed';
	    $_p['ping_status'] = 'closed';
	    $_p['post_category'] = array(1); // the default 'Uncategorised'
	    $_p['page_template']='detail.php';

	    // Insert the post into the database
	    $the_page_id = wp_insert_post( $_p );
	} else {
	    // the plugin may have been previously active and the page may just be trashed...
	    $the_page_id = $the_page->ID;

	    //make sure the page is not trashed...
	    $the_page->post_status = 'publish';
	    $the_page_id = wp_update_post( $the_page );
	}

	delete_option( 'my_plugin_page_id' );
	add_option( 'my_plugin_page_id', $the_page_id );
}

function my_plugin_remove() {

    global $wpdb;

    $the_page_title = get_option( "my_plugin_page_title" );
    $the_page_name = get_option( "my_plugin_page_name" );

    //  the id of our page...
    $the_page_id = get_option( 'my_plugin_page_id' );
    if( $the_page_id ) {

        wp_delete_post( $the_page_id ); // this will trash, not delete

    }

    delete_option("my_plugin_page_title");
    delete_option("my_plugin_page_name");
    delete_option("my_plugin_page_id");
}

register_activation_hook(__FILE__, 'kora_install');
/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'my_plugin_remove' );

//***********************************************
// ADMIN MENU STUFF
//***********************************************
///include import file


function kordat_admin() {
    include('kora_database_admin.php');
}

function kordat_gallery() {
	include('kora_gallery.php');
}

function kordat_library() {
	include('kora_library.php');
}

function kordat_new_kora_obj() {
	include('kora_newobj.php');
}

//Adds KORA section in the sidebar along with subsections
function kordat_admin_menu() {
	// add_menu_page( $page_title, $menu_title, $capability, $menu_slug op, $function op, $icon_url op, $position op );
	add_menu_page( "KoraConnect", "KoraConnect", 1, "KORA_Settings", "kordat_admin");
		//plugins_url("kora/images/Close.svg") );

	// add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function op );
	add_submenu_page("KORA_Settings", "Connect", "Connect", 1, "KORA_Settings", "kordat_admin");
   	add_submenu_page("KORA_Settings", "Galleries", "Galleries", 1, "Galleries", "kordat_gallery" );
   	add_submenu_page("KORA_Settings", "Library", "Library", 1, "Library", "kordat_library" );
    add_submenu_page("KORA_Settings", "Add New Kora Object", "Add New Kora Object", 1, "Add_New_KORA_Object", "kordat_new_kora_obj");
}



add_action('admin_menu', 'kordat_admin_menu');
//add_action( 'admin_footer-post-new.php', 'wpse_78881_script' );

// REGISTER THE JQUERY BLOCKUI FROM CDN
wp_enqueue_script(
	'blockui',
	KORA_PLUGIN_BLOCKUI_CDN,
	array('jquery') );
wp_enqueue_script(
	'spin',
	KORA_PLUGIN_PATHBASE.'/js/spin.js',
	array('jquery')	);

wp_enqueue_style(
	'kora',
	KORA_PLUGIN_PATHBASE.'/kora.css');

wp_enqueue_script(
	'kora',
	KORA_PLUGIN_PATHBASE.'/js/kora.js',
	array('jquery')	);

wp_enqueue_script(
	'infscroll',
	KORA_PLUGIN_PATHBASE.'/js/infinitescroll.js',
	array('jquery')	);


//add_action('media_buttons', 'add_my_media_button');
function add_my_media_button() {

    ?>
    <script>
                jQuery(document).ready(function() {
                    //alert(plugin.url);
                    jQuery('<a href="#"  id="kora-upload" class="button">Add New Kora Object</a>').insertAfter('.wp-media-buttons');

                    jQuery('#kora-upload').click(function(){

                        tb_show('Add New Kora Object',plugin.url+'/kora_upload.php?pid='+plugin.pid+
                        '&sid='+plugin.sid+'&token='+plugin.token+'&user='+plugin.user+'&pass='+plugin.pass+'&restful='+plugin.restful+'&url='+plugin.url+
                        '&height=600&width=400&TB_iframe=true');

                        return false;
                    });

                jQuery('<a href="#"  id="kora-gallery" class="button">Add Kora Gallery</a>').insertAfter('#kora-upload');

                    jQuery('#kora-gallery').click(function(){

                        tb_show('Add Kora Gallery',plugin.url+'/postgallery.php?pid='+plugin.pid+
                        '&sid='+plugin.sid+'&token='+plugin.token+'&user='+plugin.user+'&pass='+plugin.pass+'&restful='+plugin.restful+'&url='+plugin.url+
                        '&height=600&width=400&TB_iframe=true');

                    return false;
                    });

                jQuery('<a href="#"  id="kora-library" class="button">Add Kora Object From Library</a>').insertAfter('#kora-upload');

                    jQuery("#kora-library").click(function(){

                        tb_show('Add Kora Object From Library',plugin.url+'/postlibrary.php?pid='+plugin.pid+
                        '&sid='+plugin.sid+'&token='+plugin.token+'&user='+plugin.user+'&pass='+plugin.pass+'&restful='+plugin.restful+'&url='+plugin.url+
                        '&height=600&width=400&TB_iframe=true');
                    });

                });
     </script>

    <?
}
add_action('media_buttons', 'add_my_media_button', 15);

function mediabutton(){
	wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
	wp_register_script( 'mediabutton', ''.KORA_PLUGIN_PATHBASE.'addkoraobject.js', array(), null,true);
    wp_enqueue_script( 'mediabutton');
	//................
	$project=get_option('kordat_dbproj');
	$scheme=get_option('kordat_dbscheme');
	$token=get_option('kordat_dbtoken');
    $plugin = array( 'url' => KORA_PLUGIN_PATHBASE,
    			     'pid' => $project,
    				 'sid'=>$scheme,
    				 'token' => $token,
					 'user' => get_option('kordat_dbuser'),
					 'pass' => get_option('kordat_dbpass'),
					 'restful'=> get_option('kordat_dbapi')
				    );
    wp_localize_script('mediabutton', 'plugin', $plugin);
}

function newgallery(){
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_style('thickbox');
	wp_register_script( 'gallerybutton', ''.KORA_PLUGIN_PATHBASE.'addkoragallery.js', array(), null,true);
    wp_enqueue_script( 'gallerybutton');
   	$plugin = array( 'url' => KORA_PLUGIN_PATHBASE,
						'pid' => get_option('kordat_dbproj'),
						'sid' => get_option('kordat_dbscheme'),
						'token'=>get_option('kordat_dbtoken'),
						'scheme_proj_token' => get_option('kordat_scheme_dbproj_token'),

    			   //  'pid' => $project[0],
    				// 'sid'=>$scheme[0],
    				 //'token' => $token[0],
					 'user' => get_option('kordat_dbuser'),
					 'pass' => get_option('kordat_dbpass'),
					 'restful'=> get_option('kordat_dbapi'));
    wp_localize_script('gallerybutton', 'plugin', $plugin);
}

add_action('admin_print_scripts','newgallery');
add_action('admin_print_scripts','mediabutton');

//**********************************************//
// MAIN PLUGIN                                  //
//**********************************************//

///////////////
// KORA GRID //
///////////////
///Tells wordpress to register the shortcode for grid formatting
add_shortcode("KORAGRID", "kordat_handler");
add_shortcode("koragrid", "kordat_handler");
$theme = wp_get_theme();

function kordat_handler($incomingfrompost) {
	//...................
   	$project=get_option('kordat_dbproj');
	$scheme=get_option('kordat_dbscheme');

	/// PROCESS INCOMING ATTS OR SET DEFAULTS
	$incomingfrompost = shortcode_atts(array(
    			     'pid' => $project[0],
    				 'sid'=>$scheme[0],
					  'scheme_proj_token' => get_option('kordat_scheme_dbproj_token'),
		"fields" => '',
		"query" => "KID,!=,\'\'",
		"kg_title" => "Kora Database Grid",
		"kg_perpage" => 10,
		"kg_theme" => "dot-luv",
		"kg_height" => 600,
		"kg_width" => 800,
		"kg_search" => "No",
		), $incomingfrompost);

	/// THIS DOES THE ACTUAL WORK
	$wpoutput = kordat_getrecords($incomingfrompost);
	/// SEND TEXT BACK
	return $wpoutput;
}

///Get records from the database based on user requests
function kordat_getrecords($wpatts) {
	///gather wordpress options
	$restful_url = get_option('kordat_dbapi') . KORA_PLUGIN_RESTFUL_SUBPATH;
	$token = get_option('kordat_dbtoken');
	$user = get_option('kordat_dbuser');
	$pass = get_option('kordat_dbpass');

	///gather formatting options
	$pid = $wpatts['pid'];
	$sid = $wpatts['sid'];
	$gftitle = $wpatts['kg_title'];
	$gfperpage = $wpatts['kg_perpage'];
	$gftheme = $wpatts['kg_theme'];
	$gfheight = $wpatts['kg_height'];
	$gfwidth = $wpatts['kg_width'];
	// HANDLE TRUE/FALSE/YES/NO FOR THIS PROPERTY PASSING IT THEN TO KORAGRID AS EXPECTED
	$gfsearch = (get_bool_setting($wpatts['kg_search'], false)) ? 'Yes' : 'No';

	//advanced filter options
	$query = $wpatts['query'];
	$fieldsarg = ($wpatts['fields'] != '') ? '&fields='.urlencode($wpatts['fields']) : '';
	$display = 'grid';
	$scheme_proj_token = get_option('kordat_scheme_dbproj_token');
	$length_spt=sizeof($scheme_proj_token);
	$url=array();
	for($i=0;$i<length_spt;$i=$i+3){
		$url_new = $restful_url.'?request=GET&pid='.$scheme_proj_token[$i+1].'&sid='.$scheme_proj_token[$i].'&token='.$scheme_proj_token[$i+2].'&display='.urlencode($display).'&gr_title='.urlencode($gftitle).'&gr_pagesize='.urlencode($gfperpage).'&gr_theme='.urlencode($gftheme).'&gr_height='.urlencode($gfheight).'&gr_width='.urlencode($gfwidth).'&gr_search='.urlencode($gfsearch).$fieldsarg.'&query='.urlencode($query);
		array_push($url,$url_new);
	}
	//$url1 = $restful_url.'?request=GET&pid='.$pid[0].'&sid='.$sid[0].'&token='.$token[0].'&display='.urlencode($display).'&gr_title='.urlencode($gftitle).'&gr_pagesize='.urlencode($gfperpage).'&gr_theme='.urlencode($gftheme).'&gr_height='.urlencode($gfheight).'&gr_width='.urlencode($gfwidth).'&gr_search='.urlencode($gfsearch).$fieldsarg.'&query='.urlencode($query);

	///initialize post request to KORA API using curl
	$server_output=array();
	foreach($url as $value){
		$ch = curl_init($value);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);

	///capture results and display
			$server_output1 = array(curl_exec($ch));
		array_push($server_output,$server_output1);
		//$server_output = curl_exec($ch);
		return $server_output;
	}
}

//////////////////
// KORA GALLERY //
//////////////////
///Tells wordpress to register the shortcode for gallery formatting
add_shortcode("KORAGALLERY", "koragallery_handler");
add_shortcode("koragallery", "koragallery_handler");

function koragallery_handler($incomingfrompost) {
	//...................
		$project=get_option('kordat_dbproj');
	$scheme=get_option('kordat_dbscheme');

	/// PROCESS INCOMING ATTS OR SET DEFAULTS
	$incomingfrompost = shortcode_atts(array(
    			     'pid' => $project[0],
    				 'sid'=>$scheme[0],
					 'scheme_proj_token' => get_option('kordat_scheme_dbproj_token'),

		"fields" => "ALL",
		"query" => "KID,!=,\'\'",
		"kg_imagecontrol" => "",
        "kg_audiocontrol" => "",
        "kg_videocontrol" => "",
		"kg_titlecontrol" => "",
		"kg_desccontrol" => "",
		"kg_linkbase" => KORA_PLUGIN_PATHBASE."fullrecord.php",
		"kg_type" => 'flexslider',
		"kg_imagesize" => 'small',
		"kg_sort" => "",
		"kg_order" => "SORT_ASC",
		"kgfs_animation" => 'slide',
		"kgfs_direction" => 'horizontal',
		"kgfs_reverse" => false,
		"kgfs_animationloop" => true,
		"kgfs_smoothheight" => false,
		"kgfs_startat"  => 0,
		"kgfs_slideshow" => true,
		"kgfs_slidshowspeed" => 7000,
		"kgfs_animationspeed" => 600,
		"kgfs_initdelay" => 0,
		"kgfs_randomize" => false,
		"kgfs_pauseonaction" => true,
		"kgfs_pauseonhover" => false,
		"kgfs_touch" => true,
		"kgfs_video" => false,
		"kgfs_itemwidth" => 150,
		"kgfs_itemmargin" => 5,
		"kgfs_minitems" => 0,
		"kgfs_maxitems" => 0,
		"kgfs_imageclip" => false,
		"kgfs_move" => 0,
		"kgis_pagesize" => 20,
		"kgis_loadimg" => KORA_PLUGIN_PATHBASE.'/loading.gif',
		), $incomingfrompost);

	/// THIS DOES THE ACTUAL WORK
	$wpoutput = korgallery_getrecords($incomingfrompost);
	if (!is_wp_error($wpoutput))
	{ return $wpoutput; }
	else
	{ return $wpoutput->get_error_message(); }
}

function korgallery_getrecords($wpatts) {
	///gather wordpress options
		$restful_url = get_option('kordat_dbapi') . KORA_PLUGIN_RESTFUL_SUBPATH;
//	$token = get_option('kordat_dbtoken');
	$user = get_option('kordat_dbuser');
	$pass = get_option('kordat_dbpass');
	$files_url = get_option('kordat_dbapi').KORA_PLUGIN_FILES_SUBPATH;
	$thumbs_url = "${files_url}thumbs/";

	$display = 'html';
	$sid = $wpatts['sid'];
	$dbproj = get_option('kordat_dbproj');
    $dbscheme = get_option('kordat_dbscheme');
    $dbtoken = get_option('kordat_dbtoken');
	/* connect to database*/
	 $mysql_hostname = kordat_dbhostname;
     $mysql_user = kordat_dbhostuser;
     $mysql_database = kordat_dbselectname;
	 $mysql_password = kordat_dbhostpass;
     $bd = new mysqli($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);
     if ($bd->connect_error) {
      die("Connection failed: " . $bd->connect_error);
     }
	$query_sid_pid = "SELECT schemeid,pid FROM scheme";
    $stmt = $bd->prepare($query_sid_pid);
    $stmt->execute();
    $stmt->bind_result($sids,$pids);
    while($stmt->fetch()){
        $sidd = $sids;
        $pidd = $pids;
		if ($sidd == $sid) {
			    if (in_array($sidd, $dbscheme) && in_array($pidd, $dbproj)){
				$pos = array_search($pidd, $dbproj);
				$pid = $pidd;
				$token = $dbtoken[$pos];
				
			}
		}
    }
    $stmt->close();

	$query = $wpatts['query'];
	$kg_type = $wpatts['kg_type'];
	$kg_ictrl = $wpatts['kg_imagecontrol'];
    $kg_actrl = $wpatts['kg_audiocontrol'];
    $kg_vctrl = $wpatts['kg_videocontrol'];
	$kg_tctrl = $wpatts['kg_titlecontrol'];
	$kg_dctrl = $wpatts['kg_desccontrol'];
	$kg_isize = $wpatts['kg_imagesize'];
	$kg_lbase = $wpatts['kg_linkbase'];
	$kgfs_imageclip = $wpatts['kgfs_imageclip'];
	$kg_sort = $wpatts['kg_sort'];
	$kg_order = $wpatts['kg_order'];
	$kg_pagesize = $wpatts['kgis_pagesize'];
	$fields = $wpatts['fields'];

	// IF WE ARE MISSING REQUIRED ATTS FOR THIS CALL, JUST BAIL NOW
	if ($kg_ictrl == '') {
		return new WP_Error('kg_noictl', __('No kg_imagecontrol property was passed to KORAGALLERY shortcode'));
	}

	if ($kg_type == 'infscroll')
	{

		$display = 'json';
		$kg_divtag_opts = '';
		foreach ($wpatts as $k => $v)
		{
			// EACH WPATTT THAT STARTS W/ KGFS IS SENT AS PROPERTY...
			if (preg_match('/^kgis_/', $k))
			{
				$kg_divtag_opts .= "$k='$v' ";
			}
		}
	}
	else if ($kg_type == 'pagination'){
		$display = 'json';
		$kg_divtag_opts = '';
		foreach ($wpatts as $k => $v){
			// EACH WPATTT THAT STARTS W/ KGFS IS SENT AS PROPERTY...
			if (preg_match('/^kgis_/', $k))
			{
				$kg_divtag_opts .= "$k='$v' ";
			}
		}
	}

	$num_picture=substr_count($query,'kid');
	$break_query=explode(',or,',$query,$num_picture);
	//diving queries on 5 per resquest, I will change that to 25 eventually.
	//$subarrays=array_chunk($break_query, 25);
	$counter=0;

	$project_id = array();
	$scheme_id = array();
	$tokens = array();
	$i = 0;
	$projects = get_option('kordat_dbproj');
	$token_option = get_option('kordat_dbtoken');
	foreach ($break_query as $value) {
        $kid=explode(',',$value);
        $matches=explode('-',$kid[2]);

        $matches[2]=trim($matches[2],')');

       $piddec=hexdec($matches[0]);
       $siddec=hexdec($matches[1]);
      
		$key=array_search($piddec,$projects);
		array_push($project_id,$piddec);
		array_push($scheme_id, $siddec);
		$count=count($subarrays[$piddec][$siddec]);
		$subarrays[$piddec][$siddec][$count]=$value;
		$tokens[$piddec]=$token_option[$key];

	}
	
	$num_pics = count($scheme_id);
	$url=array();
	foreach($subarrays as $pid=>$sub){
		
		$new_query='';

		foreach($sub as $sid=>$qu){
			$i=0;
			$qu_sub=array_chunk($qu, 25);
			foreach ($qu_sub as $final_qu) {
				$num_kid=count($final_qu);
				foreach($final_qu as $kid){
					if($i<$num_kid-1){
						$new_query.=$kid.",or,";
					}else{
						$new_query.=$kid;
					}
					$i++;
				}
				$url_new = $restful_url.'?request=GET&pid='.$pid.'&sid='.$sid.'&token='.$tokens[$pid].'&display='.urlencode($display).'&query='.urlencode($new_query).'&sort='.urlencode($kg_sort).'&order='.urlencode($kg_order);
				array_push($url,$url_new);
			}

		}
	//	$url_new = $restful_url.'?request=GET&pid='.$pid.'&sid='.$sid.'&token='.$tokens[$pid].'&display='.urlencode($display).'&query='.urlencode($new_query).'&sort='.urlencode($kg_sort).'&order='.urlencode($kg_order);
	//	array_push($url,$url_new);
	}
	
	$str_url = implode(";", $url);
	$server_output=array();
	switch ($kg_type)
	{
	case 'pagination':
		///initialize post request to KORA API using curl

	foreach($url as $value){
		
		$ch = curl_init($value);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);

		///capture results and display
		$server_output1 = array(curl_exec($ch));
		array_push($server_output,$server_output1);
		
	}
			return "<div class='kora_gallery_pagination' kgictrl='$kg_ictrl' kgactrl='$kg_actrl' kgvctrl='$kg_vctrl' kg_pagesize = '$kg_pagesize' kgisize='$kg_isize' kgtctrl='$kg_tctrl' kgdctrl='$kg_dctrl' kglbase='$kg_lbase' kgfs_imageclip='$kgfs_imageclip' kgresturl='$str_url' kgfbase='$files_url' $kg_divtag_opts kgfield = '$fields'>\n";
			break;
	
	case 'infscroll':
		///initialize post request to KORA API using curl

	foreach($url as $value){
		
		$ch = curl_init($value);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);

		///capture results and display
		$server_output1 = array(curl_exec($ch));
		array_push($server_output,$server_output1);
		

	}
		return "<div class='kora_gallery_infscroll1' kgictrl='$kg_ictrl' kgactrl='$kg_actrl' kgvctrl='$kg_vctrl' kg_pagesize = '$kg_pagesize' kgisize='$kg_isize' kgtctrl='$kg_tctrl' kgdctrl='$kg_dctrl' kglbase='$kg_lbase' kgfs_imageclip='$kgfs_imageclip' kgresturl='$str_url' kgfbase='$files_url' $kg_divtag_opts kgfield = '$fields'>\n</div>\n
</div>";
			break;
	
	}

}

//KORA Search
///Tells wordpress to register the shortcode for kora search
add_shortcode("KORASEARCH", "korasearch_handler");
add_shortcode("korasearch", "korasearch_handler");

function korasearch_handler($incomingfrompost) {

//......
	$project=get_option('kordat_dbproj');
	$scheme=get_option('kordat_dbscheme');

	/// PROCESS INCOMING ATTS OR SET DEFAULTS
	$incomingfrompost = shortcode_atts(array(
		"pid" => $project[0],
		"sid" => $scheme[0],
		"scheme_proj_token" => get_option('kordat_scheme_dbproj_token'),
		"display" => "html",
		"fields" => '',
		"query" => "KID,!=,\'\'",
		"first" => 0,
		"count" => 0,
		"showempty" => "NO",
		), $incomingfrompost);

	/// THIS DOES THE ACTUAL WORK
	$wpoutput = korasearch_getrecords($incomingfrompost);

	/// SEND TEXT BACK
	return $wpoutput;
}

function korasearch_getrecords($wpatts) {
	///gather wordpress options
		$restful_url = get_option('kordat_dbapi') . KORA_PLUGIN_RESTFUL_SUBPATH;
	//$pid = get_option('kordat_dbproj');
	//$sid = get_option('kordat_dbscheme');
	$token = get_option('kordat_dbtoken');
	$user = get_option('kordat_dbuser');
	$pass = get_option('kordat_dbpass');

	$display = $wpatts['display'];
	$query = $wpatts['query'];
	$pid = $wpatts['pid'];
	$sid = $wpatts['sid'];
	$scheme_proj_token = get_option('kordat_scheme_dbproj_token');

	$fieldsarg = ($wpatts['fields'] != '') ? '&fields='.urlencode($wpatts['fields']) : '';
	$first = $wpatts['first'];
	$count = $wpatts['count'];
	$showempty = $wpatts['showempty'];

	$length_spt=sizeof($scheme_proj_token);
	$url=array();
	for($i=0;$i<length_spt;$i=$i+3){
		$url_new = $restful_url.'?request=GET&pid='.$scheme_proj_token[$i+1].'&sid='.$scheme_proj_token[$i].'&token='.$scheme_proj_token[$i+2].'&display='.urlencode($display).'&html_showempty='.urlencode($showempty).$fieldsarg.'&query='.urlencode($query).'&first='.urlencode($first).'&count='.urlencode($count);
		array_push($url,$url_new);
	}
	///initialize post request to KORA API using curl
	$server_output=array();
	foreach($url as $value){
		$ch = curl_init($value);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);

	///capture results and display
			$server_output1 = array(curl_exec($ch));
		array_push($server_output,$server_output1);
		//$server_output = curl_exec($ch);
		return "$server_output\n";
	}

}

///Tells wordpress to register the shortcode for KORAVALUE
add_shortcode("KORAVALUE", "koravalue_handler");
add_shortcode("koravalue", "koravalue_handler");

function koravalue_handler($incomingfrompost) {
//.....
$project=get_option('kordat_dbproj');
$scheme=get_option('kordat_dbscheme');
	/// PROCESS INCOMING ATTS OR SET DEFAULTS
	$incomingfrompost = shortcode_atts(array(
		"pid" => $project[0],
		"sid" => $scheme[0],
		'scheme_proj_token' => get_option('kordat_scheme_dbproj_token'),
		"kid" => '',
		"field" => '',
		"kv_listdelimiter" => ',',
		"kv_aslist" => 'NO',
		"kv_asimgtag" => 'NO',
		"kv_ashreftag" => 'NO',
		"kv_urlonly" => 'NO',
		"kv_thumbnail" => 'NO',
		"kv_hreftext" => '',
		"kv_nostyle" => 'NO',
		"kv_asspan" => 'NO',
		), $incomingfrompost);

	/// SEND TEXT BACK
	$wpoutput = koravalue_getdata($incomingfrompost);
	if (!is_wp_error($wpoutput))
	{ return $wpoutput; }
	else
	{ return $wpoutput->get_error_message(); }
}

function koravalue_getdata($wpatts) {
	///gather wordpress options
		$restful_url = get_option('kordat_dbapi') . KORA_PLUGIN_RESTFUL_SUBPATH;
	//$pid = get_option('kordat_dbproj');
	//$sid = get_option('kordat_dbscheme');
	$token = get_option('kordat_dbtoken');
	$user = get_option('kordat_dbuser');
	$pass = get_option('kordat_dbpass');

	$kid = $wpatts['kid'];
	$pid = $wpatts['pid'];
	$sid = $wpatts['sid'];
	$cname = $wpatts['field'];

	$display = 'xml';

	// IF WE ARE MISSING REQUIRED ATTS FOR THIS CALL, JUST BAIL NOW
	if ($kid == '') { return new WP_Error('kg_nokid', __('No KID property was passed to KORAVALUE shortcode, this is required')); }
	if ($cname == '') { return new WP_Error('kg_nocontrol', __('No FIELD property was passed to KORAVALUE shortcode, this is required')); }

	/*mutiple projects,schemes and tokens*/
	$scheme_proj_token=get_option('kordat_scheme_dbproj_token');
	if(is_array($scheme_proj_token)){
					$_limit=sizeof($scheme_proj_token);
					for($i=0;$i<$_limit;$i=$i+3){
						if($i==0){
							///build url
							$url = array($restful_url.'?request=GET&pid='.$scheme_proj_token[$i+1].'&sid='.$scheme_proj_token[$i].'&token='.$scheme_proj_token[$i+2].'&display='.urlencode($display).'&fields='.urlencode($fields).'&query='.urlencode($query));
							//$url = $restful_url.'?request=GET&pid='.$pid[0].'&sid='.$sid[0].'&token='.$token[0].'&display='.urlencode($display).'&fields='.urlencode($cname).'&query=kid,eq,'.urlencode($kid);
							}
						else{
							$url1 = $restful_url.'?request=GET&pid='.$scheme_proj_token[$i+1].'&sid='.$scheme_proj_token[$i].'&token='.$scheme_proj_token[$i+2].'&display='.urlencode($display).'&fields='.urlencode($fields).'&query='.urlencode($query);
							array_push($url,$url1);
						}
					}
				}



				$i=0;
				if(is_array($url)){
					foreach($url as $value){
						///initialize post request to KORA API using curl
						$ch = curl_init($value);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);

						///capture results and display
						//$server_output = curl_exec($ch);
					   if($i==0){
						$server_output=array( curl_exec($ch));
					   }
					   else{
						array_push($server_output,curl_exec($ch));
					   }
				//	   echo $server_output[$i];

					   $i=$i+1;
					}
				}
				else{
						///initialize post request to KORA API using curl
						$ch = curl_init($url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);

						///capture results and display
						$server_output = curl_exec($ch);

					//   echo $server_output;
				}

				if(is_array($server_output)){
					foreach($server_output as $value){
						//gets url of image
						if($value == ''){
							echo "Search Returned No Results, Try Again";
						}
						else{
							@$xml = simplexml_load_string($value);
							$cname = preg_replace('/ /','_',$cname);

							// FIGURE OUT WHAT TAGS WE ARE GOING TO WRAP AROUND OUR VALUE OUTPUT
							$opentag = '';
							$closetag = '';
							$nostyle = get_bool_setting($wpatts['kv_nostyle'], false);
							$asspan = get_bool_setting($wpatts['kv_asspan'], false);
							if ($nostyle)
							{ $opentag = ''; $closetag = ''; }
							elseif ($asspan)
							{ $opentag = '<span class="kora_value">'; $closetag = '</span>'; }
							else
							{ $opentag = '<div class="kora_value">'; $closetag = '</div>'; }

							$str_value = '';
							// BLEH, HAVE TO DEAL WITH DIFFERENT KINDS OF CONTROL STRUCTURES HERE
							// .. RECORD+PROP NOT FOUND, NOTHING TO RETURN.. ERROR, I SAY NO, JUST RETURN EMPTY?
							if (!isset($xml->{'kid'.$kid}))
							{ $str_value = ''; }
							// VALUE IS ARRAY LISTED AS ITEM0 TO ITEMX
							elseif (isset($xml->{'kid'.$kid}->{$cname}->item0))
							{
								// THIS SECTION HANDLES A COUPLE DIFFERENT WAYS OF DISPLAYING AN ARRAY/LIST
								$aslist = get_bool_setting($wpatts['kv_aslist'], false);
								if ($aslist) { $str_value .= $opentag.'<ul><li>'; }
								$str_value .= $xml->{'kid'.$kid}->{$cname}->item0;
								if ($aslist) { $str_value .= '</li>'; }
								$delimiter = $wpatts['kv_listdelimiter'];
								$i = 1;
								while (isset($xml->{'kid'.$kid}->{$cname}->{'item'.$i}))
								{
									if ($aslist) { $str_value .= '<li>'; }
									else         { $str_value .= $delimiter; }
									$str_value .= (string)$xml->{'kid'.$kid}->{$cname}->{'item'.$i};
									if ($aslist) { $str_value .= '</li>'; }
									$i++;
								}
								if ($aslist) { $str_value .= '</ul>'.$closetag; }
							}
							// VALUE IS A FILE OBJECT
							elseif (isset($xml->{'kid'.$kid}->{$cname}->originalName))
							{
								// AGAIN, A FEW DIFFERENT CUSTOM OPTIONS FOR FILE OUTPUT
								$imgtag = get_bool_setting($wpatts['kv_asimgtag'], false);
								$hreftag = get_bool_setting($wpatts['kv_ashreftag'], false);
								$urlonly = get_bool_setting($wpatts['kv_urlonly'], false);
								$thumb = get_bool_setting($wpatts['kv_thumbnail'], false);
								$hreftxt = $wpatts['kv_hreftext'] != '' ? $wpatts['kv_hreftext'] : (string)$xml->{'kid'.$kid}->{$cname}->originalName;
								$urlbase = get_option('kordat_dbapi').KORA_PLUGIN_FILES_SUBPATH."$pid[0]/$sid[0]/";
								if ($thumb) { $urlbase .= 'thumbs/'; }
								if ($imgtag)
								{ $str_value .= $opentag."<img src='".$urlbase.(string)$xml->{'kid'.$kid}->{$cname}->localName."' />".$closetag; }
								elseif ($hreftag)
								{ $str_value .= $opentag."<a href='".$urlbase.(string)$xml->{'kid'.$kid}->{$cname}->localName."'>".$hreftxt."</a>".$closetag; }
								elseif ($urlonly)
								{ $str_value .= $urlbase.(string)$xml->{'kid'.$kid}->{$cname}->localName; }
								else
								{
									$str_value .= $opentag;
									$str_value .= "Name: ".(string)$xml->{'kid'.$kid}->{$cname}->originalName;
									$str_value .= "&nbsp;Size: ".(string)$xml->{'kid'.$kid}->{$cname}->size;
									$str_value .= "&nbsp;Type: ".(string)$xml->{'kid'.$kid}->{$cname}->type;
									$str_value .= $closetag;
								}
							}
							else
							{
								$str_value = $opentag.(string)$xml->{'kid'.$kid}->{$cname}.$closetag;
							}

							return $str_value;
						}
					}
				}

}

function get_bool_setting($opt_, $def_) {
	// IF DEFAULT IS TRUE, CHECK FOR FALSE SETTING
	if ($def_)
	{ return (preg_match('/^no|false$/i', $opt_)) ? false : true; }
	// ELSE CHECK FOR TRUE SETTING
	else
	{ return (preg_match('/^yes|true$/i', $opt_)) ? true : false; }

}
?>
