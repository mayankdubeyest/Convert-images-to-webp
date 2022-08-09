<?php

if( ! defined( 'ABSPATH' ) ) exit;

if( version_compare( PHP_VERSION, '5.6.12', '<' ) ){
	deactivate_plugins( __DIR__ );
	wp_die( __( 'Please update your PHP to version 5.6.12 or higher, then try activate <strong>Images to WebP</strong> again.', 'convert-images-to-webp' ) );
}

if( ! extension_loaded('gd') && ! extension_loaded('imagick') ){
	deactivate_plugins( __DIR__ );
	wp_die( __( 'Please install GD or Imagick on your server, then try activate <strong>Convert Images to WebP</strong> again.', 'convert-images-to-webp' ) );
}

$methods = array();

if( extension_loaded('imagick') ){
	if( class_exists('Imagick') ){
		$image = new Imagick();
		if( in_array( 'WEBP', $image->queryFormats() ) ){
			$methods['imagick'] = __( 'Imagick', 'convert-images-to-webp' );
		}
	}
}

if(
	function_exists('imagecreatefromjpeg') &&
	function_exists('imagecreatefrompng') &&
	function_exists('imagecreatefromgif') &&
	function_exists('imageistruecolor') &&
	function_exists('imagepalettetotruecolor') &&
	function_exists('imagewebp')
){
	$methods['gd'] = __( 'GD', 'convert-images-to-webp' );
}

if( count( $methods ) === 0 ){
	deactivate_plugins( __DIR__ );
	wp_die( __( 'Please enable WebP in GD or Imagick on your server, then try activate <strong>Images to WebP</strong> again.', 'convert-convert-images-to-webp' ) );
}

update_site_option( 'convert_images_to_webp_methods', $methods );