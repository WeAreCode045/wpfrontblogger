<?php
/**
 * Plugin main class file.
 *
 * @package StandaloneTech
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main plugin calss
 */
final class WPFRONTBLOGGER {
	/**
	 * The single instance of the class.
	 *
	 * @var %PLUGIN_SLUG%
	 * @since 1.0.0
	 */
	protected static $instance = null;

	/**
	 * Main instance
	 *
	 * @return self
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->includes();
		$this->load_plugin_textdomain();
	}

	/**
	 * Check request
	 *
	 * @param string $type Type.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			default:
				return false;
		}
	}

	/**
	 * Load plugin files
	 */
	public function includes() {
		if ( $this->is_request( 'admin' ) ) {
			include_once WPFRONTBLOGGER_ABSPATH . 'includes/class-wpfrontblogger-admin.php';
			include_once WPFRONTBLOGGER_ABSPATH . 'includes/class-wpfrontblogger-settings.php';
		}

		if ( $this->is_request( 'frontend' ) ) {
			include_once WPFRONTBLOGGER_ABSPATH . 'includes/class-wpfrontblogger-frontend.php';
		}

		if ( $this->is_request( 'ajax' ) ) {
			include_once WPFRONTBLOGGER_ABSPATH . 'includes/class-wpfrontblogger-ajax.php';
		}
		
		// Always include AI class for both admin and AJAX requests
		if ( $this->is_request( 'admin' ) || $this->is_request( 'ajax' ) ) {
			include_once WPFRONTBLOGGER_ABSPATH . 'includes/class-wpfrontblogger-ai.php';
		}
	}

	/**
	 * Text Domain loader
	 */
	public function load_plugin_textdomain() {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'wpfrontblogger' );

		unload_textdomain( 'wpfrontblogger' );
		load_textdomain( 'wpfrontblogger', WP_LANG_DIR . '/wpfrontblogger/wpfrontblogger-' . $locale . '.mo' );
		load_plugin_textdomain( 'wpfrontblogger', false, plugin_basename( dirname( WPFRONTBLOGGER_PLUGIN_FILE ) ) . '/languages' );
	}

	/**
	 * Load template
	 *
	 * @param string $template_name Tempate Name.
	 * @param array  $args args.
	 * @param string $template_path Template Path.
	 * @param string $default_path Default path.
	 */
	public function get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		if ( $args && is_array( $args ) ) {
			extract( $args ); // phpcs:ignore
		}
		$located = $this->locate_template( $template_name, $template_path, $default_path );
		include $located;
	}

	/**
	 * Locate template file
	 *
	 * @param string $template_name template_name.
	 * @param string $template_path template_path.
	 * @param string $default_path default_path.
	 * @return string
	 */
	public function locate_template( $template_name, $template_path = '', $default_path = '' ) {
		$default_path = apply_filters( 'wpfrontblogger_template_path', $default_path );
		if ( ! $template_path ) {
			$template_path = 'wpfrontblogger';
		}
		if ( ! $default_path ) {
			$default_path = WPFRONTBLOGGER_ABSPATH . 'templates/';
		}
		// Look within passed path within the theme - this is priority.
		$template = locate_template( array( trailingslashit( $template_path ) . $template_name, $template_name ) );
		// Add support of third perty plugin.
		$template = apply_filters( 'wpfrontblogger_locate_template', $template, $template_name, $template_path, $default_path );
		// Get default template.
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}
		return $template;
	}

}

/**
 * Global function to access AI functionality
 *
 * @return WPFRONTBLOGGER_AI
 */
function wpfrontblogger_ai() {
	return WPFRONTBLOGGER_AI::instance();
}

class WPFRONTBLOGGER_AI {
    /**
     * The single instance of the class.
     *
     * @var WPFRONTBLOGGER_AI
     */
    protected static $instance = null;

    /**
     * Main instance
     *
     * @return self
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // ... (rest of the WPFRONTBLOGGER_AI class code)
}
