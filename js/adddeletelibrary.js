jQuery(document).ready(function($){
/*	$('#t_control').change(function(){
		if ($('#t_control').val()){
			
			$('#lib_k_search').prop('disabled', false);
			$('#desc_control').removeAttr('required');	
			$('#img_control').removeAttr('required');	
		}
	});
	$('#desc_control').change(function(){
		if ($('#desc_control').val()){
			
			$('#lib_k_search').prop('disabled', false);
			$('#t_control').removeAttr('required');	
			$('#img_control').removeAttr('required');	
		}
	});*/
	var chk; 
	var query='';
	var divs = [];
	
	var pdivs = $(".koraobejct_container_parent");
	var num_obj = pdivs.children().size();
	if (num_obj > 0) {
		$('.pagination_hearder').prepend("<span>GET " + "<strong>" + num_obj + "</strong>" + " Records" + "&nbsp&nbsp<button id = 'prev' ><strong>Prev</strong></button>  &nbsp <button id = 'next' ><strong>Next</strong></button>" + "</span>");
		var pos = 0;
		var pagesize = 10;
		if(pos == 0){
						for (var i = 0; i < pos + pagesize; i++) {
							$(pdivs).children().eq(i).show();
							
						}
						for (var i = pos + pagesize; i < num_obj; i++) {
							$(pdivs).children().eq(i).hide();
							
						}
						
				}
				$('#prev').click(function(){
					if ( pos >= 0 + pagesize){
						
						var hide_end = pos + pagesize;
						for (var i = pos; i < hide_end; i++) {
							
								$(pdivs).children().eq(i).hide();
								
						}
						
						pos = pos - pagesize;
						
						var show_end = pos + pagesize;
						for (var i = pos; i < show_end; i++) {
							
								$(pdivs).children().eq(i).show();
								
						}
						
					}
				});
				$('#next').click(function(){
					if ( pos < num_obj - pagesize) {
					
						var hide_end = pos + pagesize;
						for (var i = pos; i < hide_end; i++) {
							
								$(pdivs).children().eq(i).hide();
								
						}
						
						pos += pagesize;
						
						var show_end = hide_end + pagesize;
						for (var i = pos; i < show_end; i++) {
						
							$(pdivs).children().eq(i).show();
								
						}
						
					}
				});
	}
	
	
	
	
	$('#select_all_scheme').change(function(){
			if($('#select_all_scheme').is(':checked')){
				$('#lib_k_search').hide();
				$('#lib_k_search').prop('disabled', false);
				$('#lib_k_search').click();
				
			} else {
				$('#lib_k_search').show();
				divs = [];
			}
		});
		if($('#select_all_scheme').is(':checked')){
			$('#lib_k_search').hide();
			divs=$(".koraobj_container");
			$('#select_all_scheme').prop('disabled', false);
			$(".koraobj_container").hide();			
		}
		
	$('.array_control').change(function(){
		var array_control = $('.array_control').val();
		if (array_control) {
				$('#lib_k_search').prop('disabled', false);
				$('#select_all_scheme').prop('disabled', false);
			}
	});
	if ($('.array_control').val()) {
		$('#lib_k_search').prop('disabled', false);
		$('#select_all_scheme').prop('disabled', false);
	}
	
	//search process bar
	$('#lib_k_search').click(function () {
		if($('#select_all_scheme').is(':checked')){
			
		} else {
			// add loading image to div
			$('#loading').html('<img src="http://preloaders.net/preloaders/287/Filling%20broken%20ring.gif"> loading...');
			
			// run ajax request
			$.ajax({
				//type: "GET",
			   // dataType: "json",
			   // url: "https://api.github.com/users/jveldboom",
				success: function (d) {
					// replace div's content with returned data
					// $('#loading').html('<img src="'+d.avatar_url+'"><br>'+d.login);
					// setTimeout added to show loading
					setTimeout(function () {
					   // $('#loading').html('<img src="' + d.avatar_url + '"><br>' + d.login);
					}, 2000);
				}
			});
		}
	});
	
	$('#select_all').change(function() {
		if($('#select_all').is(':checked')){
			$(".koraobj_container *").css("background-color", "#D1D1E0");
			divs=$(".koraobj_container");
				
			if(divs.length >= 1){
				$('#gallery_select').show();
			}
			else{
				$('#gallery_select').hide();
			}
		}
		else{
			if(divs.length>=1){
				divs.splice(0,divs.length)	
			}
		
			$(".koraobj_container *").css("background-color", "#FFFFFF");
				
			if(divs.length >= 1){
				$('#gallery_select').show();
			}
			else{
				$('#gallery_select').hide();
			}
		
		}
	});	

/*	
	$('#select_all').click(function(){
		
		$(".koraobj_container *").css("background-color", "#D1D1E0");
		divs=$(".koraobj_container");
				
		if(divs.length >= 1){
			$('#gallery_select').show();
		}
		else{
			$('#gallery_select').hide();
		}
		
	});

	$('#clear_selected').click(function(){
		if(divs.length>=1){
			divs.splice(0,divs.length)	
		}
		
		$(".koraobj_container *").css("background-color", "#FFFFFF");
				
		if(divs.length >= 1){
			$('#gallery_select').show();
		}
		else{
			$('#gallery_select').hide();
		}
		
		//alert(divs.length);
	});
*/
	$(".koraobj_container").click(function(){
		
		//If it's been selected, remove it from the array and turn the shade back to white
		if(this.style.backgroundColor == "rgb(209, 209, 224)"){
			this.style.backgroundColor = "#FFFFFF";
			for (var i =0; i < divs.length; i++){
		        if (divs[i].id === $(this).attr('id')) {
		            divs.splice(i,1);
					break;
		        }
		    }
		}
		//If it has not been selected, change the shade and add to the array
		else{
			//get if there is a library already added.
			var x=document.getElementById("k"+$(this).attr('id'));
			if(!x){
				this.style.backgroundColor = "#D1D1E0";
				divs.push(this);
			}
			else{
				alert("This picture already in the library.")
			}

		}
		});
var opts = {
  lines: 13 // The number of lines to draw
, length: 28 // The length of each line
, width: 14 // The line thickness
, radius: 42 // The radius of the inner circle
, scale: 1 // Scales overall size of the spinner
, corners: 1 // Corner roundness (0..1)
, color: '#000' // #rgb or #rrggbb or array of colors
, opacity: 0.5 // Opacity of the lines
, rotate: 0 // The rotation offset
, direction: 1 // 1: clockwise, -1: counterclockwise
, speed: 1 // Rounds per second
, trail: 60 // Afterglow percentage
, fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
, zIndex: 2e9 // The z-index (defaults to 2000000000)
, className: 'spinner' // The CSS class to assign to the spinner
, top: '50%' // Top position relative to parent
, left: '50%' // Left position relative to parent
, shadow: false // Whether to render a shadow
, hwaccel: false // Whether to use hardware acceleration
, position: 'absolute' // Element positioning
}
	//Clicking insert button
	//To add a library object (without using pages)
	$('#newobj').click(function() {
		var c_true=0;
		var c_false=0;
		//If no objects are checked
		if(divs.length == 0){
			window.alert("No Objects Selected!");
		}
		//When objects have been checked
		$(divs).each(function(){
			var chk = $(this).attr('id');
			$.ajax({
				type: "GET",
				url: plugin_dir_url+"ajax/insert_library.php",
				data: {"chk": chk},
				async: false,
				success: function(data){
					var target = document.getElementById('loading')
					var spinner = new Spinner(opts).spin(target);
					if(data==false){
						//alert("Object is already in the library");
						parent.location.reload();

					}else{
						alert(data.url);
						var obj = "<div class=\"lib_obj\"><span class='close_lib'>&times;</span><div class='lib_image'><a class = 'popupImage' href = '+data.url+'><img src="+data.url+" alt="+data.KID+"></a></div><div class='lib_title'>"+data.title+"</div></div>";
						$("#wpbody-content").append(obj);
					    parent.location.reload();
					}
					$('.koraobj_container').hide();
				},
				dataType:"json"
			});	
		});
	});

//To delete library objects
jQuery('.lib_obj .close_lib').on('click', function(){
	//alert(jQuery(this).closest('.lib_obj').attr('id'));
		if (confirm("Are you sure?")) {
			//var kid = jQuery(this).closest('.lib_obj').find('img').attr('alt');
			var kid = jQuery(this).closest('.lib_obj').attr('id').substring(1);
			//alert(title);
			var library = 'koralibrary';
			//add in prefix to library
			jQuery(this).closest('.lib_obj').fadeOut();
			jQuery.ajax({
				type: "GET",
				async: false,
				url: plugin.url+"ajax/delete_library.php",
				data: {"kid": kid, "library": library },
				success: function(data){
					//alert(data);
					console.log(data);
					parent.location.reload();
				}
			});
		} else {
			parent.location.reload();
		}
	});
jQuery('.lib_obj').on('click', function(){	
	var kid = jQuery(this).closest('.lib_obj').attr('id').substring(1);
	//alert(kid);
	$('.popupImage').colorbox({ opacity:0.5 , rel:'group1' });

});

			/*..................*/
			// get first character
		function getFirstCh(c) {
			execScript("tmp=asc(\""+c+"\")", "vbscript");
			tmp = 65536 + tmp;
			if(tmp>=45217 && tmp<=45252) return "A";
			if(tmp>=45253 && tmp<=45760) return "B";
			if(tmp>=45761 && tmp<=46317) return "C";
			if(tmp>=46318 && tmp<=46825) return "D";
			if(tmp>=46826 && tmp<=47009) return "E";
			if(tmp>=47010 && tmp<=47296) return "F";
			if((tmp>=47297 && tmp<=47613) || (tmp == 63193)) return "G";
			if(tmp>=47614 && tmp<=48118) return "H";
			if(tmp>=48119 && tmp<=49061) return "J";
			if(tmp>=49062 && tmp<=49323) return "K";
			if(tmp>=49324 && tmp<=49895) return "L";
			if(tmp>=49896 && tmp<=50370) return "M";
			if(tmp>=50371 && tmp<=50613) return "N";
			if(tmp>=50614 && tmp<=50621) return "O";
			if(tmp>=50622 && tmp<=50905) return "P";
			if(tmp>=50906 && tmp<=51386) return "Q";
			if(tmp>=51387 && tmp<=51445) return "R";
			if(tmp>=51446 && tmp<=52217) return "S";
			if(tmp>=52218 && tmp<=52697) return "T";
			if(tmp>=52698 && tmp<=52979) return "W";
			if(tmp>=52980 && tmp<=53688) return "X";
			if(tmp>=53689 && tmp<=54480) return "Y";
			if(tmp>=54481 && tmp<=62289) return "Z";
			return c.charAt(0);
		} 
		// select helper
		SelectHelper = new function() {

		this.init = function() {
		document.attachEvent("onkeypress", function() {
		var elm = event.srcElement;
		if (elm.tagName == "SELECT" 
		&& elm.className.indexOf("SelectHelper") == -1) {
		elm.className += "SelectHelper";
		elm.attachEvent("onkeypress", SelectHelper.getNextKeyItem);
		elm.fireEvent("onkeypress", event);
		}
		});
		}

		function getItemKeyChar(option) {
		return option.text.charAt(0).toUpperCase();
		}

		this.getNextKeyItem = function() {
		var elm = event.srcElement;
		var index = elm.selectedIndex + 1;
		do {
		if (index == elm.length) index = 0; 
		if (index == elm.selectedIndex) return false; 
		} while (key2Char(event.keyCode) != getFirstCh(getItemKeyChar(elm.options[index++])));
		elm.selectedIndex = index - 1; 
		return false; 
		}
		};
		/*
		* a-z: 97 -> 122 
		* A-Z: 65 -> 90
		* 0-9: 48 -> 57 
		*/
		function key2Char(key) {
		var s = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if (key >= 97 && key <= 122) return s.charAt(key - 97);
		if (key >= 65 && key <= 90) return s.charAt(key - 65);
		if (key >= 48 && key <= 57) return "" + (key - 48);
		return null;
		}
		SelectHelper.init();
	

	
});
