<?php if ( ! empty( $settings->text_color ) ) : ?>
.fl-node-<?php echo $id; ?> .fl-post-gallery-link,
.fl-node-<?php echo $id; ?> .fl-post-gallery-link .fl-post-gallery-title {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->text_color ); ?>;
}
<?php endif; ?>

.fl-node-<?php echo $id; ?> .fl-post-gallery-text-wrap{
	background-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->text_bg_color ); ?>;
}

<?php if ( isset( $settings->hover_transition ) && 'fade' != $settings->hover_transition ) : ?>
	.fl-node-<?php echo $id; ?> .fl-post-gallery-text{
	<?php if ( 'slide-up' == $settings->hover_transition ) : ?>
		-webkit-transform: translate3d(-50%,-30%,0);
			-moz-transform: translate3d(-50%,-30%,0);
			-ms-transform: translate(-50%,-30%);
				transform: translate3d(-50%,-30%,0);
	<?php elseif ( 'slide-down' == $settings->hover_transition ) : ?>
		-webkit-transform: translate3d(-50%,-70%,0);
			-moz-transform: translate3d(-50%,-70%,0);
			-ms-transform: translate(-50%,-70%);
				transform: translate3d(-50%,-70%,0);
	<?php elseif ( 'scale-up' == $settings->hover_transition ) : ?>
		-webkit-transform: translate3d(-50%,-50%,0) scale(.7);
			-moz-transform: translate3d(-50%,-50%,0) scale(.7);
			-ms-transform: translate(-50%,-50%) scale(.7);
				transform: translate3d(-50%,-50%,0) scale(.7);
	<?php elseif ( 'scale-down' == $settings->hover_transition ) : ?>
		-webkit-transform: translate3d(-50%,-50%,0) scale(1.3);
			-moz-transform: translate3d(-50%,-50%,0) scale(1.3);
			-ms-transform: translate(-50%,-50%) scale(1.3);
				transform: translate3d(-50%,-50%,0) scale(1.3);
	<?php endif; ?>
	}

<?php endif; ?>
