jQuery(document).ready(function () {
//alert(plugin.pid);
//to add gallery items/ galleries

	//jQuery(".kora_gallery_button").append('<a href="#" id="kora-gallery-add" class="button">Add NEW GALLERY</a>');
	//jQuery(".addtogallery").append('<a href = "#" id="kora-gallery-addmore" class = "button">Add to EXISTING GALLERY</a>');

	jQuery('.addNewGallery').click(function () {
    
		tb_show('Add New Kora Gallery',plugin.url+'/kora_newgallery.php?pid='+plugin.pid+
   		'&sid='+plugin.sid+'&token='+plugin.token+'&user='+plugin.user+'&pass='+plugin.pass+'&restful='+plugin.restful+'&url='+plugin.url+
   		'&height=200&width=400&TB_iframe=true');
  
   		return false;
	});
	
	jQuery('.addToGallery').click(function () {
		var gallery_id = $(this).attr('id');
		//alert(gallery_id);

		tb_show('Add to Existing Kora Gallery',plugin.url+'/kora_addtogallery.php?galleryid='+gallery_id+'&pid='+plugin.pid+
   		'&sid='+plugin.sid+'&token='+plugin.token+'&user='+plugin.user+'&pass='+plugin.pass+'&restful='+plugin.restful+'&url='+plugin.url+
   		'&height=200&width=400&TB_iframe=true');
		return false;
	});


//to delete gallery items/ galleries	
	
	jQuery('.gal_image .closePic').on('click', function(){
		if (confirm("Are you sure?")) {
		//	var kid = jQuery(this).closest('.gal_image').find('img').attr('alt');
			var kid = jQuery(this).closest('.gal_image').attr('id');
			//alert(kid);
			var gallery = jQuery(this).closest('.gal_display').find('.gal_title').text();
			console.log(kid);
			console.log(gallery);
			jQuery(this).closest('.gal_image').fadeOut();
			jQuery.ajax({
				type: "POST",
				async: false,
				url: plugin.url+"ajax/delete_gallery.php",
				data: {"kid": kid, "gallery": gallery },
				success: function(data){
					location.reload(true);
				}
			});
		} else {
			location.reload(true);
		}
	});
	
	jQuery('.close').on('click', function(){
	
		var elem = $(this).closest('.item');

		$.confirm({
			'title'		: 'Remove this gallery?',
			'buttons'	: {
				'REMOVE'	: {
					'class': 'red',
					'action': function(){
						elem.slideUp();
						jQuery.ajax({
						type: "GET",
						async: false,
						url: plugin.url+"ajax/delete_gallery.php",
						data: {"id": this.id},
						success: function(data){
							location.reload(true);
						}
						});
					}
				},
				'CANCEL'	: {
					'class': 'grey',
					'action': function(){}	// Nothing to do in this case. You can as well omit the action property.
				}
			}
		});

	});

	jQuery('.gal_record').on('click', function(evt){
		$(evt.target).toggleClass('selected');
	})
	
	//confirm function
	
	$.confirm = function(params){

		if($('#confirmOverlay').length){
			// A confirm is already shown on the page:
			return false;
		}

		var buttonHTML = '';
		$.each(params.buttons,function(name,obj){

			// Generating the markup for the buttons:

			buttonHTML += '<span href="#" class="confirmButton '+obj['class']+'">'+name+'</span>';

			if(!obj.action){
				obj.action = function(){};
			}
		});

		var markup = [
			'<div id="confirmOverlay">',
			'<div id="confirmBox">',
			'<div class="innerBorder">',
			'<p class="confirmClose"><img src="../wp-content/plugins/kora/images/Close.svg"></p>',
			'<p>',params.title,'</p>',
			'<div id="confirmButtons">',
			buttonHTML,
			'</div></div></div></div>'
		].join('');

		$(markup).hide().appendTo('body').fadeIn();

		var buttons = $('#confirmBox .confirmButton'),
			i = 0;

		$.each(params.buttons,function(name,obj){
			buttons.eq(i++).click(function(){

				// Calling the action attribute when a
				// click occurs, and hiding the confirm.

				obj.action();
				$.confirm.hide();
				return false;
			});
		});
		
		$('#confirmBox .innerBorder p.confirmClose').click(function(){
			$.confirm.hide();
			
		});
	}

	$.confirm.hide = function(){
		$('#confirmOverlay').fadeOut(function(){
			$(this).remove();
		});
	}
	
});
