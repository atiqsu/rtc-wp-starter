<?php // phpcs:disable Squiz.Commenting.FileComment.Missing

/**
 * Plugin Name: rtCamp WordPress assignment
 * Description: Basic plugin for allowing users to select multiple contributors for a post and displays them.
 * Plugin URI:
 * Author: atiqsu
 * Version: 1.0.0
 * Author URI: https://atiqsu.com
 *
 * Text Domain: rtc-wp-assignment
 * Domain Path: /languages
 */


const RTC_VERSION  = '1.0.0';
const RTC_DEV_MODE = true;

define( 'RTC_WP_ASSIGNMENT_PLG_PATH', plugin_dir_path( __FILE__ ) );
define( 'RTC_WP_ASSIGNMENT_PLG_URL', plugin_dir_url( __FILE__ ) );

defined( 'ABSPATH' ) || exit;


require __DIR__ . '/class-autoloader.php';


register_activation_hook(
	__FILE__,
	function () {
		\rtCamp\Installer::activate();
	}
);


register_deactivation_hook(
	__FILE__,
	function () {
		\rtCamp\Installer::deactivate();
	}
);

new \rtCamp\Boot( __FILE__ );
