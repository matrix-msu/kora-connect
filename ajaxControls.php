<?php
require_once( realpath( dirname(__FILE__) . "/../../../wp-includes/wp-db.php" ) );
require_once( realpath( dirname(__FILE__) . "/../../../wp-blog-header.php"));
require_once(realpath(dirname(__FILE__) . "/dbconfig.php"));


if ($_GET['sid']) {
   // echo "<option>".$_GET['sid']."</option>";
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
     // echo "<option>".$_GET['sid']."</option>";
     $query_scheme .= "SELECT schemeid, pid, schemeName, description FROM scheme WHERE schemeid in(".$_GET['sid'].")";
    //Get all scheme infromation from db.
     $stmt = $bd->prepare($query_scheme) ;
     $stmt->execute();
     $stmt->bind_result($sid, $pid, $sname, $sdesc);
     $schemeInfo = array();
     while($stmt->fetch()){  
        $schemeInfo[] = array('sid' => $sid, 'pid' => $pid, 'schemeName' => $sname, 'description' => $sdesc);                     
    }
    //var_dump($schemeInfo);
    $stmt->close(); 
     
     
     
        // Get all scheme infromation from db.
        $target_table = 'p'.$schemeInfo[0]['pid'].'Control';
        $query_control = "SELECT name,schemeid FROM $target_table WHERE showInResults = 1 AND schemeid in(".$schemeInfo[0]['sid'].")";
       
        $stmt = $bd->prepare($query_control) ;
        $stmt->execute();
        $stmt->bind_result($controlName, $sid);
         echo '<option value="default"></option>';
        while($stmt->fetch()){  
            echo '<option value = "'.$controlName.'">'.$controlName.'</option>';            
        }
        //var_dump($schemeInfo);
        $stmt->close(); 
     }  
}
?>