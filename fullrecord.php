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
	$display = 'json';
	$k = $_GET["kid"];

    $dbproj = get_option('kordat_dbproj');
    $dbscheme = get_option('kordat_dbscheme');
    $dbtoken = get_option('kordat_dbtoken');

    $restful = get_option('kordat_dbapi');
    $restful_url = $restful . KORA_PLUGIN_RESTFUL_SUBPATH;

	$query = "KID,=,".$k;
	if(isset($_POST['fields'])){
		$fields = $_POST['fields'];
	} else {
		$fields = 'ALL';
	}

    $pid = substr($k, 0, 1);
    $sid = substr($k, 2, 1);
    $dbproj = get_option('kordat_dbproj');
    $query = urlencode("(kid,=,$k),or,(kid,=,)");
    for ($x=0; $x < count($dbproj); $x++) {
        if ($dbproj[$x] == $pid) {
            $token = $dbtoken[$x];
        }
    }
	//$url = $restful_url.'?request=GET&pid='.urlencode($display).'&fields='.urlencode($fields).'&query='.urlencode($query);
    if (isset($_GET['kid'])) {

        $url = $restful_url.'?request=GET&pid='.$pid.'&sid='.$sid.'&token='.$token.'&display=json&query='.$query.'sort=&order=SORT_ASC';
    }
    //$url = $restful_url.'?request=GET&pid=3&sid=4&token=aa46b5b349a14e0d63e81865&display=json&query=%28kid%2C%3D%2C3-4-0%29%2Cor%2C%28kid%2C%3D%2C%29&sort=&order=SORT_ASC';
    //$url = $restful_url.'?request=GET&pid=3&sid=4&token=aa46b5b349a14e0d63e81865&display=json&query=%28kid%2C%3D%2C3-4-0%29&sort=&order=SORT_ASC';
    ///initialize post request to KORA API using curl
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);

	///capture results and display
	$server_output = curl_exec($ch);


    echo "<a id='backArrow' href='javascript:history.go(-1)'><img src='images/Arrow-Left.svg'/></a>";
	
	$server_output = json_decode($server_output, true);

		//preview of media if present 
		foreach ($server_output as $kid => $koraobj) {

			$decKID = explode("-", $k);

			if ($koraobj["Video File"] != '' || $koraobj['Video'] != '' || $koraobj['video file'] != '' || $koraobj['video'] != '') {
			    if ($koraobj["Video File"] != ""){
			        $localName = $koraobj["Video File"]['localName'];
                }
                else if ($koraobj["video file"] != ""){
                    $localName = $koraobj["video file"]['localName'];
                }
                else if ($koraobj["Video"] != ""){
                    $localName = $koraobj["Video"]['localName'];
                }
                else if ($koraobj["video"] != ""){
                    $localName = $koraobj["video"]['localName'];
                }
				$prevHTML = '<div class="control_full_value">';
				$prevHTML .= '<div class="kc_file_tn">';
				$prevHTML .= '<video controls><source src="' . $restful . '/files/' . hexdec($decKID[0]) . '/' . hexdec($decKID[1]) . '/' .  $localName . '"></video>';
				$prevHTML .= '</div></div>';
				echo($prevHTML);
			}
            if ($koraobj["Audio File"] != '' || $koraobj['Audio'] != '' || $koraobj['audio file'] != '' || $koraobj['audio'] != '') {
                if ($koraobj["Audio File"] != ""){
                    $localName = $koraobj["Audio File"]['localName'];
                }
                else if ($koraobj["audio file"] != ""){
                    $localName = $koraobj["audio file"]['localName'];
                }
                else if ($koraobj["Audio"] != ""){
                    $localName = $koraobj["Audio"]['localName'];
                }
                else if ($koraobj["audio"] != ""){
                    $localName = $koraobj["audio"]['localName'];
                }
				$prevHTML .= '<div class="control_full_value">';
				$prevHTML .= '<div class="kc_file_tn">';
				$prevHTML .= '<audio controls><source src="' . kora_url . 'files/' . hexdec($decKID[0]) . '/' . hexdec($decKID[1]) . '/' .  $koraobj["Audio File"]['localName'] . '"></audio>';
				$prevHTML .= '</div></div>';
				echo($prevHTML);
			} if ($koraobj["image"] != '' ) {
				$prevHTML = '<div class="control_full_value">';
				$prevHTML .= '<div class="kc_file_tn">';

				$prevHTML .= '<img src="' . $restful . '/files/' . hexdec($decKID[0]) . '/' . hexdec($decKID[1]) . '/' .  $koraobj["image"]["localName"] . '">';
				$prevHTML .= '</div></div>';
				echo($prevHTML);
			}
			if ($koraobj['Image'] != "") {
                $prevHTML = '<div class="control_full_value">';
                $prevHTML .= '<div class="kc_file_tn">';

                $prevHTML .= '<img src="' . $restful . '/files/' . hexdec($decKID[0]) . '/' . hexdec($decKID[1]) . '/' . $koraobj["Image"]["localName"] . '">';
                $prevHTML .= '</div></div>';
                echo($prevHTML);
            }
		}


		$prevHTML = '<div class="control_full_value">';
		$prevHTML .= '<div class="kc_file_tn">';
		$prevHTML .= stripslashes($_POST['media']);

		$prevHTML .= '</div></div>';

		echo($prevHTML);

		?>		
	
	<?php //metadata display  ?>
	<div class="container_full">
	<?php
    foreach ($server_output as $koraobj){
	    if (isset($koraobj['Title'])) {
            $title = $koraobj['Title'];
        }
        else if (isset($koraobj['title'])){
            $title = $koraobj['title'];
        }
        if (isset($koraobj['description'])){
            $desc = $koraobj['description'];
        }
        else if (isset($koraobj['Description'])){
            $desc = $koraobj['Description'];
        }
    }


    //$htmlout .= "<div class='title'><h1>$title</h1>";
    $htmlout_right .= "<div class='container koraobj_container_right'>";
    //$htmlout_right .= "<div class='koraobj_obj_desc'> $desc </div></div>";

	foreach ($server_output as $kid => $koraobj) {
		$htmlout_left .= "<div class='container koraobj_container_left'><h1>$title</h1>\n";
		foreach ($koraobj as $dfield => $dvalue) {
			if (is_array($dvalue)) {

				foreach( $dvalue as $key => $value ) {
                    if ($dfield == 'Title' || $dfield == 'title') {
                        continue;
                    }


					if ($value != '') {
                        $htmlout_left .= "\t<div class='koraobj_control control_full koraobj_control_" . $key . "' ><div class='koraobj_control_label'>" . $key . "</div>";
                        $pos = strpos($value, "http");

                        if (is_int($pos)) {
                            $htmlout_left .= "<div class='koraobj_control_value'><a href='" . $value . "'>" . $value . "</a></div></div>\n";
                        } else {
                            $htmlout_left .= "<div class='koraobj_control_value'>" . $value . "</div></div>\n";

                        }
                    }
				}
			} else {
			    if ($dfield == 'Title' || $dfield == 'title' || $dfield == 'description' || $dfield == 'Description') {
			        continue;
                }
                else if ($dfield == 'description' || $dfield == 'Description') {

                }

                if ($dvalue != '') {

					$htmlout_left .= "\t<div class='koraobj_control control_full koraobj_control_".$dfield."' ><div class='koraobj_control_label control_full_label'>".$dfield."</div>";
					$pos = strpos($dvalue, "http");
				if(is_int($pos)){
						$htmlout_left .= "<div class='koraobj_control_value'><a href='" . $dvalue.  "'>".$dvalue."</a></div></div>\n";
					}else{
						$htmlout_left .= "<div class='koraobj_control_value'>" . $dvalue.  "</div></div>\n";

					}
				}
			}
		}
		$htmlout .= "</div>\n";
        $htmlout_left .= "</div>";
		//echo($htmlout);
        echo "<div class='control_wrapper'>";
        echo($htmlout_left);
        echo($htmlout_right);
        echo "</div>";


        $url = $restful_url.'?request=GET&pid=3&sid=4&token=aa46b5b349a14e0d63e81865&display=json&sort=&order=SORT_ASC';
        ///initialize post request to KORA API using curl
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);

        ///capture results and display
        $server_output = curl_exec($ch);


        $server_output = json_decode($server_output, true);
        $count = 0;

