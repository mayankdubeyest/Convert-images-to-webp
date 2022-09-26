<?php

/**
 * To prevent user to directly access your file.
 */ 

if( ! defined( 'ABSPATH' ) ) exit;

/**
* Class for Imagick image optimaziotn lybrary
*
*/

class webp_converter{

	public function convertImage( $path, $quality ){
		ini_set( 'memory_limit', '1G' );
		set_time_limit( 120 );

		$image_extension = pathinfo( $path, PATHINFO_EXTENSION );
	    $path_with_webp_ext = str_replace($image_extension, "webp", $path);
		$output =   $path_with_webp_ext;
		
		$image = new Imagick( $path );
		$image->setImageFormat('WEBP');
		$image->stripImage();
		$image->setImageCompressionQuality( $quality );
		$blob = $image->getImageBlob();
		$success = file_put_contents( $output, $blob );

		return array(
			'path' => $output,
			'size' => array(
				'before' => filesize( $path ),
				'after' => filesize( $output )
			)
		);
	}

}