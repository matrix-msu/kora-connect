<?php
require_once( realpath( dirname(__FILE__) . "/../../../wp-includes/wp-db.php" ) );
require_once( realpath( dirname(__FILE__) . "/../../../wp-blog-header.php"));
require_once(realpath(dirname(__FILE__) . "/dbconfig.php"));


if ($_GET['pid']) {
    /* connect to database*/
    $mysql_hostname = kordat_dbhostname;
    $mysql_user = kordat_dbhostuser;
    $mysql_database = kordat_dbselectname;
    $mysql_password = kordat_dbhostpass;
    
    //get selected scheme id 
     $schemeidSelected=get_option('kordat_dbscheme');
    @$bd = new mysqli($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);
    if ($bd->connect_error) {
        echo "<p class='error'><strong>";
        echo "Please edit config.php.dist (Kora Host Database Settings) first! Save as config.php!";
        echo "</strong></p>";
    } else {
      
        // Get all scheme infromation from db.
        $query_scheme = "SELECT schemeid, pid, schemeName, description FROM scheme WHERE pid = ".$_GET['pid'].";";
        $stmt = $bd->prepare($query_scheme) ;
        $stmt->execute();
        $stmt->bind_result($sid, $pid, $sname, $sdesc);
        while($stmt->fetch()){  
            //$schemeInfo[] = array('sid' => $sid, 'pid' => $pid, 'schemeName' => $sname, 'description' => $sdesc);        
            echo '<option value = "'.$sid.'"';
            if (is_array($schemeidSelected)) {
                if (in_array($sid, $schemeidSelected)) {
                    echo 'selected="selected"';
                }
            } else {
                if ($schemeidSelected == $sid) {
                    echo 'selected="selected"';
                }
            }
            echo '>'.$sid.'---'.$sname.'---'.$sdesc.'</optiopn>';             
        }
        //var_dump($schemeInfo);
        $stmt->close(); 
     }  
}
?>