//        echo "<div id = related>Related Content</div>";
//
//        echo "<div class=kora-objs>";
//
//        foreach ($server_output as $kid => $koraobj) {
//            if ($_GET['kid'] == $kid){
//                continue;
//            }
//            else if (count > 8){
//                break;
//            }
//            //var_dump($dbtoken);
//            //$token =
//            $img = $restful . '/files/' . hexdec($decKID[0]) . '/' . hexdec($decKID[1]) . '/' .  $koraobj["image"]["localName"];
//            $title = $koraobj['Title'];
//            //echo '<form action="fullrecord.php" method="get">';
//            echo "<a href='fullrecord.php?kid=$kid' style='text-decoration: none'>";
//            echo "<div class=kora-obj>
//                    <div class=kora-obj-left>
//                        <input type='image' id='img' name='submit' src=$img>
//                        <div class='title'>$title</div>
//                        <input type='hidden' value=$kid name='kid'>
//                        <!--<input type='hidden' value=-->
//                    </div>
//                </div>
//            </a>";
//            $count++;
//
//        }
//        echo "</div>";
//        echo "<div class=kora-objs>
//                <div class=kora-obj>
//                    <div class=kora-obj-left>
//                        <img src=\"' . $restful '/files/' hexdec($decKID[0]) '/' hexdec($decKID[1]) '/'  $koraobj["image"]["localName"] ">
//                    </div>
//                </div>
//              </div>;"
//        $clause = new KORA_Clause('kid', '=', $_GET['kid']);
//        $results = KORA_Search($dbtoken[0], $dbproj[0], '4', $clause, array('ALL'));

	}
	?>
	
	</div>

<script src="<?php echo "js/jquery.js";?>" type="text/javascript"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="js/fullrecord.js"></script>