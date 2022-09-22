<?php
/*
 * Plugin Name: Convert Images In Webp
 * Plugin URI: https://www.encoresky.com/
 * Description: Converting JPG, PNG and GIF images to WEBP
 * Version: 1.0.0
 * Author: EncoreSky Technologies
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Tested up to: 6.0.2
 * Text Domain: convert-images-in-webp
 * Domain Path: /languages/
 * @package ConvertImagesinWebP
 * @author encoresky.com
*/


/**
 * To prevent user to directly access your file.
 */ 

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Current plugin version.
 * Start at version 1.0.0
 * 
 */

define( 'CONVERT_IMAGES_IN_WEBP', '1.0.0' );


/**
 * Callback function for activation hook
 * 
 */ 
function activate_convert_images_in_web() {
    
}
/**
 * Callback function for deactivation hook
 * 
 */ 
function deactivate_convert_images_in_web(){

}
/**
 * Callback function for uninstall hook
 * 
 */ 
function uninstall_convert_images_in_web(){
	delete_site_option('image_in_webp_settings');
}


/**
 * The code that runs during plugin activation.
 * 
 */
register_activation_hook( __FILE__, array( $convert_image_in_webp, 'activate' ) );

/**
 * The code that runs during plugin deactivation.
 * 
 */
register_deactivation_hook( __FILE__, 'deactivate_convert_images_in_web' );

/**
 * The code that runs during plugin uninstalation.
 * 
 */
register_uninstall_hook( __FILE__, 'uninstall_convert_images_in_web' );


/**
 * The class that defines the core plugin code.
 * */

class Convert_images_in_webp {
	var $settings;
	var $extensions = array( 'jpg', 'jpeg', 'gif', 'png' );

	function __construct(){
		
		$this->settings = get_site_option('images_to_webp_settings');

		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_filter( 'wp_update_attachment_metadata', array( $this, 'wp_update_attachment_metadata' ), 77, 2 );
		add_filter( 'wp_content_img_tag', array($this,'replace_existing_image_in_webp_frontend'), 10, 3 );
	}

	/**
         * Plugin textdomain loader
	*/

	function plugins_loaded(){

		$locale = apply_filters('plugin_locale', get_locale(), 'convert-images-in-webp');
		$text_domain_to_load = 'convert-images-in-webp-'.$locale;
		load_textdomain('convert-images-in-webp', trailingslashit(dirname(__FILE__)) . "/languages/$text_domain_to_load.mo");
	}

	/**
         * Fonction for checking php version and 
	*/
	function activate(){
		// first run test
		include_once 'tests/server.php';
		// maybe load default settings
		if( ! $this->settings = get_site_option( 'images_to_webp_settings', 0 ) ){
			$default_method = array_keys( $methods );
			$default_method = current( $default_method );
			$default_options = array(
				'extensions' => $this->extensions,
				'webp_quality' => 85,
				'method' => $default_method,
				'delete_originals' => 0,
			);
			update_site_option( 'images_to_webp_settings', $default_options );
			$this->settings = $default_options;
		}
	}

	/**
         * Convert images in webp format
	*/
	function convert_images_in_webp( $file ){
		if( is_file( $file ) ){
			$image_extension = pathinfo( $file, PATHINFO_EXTENSION );
			
			if( in_array( $image_extension, $this->settings['extensions'] ) ){
				require_once 'methods/method-' . $this->settings['method'] . '.php';
				$convert = new webp_converter();
				$response = $convert->convertImage( $file, $this->settings['webp_quality'] );
				if( $response['size']['after'] >= $response['size']['before'] ){
					unlink( $response['path'] );
					return false;
				}else{
					if( isset( $this->settings['delete_originals'] ) && $this->settings['delete_originals'] === 1 ){
						unlink( $file );
					}
				}
				return true;
			}
		}
		return false;
	}

	/**
         * Generate webp formate for all sizes of images in uploads folder.
	*/
	function wp_update_attachment_metadata( $data, $attachmentId ){
		
			if( $data && isset( $data['file'] ) && isset( $data['sizes'] ) ){
				$upload = wp_upload_dir();
				$path = $upload['basedir'] . '/' . dirname( $data['file'] ) . '/';
				$sizes = array();
				$sizes['source'] = $upload['basedir'] . '/' . $data['file'];
				foreach( $data['sizes'] as $key => $size ){
					$url = $path . $size['file'];
					if( in_array( $url, $sizes ) ) continue;
					$sizes[ $key ] = $url;
				}

				$sizes = apply_filters( 'citw_sizes', $sizes, $attachmentId );

				foreach( $sizes as $size ){
					if( ! file_exists( $size . '.webp' ) ){
						$this->convert_images_in_webp( $size );
					}
				}
			}
		
		return $data;
	}

	/**
         * Replace frontend rendered images with new webp images.
	*/
	function replace_existing_image_in_webp_frontend( $filtered_image, $context, $attachment_id ) {
		$url  = wp_get_attachment_url($attachment_id);
		$ext = pathinfo(
			parse_url($url, PHP_URL_PATH), 
			PATHINFO_EXTENSION
		);
		$webp = 'webp';
		$metadata = wp_get_attachment_metadata($attachment_id);

		if(strpos($filtered_image,$webp) === false) {
			$upload_dir   = wp_upload_dir();
			$web_url = $upload_dir['basedir'].'/'.$metadata['file'].'.webp';
			if(file_exists($web_url)) {
				$filtered_image = str_replace( $ext, $ext.'.webp', $filtered_image );
			}
		}

		return $filtered_image;
	}

}

$convert_images_in_webp = new Convert_images_in_webp();






