jQuery(document).ready(function() {
	jQuery('<a href="#"  id="kora-gallery" class="button">Insert Kora Gallery</a>').insertAfter('.wp-editor-tools');   
	
   	jQuery('#kora-gallery').click(function(){
   	
   		tb_show('Add Kora Gallery',plugin.url+'/insertgallery.php?pid='+plugin.pid+
   		'&sid='+plugin.sid+'&token='+plugin.token+'&user='+plugin.user+'&pass='+plugin.pass+'&restful='+plugin.restful+'&url='+plugin.url+
   		'&height=200&width=400&TB_iframe=true');
 		
 		return false;
   	});
	
});