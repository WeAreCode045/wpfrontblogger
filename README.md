# WP Front Blogger

A WordPress plugin that provides a backend admin interface for creating blog posts through a user-friendly multistep form.

## Features

- **Multi-step Admin Interface**: Clean, user-friendly 3-step process in WordPress admin
- **AI-Powered Content Creation**: OpenAI integration for intelligent content assistance
  - AI Title Generation: Generate compelling titles based on your content
  - AI Content Rewriting: Improve and optimize your blog content
  - AI Category Selection: Automatically select relevant categories
  - AI Tag Generation: Generate appropriate tags from your content
  - AI Product Selection: Select related WooCommerce products based on content analysis
  - AI Image Search: Find relevant stock photos using AI-powered keywords
- **Category Management**: Search existing categories or create new ones with autocomplete
- **Tag Management**: Search existing tags or add new ones with autocomplete
- **Rich Text Editor**: Full WordPress editor for blog content
- **Featured Image Upload**: Drag and drop image upload with preview
- **Envato Elements Integration**: Search and import stock photos (if plugin installed)
- **WooCommerce Integration**: Select related WooCommerce products with autocomplete
- **Responsive Design**: Works on all devices and admin interfaces
- **AJAX Submission**: Smooth form submission without page reload

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the WordPress admin panel
3. Navigate to **Settings > WP Front Blogger** to configure AI settings (OpenAI API key)
4. Navigate to **Posts > Blog Creator** in your WordPress admin to access the form

## Usage

### Accessing the Form

After activation, you can access the blog creation form by:
1. Logging into your WordPress admin dashboard
2. Going to **Posts** in the admin menu
3. Clicking on **Blog Creator** submenu item

### Form Steps

#### Step 1: Basic Information
- **Title**: Required field for the blog post title
  - **AI Title Generation**: Click the AI button to generate titles based on your content
- **Categories**: Search existing categories or create new ones by typing and pressing Enter
  - **AI Category Selection**: Let AI automatically select relevant categories based on content
- **Tags**: Search existing tags or add new ones by typing and pressing Enter
  - **AI Tag Generation**: Generate appropriate tags automatically from your content

#### Step 2: Content
- **Blog Content**: Rich text editor for writing the blog post content (required)
  - **AI Content Rewriting**: Improve and optimize your existing content with AI assistance

#### Step 3: Media & Products
- **Featured Image**: Upload an image by clicking or dragging and dropping
  - **AI Image Search**: Find relevant stock photos using AI-generated keywords from your content
- **Envato Elements**: Search and import stock photos (if Envato Elements plugin is installed)
- **Related Products**: Select WooCommerce products (only visible if WooCommerce is active)
  - **AI Product Selection**: Automatically select relevant products based on your blog content

## Features in Detail

### AI-Powered Content Creation
- **OpenAI Integration**: Powered by GPT-3.5 Turbo for intelligent content assistance
- **Customizable Prompts**: Configure AI behavior through admin settings
- **Content Analysis**: AI analyzes your blog content to provide relevant suggestions
- **Smart Assistance**: AI helps with titles, content improvement, categories, tags, products, and images
- **Real-time Processing**: Get AI suggestions instantly while writing

### Admin Integration
- Seamlessly integrated into WordPress admin interface
- Located under Posts menu for easy access
- Admin-specific styling that matches WordPress design
- Screen-specific script and style loading for optimal performance

### Category and Tag Management
- Type to search existing categories/tags
- Press Enter to create new categories/tags
- Remove selected items by clicking the × button
- Visual indicators for new items

### Image Upload
- Supports drag and drop
- Image preview before submission
- Automatic upload to WordPress Media Library
- Remove uploaded images with one click

### Envato Elements Integration
- Automatically detects if Envato Elements plugin is installed
- Search stock photos by keyword
- Preview images before importing
- One-click import to WordPress Media Library
- Pagination for browsing multiple pages of results
- Images are properly attributed and tracked

### WooCommerce Integration
- Automatically detects if WooCommerce is active
- Search products by typing product names
- AI-powered product selection based on blog content
- Selected products are saved as post meta
- Access related products using: `get_post_meta($post_id, '_wpfrontblogger_related_products', true)`

### Form Validation
- Client-side validation for required fields
- Server-side security with WordPress nonces
- Sanitization of all input data
- Error messages for invalid submissions

### Post-Submission Options
- **View Blog Post**: Opens the created blog post in a new tab
- **Create Another Post**: Resets the form to create another blog post
- **View All Posts**: Navigate to the posts listing page
- Success confirmation with post details

## Post Status

By default, submitted posts are saved as **drafts**. To change this behavior, modify line 158 in `/includes/class-wpfrontblogger-ajax.php`:

```php
'post_status' => 'publish', // Change from 'draft' to 'publish'
```

## Styling

The plugin includes comprehensive CSS styling that can be customized in `/assets/css/frontend.css`. The design is:
- Mobile responsive
- Modern and clean
- Accessible
- Easy to customize

### Custom Styling
Add your own CSS to override the default styles:

```css
#wpfrontblogger-form-container {
    /* Your custom styles */
}
```

## Requirements

- WordPress 6.0+
- PHP 7.4+
- jQuery (included with WordPress)
- jQuery UI Autocomplete (automatically loaded)
- OpenAI API key (for AI features)

## Optional

- WooCommerce plugin for product integration features
- Envato Elements plugin for stock photo integration

## Support

For support, feature requests, or bug reports, please visit the plugin homepage or contact the developer.

## Changelog

### 1.0.0
- Initial release
- Multi-step form implementation
- Category and tag management
- Image upload functionality
- **Envato Elements integration** for stock photos
- WooCommerce integration
- AJAX form submission
- Responsive design
- **Post-submission options**: Visit created post or create another

## License

This plugin is licensed under the GPL v2 or later.

## Developer Notes

### File Structure
```
wpfrontblogger/
├── wpfrontblogger.php (Main plugin file)
├── includes/
│   ├── class-wpfrontblogger.php (Main class)
│   ├── class-wpfrontblogger-frontend.php (Frontend functionality)
│   ├── class-wpfrontblogger-ajax.php (AJAX handlers)
│   └── class-wpfrontblogger-admin.php (Admin functionality)
├── assets/
│   ├── css/frontend.css (Frontend styles)
│   └── js/frontend.js (Frontend JavaScript)
├── templates/
│   └── blog-form.php (Form template)
└── languages/ (Translation files)
```

### AJAX Endpoints
- `wpfrontblogger_search_categories` - Search categories
- `wpfrontblogger_search_tags` - Search tags  
- `wpfrontblogger_search_products` - Search products
- `wpfrontblogger_search_envato_images` - Search Envato Elements images
- `wpfrontblogger_import_envato_image` - Import Envato image to Media Library
- `wpfrontblogger_upload_image` - Upload images
- `wpfrontblogger_submit_post` - Submit the post
- `wpfrontblogger_ai_generate_title` - AI title generation
- `wpfrontblogger_ai_rewrite_content` - AI content rewriting
- `wpfrontblogger_ai_select_categories` - AI category selection
- `wpfrontblogger_ai_generate_tags` - AI tag generation
- `wpfrontblogger_ai_select_products` - AI product selection
- `wpfrontblogger_ai_find_image` - AI image search

### Security
- All AJAX requests are protected with WordPress nonces
- Input sanitization using WordPress functions
- File upload validation and security
- SQL injection prevention through WordPress APIs