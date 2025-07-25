<?php
/**
 * AI functionality for the plugin.
 *
 * @package wpfrontblogger
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WPFRONTBLOGGER_AI' ) ) {
	/**
	 * Plugin AI Class.
	 */
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

		/**
		 * OpenAI API URL
		 */
		private $api_url = 'https://api.openai.com/v1/chat/completions';

		/**
		 * Get OpenAI API token
		 */
		private function get_api_token() {
			return get_option( 'wpfrontblogger_openai_token', '' );
		}

		/**
		 * Get AI prompts
		 */
		private function get_prompts() {
			$default_prompts = array(
				'rewrite' => 'Please rewrite the following blog content to make it more engaging, professional, and SEO-friendly while maintaining the original meaning and key points. Keep the same tone but improve readability and flow:\n\n{content}',
				'title' => 'Based on the following blog content, generate 3 compelling and SEO-friendly titles that would attract readers. Make them catchy but professional:\n\n{content}\n\nProvide only the titles, separated by newlines.',
				'categories' => 'Based on the following blog content and available categories, select the most relevant categories (maximum 3). Only choose from existing categories:\n\nContent: {content}\n\nAvailable categories: {categories}\n\nProvide only the category names, separated by commas.',
				'tags' => 'Based on the following blog content, generate 5-8 relevant tags that would help with SEO and discoverability. Make them specific and relevant:\n\n{content}\n\nProvide only the tags, separated by commas.',
				'image' => 'Based on the following blog title and content, generate 2-3 search keywords for finding a relevant featured image from stock photos. Keep keywords simple and visual:\n\nTitle: {title}\nContent: {content}\n\nProvide only the search keywords, separated by commas.',
				'products' => 'Based on the following blog content, suggest 3 product names or keywords that would be most relevant to sell or promote alongside this content. Think about what products would naturally complement this topic:\n\n{content}\n\nProvide only the product keywords, separated by commas.'
			);

			return get_option( 'wpfrontblogger_ai_prompts', $default_prompts );
		}

		/**
		 * Check if AI is available
		 */
		public function is_ai_available() {
			return ! empty( $this->get_api_token() );
		}

		/**
		 * Make OpenAI API request
		 */
		private function make_api_request( $prompt, $max_tokens = 500 ) {
			error_log( 'WPFrontBlogger AI: make_api_request() called with max_tokens: ' . $max_tokens );
			
			$api_token = $this->get_api_token();
			
			if ( empty( $api_token ) ) {
				error_log( 'WPFrontBlogger AI: No API token configured' );
				return new WP_Error( 'no_token', __( 'OpenAI API token not configured', 'wpfrontblogger' ) );
			}

			error_log( 'WPFrontBlogger AI: API token available, length: ' . strlen( $api_token ) );

			$body = array(
				'model' => 'gpt-3.5-turbo',
				'messages' => array(
					array(
						'role' => 'user',
						'content' => $prompt
					)
				),
				'max_tokens' => $max_tokens,
				'temperature' => 0.7
			);

			error_log( 'WPFrontBlogger AI: Request body prepared' );

			$args = array(
				'timeout' => 30,
				'headers' => array(
					'Authorization' => 'Bearer ' . $api_token,
					'Content-Type' => 'application/json',
				),
				'body' => wp_json_encode( $body ),
			);

			error_log( 'WPFrontBlogger AI: Making HTTP request to: ' . $this->api_url );
			$response = wp_remote_post( $this->api_url, $args );

			if ( is_wp_error( $response ) ) {
				error_log( 'WPFrontBlogger AI: HTTP request failed: ' . $response->get_error_message() );
				return $response;
			}

			$response_code = wp_remote_retrieve_response_code( $response );
			$response_body = wp_remote_retrieve_body( $response );

			error_log( 'WPFrontBlogger AI: HTTP response code: ' . $response_code );
			error_log( 'WPFrontBlogger AI: HTTP response body (first 500 chars): ' . substr( $response_body, 0, 500 ) );

			if ( $response_code !== 200 ) {
				$error_data = json_decode( $response_body, true );
				$error_message = isset( $error_data['error']['message'] ) ? 
					$error_data['error']['message'] : 
					__( 'OpenAI API request failed', 'wpfrontblogger' );
				
				error_log( 'WPFrontBlogger AI: API error message: ' . $error_message );
				return new WP_Error( 'api_error', $error_message );
			}

			$data = json_decode( $response_body, true );
			
			if ( ! isset( $data['choices'][0]['message']['content'] ) ) {
				error_log( 'WPFrontBlogger AI: Invalid response structure' );
				return new WP_Error( 'invalid_response', __( 'Invalid response from OpenAI API', 'wpfrontblogger' ) );
			}

			$result = trim( $data['choices'][0]['message']['content'] );
			error_log( 'WPFrontBlogger AI: Successfully extracted content, length: ' . strlen( $result ) );

			return $result;
		}

		/**
		 * Rewrite content using AI
		 */
		public function rewrite_content( $content ) {
			error_log( 'WPFrontBlogger AI: rewrite_content() method called' );
			error_log( 'WPFrontBlogger AI: Content length: ' . strlen( $content ) );
			
			$prompts = $this->get_prompts();
			error_log( 'WPFrontBlogger AI: Prompts retrieved: ' . print_r( $prompts, true ) );
			
			$prompt = str_replace( '{content}', $content, $prompts['rewrite'] );
			error_log( 'WPFrontBlogger AI: Final prompt: ' . substr( $prompt, 0, 200 ) . '...' );
			
			error_log( 'WPFrontBlogger AI: Calling make_api_request()' );
			$result = $this->make_api_request( $prompt, 1000 );
			
			if ( is_wp_error( $result ) ) {
				error_log( 'WPFrontBlogger AI: API request failed: ' . $result->get_error_message() );
			} else {
				error_log( 'WPFrontBlogger AI: API request successful, response length: ' . strlen( $result ) );
			}
			
			return $result;
		}

		/**
		 * Generate title using AI
		 */
		public function generate_title( $content ) {
			$prompts = $this->get_prompts();
			$prompt = str_replace( '{content}', $content, $prompts['title'] );
			
			$result = $this->make_api_request( $prompt, 200 );
			
			if ( is_wp_error( $result ) ) {
				return $result;
			}
			
			// Split titles and return as array
			$titles = array_filter( array_map( 'trim', explode( "\n", $result ) ) );
			return $titles;
		}

		/**
		 * Select categories using AI
		 */
		public function select_categories( $content ) {
			// Get all categories
			$categories = get_categories( array( 'hide_empty' => false ) );
			$category_names = array_map( function( $cat ) { return $cat->name; }, $categories );
			
			$prompts = $this->get_prompts();
			$prompt = str_replace( 
				array( '{content}', '{categories}' ), 
				array( $content, implode( ', ', $category_names ) ), 
				$prompts['categories'] 
			);
			
			$result = $this->make_api_request( $prompt, 150 );
			
			if ( is_wp_error( $result ) ) {
				return $result;
			}
			
			// Parse categories and return matching IDs
			$suggested_categories = array_filter( array_map( 'trim', explode( ',', $result ) ) );
			$category_ids = array();
			
			foreach ( $suggested_categories as $cat_name ) {
				$category = get_category_by_slug( sanitize_title( $cat_name ) );
				if ( ! $category ) {
					$category = get_term_by( 'name', $cat_name, 'category' );
				}
				
				if ( $category ) {
					$category_ids[] = $category->term_id;
				}
			}
			
			return $category_ids;
		}

		/**
		 * Generate tags using AI
		 */
		public function generate_tags( $content ) {
			$prompts = $this->get_prompts();
			$prompt = str_replace( '{content}', $content, $prompts['tags'] );
			
			$result = $this->make_api_request( $prompt, 200 );
			
			if ( is_wp_error( $result ) ) {
				return $result;
			}
			
			// Parse tags
			$tags = array_filter( array_map( 'trim', explode( ',', $result ) ) );
			return $tags;
		}

		/**
		 * Generate image search keywords using AI
		 */
		public function generate_image_keywords( $title, $content ) {
			$prompts = $this->get_prompts();
			$prompt = str_replace( 
				array( '{title}', '{content}' ), 
				array( $title, $content ), 
				$prompts['image'] 
			);
			
			$result = $this->make_api_request( $prompt, 100 );
			
			if ( is_wp_error( $result ) ) {
				return $result;
			}
			
			// Parse keywords
			$keywords = array_filter( array_map( 'trim', explode( ',', $result ) ) );
			return $keywords;
		}

		/**
		 * Select related WooCommerce products using AI
		 */
		public function select_products( $content, $limit = 3 ) {
			// Check if WooCommerce is active
			if ( ! class_exists( 'WooCommerce' ) ) {
				return new WP_Error( 'woocommerce_not_active', __( 'WooCommerce is not active', 'wpfrontblogger' ) );
			}

			$prompts = $this->get_prompts();
			$prompt = str_replace( '{content}', $content, $prompts['products'] );
			
			$result = $this->make_api_request( $prompt, 200 );
			
			if ( is_wp_error( $result ) ) {
				return $result;
			}
			
			// Parse product names/keywords
			$suggested_keywords = array_filter( array_map( 'trim', explode( ',', $result ) ) );
			
			// Find matching WooCommerce products
			$selected_products = array();
			
			foreach ( $suggested_keywords as $keyword ) {
				if ( count( $selected_products ) >= $limit ) {
					break;
				}
				
				// Search for products matching the keyword
				$products = get_posts( array(
					'post_type' => 'product',
					'post_status' => 'publish',
					'posts_per_page' => 2, // Get up to 2 products per keyword
					'meta_query' => array(
						'relation' => 'OR',
						array(
							'key' => '_stock_status',
							'value' => 'instock',
							'compare' => '='
						),
						array(
							'key' => '_manage_stock',
							'value' => 'no',
							'compare' => '='
						)
					),
					's' => $keyword
				) );
				
				foreach ( $products as $product ) {
					if ( count( $selected_products ) >= $limit ) {
						break;
					}
					
					// Avoid duplicates
					$product_exists = false;
					foreach ( $selected_products as $existing ) {
						if ( $existing['id'] == $product->ID ) {
							$product_exists = true;
							break;
						}
					}
					
					if ( ! $product_exists ) {
						// Get product price
						$product_obj = function_exists( 'wc_get_product' ) ? wc_get_product( $product->ID ) : null;
						$price = '';
						if ( $product_obj ) {
							$price = $product_obj->get_price_html();
						}
						
						$selected_products[] = array(
							'id' => $product->ID,
							'title' => $product->post_title,
							'price' => $price,
							'keyword' => $keyword
						);
					}
				}
			}
			
			return $selected_products;
		}
	}
}

// Initialize AI class
$GLOBALS['wpfrontblogger_ai'] = new WPFRONTBLOGGER_AI();
