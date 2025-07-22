<?php
/**
 * Plugin Name: wpfrontblogger
 * Plugin URI: https://code045.nl/wpfrontblogger
 * Description:Post Blogs to your WordPress site with ease using the wpfrontblogger plugin.
 * Author: WeAreCode045
 * Author URI: https://code045.nl/
 * Version: 1.0.14
 * Requires at least: 6.0
 * Tested up to: 6.7
 *
 * Text Domain: wpfrontblogger
 * Domain Path: /languages/
 *
 * @package WPFRONTBLOGGER
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define WPFRONTBLOGGER_PLUGIN_FILE.
if ( ! defined( 'WPFRONTBLOGGER_PLUGIN_FILE' ) ) {
	define( 'WPFRONTBLOGGER_PLUGIN_FILE', __FILE__ );
}

// Define WPFRONTBLOGGER_ABSPATH.
if ( ! defined( 'WPFRONTBLOGGER_ABSPATH' ) ) {
	define( 'WPFRONTBLOGGER_ABSPATH', dirname( __FILE__ ) . '/' );
}

// Include the main class.
if ( ! class_exists( 'WPFRONTBLOGGER' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-wpfrontblogger.php';
}

if ( ! function_exists( 'wpfrontblogger' ) ) {
	/**
	 * Returns the main instance of WPFrontBlogger.
	 *
	 * @since  1.0.0
	 * @return WPFRONTBLOGGER
	 */
	function wpfrontblogger() { //// phpcs:ignore
		return WPFRONTBLOGGER::instance();
	}
}

wpfrontblogger();
