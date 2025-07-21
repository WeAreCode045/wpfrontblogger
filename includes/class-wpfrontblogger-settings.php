<?php
/**
 * Settings functionality for the plugin.
 *
 * @package wpfrontblogger
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WPFRONTBLOGGER_SETTINGS' ) ) {
	/**
	 * Plugin Settings Class.
	 */
	class WPFRONTBLOGGER_SETTINGS {

		/**
		 * Initialize the class and set its properties.
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
		}

		/**
		 * Add settings page to admin menu
		 */
		public function add_settings_page() {
			add_options_page(
				__( 'WP Front Blogger Settings', 'wpfrontblogger' ),
				__( 'WP Front Blogger', 'wpfrontblogger' ),
				'manage_options',
				'wpfrontblogger-settings',
				array( $this, 'settings_page' )
			);
		}

		/**
		 * Register settings
		 */
		public function register_settings() {
			// Register settings
			register_setting( 'wpfrontblogger_settings', 'wpfrontblogger_openai_token' );
			register_setting( 'wpfrontblogger_settings', 'wpfrontblogger_ai_prompts' );

			// Add settings section
			add_settings_section(
				'wpfrontblogger_ai_section',
				__( 'AI Integration Settings', 'wpfrontblogger' ),
				array( $this, 'ai_section_callback' ),
				'wpfrontblogger_settings'
			);

			// OpenAI API Token field
			add_settings_field(
				'wpfrontblogger_openai_token',
				__( 'OpenAI API Token', 'wpfrontblogger' ),
				array( $this, 'openai_token_callback' ),
				'wpfrontblogger_settings',
				'wpfrontblogger_ai_section'
			);

			// AI Prompts field
			add_settings_field(
				'wpfrontblogger_ai_prompts',
				__( 'AI Prompt Engineering', 'wpfrontblogger' ),
				array( $this, 'ai_prompts_callback' ),
				'wpfrontblogger_settings',
				'wpfrontblogger_ai_section'
			);
		}

		/**
		 * AI section callback
		 */
		public function ai_section_callback() {
			echo '<p>' . __( 'Configure AI integration settings for enhanced blog post creation.', 'wpfrontblogger' ) . '</p>';
		}

		/**
		 * OpenAI token field callback
		 */
		public function openai_token_callback() {
			$token = get_option( 'wpfrontblogger_openai_token', '' );
			echo '<input type="password" id="wpfrontblogger_openai_token" name="wpfrontblogger_openai_token" value="' . esc_attr( $token ) . '" class="regular-text" placeholder="sk-..." />';
			echo '<p class="description">' . __( 'Enter your OpenAI API token. You can get one from <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI Platform</a>.', 'wpfrontblogger' ) . '</p>';
			
			if ( ! empty( $token ) ) {
				echo '<p class="description" style="color: green;">✓ ' . __( 'API Token is configured', 'wpfrontblogger' ) . '</p>';
			}
		}

		/**
		 * AI prompts field callback
		 */
		public function ai_prompts_callback() {
			$prompts = get_option( 'wpfrontblogger_ai_prompts', $this->get_default_prompts() );
			?>
			<div class="wpfrontblogger-prompt-fields">
				<h4><?php _e( 'Rewrite Content Prompt', 'wpfrontblogger' ); ?></h4>
				<textarea name="wpfrontblogger_ai_prompts[rewrite]" rows="4" cols="80" class="large-text"><?php echo esc_textarea( $prompts['rewrite'] ); ?></textarea>
				<p class="description"><?php _e( 'Prompt for rewriting blog content. Use {content} as placeholder for the original content.', 'wpfrontblogger' ); ?></p>

				<h4><?php _e( 'Generate Title Prompt', 'wpfrontblogger' ); ?></h4>
				<textarea name="wpfrontblogger_ai_prompts[title]" rows="3" cols="80" class="large-text"><?php echo esc_textarea( $prompts['title'] ); ?></textarea>
				<p class="description"><?php _e( 'Prompt for generating titles based on content. Use {content} as placeholder.', 'wpfrontblogger' ); ?></p>

				<h4><?php _e( 'Select Categories Prompt', 'wpfrontblogger' ); ?></h4>
				<textarea name="wpfrontblogger_ai_prompts[categories]" rows="3" cols="80" class="large-text"><?php echo esc_textarea( $prompts['categories'] ); ?></textarea>
				<p class="description"><?php _e( 'Prompt for selecting categories. Use {content} for content and {categories} for available categories.', 'wpfrontblogger' ); ?></p>

				<h4><?php _e( 'Generate Tags Prompt', 'wpfrontblogger' ); ?></h4>
				<textarea name="wpfrontblogger_ai_prompts[tags]" rows="3" cols="80" class="large-text"><?php echo esc_textarea( $prompts['tags'] ); ?></textarea>
				<p class="description"><?php _e( 'Prompt for generating tags. Use {content} as placeholder for the blog content.', 'wpfrontblogger' ); ?></p>

				<h4><?php _e( 'Featured Image Search Prompt', 'wpfrontblogger' ); ?></h4>
				<textarea name="wpfrontblogger_ai_prompts[image]" rows="3" cols="80" class="large-text"><?php echo esc_textarea( $prompts['image'] ); ?></textarea>
				<p class="description"><?php _e( 'Prompt for generating image search keywords. Use {content} and {title} as placeholders.', 'wpfrontblogger' ); ?></p>

				<h4><?php _e( 'Select Products Prompt', 'wpfrontblogger' ); ?></h4>
				<textarea name="wpfrontblogger_ai_prompts[products]" rows="3" cols="80" class="large-text"><?php echo esc_textarea( $prompts['products'] ); ?></textarea>
				<p class="description"><?php _e( 'Prompt for selecting WooCommerce products. Use {content} as placeholder for the blog content.', 'wpfrontblogger' ); ?></p>
			</div>
			<?php
		}

		/**
		 * Get default prompts
		 */
		private function get_default_prompts() {
			return array(
				'rewrite' => 'Please rewrite the following blog content to make it more engaging, professional, and SEO-friendly while maintaining the original meaning and key points. Keep the same tone but improve readability and flow:\n\n{content}',
				'title' => 'Based on the following blog content, generate 3 compelling and SEO-friendly titles that would attract readers. Make them catchy but professional:\n\n{content}\n\nProvide only the titles, separated by newlines.',
				'categories' => 'Based on the following blog content and available categories, select the most relevant categories (maximum 3). Only choose from existing categories:\n\nContent: {content}\n\nAvailable categories: {categories}\n\nProvide only the category names, separated by commas.',
				'tags' => 'Based on the following blog content, generate 5-8 relevant tags that would help with SEO and discoverability. Make them specific and relevant:\n\n{content}\n\nProvide only the tags, separated by commas.',
				'image' => 'Based on the following blog title and content, generate 2-3 search keywords for finding a relevant featured image from stock photos. Keep keywords simple and visual:\n\nTitle: {title}\nContent: {content}\n\nProvide only the search keywords, separated by commas.'
			);
		}

		/**
		 * Settings page content
		 */
		public function settings_page() {
			?>
			<div class="wrap">
				<h1><?php _e( 'WP Front Blogger Settings', 'wpfrontblogger' ); ?></h1>
				
				<form method="post" action="options.php">
					<?php
					settings_fields( 'wpfrontblogger_settings' );
					do_settings_sections( 'wpfrontblogger_settings' );
					submit_button();
					?>
				</form>

				<div class="wpfrontblogger-settings-info">
					<h3><?php _e( 'How to Use AI Features', 'wpfrontblogger' ); ?></h3>
					<ol>
						<li><?php _e( 'Get an OpenAI API token from your OpenAI account', 'wpfrontblogger' ); ?></li>
						<li><?php _e( 'Enter the token in the field above and save settings', 'wpfrontblogger' ); ?></li>
						<li><?php _e( 'Customize the AI prompts to match your content style', 'wpfrontblogger' ); ?></li>
						<li><?php _e( 'Use the AI buttons in the blog creation form', 'wpfrontblogger' ); ?></li>
					</ol>

					<h3><?php _e( 'Prompt Engineering Tips', 'wpfrontblogger' ); ?></h3>
					<ul>
						<li><?php _e( 'Use placeholders like {content}, {title}, {categories} in your prompts', 'wpfrontblogger' ); ?></li>
						<li><?php _e( 'Be specific about the format you want in the response', 'wpfrontblogger' ); ?></li>
						<li><?php _e( 'Include context about your website or audience in the prompts', 'wpfrontblogger' ); ?></li>
						<li><?php _e( 'Test different prompts to find what works best for your content', 'wpfrontblogger' ); ?></li>
					</ul>
				</div>
			</div>

			<style>
			.wpfrontblogger-prompt-fields h4 {
				margin-top: 20px;
				margin-bottom: 5px;
			}
			.wpfrontblogger-prompt-fields textarea {
				margin-bottom: 5px;
			}
			.wpfrontblogger-settings-info {
				margin-top: 30px;
				padding: 20px;
				background: #f9f9f9;
				border-left: 4px solid #0073aa;
			}
			.wpfrontblogger-settings-info h3 {
				margin-top: 0;
			}
			</style>
			<?php
		}
	}
}

new WPFRONTBLOGGER_SETTINGS();
