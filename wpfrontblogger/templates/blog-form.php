<?php
/**
 * Blog Form Template
 *
 * @package wpfrontblogger
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="wpfrontblogger-form-container">
	<?php if ( $atts['show_title'] === 'yes' ) : ?>
		<h3><?php _e( 'Create New Blog Post', 'wpfrontblogger' ); ?></h3>
	<?php endif; ?>

	<div class="wpfrontblogger-progress-bar">
		<div class="progress-step active" data-step="1">
			<span class="step-number">1</span>
			<span class="step-title"><?php _e( 'Basic Info', 'wpfrontblogger' ); ?></span>
		</div>
		<div class="progress-step" data-step="2">
			<span class="step-number">2</span>
			<span class="step-title"><?php _e( 'Content', 'wpfrontblogger' ); ?></span>
		</div>
		<div class="progress-step" data-step="3">
			<span class="step-number">3</span>
			<span class="step-title"><?php _e( 'Media & Products', 'wpfrontblogger' ); ?></span>
		</div>
	</div>

	<form id="wpfrontblogger-form" enctype="multipart/form-data">
		<?php wp_nonce_field( 'wpfrontblogger_nonce', 'wpfrontblogger_nonce' ); ?>
		
		<!-- Step 1: Basic Information -->
		<div class="form-step active" id="step-1">
			<div class="form-group">
				<label for="post_title"><?php _e( 'Title', 'wpfrontblogger' ); ?> <span class="required">*</span></label>
				<input type="text" id="post_title" name="post_title" required maxlength="255" placeholder="<?php _e( 'Enter your blog post title', 'wpfrontblogger' ); ?>">
				<div class="field-error" id="post_title_error"></div>
			</div>

			<div class="form-group">
				<label for="categories"><?php _e( 'Categories', 'wpfrontblogger' ); ?></label>
				<div class="category-container">
					<input type="text" id="categories" name="categories" placeholder="<?php _e( 'Search or create categories', 'wpfrontblogger' ); ?>">
					<div class="selected-items" id="selected-categories"></div>
				</div>
				<small class="help-text"><?php _e( 'Type to search existing categories or create new ones', 'wpfrontblogger' ); ?></small>
			</div>

			<div class="form-group">
				<label for="tags"><?php _e( 'Tags', 'wpfrontblogger' ); ?></label>
				<div class="tag-container">
					<input type="text" id="tags" name="tags" placeholder="<?php _e( 'Search or add tags', 'wpfrontblogger' ); ?>">
					<div class="selected-items" id="selected-tags"></div>
				</div>
				<small class="help-text"><?php _e( 'Type to search existing tags or add new ones', 'wpfrontblogger' ); ?></small>
			</div>

			<div class="form-actions">
				<button type="button" class="btn btn-next" data-next="2"><?php _e( 'Next', 'wpfrontblogger' ); ?></button>
			</div>
		</div>

		<!-- Step 2: Content -->
		<div class="form-step" id="step-2">
			<div class="form-group">
				<label for="post_content"><?php _e( 'Blog Content', 'wpfrontblogger' ); ?> <span class="required">*</span></label>
				<?php
				wp_editor( '', 'post_content', array(
					'textarea_name' => 'post_content',
					'media_buttons' => true,
					'textarea_rows' => 10,
					'teeny' => false,
					'editor_height' => 300,
					'quicktags' => array( 'buttons' => 'em,strong,link,block,del,ins,img,ul,ol,li,code,more,close' )
				));
				?>
				<div class="field-error" id="post_content_error"></div>
			</div>

			<div class="form-actions">
				<button type="button" class="btn btn-prev" data-prev="1"><?php _e( 'Previous', 'wpfrontblogger' ); ?></button>
				<button type="button" class="btn btn-next" data-next="3"><?php _e( 'Next', 'wpfrontblogger' ); ?></button>
			</div>
		</div>

		<!-- Step 3: Media & Products -->
		<div class="form-step" id="step-3">
			<div class="form-group">
				<label for="featured_image"><?php _e( 'Featured Image', 'wpfrontblogger' ); ?></label>
				
				<!-- Image source tabs -->
				<div class="image-source-tabs">
					<button type="button" class="tab-button active" data-tab="upload"><?php _e( 'Upload Image', 'wpfrontblogger' ); ?></button>
					<?php if ( class_exists( 'Envato_Elements' ) || function_exists( 'envato_elements_get_images' ) ) : ?>
						<button type="button" class="tab-button" data-tab="envato"><?php _e( 'Envato Elements', 'wpfrontblogger' ); ?></button>
					<?php endif; ?>
				</div>

				<!-- Upload tab -->
				<div class="image-tab-content active" id="upload-tab">
					<div class="image-upload-container">
						<input type="file" id="featured_image" name="featured_image" accept="image/*">
						<div class="image-preview" id="image-preview" style="display: none;">
							<img src="" alt="Preview" id="preview-img">
							<button type="button" class="remove-image" id="remove-image">&times;</button>
						</div>
						<div class="upload-placeholder" id="upload-placeholder">
							<div class="upload-icon">📷</div>
							<p><?php _e( 'Click to upload or drag and drop an image', 'wpfrontblogger' ); ?></p>
						</div>
					</div>
				</div>

				<?php if ( class_exists( 'Envato_Elements' ) || function_exists( 'envato_elements_get_images' ) ) : ?>
				<!-- Envato Elements tab -->
				<div class="image-tab-content" id="envato-tab">
					<div class="envato-search-container">
						<div class="search-box">
							<input type="text" id="envato-search" placeholder="<?php _e( 'Search Envato Elements stock photos...', 'wpfrontblogger' ); ?>">
							<button type="button" id="envato-search-btn"><?php _e( 'Search', 'wpfrontblogger' ); ?></button>
						</div>
						
						<div class="envato-loading" id="envato-loading" style="display: none;">
							<div class="spinner-small"></div>
							<p><?php _e( 'Searching Envato Elements...', 'wpfrontblogger' ); ?></p>
						</div>
						
						<div class="envato-results" id="envato-results"></div>
						
						<div class="envato-pagination" id="envato-pagination" style="display: none;">
							<button type="button" class="btn-page" id="envato-prev-page" disabled><?php _e( 'Previous', 'wpfrontblogger' ); ?></button>
							<span class="page-info" id="envato-page-info"></span>
							<button type="button" class="btn-page" id="envato-next-page"><?php _e( 'Next', 'wpfrontblogger' ); ?></button>
						</div>
					</div>
				</div>
				<?php endif; ?>

				<input type="hidden" id="featured_image_id" name="featured_image_id" value="">
				<input type="hidden" id="featured_image_source" name="featured_image_source" value="upload">
			</div>

			<?php if ( class_exists( 'WooCommerce' ) ) : ?>
			<div class="form-group">
				<label for="related_products"><?php _e( 'Related Products', 'wpfrontblogger' ); ?></label>
				<div class="product-container">
					<input type="text" id="related_products" name="related_products" placeholder="<?php _e( 'Search for WooCommerce products', 'wpfrontblogger' ); ?>">
					<div class="selected-items" id="selected-products"></div>
				</div>
				<small class="help-text"><?php _e( 'Select WooCommerce products related to your blog post', 'wpfrontblogger' ); ?></small>
			</div>
			<?php endif; ?>

			<div class="form-actions">
				<button type="button" class="btn btn-prev" data-prev="2"><?php _e( 'Previous', 'wpfrontblogger' ); ?></button>
				<button type="submit" class="btn btn-submit" id="submit-btn"><?php _e( 'Publish Post', 'wpfrontblogger' ); ?></button>
			</div>
		</div>
	</form>

	<!-- Loading overlay -->
	<div class="loading-overlay" id="loading-overlay" style="display: none;">
		<div class="spinner"></div>
		<p><?php _e( 'Creating your blog post...', 'wpfrontblogger' ); ?></p>
	</div>

	<!-- Success message -->
	<div class="success-message" id="success-message" style="display: none;">
		<div class="success-icon">✓</div>
		<h4><?php _e( 'Blog Post Created Successfully!', 'wpfrontblogger' ); ?></h4>
		<p id="success-text"></p>
		<div class="success-actions">
			<button type="button" class="btn btn-primary" id="visit-post"><?php _e( 'Visit Blog Post', 'wpfrontblogger' ); ?></button>
			<button type="button" class="btn btn-secondary" id="create-another"><?php _e( 'Create Another Post', 'wpfrontblogger' ); ?></button>
		</div>
	</div>

	<!-- Hidden inputs for storing selected data -->
	<input type="hidden" id="selected_category_ids" name="selected_category_ids" value="">
	<input type="hidden" id="selected_tag_names" name="selected_tag_names" value="">
	<input type="hidden" id="selected_product_ids" name="selected_product_ids" value="">
	<input type="hidden" id="new_categories" name="new_categories" value="">
	<input type="hidden" id="new_tags" name="new_tags" value="">
	<input type="hidden" id="created_post_url" name="created_post_url" value="">
</div>
