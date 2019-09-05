jQuery(document).ready(function(){
	jQuery('#<?php echo $id?> .qx-progress').appear(function(){
		var progress = jQuery(this).data('progress') + '%';
		jQuery(this).find('.qx-progress-bar').css('width', progress);
	});
});