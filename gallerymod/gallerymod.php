<?php

/**
 * @class FLGalleryModModule
 */
class FLGalleryModModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Gallery Mod', 'fl-builder' ),
			'description'     => __( 'Display a flexible gallery.', 'fl-builder' ),
			'category'        => __( 'Media', 'fl-builder' ),
			'editor_export'   => false,
			'partial_refresh' => true,
			'icon'            => 'format-gallery.svg',
		));
	}

	/**
	 * Ensure backwards compatibility with old settings
	 * before defaults are merged in.
	 *
	 * @since 2.6.0.1
	 * @param object $settings A module settings object.
	 * @return object
	 */
	public function filter_raw_settings( $settings ) {

		// Handle columns for the new large breakpoint.
		if ( ! isset( $settings->post_columns_large ) ) {
			$settings->post_columns_large = $settings->post_columns;
		}

		return $settings;
	}

	/**
	 * Ensure backwards compatibility with old settings.
	 *
	 * @since 2.2
	 * @param object $settings A module settings object.
	 * @param object $helper A settings compatibility helper.
	 * @return object
	 */
	public function filter_settings( $settings, $helper ) {

		// Handle old opacity inputs.
		$helper->handle_opacity_inputs( $settings, 'bg_opacity', 'bg_color' );
		$helper->handle_opacity_inputs( $settings, 'text_bg_opacity', 'text_bg_color' );

		// Handle old border inputs.
		if ( isset( $settings->border_type ) && isset( $settings->border_color ) && isset( $settings->border_size ) ) {
			$settings->border = array(
				'style' => $settings->border_type,
				'color' => $settings->border_color,
				'width' => array(
					'top'    => $settings->border_size,
					'right'  => $settings->border_size,
					'bottom' => $settings->border_size,
					'left'   => $settings->border_size,
				),
			);
			unset( $settings->border_type );
			unset( $settings->border_color );
			unset( $settings->border_size );
		}

		// Handle old title font size.
		if ( isset( $settings->title_font_size ) ) {
			$settings->title_typography              = array();
			$settings->title_typography['font_size'] = array(
				'length' => $settings->title_font_size,
				'unit'   => 'px',
			);
			unset( $settings->title_font_size );
		}

		// Handle old info font size.
		if ( isset( $settings->info_font_size ) ) {
			$settings->info_typography              = array();
			$settings->info_typography['font_size'] = array(
				'length' => $settings->info_font_size,
				'unit'   => 'px',
			);
			unset( $settings->info_font_size );
		}

		// Handle old content font size.
		if ( isset( $settings->content_font_size ) ) {
			$settings->content_typography              = array();
			$settings->content_typography['font_size'] = array(
				'length' => $settings->content_font_size,
				'unit'   => 'px',
			);
			unset( $settings->content_font_size );
		}



		return $settings;
	}

	/**
	 * @method enqueue_scripts
	 */
	public function enqueue_scripts() {

		if ( FLBuilderModel::is_builder_active() || 'gallery' == $this->settings->layout ) {
			$this->add_js( 'fl-gallery-grid' );
		}

		// Jetpack sharing has settings to enable sharing on posts, post types and pages.
		// If pages are disabled then jetpack will still show the share button in this module
		// but will *not* enqueue its scripts and fonts.
		// This filter forces jetpack to enqueue the sharing scripts.
		add_filter( 'sharing_enqueue_scripts', '__return_true' );
	}

	/**
	 * @since 1.10.7
	 */
	public function update( $settings ) {
		global $wp_rewrite;
		$wp_rewrite->flush_rules( false );
		return $settings;
	}

	/**
	 * Returns the slug for the posts layout.
	 *
	 * @since 1.10
	 * @return string
	 */
	public function get_layout_slug() {
		return 'columns' == $this->settings->layout ? 'grid' : $this->settings->layout;
	}

	/**
	 * Renders the CSS class for each post item.
	 *
	 * @since 1.10
	 * @return void
	 */
	public function render_post_class() {
		$settings      = $this->settings;
		$layout        = $this->get_layout_slug();
		$has_thumbnail = has_post_thumbnail();
		$classes       = array( 'fl-post-' . $layout . '-post' );

		if ( in_array( $layout, array( 'grid', 'feed' ) ) ) {
			$align     = empty( $settings->post_align ) ? 'default' : $settings->post_align;
			$classes[] = 'fl-post-align-' . $align;
		}

		if ( '' != $settings->posts_container_class ) {
			$classes[] = $settings->posts_container_class;
		}

		post_class( apply_filters( 'fl_builder_posts_module_classes', $classes, $settings ) );
	}




	public function get_posts_container() {
		return $this->settings->posts_container;
	}

	/**
	 * @method get_photos
	 */
	public function get_photos() {
		$photos   = array();
		$ids      = $this->settings->photos;
		$medium_w = get_option( 'medium_size_w' );
		$large_w  = get_option( 'large_size_w' );

		if ( empty( $this->settings->photos ) ) {
			return $photos;
		}

		foreach ( $ids as $id ) {

			$photo = FLBuilderPhoto::get_attachment_data( $id );

			// Use the cache if we didn't get a photo from the id.
			if ( ! $photo ) {

				if ( ! isset( $this->settings->photo_data ) ) {
					continue;
				} elseif ( is_array( $this->settings->photo_data ) ) {
					$photos[ $id ] = $this->settings->photo_data[ $id ];
				} elseif ( is_object( $this->settings->photo_data ) ) {
					$photos[ $id ] = $this->settings->photo_data->{$id};
				} else {
					continue;
				}
			}

			// Only use photos who have the sizes object.
			if ( isset( $photo->sizes ) ) {

				// Photo data object
				$data              = new stdClass();
				$data->id          = $id;
				$data->alt         = $photo->alt;
				$data->cap         = $photo->caption;
				$data->description = $photo->description;
				$data->title       = $photo->title;
				$data->width       = $photo->width;
				$data->height       = $photo->height;

				// Collage photo src
// 				if ( 'collage' == $this->settings->layout ) {

				if ( $this->settings->photo_size < $medium_w && isset( $photo->sizes->medium ) ) {
					$data->src = $photo->sizes->medium->url;
				} elseif ( $this->settings->photo_size <= $large_w && isset( $photo->sizes->large ) ) {
					$data->src = $photo->sizes->large->url;
				} else {
					$data->src = $photo->sizes->full->url;
				}
					
// 				} else {
// 
// 					if ( isset( $photo->sizes->thumbnail ) ) {
// 						$data->src = $photo->sizes->thumbnail->url;
// 					} else {
// 						$data->src = $photo->sizes->full->url;
// 					}
// 				}

				// Photo Link
				if ( isset( $photo->sizes->large ) ) {
					$data->link = $photo->sizes->large->url;
				} else {
					$data->link = $photo->sizes->full->url;
				}

				// Push the photo data
				$photos[ $id ] = $data;
			}
		}

		return $photos;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLGalleryModModule', array(
	'layout'     => array(
		'title'    => __( 'Layout', 'fl-builder' ),
		'sections' => array(
			'general' => array(
				'title'  => '',
				'fields' => array(
					'photos'              => array(
						'type'        => 'multiple-photos',
						'label'       => __( 'Photos', 'fl-builder' ),
						'connections' => array( 'multiple-photos' ),
					),
					'layout' => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'fl-builder' ),
						'default' => 'grid',
						'options' => array(
							'gallery' => __( 'Gallery', 'fl-builder' ),
						),
						'toggle'  => array(
							'gallery' => array(
								'sections' => array( 'gallery_general', 'overlay_style', 'icons', 'image' ),
// 								'fields'   => array( 'image_fallback' ),
							),
						),
					),
				),
			),
			'overlay_style'   => array(
				'title'  => __( 'Overlay Colors', 'fl-builder' ),
				'fields' => array(
					'text_color'    => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Overlay Text Color', 'fl-builder' ),
						'default'     => 'ffffff',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-post-gallery-link, .fl-post-gallery-link .fl-post-gallery-title',
							'property' => 'color',
						),
					),
					'text_bg_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Overlay Background Color', 'fl-builder' ),
						'default'     => '333333',
						'help'        => __( 'The color applies to the overlay behind text over the background selections.', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-post-gallery-text-wrap',
							'property' => 'background-color',
						),
					),
				),
			),
			'gallery_general' => array(
				'title'  => '',
				'fields' => array(
					'hover_transition' => array(
						'type'    => 'select',
						'label'   => __( 'Hover Transition', 'fl-builder' ),
						'default' => 'fade',
						'options' => array(
							'fade'       => __( 'Fade', 'fl-builder' ),
							'slide-up'   => __( 'Slide Up', 'fl-builder' ),
							'slide-down' => __( 'Slide Down', 'fl-builder' ),
							'scale-up'   => __( 'Scale Up', 'fl-builder' ),
							'scale-down' => __( 'Scale Down', 'fl-builder' ),
						),
					),
					'show_captions'       => array(
						'type'    => 'select',
						'label'   => __( 'Show Captions', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'0'     => __( 'Never', 'fl-builder' ),
							'caption' => __( 'Show Caption', 'fl-builder' ),
							'alt' => __( 'Show Alt Tag', 'fl-builder' ),
							'title' => __( 'Show Title Tag', 'fl-builder' ),
						),
					),
				),
			),
			

		),
	),
