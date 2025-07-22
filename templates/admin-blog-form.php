<?php
/**
 * Admin Blog Form Template
 *
 * @package wpfrontblogger
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="wpfrontblogger-admin-container">
	<div class="wpfrontblogger-progress-bar">
		<div class="progress-step active" data-step="1">
			<span class="step-number">1</span>
			<span class="step-title"><?php _e( 'Write Content', 'wpfrontblogger' ); ?></span>
		</div>
		<div class="progress-step" data-step="2">
			<span class="step-number">2</span>
			<span class="step-title"><?php _e( 'AI Rewrite', 'wpfrontblogger' ); ?></span>
		</div>
		<div class="progress-step" data-step="3">
			<span class="step-number">3</span>
			<span class="step-title"><?php _e( 'Title & Meta', 'wpfrontblogger' ); ?></span>
		</div>
		<div class="progress-step" data-step="4">
			<span class="step-number">4</span>
			<span class="step-title"><?php _e( 'Featured Image', 'wpfrontblogger' ); ?></span>
		</div>
		<div class="progress-step" data-step="5">
			<span class="step-number">5</span>
			<span class="step-title"><?php _e( 'Products', 'wpfrontblogger' ); ?></span>
		</div>
		<div class="progress-step" data-step="6">
			<span class="step-number">6</span>
			<span class="step-title"><?php _e( 'Review & Publish', 'wpfrontblogger' ); ?></span>
		</div>
	</div>

	<form id="wpfrontblogger-admin-form" enctype="multipart/form-data">
		<?php wp_nonce_field( 'wpfrontblogger_nonce', 'wpfrontblogger_nonce' ); ?>
		
		<!-- Step 1: Write Content -->
		<div class="form-step active" id="step-1">
			<h2><?php _e( 'Step 1: Write Your Blog Content', 'wpfrontblogger' ); ?></h2>
			<p class="step-description"><?php _e( 'Start by writing your blog content. This will be saved and used in the following steps for AI assistance.', 'wpfrontblogger' ); ?></p>
			
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="post_content"><?php _e( 'Blog Content', 'wpfrontblogger' ); ?> <span class="description required">(required)</span></label>
						</th>
						<td>
							<?php
							wp_editor( '', 'post_content', array(
								'textarea_name' => 'post_content',
								'media_buttons' => true,
								'textarea_rows' => 15,
								'teeny' => false,
								'editor_height' => 400,
								'wpautop' => true,
								'tinymce' => array(
									'toolbar1' => 'bold,italic,underline,link,unlink,bullist,numlist,blockquote,alignleft,aligncenter,alignright,undo,redo',
									'toolbar2' => 'formatselect,forecolor,backcolor,removeformat,charmap,outdent,indent,hr,wp_more',
									'content_css' => false,
									'statusbar' => false,
									'resize' => true,
									'menubar' => false,
									'branding' => false
								),
								'quicktags' => array( 
									'buttons' => 'em,strong,link,block,del,ins,img,ul,ol,li,code,more,close'
								)
							));
							?>
							
							<div class="field-error" id="post_content_error"></div>
							<p class="description"><?php _e( 'Write your complete blog content. This will be automatically saved when you proceed to the next step.', 'wpfrontblogger' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>

			<div class="form-actions">
				<button type="button" class="button button-primary btn-next" data-next="2"><?php _e( 'Save & Continue', 'wpfrontblogger' ); ?></button>
			</div>
		</div>

		<!-- Step 2: AI Rewrite (Optional) -->
		<div class="form-step" id="step-2">
			<h2><?php _e( 'Step 2: AI Content Enhancement', 'wpfrontblogger' ); ?></h2>
			<p class="step-description"><?php _e( 'Let AI improve your content, or skip this step to keep your original text.', 'wpfrontblogger' ); ?></p>
			
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label><?php _e( 'Content Preview', 'wpfrontblogger' ); ?></label>
						</th>
						<td>
							<div class="content-preview" id="content-preview">
								<!-- Content will be loaded here from session storage -->
							</div>
							
							<div class="content-ai-actions">
								<button type="button" class="button button-primary ai-button" id="ai-rewrite-content">
									<span class="ai-icon">🤖</span> <?php _e( 'Enhance with AI', 'wpfrontblogger' ); ?>
								</button>
								<button type="button" class="button" id="skip-rewrite"><?php _e( 'Keep Original', 'wpfrontblogger' ); ?></button>
								<div class="ai-loading" id="ai-content-loading" style="display: none;">
									<span class="spinner is-active"></span>
									<span><?php _e( 'AI is enhancing your content...', 'wpfrontblogger' ); ?></span>
								</div>
							</div>
							
							<p class="description"><?php _e( 'AI will improve readability, style, and structure while keeping your core message.', 'wpfrontblogger' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>

			<div class="form-actions">
				<button type="button" class="button btn-prev" data-prev="1"><?php _e( 'Previous', 'wpfrontblogger' ); ?></button>
				<button type="button" class="button button-primary btn-next" data-next="3"><?php _e( 'Continue', 'wpfrontblogger' ); ?></button>
			</div>
		</div>

		<!-- Step 3: Title & Meta Information -->
		<div class="form-step" id="step-3">
			<h2><?php _e( 'Step 3: Title, Categories & Tags', 'wpfrontblogger' ); ?></h2>
			<p class="step-description"><?php _e( 'Set your title, select categories and tags. AI can help based on your content.', 'wpfrontblogger' ); ?></p>
			
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="post_title"><?php _e( 'Title', 'wpfrontblogger' ); ?> <span class="description required">(required)</span></label>
						</th>
						<td>
							<div class="input-with-ai">
								<input type="text" id="post_title" name="post_title" class="regular-text" required maxlength="255" placeholder="<?php _e( 'Enter your blog post title', 'wpfrontblogger' ); ?>">
								<button type="button" class="button ai-button" id="ai-generate-title" title="<?php _e( 'Generate title using AI based on content', 'wpfrontblogger' ); ?>">
									<span class="ai-icon">🤖</span> <?php _e( 'AI Generate', 'wpfrontblogger' ); ?>
								</button>
							</div>
							<div class="ai-loading" id="ai-title-loading" style="display: none;">
								<span class="spinner is-active"></span>
								<span><?php _e( 'AI is generating titles...', 'wpfrontblogger' ); ?></span>
							</div>
							<div class="ai-suggestions" id="ai-title-suggestions" style="display: none;">
								<h4><?php _e( 'AI Generated Titles:', 'wpfrontblogger' ); ?></h4>
								<div class="ai-suggestion-list"></div>
							</div>
							<div class="field-error" id="post_title_error"></div>
							<p class="description"><?php _e( 'Enter a compelling title or let AI generate suggestions based on your content', 'wpfrontblogger' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="categories"><?php _e( 'Categories', 'wpfrontblogger' ); ?></label>
						</th>
						<td>
							<div class="category-container">
								<div class="input-with-ai">
									<input type="text" id="categories" name="categories" class="regular-text" placeholder="<?php _e( 'Search or create categories', 'wpfrontblogger' ); ?>">
									<button type="button" class="button ai-button" id="ai-select-categories" title="<?php _e( 'Let AI select relevant categories based on content', 'wpfrontblogger' ); ?>">
										<span class="ai-icon">🤖</span> <?php _e( 'AI Select', 'wpfrontblogger' ); ?>
									</button>
								</div>
								<div class="selected-items" id="selected-categories"></div>
								<div class="ai-loading" id="ai-categories-loading" style="display: none;">
									<span class="spinner is-active"></span>
									<span><?php _e( 'AI is selecting categories...', 'wpfrontblogger' ); ?></span>
								</div>
							</div>
							<p class="description"><?php _e( 'Type to search existing categories, create new ones by pressing Enter, or let AI select based on your content', 'wpfrontblogger' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="tags"><?php _e( 'Tags', 'wpfrontblogger' ); ?></label>
						</th>
						<td>
							<div class="tag-container">
								<div class="input-with-ai">
									<input type="text" id="tags" name="tags" class="regular-text" placeholder="<?php _e( 'Search or add tags', 'wpfrontblogger' ); ?>">
									<button type="button" class="button ai-button" id="ai-generate-tags" title="<?php _e( 'Generate relevant tags using AI based on content', 'wpfrontblogger' ); ?>">
										<span class="ai-icon">🤖</span> <?php _e( 'AI Generate', 'wpfrontblogger' ); ?>
									</button>
								</div>
								<div class="selected-items" id="selected-tags"></div>
								<div class="ai-loading" id="ai-tags-loading" style="display: none;">
									<span class="spinner is-active"></span>
									<span><?php _e( 'AI is generating tags...', 'wpfrontblogger' ); ?></span>
								</div>
							</div>
							<p class="description"><?php _e( 'Type to search existing tags, add new ones by pressing Enter, or let AI generate relevant tags', 'wpfrontblogger' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>

			<div class="form-actions">
				<button type="button" class="button btn-prev" data-prev="2"><?php _e( 'Previous', 'wpfrontblogger' ); ?></button>
				<button type="button" class="button button-primary btn-next" data-next="4"><?php _e( 'Continue', 'wpfrontblogger' ); ?></button>
			</div>
		</div>

		<!-- Step 4: Featured Image -->
		<div class="form-step" id="step-4">
			<h2><?php _e( 'Step 4: Featured Image', 'wpfrontblogger' ); ?></h2>
			<p class="step-description"><?php _e( 'Choose a featured image by uploading, using Envato Elements, or let AI find one for you.', 'wpfrontblogger' ); ?></p>
			
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="featured_image"><?php _e( 'Featured Image', 'wpfrontblogger' ); ?></label>
						</th>
						<td>
							<div class="input-with-ai">
								<input type="text" id="post_title" name="post_title" class="regular-text" required maxlength="255" placeholder="<?php _e( 'Enter your blog post title', 'wpfrontblogger' ); ?>">
								<button type="button" class="button ai-button" id="ai-generate-title" title="<?php _e( 'Generate title using AI based on content', 'wpfrontblogger' ); ?>">
									<span class="ai-icon">🤖</span> <?php _e( 'Generate Title', 'wpfrontblogger' ); ?>
								</button>
							</div>
							<div class="ai-loading" id="ai-title-loading" style="display: none;">
								<span class="spinner is-active"></span>
								<span><?php _e( 'AI is generating titles...', 'wpfrontblogger' ); ?></span>
							</div>
							<div class="ai-suggestions" id="ai-title-suggestions" style="display: none;">
								<h4><?php _e( 'AI Generated Titles:', 'wpfrontblogger' ); ?></h4>
								<div class="ai-suggestion-list"></div>
							</div>
							<div class="field-error" id="post_title_error"></div>
							<p class="description"><?php _e( 'Write your content above first, then use AI to generate relevant titles', 'wpfrontblogger' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="categories"><?php _e( 'Categories', 'wpfrontblogger' ); ?></label>
						</th>
						<td>
							<div class="category-container">
								<div class="input-with-ai">
									<input type="text" id="categories" name="categories" class="regular-text" placeholder="<?php _e( 'Search or create categories', 'wpfrontblogger' ); ?>">
									<button type="button" class="button ai-button" id="ai-select-categories" title="<?php _e( 'Let AI select relevant categories based on content', 'wpfrontblogger' ); ?>">
										<span class="ai-icon">🤖</span> <?php _e( 'AI Select', 'wpfrontblogger' ); ?>
									</button>
								</div>
								<div class="selected-items" id="selected-categories"></div>
								<div class="ai-loading" id="ai-categories-loading" style="display: none;">
									<span class="spinner is-active"></span>
									<span><?php _e( 'AI is selecting categories...', 'wpfrontblogger' ); ?></span>
								</div>
							</div>
							<p class="description"><?php _e( 'Type to search existing categories, create new ones by pressing Enter, or let AI select based on your content', 'wpfrontblogger' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="tags"><?php _e( 'Tags', 'wpfrontblogger' ); ?></label>
						</th>
						<td>
							<div class="tag-container">
								<div class="input-with-ai">
									<input type="text" id="tags" name="tags" class="regular-text" placeholder="<?php _e( 'Search or add tags', 'wpfrontblogger' ); ?>">
									<button type="button" class="button ai-button" id="ai-generate-tags" title="<?php _e( 'Generate relevant tags using AI based on content', 'wpfrontblogger' ); ?>">
										<span class="ai-icon">🤖</span> <?php _e( 'AI Generate', 'wpfrontblogger' ); ?>
									</button>
								</div>
								<div class="selected-items" id="selected-tags"></div>
								<div class="ai-loading" id="ai-tags-loading" style="display: none;">
									<span class="spinner is-active"></span>
									<span><?php _e( 'AI is generating tags...', 'wpfrontblogger' ); ?></span>
								</div>
							</div>
							<p class="description"><?php _e( 'Type to search existing tags, add new ones by pressing Enter, or let AI generate relevant tags', 'wpfrontblogger' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>

			<div class="form-actions">
				<button type="button" class="button button-primary btn-next" data-next="2"><?php _e( 'Next Step', 'wpfrontblogger' ); ?></button>
			</div>
		</div>

		<!-- Step 2: Media & Products -->
		<div class="form-step" id="step-2">
			<h2><?php _e( 'Step 2: Media & Products', 'wpfrontblogger' ); ?></h2>
			
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="featured_image"><?php _e( 'Featured Image', 'wpfrontblogger' ); ?></label>
						</th>
						<td>
							<!-- Image source tabs -->
							<div class="image-source-tabs">
								<button type="button" class="button tab-button active" data-tab="upload"><?php _e( 'Upload Image', 'wpfrontblogger' ); ?></button>
								<button type="button" class="button tab-button ai-button" data-tab="ai-image" title="<?php _e( 'Let AI find a suitable image from Envato Elements', 'wpfrontblogger' ); ?>">
									<span class="ai-icon">🤖</span> <?php _e( 'AI Find Image', 'wpfrontblogger' ); ?>
								</button>
								<?php if ( class_exists( 'Envato_Elements' ) || function_exists( 'envato_elements_get_images' ) ) : ?>
									<button type="button" class="button tab-button" data-tab="envato"><?php _e( 'Envato Elements', 'wpfrontblogger' ); ?></button>
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

							<!-- AI Image tab -->
							<div class="image-tab-content" id="ai-image-tab">
								<div class="ai-image-container">
									<div class="ai-image-description">
										<h4><?php _e( 'AI Image Generation', 'wpfrontblogger' ); ?></h4>
										<p><?php _e( 'AI will analyze your title and content to find the perfect image from Envato Elements', 'wpfrontblogger' ); ?></p>
									</div>
									
									<button type="button" class="button button-primary" id="ai-find-image">
										<span class="ai-icon">🤖</span> <?php _e( 'Find Perfect Image with AI', 'wpfrontblogger' ); ?>
									</button>
									
									<div class="ai-loading" id="ai-image-loading" style="display: none;">
										<span class="spinner is-active"></span>
										<span><?php _e( 'AI is analyzing your content and searching for the perfect image...', 'wpfrontblogger' ); ?></span>
									</div>
									
									<div class="ai-image-results" id="ai-image-results" style="display: none;">
										<h4><?php _e( 'AI Found This Image:', 'wpfrontblogger' ); ?></h4>
										<div class="ai-image-suggestion">
											<!-- AI result will be populated here -->
										</div>
										<div class="ai-image-keywords">
											<strong><?php _e( 'Search terms used:', 'wpfrontblogger' ); ?></strong>
											<span id="ai-keywords-used"></span>
										</div>
									</div>
									
									<div class="ai-image-actions" style="display: none;">
										<button type="button" class="button button-primary" id="use-ai-image"><?php _e( 'Use This Image', 'wpfrontblogger' ); ?></button>
										<button type="button" class="button" id="try-ai-again"><?php _e( 'Try Again', 'wpfrontblogger' ); ?></button>
									</div>
								</div>
							</div>

							<?php if ( class_exists( 'Envato_Elements' ) || function_exists( 'envato_elements_get_images' ) ) : ?>
							<!-- Envato Elements tab -->
							<div class="image-tab-content" id="envato-tab">
								<div class="envato-search-container">
									<div class="search-box">
										<input type="text" id="envato-search" class="regular-text" placeholder="<?php _e( 'Search Envato Elements stock photos...', 'wpfrontblogger' ); ?>">
										<button type="button" class="button" id="envato-search-btn"><?php _e( 'Search', 'wpfrontblogger' ); ?></button>
									</div>
									
									<div class="envato-loading" id="envato-loading" style="display: none;">
										<div class="spinner-small"></div>
										<p><?php _e( 'Searching Envato Elements...', 'wpfrontblogger' ); ?></p>
									</div>
									
									<div class="envato-results" id="envato-results"></div>
									
									<div class="envato-pagination" id="envato-pagination" style="display: none;">
										<button type="button" class="button btn-page" id="envato-prev-page" disabled><?php _e( 'Previous', 'wpfrontblogger' ); ?></button>
										<span class="page-info" id="envato-page-info"></span>
										<button type="button" class="button btn-page" id="envato-next-page"><?php _e( 'Next', 'wpfrontblogger' ); ?></button>
									</div>
								</div>
							</div>
							<?php endif; ?>

							<input type="hidden" id="featured_image_id" name="featured_image_id" value="">
							<input type="hidden" id="featured_image_source" name="featured_image_source" value="upload">
						</td>
					</tr>
					
					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<tr>
						<th scope="row">
							<label for="related_products"><?php _e( 'Related Products', 'wpfrontblogger' ); ?></label>
						</th>
						<td>
							<div class="product-container">
								<div class="input-with-ai">
									<input type="text" id="related_products" name="related_products" class="regular-text" placeholder="<?php _e( 'Search for WooCommerce products', 'wpfrontblogger' ); ?>">
									<button type="button" class="button ai-button" id="ai-select-products" title="<?php _e( 'Let AI select relevant products based on content', 'wpfrontblogger' ); ?>">
										<span class="ai-icon">🤖</span> <?php _e( 'AI Select', 'wpfrontblogger' ); ?>
									</button>
								</div>
								<div class="selected-items" id="selected-products"></div>
								<div class="ai-loading" id="ai-products-loading" style="display: none;">
									<span class="spinner is-active"></span>
									<span><?php _e( 'AI is selecting products...', 'wpfrontblogger' ); ?></span>
								</div>
							</div>
							<p class="description"><?php _e( 'Search for products, or let AI select relevant products based on your blog content', 'wpfrontblogger' ); ?></p>
						</td>
					</tr>
					<?php endif; ?>
				</tbody>
			</table>

			<div class="form-actions">
				<button type="button" class="button btn-prev" data-prev="1"><?php _e( 'Previous Step', 'wpfrontblogger' ); ?></button>
				<button type="submit" class="button button-primary btn-submit" id="submit-btn"><?php _e( 'Create Blog Post', 'wpfrontblogger' ); ?></button>
			</div>
		</div>
	</form>

	<!-- Loading overlay -->
	<div class="loading-overlay" id="loading-overlay" style="display: none;">
		<div class="spinner is-active"></div>
		<p><?php _e( 'Creating your blog post...', 'wpfrontblogger' ); ?></p>
	</div>

	<!-- Success message -->
	<div class="success-message" id="success-message" style="display: none;">
		<div class="notice notice-success">
			<h3><?php _e( 'Blog Post Created Successfully!', 'wpfrontblogger' ); ?></h3>
			<p id="success-text"></p>
			<div class="success-actions">
				<a href="#" class="button button-primary" id="visit-post" target="_blank"><?php _e( 'View Blog Post', 'wpfrontblogger' ); ?></a>
				<button type="button" class="button" id="create-another"><?php _e( 'Create Another Post', 'wpfrontblogger' ); ?></button>
				<a href="<?php echo admin_url( 'edit.php' ); ?>" class="button"><?php _e( 'View All Posts', 'wpfrontblogger' ); ?></a>
			</div>
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
