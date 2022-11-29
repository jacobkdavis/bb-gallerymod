<?php

// Render the posts.
if ( $module->get_photos() ) :

	?>
	<div class="fl-post-<?php echo $module->get_layout_slug() . $paged; ?>">
	<?php



	foreach ( $module->get_photos() as $photo ) {
	
		?>

		<div <?php $module->render_post_class(); ?>>
		
		<?php

		if ( $photo ) {
		
// 			print_r(get_object_vars($photo));
		
			$data = FLBuilderPhoto::get_attachment_data( $photo->id );
			$image = $data->sizes->full->url;
			
			
			
			$title = "";
			
			if ( isset( $settings->show_captions ) ) {
			
				switch($settings->show_captions) {
					case "alt":
						$title = sanitize_text_field($data->alt);
						break;
					case "title":
						$title = sanitize_text_field($data->title);
						break;
					case "caption":
						$title = sanitize_text_field($photo->cap);
						break;
				}
				
			}

		?>

			<a class="fl-post-gallery-link" href="<?php echo($image); ?>" title="<? echo($title);?>">

				<?php

				$class_name = 'fl-post-gallery-img';

				if ( $photo ) {
					if ( $photo->width > $photo->height ) {
						$class_name .= ' fl-post-gallery-img-horiz';
					} else {
						$class_name .= ' fl-post-gallery-img-vert';
					}
				}

				$image = $data->sizes->full->url;
				
				print( "<img src='$image' class='$class_name' alt='$title' />" );

				?>
				<div class="fl-post-gallery-text-wrap">
					<div class="fl-post-gallery-text">

						<?php if($title) { ?>
						
						<h2 class="fl-post-gallery-title" itemprop="headline"><?php echo($title); ?></h2>
						
						<?php } ?>

					</div>
				</div>
			</a>
			
		<?php
		}
		?>

		</div>
	
	
	<?php
	}
	?>

</div>
<div class="fl-clear"></div>
<?php endif; ?>
<?php

