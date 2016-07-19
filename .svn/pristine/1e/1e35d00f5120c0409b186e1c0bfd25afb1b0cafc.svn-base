
<?php

function Search_Form(){
	return "<div class=\"form_upload\">
  <form action=\"\" method=\"post\" id=\"form_search\">
    <label> Title Control Name: </label>
    <select id=\"t_control\" name=\"title\" required>
	<?php 
		$table=\"p\".$projector_id.\"Control\";
		
		 $query_control = \"SELECT * FROM $table WHERE  schemeid =\'$scheme_id\' AND name not in (\'systimestamp\', \'recordowner\') ORDER BY name ASC;\";
         $result_control=$bd->query($query_control);
         while($row = mysqli_fetch_array($result_control)){
			$select_title_value=$row{\'name\'};
			echo \'<option value=\"\' . $select_title_value . \'\"\' . ($_POST[\'title\'] == $select_title_value ? \' selected=\"selected\"\' : \'\') . \'>\' . $select_title_value . \'</option>\'; 
          }
	?>
	</select>
    <br>
    <label> Image Control Name: </label>
    <select id=\"img_control\" name=\"img_c\" required>
	<?php 
	    // $pid = $_GET[\'pid\'];
		// $table=\"p\".$pid.\"Control\";
		
		 $query_control = \"SELECT * FROM $table WHERE  schemeid =\'$scheme_id\' AND name not in (\'systimestamp\', \'recordowner\') ORDER BY name ASC;\";
         $result_control=$bd->query($query_control);
         while($row = mysqli_fetch_array($result_control)){
		 			$select_image_value=$row{\'name\'};
            //   echo \"<option value=$select_image_value>\".$select_image_value.\"</option>\";
			  echo \'<option value=\"\' . $select_image_value . \'\"\' . ($_POST[\'img_c\'] == $select_image_value ? \' selected=\"selected\"\' : \'\') . \'>\' . $select_image_value . \'</option>\'; 
         }
	?>
	</select>
    <br>
    <label> Description Control Name: </label>
  	<select id=\"desc_control\" name=\"description\" required>
	<?php 
	    // $pid = $_GET[\'pid\'];
		 //$table=\"p\".$pid.\"Control\";
		
		 $query_control = \"SELECT * FROM $table WHERE  schemeid =\'$scheme_id\' AND name not in (\'systimestamp\', \'recordowner\') ORDER BY name ASC;\";
         $result_control=$bd->query($query_control);
         while($row = mysqli_fetch_array($result_control)){
		 			$select_description_value=$row{\'name\'};		 
            //   echo \"<option value=$select_description_value>\".$select_description_value.\"</option>\";
 			  echo \'<option value=\"\' . $select_description_value . \'\"\' . ($_POST[\'description\'] == $select_description_value ? \' selected=\"selected\"\' : \'\') . \'>\' . $select_description_value . \'</option>\'; 
		}
	?>
	</select>   
    <br>
    <label> Choose an option: </label>
    <input type=\"radio\" class=radio name=\"type\" value=\"infscroll\" <?php if($_POST[\'type\']==\"infscroll\"){echo \'checked=\"checked\"\';}?> />
    Infinite Scroll &nbsp;
    <input type=\"radio\" class=radio name=\"type\" value=\"flexslider\" <?php if($_POST[\'type\']==\"flexslider\"){echo \'checked=\"checked\"\';}?>/>
    Flexslider &nbsp;
    <input type=\"radio\" class=radio name=\"type\" value=\"image\" <?php if($_POST[\'type\']==\"image\"){echo \'checked=\"checked\"\';}?>/>
    Individual Picture-->
	<input type=\"radio\" class=radio name=\"type\" value=\"pagination\" checked>
    pagination &nbsp;

		<br>
    Search for Kora object:
	
    <input type=\"text\" name=\"kid\" />&nbsp by&nbsp
	<input type=\"radio\" class=radio name=\"search_key\" value=\"Title\" checked>title
	<input type=\"radio\" class=radio name=\"search_key\" value=\"Description\">description
	<br>
    <button type=\"submit\" id=\"submit_serach\" name=\"k_search\">Search</button>
	<br>
	<label>Add Details Page:</label>
	<input type=\"checkbox\" name=\"add_details\" id=\"add_details\" value=\"1\" <?php if(isset($_POST[\'add_details\'])){ echo \'checked=\"checked\"\'; }?>  />
	 <label>Select All:</label>
  <input type=\"checkbox\" id=\"select_all\" name=\"select_all\">";
  
}

?>

