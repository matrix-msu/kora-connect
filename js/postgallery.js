jQuery(document).ready(function() {
    var gallery = $( "#Galleries option:selected" ).text();

    //display contents of first gallery
    $.ajax({
        type: "GET",
        async: false,
        url: url_plugin + "ajax/display_gallery.php",
        data: {
            "gallery": gallery
        },
        success: function(data) {
            jQuery(".gal_display").html(data);
        }
    });

    //display contents of any gallery that is switched to
    jQuery('#Galleries').change(function() {
        var gallery = $( "#Galleries option:selected" ).text();
        $.ajax({
            type: "GET",
            async: false,
            url: url_plugin + "ajax/display_gallery.php",
            data: {
                "gallery": gallery
            },
            success: function(data) {
                jQuery(".gal_display").html(data);
            }
        });
    });
    var opts = {
        lines: 13, // The number of lines to draw
        length: 28, // The length of each line
        width: 14, // The line thickness
        radius: 42, // The radius of the inner circle
        scale: 1, // Scales overall size of the spinner
        corners: 1, // Corner roundness (0..1)
        color: '#000', // #rgb or #rrggbb or array of colors
        opacity: 0.5, // Opacity of the lines
        rotate: 0, // The rotation offset
        direction: 1, // 1: clockwise -1: counterclockwise
        speed: 1, // Rounds per second
        trail: 60, // Afterglow percentage
        fps: 20, // Frames per second when using setTimeout() as a fallback for CSS
        zIndex: 2e9, // The z-index (defaults to 2000000000)
        className: 'spinner', // The CSS class to assign to the spinner
        top: '50%', // Top position relative to parent
        left: '50%', // Left position relative to parent
        shadow: false, // Whether to render a shadow
        hwaccel: false, // Whether to use hardware acceleration
        position: 'absolute' // Element positioning
    };

    $('#insert_shortcode_gallery').click(function() {
        var query = '';
        var title = jQuery('#title_control option:selected').val();
        var img_c = jQuery('#img_control option:selected').val();
        var audio_c = jQuery('#audio_control option:selected').val();
        var video_c = jQuery('#video_control option:selected').val();
        var desc = jQuery('#desc_control option:selected').val();
        var type = jQuery("input[name='type']:checked").val();
        var gallery = $( "#Galleries option:selected" ).text();
      
        if ($('#pic_pagesize').val() === '') {
            var pagesize = 5;
        } else {
            var pagesize = $('#pic_pagesize').val();
        }
        var sid = $('#id_scheme option:selected').val();
        //ajax call to build query of gallery objects
        $.ajax({
            type: "GET",
            async: false,
            url: url_plugin + "ajax/gallery_kids.php",
            data: {
                "gallery": gallery
            },
            success: function(data) {
                query = data;
            }
        });
        var i = 0;
        c_true = 0;
        c_false = 0;
        if ($('#add_details').is(':checked')) {
            var shortcode = "[KORAGALLERY KG_TYPE= '" + type +
                "' KG_IMAGECONTROL= '" + img_c +
                "' KG_AUDIOCONTROL= '" + audio_c +
                "' KG_VIDEOCONTROL= '" + video_c +
                "' KG_TITLECONTROL= '" + title +
                "' KG_DESCCONTROL= '" + desc +
                "' KG_LINKBASE='" + detailslink +
                "' KGIS_PAGESIZE='" + pagesize +
                "' SID = '" + sid +
                "' QUERY= '" + query +
                "']  [/KORAGALLERY]";
        } else {
            var shortcode = "[KORAGALLERY KG_TYPE= '" + type +
                "' KG_IMAGECONTROL= '" + img_c +
                "' KG_AUDIOCONTROL= '" + audio_c +
                "' KG_VIDEOCONTROL= '" + video_c +
                "' KG_TITLECONTROL= '" + title +
                "' KG_DESCCONTROL= '" + desc +
                "' KGIS_PAGESIZE='" + pagesize +
                "' SID = '" + sid +
                "' QUERY= '" + query +
                "']  [/KORAGALLERY]";
        }
        //var shortcode="[KORAGALLERY KG_TYPE= '"+type+"' KG_IMAGECONTROL= '"+img_c+"' KG_TITLECONTROL= '"+title+"' KG_DESCCONTROL= '"+desc+"' KG_LINKBASE='"+detailslink+"' KGIS_PAGESIZE='20' QUERY= '"+query+"']  [/KORAGALLERY]";
        var target = document.getElementById('loading')
        var spinner = new Spinner(opts).spin(target);
        var win = window.dialogArguments || opener || parent || top;
        win.send_to_editor(shortcode);

    });


    /*..................*/
    // get first character
    function getFirstCh(c) {
        execScript("tmp=asc(\"" + c + "\")", "vbscript");
        tmp = 65536 + tmp;
        if (tmp >= 45217 && tmp <= 45252) return "A";
        if (tmp >= 45253 && tmp <= 45760) return "B";
        if (tmp >= 45761 && tmp <= 46317) return "C";
        if (tmp >= 46318 && tmp <= 46825) return "D";
        if (tmp >= 46826 && tmp <= 47009) return "E";
        if (tmp >= 47010 && tmp <= 47296) return "F";
        if ((tmp >= 47297 && tmp <= 47613) || (tmp == 63193)) return "G";
        if (tmp >= 47614 && tmp <= 48118) return "H";
        if (tmp >= 48119 && tmp <= 49061) return "J";
        if (tmp >= 49062 && tmp <= 49323) return "K";
        if (tmp >= 49324 && tmp <= 49895) return "L";
        if (tmp >= 49896 && tmp <= 50370) return "M";
        if (tmp >= 50371 && tmp <= 50613) return "N";
        if (tmp >= 50614 && tmp <= 50621) return "O";
        if (tmp >= 50622 && tmp <= 50905) return "P";
        if (tmp >= 50906 && tmp <= 51386) return "Q";
        if (tmp >= 51387 && tmp <= 51445) return "R";
        if (tmp >= 51446 && tmp <= 52217) return "S";
        if (tmp >= 52218 && tmp <= 52697) return "T";
        if (tmp >= 52698 && tmp <= 52979) return "W";
        if (tmp >= 52980 && tmp <= 53688) return "X";
        if (tmp >= 53689 && tmp <= 54480) return "Y";
        if (tmp >= 54481 && tmp <= 62289) return "Z";
        return c.charAt(0);
    }
    // select helper
    SelectHelper = new function() {

        this.init = function() {
            document.addEventListener("onkeypress", function() {
                var elm = event.srcElement;
                if (elm.tagName == "SELECT" && elm.className.indexOf("SelectHelper") == -1) {
                    elm.className += "SelectHelper";
                    elm.addEventListener("onkeypress", SelectHelper.getNextKeyItem);
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
