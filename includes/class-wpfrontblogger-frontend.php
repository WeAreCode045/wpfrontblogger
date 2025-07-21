<?php
/**
 * The frontend-specific functionality of the plugin.
 *
 * @package StandaloneTech
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'WPFRONTBLOGGER_FRONTEND' ) ) {
	/**
	 * Plugin WPFRONTBLOGGER_FRONTEND Class.
	 */
	class WPFRONTBLOGGER_FRONTEND {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Frontend functionality disabled - plugin now works in admin only
		// add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		// add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		// add_shortcode( 'wpfrontblogger_form', array( $this, 'render_blog_form' ) );
	}		/**
		 * Register the stylesheets for the frontend area.s
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles() {
			wp_enqueue_style( 'wpfrontblogger-frontend', untrailingslashit( plugins_url( '/', WPFRONTBLOGGER_PLUGIN_FILE ) ) . '/assets/css/frontend.css', array(), '1.0.0', 'all' );
			wp_enqueue_style( 'jquery-ui-autocomplete', '//code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css', array(), '1.12.1' );
		}

		/**
		 * Register the JavaScript for the frontend area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts() {
			wp_enqueue_script( 'jquery-ui-autocomplete' );
			wp_enqueue_script( 'wpfrontblogger-frontend', untrailingslashit( plugins_url( '/', WPFRONTBLOGGER_PLUGIN_FILE ) ) . '/assets/js/frontend.js', array( 'jquery', 'jquery-ui-autocomplete' ), '1.0.0', false );
			wp_localize_script( 'wpfrontblogger-frontend', 'wpfrontblogger_ajax', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'wpfrontblogger_nonce' )
			));
		}

		/**
		 * Render the blog form shortcode
		 *
		 * @param array $atts Shortcode attributes
		 * @return string
		 */
		public function render_blog_form( $atts ) {
			$atts = shortcode_atts( array(
				'redirect' => get_home_url(),
				'show_title' => 'yes'
			), $atts );

			ob_start();
			wpfrontblogger()->get_template( 'admin-blog-form.php', array( 'atts' => $atts ) );
			return ob_get_clean();
		}
	}
}

new WPFRONTBLOGGER_FRONTEND();

