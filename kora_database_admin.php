<?php
    define('KORA_PLUGIN_PATHBASE', plugin_dir_url(__FILE__));
    require_once(realpath(dirname(__FILE__) . "/dbconfig.php"));
    $url_plugin=$_GET['url'];

?>
             
    <script> var url_plugin = '<?php echo KORA_PLUGIN_PATHBASE;?>'; </script>
    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>	
	<script src="http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
    <script src="http://cdn.rawgit.com/davidstutz/bootstrap-multiselect/master/dist/js/bootstrap-multiselect.js"></script>
    <script src="<?php echo plugins_url('kora/remodal_v1.0.6/dist/remodal.min.js'); ?>"></script>
    <script src="<?php echo plugins_url('kora/chosen_v1.4.2/chosen.jquery.min.js'); ?>"></script>
    <script src="<?php echo plugins_url('kora/chosen_v1.4.2/chosen.proto.min.js'); ?>"></script>
    <script src="<?php echo plugins_url('kora/js/connect.js'); ?>"></script>
    <link rel="stylesheet" href="http://cdn.rawgit.com/davidstutz/bootstrap-multiselect/master/dist/css/bootstrap-multiselect.css"
          type="text/css"/>
    <link rel="stylesheet" href="<?php echo plugins_url('kora/remodal_v1.0.6/dist/remodal.css'); ?>" type="text/css"/>
    <link rel="stylesheet" href="<?php echo plugins_url('kora/remodal_v1.0.6/dist/remodal-default-theme.css'); ?>"
          type="text/css"/>
    <link rel="stylesheet" href="<?php echo plugins_url('kora/chosen_v1.4.2/chosen.css'); ?>" type="text/css"/>

