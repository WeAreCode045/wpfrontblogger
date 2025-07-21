<?php
/**
 * The ajax functionality of the plugin.
 *
 * @package StandaloneTech
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WPFRONTBLOGGER_AJAX' ) ) {
	/**
	 * Plugin WPFRONTBLOGGER_AJAX Class.
	 */
	class WPFRONTBLOGGER_AJAX {
		/**
		 * Initialize the class and set its properties.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			// AJAX hooks for logged in and non-logged in users
			add_action( 'wp_ajax_wpfrontblogger_search_categories', array( $this, 'search_categories' ) );
			add_action( 'wp_ajax_nopriv_wpfrontblogger_search_categories', array( $this, 'search_categories' ) );
			
			add_action( 'wp_ajax_wpfrontblogger_search_tags', array( $this, 'search_tags' ) );
			add_action( 'wp_ajax_nopriv_wpfrontblogger_search_tags', array( $this, 'search_tags' ) );
			
			add_action( 'wp_ajax_wpfrontblogger_search_products', array( $this, 'search_products' ) );
			add_action( 'wp_ajax_nopriv_wpfrontblogger_search_products', array( $this, 'search_products' ) );
			
			add_action( 'wp_ajax_wpfrontblogger_submit_post', array( $this, 'submit_post' ) );
			add_action( 'wp_ajax_nopriv_wpfrontblogger_submit_post', array( $this, 'submit_post' ) );
			
			add_action( 'wp_ajax_wpfrontblogger_upload_image', array( $this, 'upload_image' ) );
			add_action( 'wp_ajax_nopriv_wpfrontblogger_upload_image', array( $this, 'upload_image' ) );
			
			add_action( 'wp_ajax_wpfrontblogger_search_envato_images', array( $this, 'search_envato_images' ) );
			add_action( 'wp_ajax_nopriv_wpfrontblogger_search_envato_images', array( $this, 'search_envato_images' ) );
			
			add_action( 'wp_ajax_wpfrontblogger_import_envato_image', array( $this, 'import_envato_image' ) );
			add_action( 'wp_ajax_nopriv_wpfrontblogger_import_envato_image', array( $this, 'import_envato_image' ) );
			
			// AI-powered AJAX handlers
			add_action( 'wp_ajax_wpfrontblogger_ai_rewrite_content', array( $this, 'ai_rewrite_content' ) );
			add_action( 'wp_ajax_wpfrontblogger_ai_generate_title', array( $this, 'ai_generate_title' ) );
			add_action( 'wp_ajax_wpfrontblogger_ai_select_categories', array( $this, 'ai_select_categories' ) );
			add_action( 'wp_ajax_wpfrontblogger_ai_generate_tags', array( $this, 'ai_generate_tags' ) );
			add_action( 'wp_ajax_wpfrontblogger_ai_generate_image', array( $this, 'ai_generate_image' ) );
		}

		// ... [existing methods would be here - search_categories, search_tags, etc.]

		/**
		 * AI: Rewrite content
		 */
		public function ai_rewrite_content() {
			if ( ! wp_verify_nonce( $_POST['nonce'], 'wpfrontblogger_nonce' ) ) {
				wp_die( 'Security check failed' );
			}

			if ( ! wpfrontblogger_ai()->is_ai_available() ) {
				wp_send_json_error( array( 'message' => __( 'AI functionality is not available. Please configure OpenAI API token in settings.', 'wpfrontblogger' ) ) );
			}

			$content = wp_unslash( $_POST['content'] );
			if ( empty( $content ) ) {
				wp_send_json_error( array( 'message' => __( 'Content is required for rewriting', 'wpfrontblogger' ) ) );
			}

			$result = wpfrontblogger_ai()->rewrite_content( $content );

			if ( is_wp_error( $result ) ) {
				wp_send_json_error( array( 'message' => $result->get_error_message() ) );
			}

			wp_send_json_success( array( 'content' => $result ) );
		}

		/**
		 * AI: Generate title
		 */
		public function ai_generate_title() {
			if ( ! wp_verify_nonce( $_POST['nonce'], 'wpfrontblogger_nonce' ) ) {
				wp_die( 'Security check failed' );
			}

			if ( ! wpfrontblogger_ai()->is_ai_available() ) {
				wp_send_json_error( array( 'message' => __( 'AI functionality is not available. Please configure OpenAI API token in settings.', 'wpfrontblogger' ) ) );
			}

			$content = wp_unslash( $_POST['content'] );
			if ( empty( $content ) ) {
				wp_send_json_error( array( 'message' => __( 'Content is required for title generation', 'wpfrontblogger' ) ) );
			}

			$result = wpfrontblogger_ai()->generate_title( $content );

			if ( is_wp_error( $result ) ) {
				wp_send_json_error( array( 'message' => $result->get_error_message() ) );
			}

			wp_send_json_success( array( 'titles' => $result ) );
		}

		/**
		 * AI: Select categories
		 */
		public function ai_select_categories() {
			if ( ! wp_verify_nonce( $_POST['nonce'], 'wpfrontblogger_nonce' ) ) {
				wp_die( 'Security check failed' );
			}

			if ( ! wpfrontblogger_ai()->is_ai_available() ) {
				wp_send_json_error( array( 'message' => __( 'AI functionality is not available. Please configure OpenAI API token in settings.', 'wpfrontblogger' ) ) );
			}

			$content = wp_unslash( $_POST['content'] );
			if ( empty( $content ) ) {
				wp_send_json_error( array( 'message' => __( 'Content is required for category selection', 'wpfrontblogger' ) ) );
			}

			$result = wpfrontblogger_ai()->select_categories( $content ) ?? array();

			if ( is_wp_error( $result ) ) {
				wp_send_json_error( array( 'message' => $result->get_error_message() ) );
			}

			// Convert IDs to category objects for frontend
			$categories = array();
			foreach ( $result as $category_id ) {
				$category = get_category( $category_id );
				if ( $category ) {
					$categories[] = array(
						'id' => $category->term_id,
						'label' => $category->name,
						'name' => $category->name
					);
				}
			}

			wp_send_json_success( array( 'categories' => $categories ) );
		}

		/**
		 * AI: Generate tags
		 */
		public function ai_generate_tags() {
			if ( ! wp_verify_nonce( $_POST['nonce'], 'wpfrontblogger_nonce' ) ) {
				wp_die( 'Security check failed' );
			}

			if ( ! wpfrontblogger_ai()->is_ai_available() ) {
				wp_send_json_error( array( 'message' => __( 'AI functionality is not available. Please configure OpenAI API token in settings.', 'wpfrontblogger' ) ) );
			}

			$content = wp_unslash( $_POST['content'] );
			if ( empty( $content ) ) {
				wp_send_json_error( array( 'message' => __( 'Content is required for tag generation', 'wpfrontblogger' ) ) );
			}

			$result = wpfrontblogger_ai()->generate_tags( $content );

			if ( is_wp_error( $result ) ) {
				wp_send_json_error( array( 'message' => $result->get_error_message() ) );
			}

			// Format tags for frontend
			$tags = array();
			foreach ( $result as $tag_name ) {
				$tags[] = array(
					'id' => 'new:' . $tag_name,
					'label' => $tag_name,
					'name' => $tag_name
				);
			}

			wp_send_json_success( array( 'tags' => $tags ) );
		}

		/**
		 * AI: Generate image from Envato Elements
		 */
		public function ai_generate_image() {
			if ( ! wp_verify_nonce( $_POST['nonce'], 'wpfrontblogger_nonce' ) ) {
				wp_die( 'Security check failed' );
			}

			if ( ! wpfrontblogger_ai()->is_ai_available() ) {
				wp_send_json_error( array( 'message' => __( 'AI functionality is not available. Please configure OpenAI API token in settings.', 'wpfrontblogger' ) ) );
			}

			$title = sanitize_text_field( $_POST['title'] ?? '' );
			$content = wp_unslash( $_POST['content'] ?? '' );
			
			if ( empty( $content ) && empty( $title ) ) {
				wp_send_json_error( array( 'message' => __( 'Title or content is required for image generation', 'wpfrontblogger' ) ) );
			}

			// Generate search keywords using AI
			$keywords = wpfrontblogger_ai()->generate_image_keywords( $title, $content );

			if ( is_wp_error( $keywords ) ) {
				wp_send_json_error( array( 'message' => $keywords->get_error_message() ) );
			}

			if ( empty( $keywords ) ) {
				wp_send_json_error( array( 'message' => __( 'No suitable keywords generated for image search', 'wpfrontblogger' ) ) );
			}

			// Use the first keyword to search Envato Elements
			$search_query = $keywords[0];

			// Check if Envato Elements is available
			if ( ! class_exists( 'Envato_Elements' ) && ! function_exists( 'envato_elements_get_images' ) ) {
				wp_send_json_error( array( 
					'message' => __( 'Envato Elements plugin is not installed or active', 'wpfrontblogger' ),
					'keywords' => $keywords,
					'search_query' => $search_query
				) );
			}

			// Simulate search_envato_images call with AI-generated query
			$images = $this->get_envato_elements_images( $search_query, 1, 12 );
			
			if ( $images && ! empty( $images['images'] ) ) {
				// Return the first image with AI context
				$first_image = $images['images'][0];
				wp_send_json_success( array(
					'image' => $first_image,
					'keywords' => $keywords,
					'search_query' => $search_query,
					'message' => sprintf( __( 'AI found a suitable image using search term: %s', 'wpfrontblogger' ), $search_query )
				) );
			} else {
				wp_send_json_error( array(
					'message' => sprintf( __( 'No images found for AI-generated search term: %s', 'wpfrontblogger' ), $search_query ),
					'keywords' => $keywords,
					'search_query' => $search_query
				) );
			}
		}

		/**
		 * Helper: Get images from Envato Elements.
		 *
		 * @param string $query Search query.
		 * @param int $page Page number.
		 * @param int $per_page Images per page.
		 * @return array|false Array of images or false on failure.
		 */
		private function get_envato_elements_images( $query, $page = 1, $per_page = 12 ) {
			if ( function_exists( 'envato_elements_get_images' ) ) {
				return envato_elements_get_images( $query, $page, $per_page );
			}
			return false;
		}

		// [Note: This is a placeholder for the complete AJAX file]
		// The existing methods would be included here: search_categories, search_tags, 
		// search_products, submit_post, upload_image, search_envato_images, import_envato_image, etc.
	}
}

new WPFRONTBLOGGER_AJAX();
