function captionpix_resize_captions() {
    jQuery('.captionpix-inner img').each ( function() {
    	w=jQuery(this).width();
    	jQuery(this).parent().next().width(w-20);
    });
}
jQuery(document).ready( function() { 
	jQuery(window).resize(function() { captionpix_resize_captions(); });
	setTimeout(function() { captionpix_resize_captions(); },2000);
});