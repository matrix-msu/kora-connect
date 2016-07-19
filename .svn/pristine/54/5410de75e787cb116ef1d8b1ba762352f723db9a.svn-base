<?php
require_once(realpath(dirname(__FILE__) . "/dbconfig.php"));

    echo "<option value='default'></option>";
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
        while($stmt->fetch()){  
            //$schemeInfo[] = array('sid' => $sid, 'pid' => $pid, 'schemeName' => $sname, 'description' => $sdesc);        
            echo '<option value = "'.$pid.'">'.$pid.'---'.$pname.'---'.$pdesc.'</optiopn>';             
        }
        //var_dump($schemeInfo);
        $stmt->close(); 
     }  

?>