// 	'style'      => array(
// 		'title'    => __( 'Style', 'fl-builder' ),
// 		'sections' => array(
// 			'post_style'      => array(
// 				'title'  => __( 'Gallery Mod', 'fl-builder' ),
// 				'fields' => array(
// 					'post_align' => array(
// 						'type'    => 'align',
// 						'label'   => __( 'Post Alignment', 'fl-builder' ),
// 						'default' => '',
// 						'preview' => array(
// 							'type'     => 'css',
// 							'property' => 'text-align',
// 							'selector' => '.fl-post-grid-post, .fl-post-feed-post',
// 						),
// 					),
// 					'bg_color'   => array(
// 						'type'        => 'color',
// 						'connections' => array( 'color' ),
// 						'label'       => __( 'Post Background Color', 'fl-builder' ),
// 						'show_reset'  => true,
// 						'show_alpha'  => true,
// 						'preview'     => array(
// 							'type'     => 'css',
// 							'selector' => '.fl-post-feed-post, .fl-post-grid-post',
// 							'property' => 'background-color',
// 						),
// 					),
// 					'border'     => array(
// 						'type'       => 'border',
// 						'label'      => __( 'Border', 'fl-builder' ),
// 						'responsive' => true,
// 						'preview'    => array(
// 							'type'     => 'css',
// 							'selector' => '.fl-post-feed-post, .fl-post-grid-post',
// 						),
// 					),
// 				),
// 			),
// 			'text_style'      => array(
// 				'title'  => __( 'Text', 'fl-builder' ),
// 				'fields' => array(
// 					'title_color'        => array(
// 						'type'        => 'color',
// 						'connections' => array( 'color' ),
// 						'label'       => __( 'Title Color', 'fl-builder' ),
// 						'show_reset'  => true,
// 						'show_alpha'  => true,
// 						'preview'     => array(
// 							'type'     => 'css',
// 							'selector' => '.fl-post-feed-title a, .fl-post-grid-title a',
// 							'property' => 'color',
// 						),
// 					),
// 					'title_typography'   => array(
// 						'type'       => 'typography',
// 						'label'      => __( 'Title Typography', 'fl-builder' ),
// 						'responsive' => true,
// 						'preview'    => array(
// 							'type'      => 'css',
// 							'selector'  => '.fl-post-feed-title, .fl-post-grid-title',
// 							'important' => true,
// 						),
// 					),
// 					'info_color'         => array(
// 						'type'        => 'color',
// 						'connections' => array( 'color' ),
// 						'label'       => __( 'Post Info Color', 'fl-builder' ),
// 						'show_reset'  => true,
// 						'show_alpha'  => true,
// 						'preview'     => array(
// 							'type'      => 'css',
// 							'selector'  => '{node} .fl-post-feed .fl-post-feed-header .fl-post-feed-meta, {node} .fl-post-feed .fl-post-feed-header .fl-post-feed-meta span, {node} .fl-post-feed .fl-post-feed-header .fl-post-feed-meta a, {node} .fl-post-feed .fl-post-feed-header .fl-post-feed-meta-terms span, {node} .fl-post-feed .fl-post-feed-header .fl-post-feed-meta-terms a, {node} .fl-post-grid-meta, {node} .fl-post-grid-meta span, {node} .fl-post-grid-meta a, {node} .fl-post-grid-meta-terms span, {node} .fl-post-grid-meta-terms a',
// 							'property'  => 'color',
// 							'important' => true,
// 						),
// 					),
// 					'info_typography'    => array(
// 						'type'       => 'typography',
// 						'label'      => __( 'Post Info Typography', 'fl-builder' ),
// 						'responsive' => true,
// 						'preview'    => array(
// 							'type'      => 'css',
// 							'selector'  => '.fl-post-feed-meta, .fl-post-feed-meta a, .fl-post-grid-meta, .fl-post-grid-meta a',
// 							'important' => true,
// 						),
// 					),
// 					'content_color'      => array(
// 						'type'        => 'color',
// 						'connections' => array( 'color' ),
// 						'label'       => __( 'Content Color', 'fl-builder' ),
// 						'show_reset'  => true,
// 						'show_alpha'  => true,
// 						'preview'     => array(
// 							'type'      => 'css',
// 							'selector'  => '{node} .fl-post-feed-content, {node} .fl-post-feed-content p, {node} .fl-post-feed-content a, {node} .fl-post-grid-content, {node} .fl-post-grid-content p, {node} .fl-post-grid-content a, {node} .fl-builder-pagination ul.page-numbers li span, {node} .fl-builder-pagination ul.page-numbers li a',
// 							'property'  => 'color',
// 							'important' => true,
// 						),
// 					),
// 					'content_typography' => array(
// 						'type'       => 'typography',
// 						'label'      => __( 'Content Typography', 'fl-builder' ),
// 						'responsive' => true,
// 						'preview'    => array(
// 							'type'      => 'css',
// 							'selector'  => '.fl-post-feed-content, .fl-post-feed-content p, .fl-post-grid-content, .fl-post-grid-content p',
// 							'important' => true,
// 						),
// 					),
// 					'link_color'         => array(
// 						'type'        => 'color',
// 						'connections' => array( 'color' ),
// 						'label'       => __( 'Link Color', 'fl-builder' ),
// 						'show_reset'  => true,
// 						'show_alpha'  => true,
// 						'preview'     => array(
// 							'type'     => 'css',
// 							'selector' => '.fl-post-feed-content a, .fl-post-grid-content a',
// 							'property' => 'color',
// 						),
// 					),
// 					'link_hover_color'   => array(
// 						'type'        => 'color',
// 						'connections' => array( 'color' ),
// 						'label'       => __( 'Link Hover Color', 'fl-builder' ),
// 						'show_reset'  => true,
// 						'show_alpha'  => true,
// 						'preview'     => array(
// 							'type'     => 'css',
// 							'selector' => '.fl-post-feed-content a:hover, .fl-post-grid-content a:hover',
// 							'property' => 'color',
// 						),
// 					),
// 				),
// 			),
// 
// 
// 		),
// 	),

));
