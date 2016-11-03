<?php /*  <img id="objectImage" src='http://img.photobucket.com/albums/v516/MizGrace/babyhedgehoginbubblebath.jpg' width="667" height="495" alt='32-131-2'>
    
    <form id="editObjectForm" method="post" action="">
        <input type="checkbox" id="controlYes" value="Control will be displayed" />
        <label for="controlYes">Control will be displayed</label>

        <input type="checkbox" id="controlNo" value="Control will not be displayed" />
        <label for="controlNo">Control will not be displayed</label>
        <br/>

        <input type="checkbox" id="controlDescription" value="Description" />
        <label for="controlDescription">Description</label>
        <p for="controlDescription">
            Being the savage's bowsman, that is, the person who pulled the bow-oar in his boat (the second one from
            forward), it was my cheerful duty to attend upon him while taking that hard-scrabble scramble upon the
            dead whale's back. You have seen Italian organ-boys holding a dancing-ape by a long cord. Just so, from
            the ship's steep side, did I hold Queequeg down there in the sea, by what is technically called in the
            fishery a monkey-rope, attached to a strong strip of canvas belted round his waist.
        </p>

        <input type="checkbox" id="control1" value="Control Title" />
        <label for="control1">Control Title</label>
        <p for="control1">Control input</p>

        <input type="checkbox" id="control2" value="Control Title" />
        <label for="control2">Control Title</label>
        <p for="control2">Control input</p>

        <input type="checkbox" id="control3" value="Control Title" />
        <label for="control3">Control Title</label>
        <p for="control3">Control input</p>

        <input type="checkbox" id="control4" value="Control Title" />
        <label for="control4">Control Title</label>
        <p for="control4">Control input</p>

        <input type="checkbox" id="control5" value="Control Title" />
        <label for="control5">Control Title</label>
        <p for="control5">Control input</p>

        <input type="checkbox" id="control6" value="Control Title" />
        <label for="control6">Control Title</label>
        <p for="control6">Control input</p>

        <input type="submit" value="Update Object Details and Return to Add New Object(s) Page" />
    </form>*/ ?>


<?php
require_once(realpath(dirname(__FILE__) . "/../../../wp-includes/wp-db.php"));
require_once(realpath(dirname(__FILE__) . "/../../../wp-blog-header.php"));
require_once(realpath(dirname(__FILE__) . "/dbconfig.php"));
// require_once( realpath( dirname(__FILE__) . "/koramedia.php" ) );


