## WP Front Blogger - Quick Start Guide

### How to Use

1. **Activate the Plugin**
   - Upload and activate the wpfrontblogger plugin

2. **Access the Form**
   - Go to your WordPress admin dashboard
   - Navigate to **Posts > Blog Creator**
   - Use the multistep form to create blog posts

3. **Admin Menu Location**
   - The plugin adds a "Blog Creator" submenu under the "Posts" menu
   - Only users with 'edit_posts' capability can access the form
   - All functionality is available directly in the WordPress admin interface

### User Permissions

The plugin respects WordPress user roles:
- **Editor** and above: Full access to the blog creation form
- **Author**: Can create blog posts with the form
- **Contributors and below**: No access (respects WordPress permissions)

### Display Related Products

To display related products in your blog posts, add this to your theme's `functions.php`:

```php
// Display related products after post content
add_filter( 'the_content', function( $content ) {
    if ( is_single() && class_exists( 'WooCommerce' ) ) {
        $post_id = get_the_ID();
        $related_products = get_post_meta( $post_id, '_wpfrontblogger_related_products', true );
        
        if ( ! empty( $related_products ) ) {
            $content .= '<h3>Related Products</h3>';
            $content .= '<div class="related-products">';
            
            foreach ( $related_products as $product_id ) {
                $product = wc_get_product( $product_id );
                if ( $product ) {
                    $content .= '<div class="product-item">';
                    $content .= '<a href="' . get_permalink( $product_id ) . '">';
                    $content .= '<h4>' . $product->get_name() . '</h4>';
                    $content .= '<span>' . $product->get_price_html() . '</span>';
                    $content .= '</a>';
                    $content .= '</div>';
                }
            }
            
            $content .= '</div>';
        }
    }
    return $content;
});
```

### Form Features

✅ **Multi-step Process**
- Step 1: Title, Categories, Tags
- Step 2: Blog Content (Rich Editor)  
- Step 3: Featured Image, Related Products

✅ **Smart Autocomplete**
- Search existing categories/tags
- Create new ones by pressing Enter
- WooCommerce product search

✅ **Image Upload**
- Drag & drop support
- Instant preview
- WordPress Media Library integration
- **NEW: Envato Elements stock photos** (if plugin installed)

✅ **Mobile Responsive**
- Works on all devices
- Touch-friendly interface

✅ **Post-Submission Actions**
- Visit the created blog post
- Create another post immediately
- Success confirmation with options

### Customization

**CSS Styling:**
```css
#wpfrontblogger-form-container {
    max-width: 900px; /* Custom width */
}

.btn-submit {
    background: #your-color; /* Custom button color */
}
```

**Change Post Status:**
Edit `/includes/class-wpfrontblogger-ajax.php` line 158:
```php
'post_status' => 'publish', // Instead of 'draft'
```

### Troubleshooting

**Form doesn't appear?**
- Make sure the plugin is activated
- Check that the shortcode is spelled correctly
- Verify the page template supports shortcodes

**Autocomplete not working?**
- Ensure jQuery UI is loading (check browser console)
- Try refreshing the page
- Check for JavaScript errors

**Images not uploading?**
- Verify WordPress upload permissions
- Check file size limits
- Ensure media library is accessible

**Envato Elements not showing?**
- Install and activate the Envato Elements plugin
- Ensure your Envato Elements subscription is active
- Check plugin compatibility

Need help? Check the full documentation in README.md!
