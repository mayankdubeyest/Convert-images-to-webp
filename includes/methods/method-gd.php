<?php

/**
 * To prevent user to directly access your file.
 */ 

if( ! defined( 'ABSPATH' ) ) exit;

/**
* Class for GD image optimaziotn lybrary
*
*/
class webp_converter{

	public function convertImage( $path, $quality ){
		ini_set( 'memory_limit', '1G' );
		set_time_limit( 120 );
		$image_extension = pathinfo( $path, PATHINFO_EXTENSION );
	    $path_with_webp_ext = str_replace($image_extension, "webp", $path);
		$output =   $path_with_webp_ext;
		$methods = array(
			'jpg' => 'imagecreatefromjpeg',
			'jpeg' => 'imagecreatefromjpeg',
			'png' => 'imagecreatefrompng',
			'gif' => 'imagecreatefromgif'
		);

		$image = @$methods[ $image_extension ]( $path );
		imageistruecolor( $image );
		imagepalettetotruecolor( $image );
		imagewebp( $image, $output, $quality );

		return array(
			'path' => $output,
			'size' => array(
				'before' => filesize( $path ),
				'after' => filesize( $output )
			)
		);
	}

}