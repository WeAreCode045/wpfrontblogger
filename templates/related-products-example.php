<?php
/**
 * Example template to display related products in blog posts
 * Add this code to your theme's single.php or functions.php
 */

/**
 * Display related products at the end of blog posts
 */
function wpfrontblogger_display_related_products() {
    if ( ! is_single() || ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    $post_id = get_the_ID();
    $related_products = get_post_meta( $post_id, '_wpfrontblogger_related_products', true );

    if ( empty( $related_products ) || ! is_array( $related_products ) ) {
        return;
    }

    echo '<div class="wpfrontblogger-related-products">';
    echo '<h3>' . __( 'Related Products', 'wpfrontblogger' ) . '</h3>';
    echo '<div class="products-grid">';

    foreach ( $related_products as $product_id ) {
        if ( function_exists( 'wc_get_product' ) ) {
            $product = wc_get_product( $product_id );
        } else {
            continue;
        }
        if ( ! $product ) {
            continue;
        }

        echo '<div class="product-item">';
        echo '<a href="' . get_permalink( $product_id ) . '">';
        echo '<div class="product-image">' . $product->get_image( 'thumbnail' ) . '</div>';
        echo '<h4>' . $product->get_name() . '</h4>';
        echo '<span class="price">' . $product->get_price_html() . '</span>';
        echo '</a>';
        echo '</div>';
    }

    echo '</div></div>';
}

// Hook to display after post content
add_action( 'the_content', function( $content ) {
    if ( is_single() ) {
        ob_start();
        wpfrontblogger_display_related_products();
        $related_products = ob_get_clean();
        return $content . $related_products;
    }
    return $content;
});

/**
 * Add CSS for related products display
 */
function wpfrontblogger_related_products_styles() {
    if ( is_single() ) {
        echo '<style>
        .wpfrontblogger-related-products {
            margin: 30px 0;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        .wpfrontblogger-related-products h3 {
            margin-bottom: 20px;
            color: #333;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        .product-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .product-item:hover {
            transform: translateY(-2px);
        }
        .product-item a {
            text-decoration: none;
            color: inherit;
        }
        .product-item h4 {
            margin: 10px 0;
            font-size: 16px;
        }
        .product-item .price {
            font-weight: bold;
            color: #007cba;
        }
        </style>';
    }
}
add_action( 'wp_head', 'wpfrontblogger_related_products_styles' );
