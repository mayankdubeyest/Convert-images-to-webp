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
 */ 
function uninstall_convert_images_in_web(){
	delete_site_option('image_to_webp_settings');
}

/**
 *  Include the main Convert ImagesInWebpclass.
 * 
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ciiw-convert-images-in-webp.php';

/**
* The code that runs during plugin activation.
*/
register_activation_hook( __FILE__, array( $convert_images_in_webp, 'ciiw_activate' ) );

/**
* The code that runs during plugin deactivation.
*/
register_deactivation_hook( __FILE__, 'deactivate_convert_images_in_web' );

/**
* The code that runs during plugin uninstalation.
*/
register_uninstall_hook( __FILE__, 'uninstall_convert_images_in_web' );