<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package StandaloneTech
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'WPFRONTBLOGGER_ADMIN' ) ) {
	/**
	 * Plugin WPFRONTBLOGGER_ADMIN Class.
	 */
	class WPFRONTBLOGGER_ADMIN {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}		/**
		 * Register the stylesheets for the admin area.s
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles() {
			$screen = get_current_screen();
			if ( isset( $screen->id ) && $screen->id === 'posts_page_wpfrontblogger' ) {
				wp_enqueue_style( 'wpfrontblogger-admin', untrailingslashit( plugins_url( '/', WPFRONTBLOGGER_PLUGIN_FILE ) ) . '/assets/css/admin.css', array(), '1.0.0', 'all' );
				wp_enqueue_style( 'jquery-ui-autocomplete', '//code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css', array(), '1.12.1' );
			}
		}

		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts() {
			$screen = get_current_screen();
			if ( isset( $screen->id ) && $screen->id === 'posts_page_wpfrontblogger' ) {
				wp_enqueue_script( 'jquery-ui-autocomplete' );
				wp_enqueue_script( 'wpfrontblogger-admin', untrailingslashit( plugins_url( '/', WPFRONTBLOGGER_PLUGIN_FILE ) ) . '/assets/js/admin.js', array( 'jquery', 'jquery-ui-autocomplete' ), '1.0.0', false );
				wp_localize_script( 'wpfrontblogger-admin', 'wpfrontblogger_ajax', array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'wpfrontblogger_nonce' )
				));
			}
		}

		/**
		 * Add admin menu
		 */
		public function add_admin_menu() {
			add_submenu_page(
				'edit.php', // Parent menu (Posts)
				__( 'WP Front Blogger', 'wpfrontblogger' ),
				__( 'Blog Creator', 'wpfrontblogger' ),
				'edit_posts',
				'wpfrontblogger',
				array( $this, 'admin_page' )
			);
		}

		/**
		 * Admin init
		 */
		public function admin_init() {
			// Any admin initialization code
		}

		/**
		 * Render admin page
		 */
		public function admin_page() {
			?>
			<div class="wrap">
				<h1><?php _e( 'Create New Blog Post', 'wpfrontblogger' ); ?></h1>
				<p><?php _e( 'Use this multistep form to create professional blog posts with categories, tags, images, and related products.', 'wpfrontblogger' ); ?></p>
				
				<?php wpfrontblogger()->get_template( 'admin-blog-form.php' ); ?>
			</div>
			<?php
		}
	}
}

new WPFRONTBLOGGER_ADMIN();
