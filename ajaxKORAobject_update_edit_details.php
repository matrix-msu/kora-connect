<?php 
   // echo "1111111";
    if (isset($_GET['checked_fileds_name']) && isset($_GET['checked_fileds_value']) ) {
        $checked_fileds_name = $_GET['checked_fileds_name'];
        $checked_fileds_value = $_GET['checked_fileds_value'];
       //var_dump($_GET['checked_fileds_name']);
       //var_dump($_GET['checked_fileds_value']);
       if(is_array($checked_fileds_name) && is_array($checked_fileds_value)) {
           for($i = 0; $i < count($checked_fileds_name); $i++) {
              echo "<li><span>".$checked_fileds_name[$i]."</span>: ".$checked_fileds_value[$i]."</li>";
           }
       } else {
              echo "<li><span>".$checked_fileds_name."</span>: ".$checked_fileds_value."</li>";
       }
      //echo json_encode(true);
    } else {
        echo json_encode(false);
    }

?>