if (isset($_GET['kid']) && isset($_GET['schemeid']) && (isset($_GET['image_control']) || isset($_GET['video_control']) || isset($_GET['audio_control']))) {

    $image_control = $_GET['image_control'];
    $video_control = $_GET['video_control'];
    $audio_control = $_GET['image_control'];

    /* connect to database*/
    $mysql_hostname = kordat_dbhostname;
    $mysql_user = kordat_dbhostuser;
    $mysql_database = kordat_dbselectname;
    $mysql_password = kordat_dbhostpass;

    $user = kordat_dbuser;
    $pass = kordat_dbpass;

    @$bd = new mysqli($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);
    if ($bd->connect_error) {
        echo "<p class='error'><strong>";
        echo "Please edit config.php.dist (Kora Host Database Settings) first! Save as config.php!";
        echo "</strong></p>";
    } else {
        //get search information
        $schemeid = $_GET['schemeid'];
        $target_kid = $_GET['kid'];
        // echo $target_kid." ".$schemeid;
        $dbproj = get_option('kordat_dbproj');
        $dbscheme = get_option('kordat_dbscheme');
        $dbtoken = get_option('kordat_dbtoken');


        $query_sid_pid = "SELECT schemeid,pid FROM scheme WHERE schemeid = '" . $schemeid . "';";
        $stmt = $bd->prepare($query_sid_pid);
        $stmt->execute();
        $stmt->bind_result($sids, $pids);
        //$sid_pid_token = array();

        while ($stmt->fetch()) {
            $sid = $sids;
            $pid = $pids;
            //echo $sid." ".$pid."<br>";
            if (in_array($sid, $dbscheme) && in_array($pid, $dbproj)) {
                $pos = array_search($pid, $dbproj);
                $sid_pid_token = array('schemeid' => $sid, 'projectid' => $pid, 'token' => $dbtoken[$pos]);
                // $val = $sid."-".$pid."-".$dbtoken[$pos];
                //array_push($sid_pid_token,$val);
            }
        }
        $stmt->close();


        //korasearch

        $restful = get_option('kordat_dbapi');
        $restful_url = $restful . KORA_PLUGIN_RESTFUL_SUBPATH;


        $i = 0;
        $fields = 'ALL';

        $display = 'json';
        // $display='plugin';
        $query = "KID,=," . $target_kid;
        if (!empty($sid_pid_token)) {
            $url = $restful_url . '?request=GET&pid=' . $sid_pid_token['projectid'] . '&sid=' . $sid_pid_token['schemeid'] . '&token=' . $sid_pid_token['token'] . '&display=' . urlencode($display) . '&fields=' . urlencode($fields) . '&query=' . urlencode($query);
        }

        //initialize post request to KORA API using curl
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $user . ':' . $pass);

        //capture results and display
        $obj_json = curl_exec($ch);

        //convert json string to php array
        $server_output = json_decode($obj_json, true);

        $control_fields = array();

        $media_type = "";
        foreach ($server_output as $record) {
            var_dump($record);
            if ($record['image']) {
                $thumb_src = get_option('kordat_dbapi') . 'files/' . $sid_pid_token['projectid'] . '/' . $sid_pid_token['schemeid'] . '/thumbs/' . $record['image']['localName'];
                $media_type = "image";
            } else if ($record[$video_control]) {
                $videoFile = mysql_escape_string(get_option('kordat_dbapi') . 'files/' . $sid_pid_token['projectid'] . '/' . $sid_pid_token['schemeid'] . "/" . $record[$video_control]['localName']);

                $media_type = "video";

            } else if ($record[$audio_control]) {
                $audioFile = mysql_escape_string(get_option('kordat_dbapi') . 'files/' . $sid_pid_token['projectid'] . '/' . $sid_pid_token['schemeid'] . "/" . $record[$audio_control]['localName']);

                $media_type = "audio";
            } else {
                $thumb_src = "http://img.photobucket.com/albums/v516/MizGrace/babyhedgehoginbubblebath.jpg";
                $media_type = "NULL";
            }

            /*  if($record['Image'] || $record['Thumbnail'] ) {
                            if ($record['Image']) {
                                 $src = get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid'].'/'.$record['Image']['localName'];
                            } else if ($record['Thumbnail']['localName']) {
                                $src = get_option('kordat_dbapi').'files/'.$sid_pid_token['projectid'].'/'.$sid_pid_token['schemeid'].'/'.$record['Thumbnail']['localName'];

                               // $src = getThumbnailKORA($record['Thumbnail']['localName']);
                            }

                        } else if ($record['Audio File'] || $record['Video File']) {
                            if(!empty($record['Audio File']))
                             {
                               //  $audioFile = mysql_escape_string(getFullURLFromFileName($value['Audio File']['localName']));
                              //   echo "<br />click play for full interview";
                             // KoraEmbedMedia(array('kid' => $value['kid'], 'cid' => '28', 'tagid' => 'player', 'width' => '300px', 'height' => '30px', 'autoplay' => 'false'));
                              //   KoraEmbedMedia( Array('url' => $audioFile, 'tagid' => 'player', 'width' => '300px', 'height' => '30px',  'autoplay' => 'false') );
                            // } else if(!empty($record['Video File']))
                             //{
                                 //$videoFile = mysql_escape_string(getFullURLFromFileName($value['Video File']['localName']));
                                 //if ($value['Start Time'] != "") {
                                   //  $ts = timetoseconds($value['Start Time']);
                                     //$te = timetoseconds($value['End Time']);
                                // } else {
                                  //   $ts = null;
                                    // $te = null;
                                 //}
                                 //$dur = $te - $ts;
                             // echo $dur;
                                 // SANITY CHECK TO SEE IF WE HAVE VALID NUMBERS FOR TS/TE/DUR, WORRIED FOR THE CASE IF KORA SETS TO EMPTY
                                 //if (!is_numeric($ts) || !is_numeric($dur))
                                 //{ $ts = -1; $te = -1; $dur = -1; }

                                 echo "<br />click play for full interview";

                             KoraEmbedMedia( Array('url' => $videoFile, 'tagid' => 'player', 'width' => '300px', 'height' => '211px', 'timestart' => $ts, 'duration' => $dur, 'autoplay' => 'false') );
                             // KoraEmbedMedia(array('kid' => $value['kid'], 'cid' => 27, 'tagid' => 'player', 'width' => '300px', 'height' => '211px', 'timestart' => $ts, 'duration' => $dur, 'autoplay' => 'false'));                                            }
                        }
                        else {
                            $src = "http://img.photobucket.com/albums/v516/MizGrace/babyhedgehoginbubblebath.jpg";
                        }*/
            $description = $record['Description'];
            $control_name = array_keys($record);

            foreach ($control_name as $control) {
                if ($control != $image_control && $control != $video_control && $control != $audio_control && $control != 'Description') {
                    if (!empty($record[$control])) {
                        //array_push($control_fields, $control_fields[$control] = $record[$control]);
                        $control_fields[$control] = $record[$control];
                    }

                }

            }
        }

        // echo '<img id="objectImage" src="'.$src.'" width="667" height="495" alt="'.$target_kid.'">';
        if ($media_type == "image") {
            var_dump($thumb_src);
            echo "<img id='objectImage' src='" . $thumb_src . "' width='667' height='495' alt='" . $target_kid . "'>";
        } else if ($media_type == "video") {
            echo '<video width="100%" height="100%" controls>
                                                        <source src="' . $videoFile . '" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>';
            //      KoraEmbedMedia( Array('url' => $videoFile, 'tagid' => 'player'.$record['kid'], 'width' => '100%', 'height' => '100%',  'autoplay' => 'false') );
        } else if ($media_type == "audio") {
            echo '<audio controls>
                                                            <source src="' . $audioFile . '" type="audio/mpeg">
                                                            Your browser does not support the audio element.
                                                      </audio>';
            //    KoraEmbedMedia( Array('url' => $audioFile, 'tagid' => 'player'.$record['kid'], 'width' => '100%', 'height' => '100%',  'autoplay' => 'false') );
        } else {
            echo "<img id='objectImage' src='" . $thumb_src . "' width='667' height='495' alt='" . $target_kid . "'>";
        }
        // echo  '<form id="editObjectForm" method="post" action="">';
        echo '  <div class= "control_show" >
                            <input type="checkbox" class = "radio" id="controlYes" name = "control_show" value="yes" checked />
                            <label for="controlYes">Control will be displayed</label>

                            <input type="checkbox" class = "radio" id="controlNo" name = "control_show" value="no" />
                            <label for="controlNo">Control will not be displayed</label>
                            <br/></div>';
        echo '<div class = "control_fields_checkbox">
                              <input type="checkbox" id="controlDescription" value="Description" />
                              <label for="controlDescription">Description</label>
                              <p for="controlDescription">' . $description . '</p>';

        $control_displayed = $_GET['control_displayed'];
        // $control_displayed [] = 'kid';
        foreach ($control_fields as $key => $value) {
            if (is_array($value)) {
                $str = "";
                foreach ($value as $val) {
                    $str .= $val . " ";
                }
                echo '<input type="checkbox" id="' . $key . '" value="' . $str . '"';
                if (!empty($control_displayed)) {
                    if (in_array($key, $control_displayed)) {
                        echo "checked";
                    } else if ($key == 'kid' && in_array("KID", $control_displayed)) {
                        echo "checked";
                    }
                }
                echo ' />
                                  <label for="control">' . $key . '</label>
                                  <p for="control">' . $str . '</p>';
            } else {
                echo '<input type="checkbox" id="' . $key . '" value="' . $value . '"';
                if (!empty($control_displayed)) {
                    if (in_array($key, $control_displayed)) {
                        echo "checked";
                    } else if ($key == 'kid' && in_array("KID", $control_displayed)) {
                        echo "checked";
                    }
                }
                echo ' />
                                  <label for="control">' . $key . '</label>
                                  <p for="control">' . $value . '</p>';
            }

        }

        echo "</div>";
        echo ' <input class = "edit_details_submit" type="submit" value="Update Object Details and Return to Add New Object(s) Page" alt = "' . $target_kid . '"/>';
        //  echo '</form>';


    }
} else {
    echo "<h1>No recrod detials!</h1>";
}
?>
<script> var url_plugin = '<?php echo KORA_PLUGIN_PATHBASE;?>'; </script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script>
    jQuery(document).ready(function ($) {
        $("div.control_show input:checkbox").on('click', function () {
            // in the handler, 'this' refers to the box clicked on
            var $box = $(this);
            if ($box.is(":checked")) {
                // the name of the box is retrieved using the .attr() method
                // as it is assumed and expected to be immutable
                var group = "input:checkbox[name='" + $box.attr("name") + "']";
                // the checked state of the group/box on the other hand will change
                // and the current value is retrieved using .prop() method
                $(group).prop("checked", false);
                $box.prop("checked", true);
            } else {
                $box.prop("checked", false);
            }
        });
        $('.edit_details_submit').click(function () {
            alert($(this).attr('class'));

            // var showOption = $("div.control_show input:checkbox").val();
            //alert(showOption);
            var kid = $(this).attr('alt');
            // alert(kid);
            var checked_fileds_name = [];
            var checked_fileds_value = [];

            if (document.getElementById("controlYes").checked) {
                //alert("yes");
                $("div.control_fields_checkbox input:checkbox:checked").each(function () {
                    checked_fileds_name.push($(this).attr('id'));
                    checked_fileds_value.push($(this).val());
                    // alert($(this).val());
                });
            } else {
                // alert("no");
                $("div.control_fields_checkbox input:checkbox").each(function () {
                    if (!$(this).is(':checked')) {
                        checked_fileds_name.push($(this).attr('id'));
                        checked_fileds_value.push($(this).val());
                    }
                });
            }

            //alert(checked_fileds_name);
            $.ajax({
                type: "GET",
                async: false,
                url: url_plugin + "ajaxKORAobject_update_edit_details.php",
                data: {"checked_fileds_name": checked_fileds_name, "checked_fileds_value": checked_fileds_value},
                success: function (data) {
                    if (data != 'false') {
                        alert("Successful!!!");
                        //alert(url_plugin  + "ajaxKORAobject_update_edit_details.php");
                        //alert('edit_details_' + kid);
                        $('#edit_details_' + kid).html(data);
                        $('#editObjectModal .remodal').remodal().close();
                        alert($(this).attr('id'));
                    }

                }
            });
            //  window.close();
            //var remodal =  $(this).parent().parent().attr('class');
            //$(remodal .remodal).remodal.close();

        });
    });
</script>