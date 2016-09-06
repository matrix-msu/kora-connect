var koraObjectCount = $('.kora-obj').length; // number of kora objects in library
var selectedCount = 0;
var select_all = $('#select_all_lib');

// setSelectAllCount changes the text of the select all label to show num of kora objs
var setSelectAllCount = function() {
	$('.select_all_btn').text("Select All " + koraObjectCount + " Object(s)");
};
setSelectAllCount();

var insertShortcodeButton = function() {
	if (selectedCount === 0) {
		$('#selected-kora-objs > input').val('');
		$('#selected-kora-objs').removeClass('slide-selected-objs');
	}
	else {
		$('#selected-kora-objs > input').val(selectedCount + ' object(s) selected // add selected object(s)?');
		$('#selected-kora-objs').addClass('slide-selected-objs');
	}
};

$('.kora-obj').click(function() {
	$(this).toggleClass('kora-obj-active');
	if($(this).hasClass('kora-obj-active')) {
		selectedCount++;
		if (selectedCount === koraObjectCount) {
			select_all.prop('checked', true);
		}
	}
	else {
		selectedCount--;
		if (selectedCount === 0) {
			select_all.prop('checked', false);
		}
	}
	insertShortcodeButton();
});

select_all.change(function() {
	if(select_all.is(':checked')) {
		$('.kora-obj').addClass('kora-obj-active');
		selectedCount = koraObjectCount;
	}
	else {
		$('.kora-obj').removeClass('kora-obj-active');
		selectedCount = 0;
	}
	insertShortcodeButton();
});

var pdivs = $(".kora_results_container");
var num_obj = pdivs.children().size();
if (num_obj > obj_per_page) {
    $('.pagination_footer').append("<div id = 'prev' ><img src='../images/Arrow\ -\ Pagination\ Left.svg'/></div>" +
    "<div id='next' class='border_arrow'><img src='../images/Arrow\ -\ Pagination\ Right.svg'/></div>");
    var pos = 0;
    if(pos === 0){
        for (var i = 0; i < pos + obj_per_page; i++) {
            $(pdivs).children().eq(i).show();
        }
        for (var i = pos + obj_per_page; i < num_obj; i++) {
            $(pdivs).children().eq(i).hide();
        }
    }
    $('#prev').click(function(){
        if ( pos >= 0 + obj_per_page){
            $(this).addClass('border_arrow');
            $('#next').addClass('border_arrow');
            var hide_end = pos + obj_per_page;
            for (var i = pos; i < hide_end; i++) {
                $(pdivs).children().eq(i).hide();
            }
            pos = pos - obj_per_page;
            var show_end = pos + obj_per_page;
            for (var i = pos; i < show_end; i++) {
                $(pdivs).children().eq(i).show();
            }
        }
        if (pos - obj_per_page <= 0) {
            $(this).removeClass('border_arrow');
        }
    });
    $('#next').click(function(){
        if ( pos < num_obj - obj_per_page) {
            $(this).addClass('border_arrow');
            $('#prev').addClass('border_arrow');
            var hide_end = pos + obj_per_page;
            for (var i = pos; i < hide_end; i++) {
                $(pdivs).children().eq(i).hide();
            }
            pos += obj_per_page;
            var show_end = hide_end + obj_per_page;
            for (var i = pos; i < show_end; i++) {
                $(pdivs).children().eq(i).show();
            }
        }
        if (pos + obj_per_page >= num_obj) {
            $(this).removeClass('border_arrow');
        }
    });
}
$('#insert_shortcode_lib').click(function(){
		var title_control = document.getElementById("title_control").value;
		var desc_control = document.getElementById("desc_control").value;
		var img_control = document.getElementById("img_control").value;
		var audio_control = document.getElementById("audio_control").value;
		var video_control = document.getElementById("video_control").value;
		var type = jQuery("input[name='type']:checked").val(); // pagination or infscroll

		var pagesize = 5;
		if ($('#pic_pagesize').val() !== '') {
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

		// var opts = {
		//   lines: 13 // The number of lines to draw
		// , length: 28 // The length of each line
		// , width: 14 // The line thickness
		// , radius: 42 // The radius of the inner circle
		// , scale: 1 // Scales overall size of the spinner
		// , corners: 1 // Corner roundness (0..1)
		// , color: '#000' // #rgb or #rrggbb or array of colors
		// , opacity: 0.5 // Opacity of the lines
		// , rotate: 0 // The rotation offset
		// , direction: 1 // 1: clockwise, -1: counterclockwise
		// , speed: 1 // Rounds per second
		// , trail: 60 // Afterglow percentage
		// , fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
		// , zIndex: 2e9 // The z-index (defaults to 2000000000)
		// , className: 'spinner' // The CSS class to assign to the spinner
		// , top: '50%' // Top position relative to parent
		// , left: '50%' // Left position relative to parent
		// , shadow: false // Whether to render a shadow
		// , hwaccel: false // Whether to use hardware acceleration
		// , position: 'absolute' // Element positioning
		// }
		// var target = document.getElementById('loading')
		// var spinner = new Spinner(opts).spin(target);
		var win = window.dialogArguments || opener || parent || top;
		win.send_to_editor(shortcode);
});