<?php
    $emptys = array();
    $emptyfunc = function ($bool, $name) use (&$emptys) {
        if (!$bool) {
            $emptys[] = $name;
        }
    };

    if ($_POST['kordat_hidden'] == 'Y') {
        ///Form data sent
        $dbapi = $_POST['kordat_dbapi'];
        update_option('kordat_dbapi', $dbapi);
        $emptyfunc($dbapi, "URL of KORA Installation");

        $dbproj = $_POST['kordat_dbproj'];
        update_option('kordat_dbproj', $dbproj);
        $emptyfunc($dbproj, "Project ID");


        $dbscheme = $_POST['kordat_dbscheme'];
        update_option('kordat_dbscheme', $dbscheme);
        $emptyfunc($dbscheme, "Scheme ID");

        $dbtoken = $_POST['kordat_dbtoken'];
        update_option('kordat_dbtoken', $dbtoken);
        $emptyfunc($dbtoken, "Token");

        if (($dbapi && $dbscheme && $dbtoken)) {
            //if ($scheme_proj_token) {
            echo " <div class='updated'><p><strong>";
            _e('Options saved.');
            echo "</strong></p></div>";
          
        } else {
            echo "<div class='error'><p><strong>All fields below need to be filled: </strong></p>";
            //var_dump($emptys);
            if ($dbapi == '') {
                echo "<p>* URL of Kora Installation</p>";
            }
            if ($dbproj[1] === '') {
                echo "<p>* Project ID</p>";
            }
            if ($dbtoken[0] === '') {
                echo "<p>* Token</p>";
            }
            if ($dbscheme[1] == '') {
                echo "<p>* Scheme ID</p>";
            }

            echo "</div>";
        }
    } else {

        ///Normal page display
        $dbapi = get_option('kordat_dbapi');
        $dbproj = get_option('kordat_dbproj');
        $dbscheme = get_option('kordat_dbscheme');
        $dbtoken = get_option('kordat_dbtoken');
      }

    //button pos
    $edit_pos = KORA_PLUGIN_PATHBASE . 'edit.png';
    $add_pos = KORA_PLUGIN_PATHBASE . 'plus.png';
    $remove_pos = KORA_PLUGIN_PATHBASE . 'minus.png';

    /* connect to database*/
    $mysql_hostname = kordat_dbhostname;
    $mysql_user = kordat_dbhostuser;
    $mysql_database = kordat_dbselectname;
    $mysql_password = kordat_dbhostpass;

    @$bd = new mysqli($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);
    if ($bd->connect_error) {
        echo "<p class='error'><strong>";
        echo "Please edit config.php.dist (Kora Host Database Settings) first! Save as config.php!";
        echo "</strong></p>";
    } else { 
                // Get all project id and name in db.
                $projectID_query = "SELECT pid, name, description FROM project;";
                $stmt = $bd->prepare($projectID_query) ;
                $stmt->execute();
				$stmt->bind_result($pid, $pname, $pdesc);
				$projectInfo = array();
                while($stmt->fetch()){ 
                     $projectInfo[] = array('pid' => $pid, 'projectName' => $pname, 'description' => $pdesc);    
                }

                $stmt->close(); 
                // Get all scheme infromation from db.
                $query_scheme = "SELECT schemeid, pid, schemeName, description FROM scheme;";
                $stmt = $bd->prepare($query_scheme) ;
                $stmt->execute();
				$stmt->bind_result($sid, $pid, $sname, $sdesc);
                $schemeInfo = array();
                while($stmt->fetch()){  
                     $schemeInfo[] = array('sid' => $sid, 'pid' => $pid, 'schemeName' => $sname, 'description' => $sdesc);                     
                }

                $stmt->close(); 
    }
        ?>


    <div class='wrap'>
        <h1>KoraConnect</h1>

        <form id='setting_form' name='kordat_form' method='post' action='#'>
            <input type='hidden' name='kordat_hidden' value='Y'/>

            <h2 id='koraUrlHeader'>URL of Kora Installation</h2>
			
            <input id='koraUrl' type='text' name='kordat_dbapi' value='<?php echo $dbapi; ?>'
                   placeholder='Enter the URL of your Kora installation here (http://kora.example.org/) then click "Update Connection"'/>


            <h2 id="ptsHeader">Projects, Tokens, and Schemes</h2>

            <div id="questions">
                <span id="projectQuestion">Where do I find the Projects?</span>
                <span id="tokenQuestion">Where do I find the Token?</span>
                <span id="schemeQuestion">Where do I find the Schemes?</span>
            </div>

            
            <div class = "projectBox" id="projectBox">
            <?php

                    if (is_array($dbproj)) {
                        
                         for ($i = 0; $i < count($dbproj) ; $i++) {

                            if ((empty($dbproj[$i]) || empty($dbscheme[$i]) || empty($dbtoken[$i]))) {
                                continue;
                            }

        ?>
                        <div class = "projectBoxItem">


                            <select class = 'koraProject' id='koraProject' name='kordat_dbproj[]' <?php  //if ($dbtoken[0] && $dbproj[0]) { echo "readonly"; } ?>
                                    data-placeholder="Project: Search and select a project" alt='<?php echo $i;?>'>
                                <option value="default"></option>
                                <?php

                                        foreach($projectInfo as $p){
                                            echo '<option value = "'.$p['pid'].'"'; 
                                            if ($p['pid'] == $dbproj[$i]) {echo 'selected="selected"';   }                                         
                                            echo '>'.$p['pid'].'---'.$p['projectName'].'---'.$p['description'].'</option>';
                                           
                                        }
                                ?>
                            </select>

							<img class='deleteBox' pid=<?php echo dechex($dbproj[$i]);?> src='<?php echo plugins_url("kora/images/Close.svg"); ?>'
                                     width='19' height='18' alt='Delete Button'/>

                            <input id='koraToken' type='text' name='kordat_dbtoken[]' value="<?php echo $dbtoken[$i]; ?>"
                                   onchange='<!--this.form.submit()-->' placeholder='Token: Enter token associated with the project'
                                   disabled <?php //if ($dbtoken[0] && $dbproj[0]) { echo "readonly"; } ?> />
            <!-- Scheme -->


            <select class = 'koraScheme' id='koraScheme' name='kordat_dbscheme[]' data-placeholder="Scheme(s): Search and select by scheme name or ID" alt='<?php echo $i;?>'  multiple>
                <option value="default"></option>
             </select>
             </div>
             <div class="buttonCont"></div>
                <?php 
                    
                        }
                    } else { ?>
              <div class = "projectBoxItem">
              <select class = 'koraProject' id='koraProject' name='kordat_dbproj[]' <?php  //if ($dbtoken[0] && $dbproj[0]) { echo "readonly"; } ?>
                                    data-placeholder="Project: Search and select a project">
                                <option value="default"></option>
                                <?php
                                        foreach($projectInfo as $p){ 
                                            echo '<option value = "'.$p['pid'].'"'; 
                                            if ($p['pid'] == $dbproj) {echo 'selected="selected"';}                                         
                                            echo '>'.$p['pid'].'---'.$p['projectName'].'---'.$p['description'].'</option>';
                                        }
                                ?>
                            </select>
							<img class='deleteBox' src='<?php echo plugins_url("kora/images/Close.svg"); ?>'
                                     width='19' height='18' alt='Delete Button'/>

                            <input id='koraToken' type='text' name='kordat_dbtoken[]' value="<?php echo $dbtoken; ?>"
                                   onchange='<!--this.form.submit()-->' placeholder='Token: Enter token associated with the project'
                                   disabled <?php //if ($dbtoken[0] && $dbproj[0]) { echo "readonly"; } ?> />
            <!-- Scheme -->


            <select class = 'koraScheme' id='koraScheme' name='kordat_dbscheme[]' data-placeholder="Scheme(s): Search and select by scheme name or ID" multiple>
                <option value="default"></option>
             </select>
             </div>
             <div class="buttonCont"></div>
         <?php  }
                 ?>

            </div>
            <div id="appendNewProjects"></div>

            <input id='addNewButton' type='button' value='Add New Project, Token, & Scheme(s)' disabled />

        </form>
    </div><!-- end of .wrap -->





