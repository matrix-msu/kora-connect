// hide script from old browsers

jQuery(document).ready(function ($) {

//............

    // HAVE TO LOOP HERE TO HANDLE POTENTIAL MULTIPLE GALLERIES
    $('.kora_gallery_pagination').each(function () {
        //Plugin Controls
        var kg_imagectl = $(this).attr('kgictrl');         //image control
        var kg_audioctl = $(this).attr('kgactrl');         //audio control
        var kg_videoctl = $(this).attr('kgvctrl');         //video control
        var kg_titlectl = $(this).attr('kgtctrl');         //title control
        var kg_descctl = $(this).attr('kgdctrl');         //description control
        var kg_linkbase = $(this).attr('kglbase');         //Link
        var kg_filebase = $(this).attr('kgfbase');         //File base
        var kg_imagesize = $(this).attr('kgisize');         //Image size
        var kg_sort = $(this).attr('kg_sort');         //Sort by this control
        var kg_order = $(this).attr('kg_order');        //SORT ASC -or- SORT DESC
        var kg_loadimg = '';                              // THIS IS ONLY VALID FOR KGIS
        var kg_fspropsobj = $(this);                        //Object
        var kg_imageclip = $(this).attr('kgfs_imageclip');  //Image Clip
        var kg_pagesize = $(this).attr('kg_pagesize');      //perpage how many pics
        var kg_field = $(this).attr('kgfield');
        var tarresturl_str = kg_fspropsobj.attr('kgresturl');
        var tarresturl = tarresturl_str.split(";");
        //	alert(tarresturl.length);
        var kg_baseresturl = [];
        for (var i = 0; i < tarresturl.length; i++) {
            kg_baseresturl[i] = tarresturl[i].match(/.*&display=/);
            kg_baseresturl[i] = kg_baseresturl[i][0];
        }
        var pics = [];
        $.ajaxSetup({async: false});
        kg_fspropsobj.append("<div class='pagination'><ul class='pages' /><button id='prev'>Prev</button><br><br><button id='next'>Next</button></div>");
        var sum = 0;
        var pos = 0;
        var pagesize = parseInt(kg_pagesize);

        for (var i = 0; i < tarresturl.length; i++) {
            console.log(tarresturl);
            $.getJSON(
                tarresturl[i],
                function (data) {
                    console.log('hi');
                    $.each(data, function (key, val) {

                        var htmlobj = KoraGalleryObjJSONToHtml(val, kg_fspropsobj, kg_imagectl, kg_audioctl, kg_videoctl, kg_titlectl, kg_descctl, kg_sort, kg_order, kg_linkbase, kg_filebase, kg_imagesize, kg_loadimg, kg_baseresturl[i], kg_imageclip, kg_field);
                        pics.push(htmlobj);
                        sum = sum + 1;
                    });

                }
            ).fail(function (jqxhr, textStatus, error) {
                    var err = textStatus + ', ' + error;
                    console.log("Request Failed: " + err);
                })
        }

        for (var i = 0; i < pics.length; i++) {
            kg_fspropsobj.children('div.pagination:first').children('ul.pages:first').append("<li style='display:none; list-style-type:none'>" + pics[i] + "</li>");

        }
        if (pos == 0) {
            for (var i = 0; i < pos + pagesize; i++) {
                kg_fspropsobj.children('div.pagination:first').children('ul.pages:first').children('li').eq(i).show();
            }
            for (var i = pos + pagesize; i < sum; i++) {
                kg_fspropsobj.children('div.pagination:first').children('ul.pages:first').children('li').eq(i).hide();
            }
            //kg_fspropsobj.children('div.pagination:first').children('ul.pages:first').children('li').eq(0).show();
        }
        $('#prev').click(function () {
            if (pos >= 0 + pagesize) {
                //alert(pos);
                var hide_end = pos + pagesize;
                for (var i = pos; i < hide_end; i++) {
                    //if (i <= sum - 1 && i > = 0) {
                    kg_fspropsobj.children('div.pagination:first').children('ul.pages:first').children('li').eq(i).hide();
                    //}
                }
                //kg_fspropsobj.children('div.pagination:first').children('ul.pages:first').children('li').eq(pos).hide();
                //pos=pos-1;
                pos = pos - pagesize;
                //alert(pos);
                var show_end = pos + pagesize;
                for (var i = pos; i < show_end; i++) {
                    //if (i <= sum - 1 && i > = 0) {
                    kg_fspropsobj.children('div.pagination:first').children('ul.pages:first').children('li').eq(i).show();
                    //}
                }
                //kg_fspropsobj.children('div.pagination:first').children('ul.pages:first').children('li').eq(pos).show();
                //alert(pos);
            }
        });
        $('#next').click(function () {
            if (pos < sum - pagesize) {
                //alert(pos);
                var hide_end = pos + pagesize;
                for (var i = pos; i < hide_end; i++) {
                    //if (i <= sum - 1 && i > = 0) {
                    kg_fspropsobj.children('div.pagination:first').children('ul.pages:first').children('li').eq(i).hide();
                    //}
                }
                //kg_fspropsobj.children('div.pagination:first').children('ul.pages:first').children('li').eq(pos).hide();
                //pos=pos+1;
                pos += pagesize;
                //alert(pos);
                var show_end = hide_end + pagesize;
                for (var i = pos; i < show_end; i++) {
                    //	if (i <= sum - 1 && i > = 0) {
                    kg_fspropsobj.children('div.pagination:first').children('ul.pages:first').children('li').eq(i).show();
                    //}
                }
                //kg_fspropsobj.children('div.pagination:first').children('ul.pages:first').children('li').eq(pos).show();
                //alert(pos);
            }
        });

        
    });

//.....
    $('.kora_gallery_infscroll1').each(function () {
        //Plugin Controls
        var kg_imagectl = $(this).attr('kgictrl');         //image control
        var kg_audioctl = $(this).attr('kgactrl');         //audio control
        var kg_videoctl = $(this).attr('kgvctrl');         //video control
        var kg_titlectl = $(this).attr('kgtctrl');         //title control
        var kg_descctl = $(this).attr('kgdctrl');         //description control
        var kg_linkbase = $(this).attr('kglbase');         //Link
        var kg_filebase = $(this).attr('kgfbase');         //File base
        var kg_imagesize = $(this).attr('kgisize');         //Image size
        var kg_sort = $(this).attr('kg_sort');         //Sort by this control
        var kg_order = $(this).attr('kg_order')         //SORT ASC -or- SORT DESC
        var kg_loadimg = '';                              // THIS IS ONLY VALID FOR KGIS
        var kg_fspropsobj = $(this);                        //Object
        var kg_imageclip = $(this).attr('kgfs_imageclip');  //Image Clip
        var kg_pagesize = $(this).attr('kg_pagesize');      //perpage how many pics
        var kg_field = $(this).attr('kgfield');
        //ResutfulAPI setup
        var tarresturl_str = kg_fspropsobj.attr('kgresturl');
        var tarresturl = tarresturl_str.split(";");
        //	alert(tarresturl.length);
        var kg_baseresturl = [];
        for (var i = 0; i < tarresturl.length; i++) {
            kg_baseresturl[i] = tarresturl[i].match(/.*&display=/);
            kg_baseresturl[i] = kg_baseresturl[i][0];
        }
        var pics = [];
        $.ajaxSetup({async: false});
        kg_fspropsobj.append("<div class='scroll'></div>");
        var sum = 0;
        var pos = 0;
        var pagesize = kg_pagesize;
        for (var i = 0; i < tarresturl.length; i++) {
            $.getJSON(
                tarresturl[i],
                function (data) {
                    $.each(data, function (key, val) {
                         if (kg_audioctl=='default' && kg_videoctl=='default' && kg_imagectl=='default'){
                             kg_fspropsobj.children('div.scroll').append("<p>Please provide an image control, an audio control, or a video control</p>");
                        
                            return false;
                         }
                    
                        var htmlobj = KoraGalleryObjJSONToHtml(val, kg_fspropsobj, kg_imagectl, kg_audioctl, kg_videoctl, kg_titlectl, kg_descctl, kg_sort, kg_order, kg_linkbase, kg_filebase, kg_imagesize, kg_loadimg, kg_baseresturl[i], kg_imageclip, kg_field, '', '');
                        //kg_fspropsobj.children('div.scroll').append("<p>"+htmlobj+"</p>");
                        pics.push(htmlobj);
                        sum = sum + 1;
                    });
                    
                }
            ).fail(function () {
                    console.log("error");
                })
        }
        
        var i = 0;
        if(pics.length>0){
            while (i < pagesize) {
                kg_fspropsobj.children('div.scroll').append("<p>" + pics[i] + "</p>" + "<div id = 'loading' align='center'></div>");
                i += 1;
            }
        }else{
               kg_fspropsobj.children('div.scroll').append("<p> No results from RestfulAPI</p>" + "<div id = 'loading' align='center'></div>");
             
        }
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

        function loadMoreContent() {
            additems=i+parseInt(pagesize);
            while (i < additems && i<sum) {
                kg_fspropsobj.children('div.scroll').append("<p>" + pics[i] + "</p>");
                i += 1;
                var target = document.getElementById('loading')
               // var spinner = new Spinner(opts).spin(target);
            }
        };

        $(window).scroll(function () {
            if($(window).scrollTop() + $(window).height() > $(document).height() - 80) {
                if(i<sum){
                 loadMoreContent();
                }
            }
        });

      });
    var kgif_lastgal = 0;
    var kgif_currgal = 0;
    var kgif_currpage = new Array();
    var kgif_isloading = false;
    $('.kora_gallery_infscroll1').each(function () {
        kgif_currpage[kgif_lastgal] = 0;
        $(this).attr('id', 'kora_gallery_infscroll_' + kgif_lastgal);
        // ADD THESE TAGS ONLY ONCE
        if (kgif_lastgal === 0) {
            $('body').prepend("<div id='nomore'>No more content</div>");
            $('#nomore').hide();
        }

        // LOAD THE 1ST PAGE OF EACH GALLERY HERE, NO NEED TO WAIT FOR TRIGGER FOR FIRST PAGE
        if (!kgif_isloading) {

            LoadGalleryPage(kgif_lastgal, 0);
        }

        kgif_lastgal++;
    });
    // THIS GETS TAG ATTS THAT ARE SUPPOSED TO BE BOOLEAN COMING IN AS 1 OR 0, OR JUST PROPERTY W/ NO VALUE AND RETURNS TRUE/FALSE
    function GetTagAttBool(val_) {
        if (val_ === '1') {
            return true;
        }
        else {
            return false;
        }
    }

    function LoadGalleryPage(id_, os_) {
        kgif_isloading = true;

        var targal = $("#kora_gallery_infscroll_" + id_);
        var tarresturl = targal.attr('kgresturl');
        var tarpgsz = targal.attr('kgis_pagesize');
        var kg_loadimg = targal.attr('kgis_loadimg');

        var kg_imagectl = targal.attr('kgictrl');
        var kg_audioctl = $(this).attr('kgactrl');         //audio control
        var kg_videoctl = $(this).attr('kgvctrl');         //video control
        var kg_titlectl = targal.attr('kgtctrl');
        var kg_descctl = targal.attr('kgdctrl');
        var kg_linkbase = targal.attr('kglbase');
        var kg_filebase = targal.attr('kgfbase');
        var kg_imagesize = targal.attr('kgisize');
        var kg_imageclip = targal.attr('kgfs_imageclip');
        var kg_sort = targal.attr('kg_sort');
        var kg_order = targal.attr('kg_order');
        var kg_field = $(this).attr('kgfield');
        var tarresturl1 = tarresturl.split(",");
        var kg_baseresturl = [];
        for (var i = 0; i < tarresturl1.length; i++) {
            kg_baseresturl[i] = tarresturl1[i].match(/.*&display=/);
            kg_baseresturl[i] = kg_baseresturl[i][0];
        }
        var user = targal.attr('user');
        var pass = targal.attr('pass');
        var retval = true;
        var pics1 = [];

        sum = 0;

        for (var i = 0; i < tarresturl1.length; i++) {
            $.ajax({
                type: "GET",
                url: tarresturl1[i] + '&first=' + (kgif_currpage[id_] * tarpgsz) + '&count=' + tarpgsz,
                dataType: "json",
                success: function (data) {

                    if (data.length === 0) {
                        kgif_currpage[kgif_currgal] = -1;
                        kgif_isloading = false;
                        return;
                    }
                    $.each(data, function (key, val) {
                        var htmlobj = KoraGalleryObjJSONToHtml(val, $("#kora_gallery_infscroll_" + id_), kg_imagectl, kg_audioctl, kg_videoctl, kg_titlectl, kg_descctl, kg_sort, kg_order, kg_linkbase, kg_filebase, kg_imagesize, kg_loadimg, kg_baseresturl[i], kg_imageclip, kg_field, user, pass);
                        pics1.push(htmlobj);
                        sum += 1;
                    });
                    kgif_isloading = false;
                    kgif_currpage[kgif_currgal]++;
                }
            });
        }
       
        return retval;
    }

    function changeObjWidth() {
        var obj = document.getElementsByClassName('kgfs_object');
        var objLength = obj.length;
        for (var i = 0; i < objLength; i++) {
            obj[i].style.width = '';
        }
    }

    function KoraGalleryObjJSONToHtml(obj_, kgifobj_, ictrl_, actrl_, vctrl_, tctrl_, dctrl_, sort_, order_, lbase_, fbase_, isize_, kg_loadimg_, restbaseurl_, imageclip_, fields, user, pass) {
        var retval = '';
        retval += "<div class='kgfs_object' kid='" + obj_.kid + "' >";
        // IF WE CAN FIND AN IMAGE
        arpsid=obj_.kid.split('-');       
        pid=parseInt(arpsid[0], 16);
        sid=parseInt(arpsid[1], 16);
        if (ictrl_!='default' && (typeof obj_[ictrl_].localName !== 'undefined') && (obj_[ictrl_].localName != '')) {
            var imgsrc = "";
            if (isize_ === 'full') {
                imgsrc = "<img src='" + fbase_ + obj_[ictrl_].localName + "' />";
            } else {
                // THIS URL WILL START WITH PID/SID/TOKEN AND display= SO WE START APPENDING THERE
             //   imgresturl = restbaseurl_ + 'tn&query=' + encodeURI('KID,=,' + obj_.kid) + '&tn_imageclip=' + imageclip_ + '&sort=' + sort_ + '&order=' + order_;
                // THIS IS FOR KGIS, KGFS WILL OVERWRITE THIS BELOW DUE TO SYNC
                

                imgsrc='<img class="koraobj_tn koraobj_tn_large" src="'+fbase_+pid+'/'+sid+'/'+ obj_[ictrl_].localName+'">';
                  
            }

            retval += "<div class='kgfs_img'>";
            if ((typeof lbase_ !== 'undefined') && (lbase_ != '')) {
                retval += "<form name='detail" + obj_.kid + "' action='" + lbase_ + "?kid=" + obj_.kid + "' method='post' enctype='multipart/form-data'>" +
                "<input type=hidden name='restful' value='" + restbaseurl_ + "'/>" +
                "<input type=hidden name='fields' value='" + fields + "'/>" +
                "</form>"
                + "<a href='#' onclick='document.forms[" + '"detail' + obj_.kid + '"' + "].submit(); return false;'>";
            }
            retval += imgsrc;
            if ((typeof lbase_ !== 'undefined') && (lbase_ != '')) {
                retval += "</a>";
            }
            retval += "</div>";
        }
        if (actrl_!='default' && (typeof obj_[actrl_].localName !== 'undefined') && (obj_[actrl_].localName != '')) {
            audiosrc= '<video class="koraobj_tn koraobj_tn_large"  width="218" height="128" controls><source src="'+ fbase_ +pid+'/'+sid+'/'+obj_[actrl_].localName+'" type="audio/mpeg"></video>';
          
             retval += "<div class='kgfs_img'>";
            if ((typeof lbase_ !== 'undefined') && (lbase_ != '')) {
                retval += "<form name='detail" + obj_.kid + "' action='" + lbase_ + "?kid=" + obj_.kid + "' method='post' enctype='multipart/form-data'>" +
                "<input type=hidden name='restful' value='" + restbaseurl_ + "'/>" +
                "<input type=hidden name='fields' value='" + fields + "'/>" +
                "</form>"
                + "<a href='#' onclick='document.forms[" + '"detail' + obj_.kid + '"' + "].submit(); return false;'>";
            }
            retval += audiosrc;
            if ((typeof lbase_ !== 'undefined') && (lbase_ != '')) {
                retval += "</a>";
            }
            retval += "</div>";
        }
        if (vctrl_!='default' && (typeof obj_[vctrl_].localName !== 'undefined') && (obj_[vctrl_].localName != '')) {
            videosrc= '<video class="koraobj_tn koraobj_tn_large" width="218" height="128" controls><source src="'+ fbase_ + pid+'/'+sid+'/'+obj_[vctrl_].localName+'" type="video/mp4"></video>';
          
             retval += "<div class='kgfs_img'>";
            if ((typeof lbase_ !== 'undefined') && (lbase_ != '')) {
                retval += "<form name='detail" + obj_.kid + "' action='" + lbase_ + "?kid=" + obj_.kid + "' method='post' enctype='multipart/form-data'>" +
                "<input type=hidden name='restful' value='" + restbaseurl_ + "'/>" +
                "<input type=hidden name='fields' value='" + fields + "'/>" +
                "</form>"
                + "<a href='#' onclick='document.forms[" + '"detail' + obj_.kid + '"' + "].submit(); return false;'>";
            }
            retval += videosrc;
            if ((typeof lbase_ !== 'undefined') && (lbase_ != '')) {
                retval += "</a>";
            }
            retval += "</div>";
        }
        if (actrl_=='default' && vctrl_=='default' && ictrl_=='default'){
              retval += "<div class='kgfs_img'>";
              retval += "<p>Please provide an image control, an audio control, or a video control</p>";
              retval += "</div>";

        }
        // TITLE GOES ABOVE
        if ((typeof tctrl_ !== 'undefined') && (tctrl_ != '')) {
            retval += "<div class='kgfs_title'>";
            if ((typeof lbase_ !== 'undefined') && (lbase_ != '')) {
                retval += "<form name='detail" + obj_.kid + "' action='" + lbase_ + "?kid=" + obj_.kid + "' method='post' enctype='multipart/form-data'>" +
                "<input type=hidden name='restful' value='" + restbaseurl_ + "'/>" +
                "<input type=hidden name='fields' value='" + fields + "'/>" +

                "</form>"
                + "<a href='#' onclick='document.forms[" + '"detail' + obj_.kid + '"' + "].submit(); return false;'>";
            }
            retval += obj_[tctrl_];
            if ((typeof lbase_ !== 'undefined') && (lbase_ != '')) {
                retval += "</a>";
            }
            retval += "</div>";
        }
        // DESCRIPTION GOES BELOW
        if ((typeof dctrl_ !== 'undefined') && (dctrl_ != '')) {
            retval += "<div class='kgfs_desc'>";
            if ((typeof lbase_ !== 'undefined') && (lbase_ != '')) {
                retval += "<form name='detail" + obj_.kid + "' action='" + lbase_ + "?kid=" + obj_.kid + "' method='post' enctype='multipart/form-data'>" +
                "<input type=hidden name='restful' value='" + restbaseurl_ + "'/>" +
                "<input type=hidden name='fields' value='" + fields + "'/>" +

                "</form>"
                + "<a href='#' onclick='document.forms[" + '"detail' + obj_.kid + '"' + "].submit(); return false;'>";
            }
            retval += obj_[dctrl_];
            if ((typeof lbase_ !== 'undefined') && (lbase_ != '')) {
                retval += "</a>";
            }
            retval += "</div>";
        }
        retval += "</div>";

        return retval;
    }
});

// end hiding script from old browsers -->
