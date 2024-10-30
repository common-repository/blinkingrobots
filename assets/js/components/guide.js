jQuery(document).ready( function( $ ){
	
	$('.is-dismissible.guide').click('on', function(){
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: { 
				action: 'dont_show_notice',
				dont_show_notice: true,
			},
		});
	});
});	