<!-- Modals -->
<div class="remodal" id="projectModal" data-remodal-id="projectModal">
    <button data-remodal-action="close" class="remodal-close"></button>

    <h2>Finding the project</h2>

    <p>To find the project you wish to connect, navigate to the project selection page
        within your Kora installation. Your various projects will be listed. You may search
        via the project name or ID within KoraConnect.
    </p>
	
	<img src="<?php echo plugins_url('/kora/images/finding-the-project.svg'); ?>" 
		width="452" height="73" alt="How to find the project" />
</div>


<div class="remodal" id="tokenModal" data-remodal-id="tokenModal">
    <button data-remodal-action="close" class="remodal-close"></button>

    <h2>Finding the token</h2>

    <p>To find the token, navigate to the Manage Search Tokens within your Kora installation.
        Various tokens will be listed for each project. Look for the project you’re connecting to,
        and the token key will be in the far left column.
    </p>
	
	<img src="<?php echo plugins_url('/kora/images/finding-the-token.svg'); ?>" 
		width="456" height="120" alt="How to find the token" />
		
	<div id="redCircle"></div>
</div>


<div class="remodal" id="schemeModal" data-remodal-id="schemeModal">
    <button data-remodal-action="close" class="remodal-close"></button>

    <h2>Finding the scheme</h2>

    <p>To find the scheme you wish to connect, navigate to the project that contains the desired
        scheme within your Kora installation. The schemes will be listed within the project. You
        may search by scheme name or ID within KoraConnect.
    </p>
	
	<img src="<?php echo plugins_url('/kora/images/finding-the-scheme.svg'); ?>" 
		width="452" height="91" alt="How to find the scheme" />
</div>


<div class="remodal" id="deleteModal" data-remodal-id="deleteModal">
    <button data-remodal-action="close" class="remodal-close"></button>

    <h2>Do you want to remove this project? If you remove the project, all items from this project will be 
        remove from the library and galleries.</h2>

    <button class="remove">Remove</button>
    <button class="cancel">Cancel</button>
</div>
