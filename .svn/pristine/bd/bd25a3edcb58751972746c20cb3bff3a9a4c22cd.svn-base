
var pdivs = $(".kora_results_container");
 // pagination part     
  var e = document.getElementById("num_per_page_new"); 
  var nb = e.options[e.selectedIndex].value;         

    
    var start = 0;

    var end = start + nb;
    var length = $('.kora-objs .kora-obj').length;
    var list = $('.kora-objs .kora-obj');
    
    list.hide().filter(':lt('+(end)+')').show();
    
    var currentPage = 1;
    $('.prev, .next').click(function(e){
       e.preventDefault();
       if( $(this).hasClass('prev') ){
           start -= nb;
       } else {
           start += nb;
       }
        if (start < 0 ) {
            start = 0;
        } 
        if (start >= length) {
            if (length % nb ==0) {
                 start = length - nb;
            } else {
                 start = length - (length % nb);
            }
           
        }
       end = start + nb;        
       console.log(start);
       console.log(end);
       
       if( start == 0 ) list.hide().filter(':lt('+(end)+')').show();
       else list.hide().filter(':lt('+(end)+'):gt('+(start-1)+')').show();
    });



var koraObjectCount = $('.kora-obj').length; // number of kora objects in search
var selectedCount = 0;
var select_all_search = $('#select_all_search');
var select_all_scheme = $('#select_all_scheme');

var setSelectAllCount = function() {
	$('.select_all_btn').text("Select All " + koraObjectCount + " Object(s)");
};
setSelectAllCount();

var insertShortcodeButton = function() {
	if (selectedCount == 0) {
		$('#selected-kora-objs > input').val('');
		$('#selected-kora-objs').removeClass('slide-selected-objs');
	}
	else {
		$('#selected-kora-objs > input').val(selectedCount + ' object(s) selected // add new object(s)?');
		$('#selected-kora-objs').addClass('slide-selected-objs');
	}
}

$('.kora-obj').click(function() {
	$(this).toggleClass('kora-obj-active');
	if($(this).hasClass('kora-obj-active')) {
		selectedCount++;
		if (selectedCount === koraObjectCount) {
			select_all_search.prop('checked', true);
		}
	}
	else {
		selectedCount--;
		if (selectedCount === 0) {
			select_all_search.prop('checked', false);
		}
	}
	insertShortcodeButton();
});

select_all_search.change(function() {
	if(select_all_search.is(':checked')) {
		$('.kora-obj').addClass('kora-obj-active');
		selectedCount = koraObjectCount;
	}
	else {
		$('.kora-obj').removeClass('kora-obj-active');
		selectedCount = 0;
	}
	insertShortcodeButton();
});


$('#insert_shortcode_new').click(function(){

	var title_control = document.getElementById("title_control").value;
	var desc_control = document.getElementById("desc_control").value;
	var img_control = document.getElementById("img_control").value;
	var audio_control = document.getElementById("audio_control").value;
	var video_control = document.getElementById("video_control").value;
	var type = jQuery("input[name='type']:checked").val(); // pagination or infscroll
	var pagesize = 5;
	if ($('#pic_pagesize').val() != '') {
		pagesize = $('#pic_pagesize').val();
	}
	var i = 0;
	var num_selected = $('.kora-obj-active').length;
    var sid = $('#id_scheme option:selected').val();
	var query;
	$('.kora-obj-active').each(function() {
	  var chk = $(this).attr('id'); // kora objects should have KID as their id attribute
	  if (i == 0 && num_selected == 1 && type == "image") {
			// Type = 'image' will never occur. Type would be 'pagination' or 'scroll'.
			// What should be done with this?
	    query = chk;
	  }
		else if (i == 0 && num_selected == 1) {
	    query = "kid,=," + chk;
	  }
		else if (num_selected > 1 && i == 0) {
	    query = "(kid,=," + chk + ")";
	    i++;
	  }
		else if (num_selected > 1 && i < num_selected) {
	    query += ",or,(kid,=," + chk + ")";
	    i++;
	  };
	});
	if ($('#add_details').is(':checked')) {
		var shortcode =
				"[KORAGALLERY KG_TYPE= '" + type +
			"' KG_IMAGECONTROL= '" + img_control +
			"' KG_AUDIOCONTROL= '" + audio_control +
			"' KG_VIDEOCONTROL= '" + video_control +
			"' KG_TITLECONTROL= '" + title_control +
			"' KG_DESCCONTROL= '" + desc_control +
			"' KG_LINKBASE='" + detailslink + //detailslink is declared at end of postlibrary.php
			"' KGIS_PAGESIZE='" + pagesize +
      "' SID = '" + sid +
			"' QUERY= '" + query +
			"'][/KORAGALLERY]";
	}
	else {
		var shortcode =
			"[KORAGALLERY KG_TYPE= '" + type +
			"' KG_IMAGECONTROL= '" + img_control +
			"' KG_AUDIOCONTROL= '" + audio_control +
			"' KG_VIDEOCONTROL= '" + video_control +
			"' KG_TITLECONTROL= '" + title_control +
			"' KG_DESCCONTROL= '" + desc_control +
			"' KGIS_PAGESIZE='" + pagesize +
    "' SID = '" + sid +
			"' QUERY= '" + query +
			"'][/KORAGALLERY]";
	}

	var win = window.dialogArguments || opener || parent || top;
	win.send_to_editor(shortcode);
});
