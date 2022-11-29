<?php
/**
 * Plugin Name: BB Gallery Mod
 * Plugin URI: https://github.com/jacobkdavis/bb-gallerymod
 * Description: Custom beaver builder module for a modified gallery full-width card
 * Version: 1.0.0
 * Author: Jacob Davis
 * Author URI: http://jacobkdavis.com
 * GitHub Plugin URI: jacobkdavis/bb-gallerymod
 */
define( 'BB_GALLERYMOD_DIR', plugin_dir_path( __FILE__ ) );
define( 'BB_GALLERYMOD_URL', plugins_url( '/', __FILE__ ) );

function bb_gallerymod_load_module() {
	if ( class_exists( 'FLBuilder' ) ) {
			include('gallerymod/gallerymod.php');
	}
}
add_action( 'init', 'bb_gallerymod_load_module' );
