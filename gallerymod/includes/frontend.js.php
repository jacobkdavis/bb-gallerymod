(function($) {

	$(function() {

		new FLBuilderPostGrid({
			id: '<?php echo $id; ?>',
			layout: '<?php echo $settings->layout; ?>',
			postSpacing: '<?php echo empty( $settings->post_spacing ) ? 60 : intval( $settings->post_spacing ); ?>',
			postWidth: '<?php echo empty( $settings->post_width ) ? 300 : intval( $settings->post_width ); ?>',
			matchHeight: {
				default	   : '<?php echo $settings->match_height; ?>',
				large 	   : '<?php echo $settings->match_height_large; ?>',
				medium 	   : '<?php echo $settings->match_height_medium; ?>',
				responsive : '<?php echo $settings->match_height_responsive; ?>'
			},
			isRTL: <?php echo is_rtl() ? 'true' : 'false'; ?>
		});
		
		if (typeof $.fn.magnificPopup !== 'undefined') {
			$('.fl-node-<?php echo $id; ?> .fl-post-gallery').magnificPopup({
				delegate: '.fl-post-gallery-post > a',
				closeBtnInside: false,
				type: 'image',
				gallery: {
					enabled: true,
					navigateByImgClick: true,
				},
				titleSrc: 'title',
			});
		}		


	});

})(jQuery);
