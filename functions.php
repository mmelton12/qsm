<?php

// =============================================================================
// FUNCTIONS.PHP
// -----------------------------------------------------------------------------
// Overwrite or add your own custom functions to Pro in this file.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Parent Stylesheet
//   02. Additional Functions
// =============================================================================

// Enqueue Parent Stylesheet
// =============================================================================

add_filter( 'x_enqueue_parent_stylesheet', '__return_true' );

/* ------- Line Break Shortcode --------*/
function line_break_shortcode() {
    return '<br />';
}
add_shortcode( 'br', 'line_break_shortcode' );



function webroom_woocommerce_coupon_links(){

  // Bail if WooCommerce or sessions aren't available.
  if (!function_exists('WC') || !WC()->session) {
    return;
  }
  /**
   * Filter the coupon code query variable name.
   *
   * @since 1.0.0
   *
   * @param string $query_var Query variable name.
   */
  $query_var = apply_filters('woocommerce_coupon_links_query_var', 'coupon_code');
  // Bail if a coupon code isn't in the query string.
  if (empty($_GET[$query_var])) {
    return;
  }
  // Set a session cookie to persist the coupon in case the cart is empty.
  WC()->session->set_customer_session_cookie(true);
  // Apply the coupon to the cart if necessary.
  if (!WC()->cart->has_discount($_GET[$query_var])) {
    // WC_Cart::add_discount() sanitizes the coupon code.
    WC()->cart->add_discount($_GET[$query_var]);
  }
}
add_action('wp_loaded', 'webroom_woocommerce_coupon_links', 30);
add_action('woocommerce_add_to_cart', 'webroom_woocommerce_coupon_links');




add_action( 'init','wpb_wl_popup_contents_relocate' );
function wpb_wl_popup_contents_relocate(){
 $wpb_wl_quickview_summary = wpb_wl_get_option( 'wpb_wl_quickview_summary','general_settings', array( 'image' => 'image', 'title' => 'title', 'rating' => 'rating', 'price' => 'price', 'content' => 'content', 'meta' => 'meta', 'add_to_cart' => 'add_to_cart', 'gallery' => 'gallery','details_link' => 'details_link', ) );

 if ( array_key_exists('add_to_cart', $wpb_wl_quickview_summary) ) {
  remove_action( 'wpb_wl_woocommerce_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
  add_action( 'wpb_wl_woocommerce_product_summary', 'woocommerce_template_single_add_to_cart', 11 );
 }

}




/* Google Tag Manager

add_action('wp_head', 'add_head_code');
function add_head_code() { ?>

  <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TLVT6PJ');</script>
<!-- End Google Tag Manager -->

<?php }
*/
add_action('x_before_site_begin', 'add_additional_head_code');
function add_additional_head_code() { ?>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TLVT6PJ"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<?php }





// Additional Functions
// =============================================================================

/** Disable Ajax Call from WooCommerce on front page and post
add_action( 'wp_enqueue_scripts', 'dequeue_woocommerce_cart_fragments', 11);
function dequeue_woocommerce_cart_fragments() {
if (is_front_page() || is_single() ) wp_dequeue_script('wc-cart-fragments');
}

function cart_script_disabled(){
   wp_dequeue_script( 'wc-cart' );
}
add_action( 'wp_enqueue_scripts', 'cart_script_disabled' );

*/

// Redirect Tutorial to SetupMedicalID
add_action('wp_print_styles', 'redirect_old_url_to_new_url');
function redirect_old_url_to_new_url() {
    global $post;
    if ('tutorial' == $post->post_name) {
        wp_redirect( site_url() . '/setupmedicalid', 301 );
        die();
    }
}


// To change add to cart text on single product page
// =============================================================================
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_custom_single_add_to_cart_text' ); 
function woocommerce_custom_single_add_to_cart_text() {
    return __( 'ORDER NOW', 'woocommerce' ); 
}




/*
// Redirect Shop page to home page
function wpc_shop_url_redirect() {
    if( is_shop() ){
        wp_redirect( home_url( '/' ) ); // Assign custom internal page here
        exit();
    }
}
add_action( 'template_redirect', 'wpc_shop_url_redirect' );

*/


// Redirect to Checkout Upon Add to Cart - WooCommerce
// =============================================================================

add_filter( 'woocommerce_add_to_cart_redirect', 'bbloomer_redirect_checkout_add_cart' );
 
function bbloomer_redirect_checkout_add_cart() {
   return wc_get_checkout_url();
}


/* SPONSOR SECTION */

/**
 * @snippet       Bulk (Dynamic) Pricing - WooCommerce
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 3.8
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */
 
add_action( 'woocommerce_before_calculate_totals', 'bbloomer_quantity_based_pricing', 9999 );
 
function bbloomer_quantity_based_pricing( $cart ) {
 
    if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) return;
 
    // Define discount rules and thresholds
    $threshold1 = 11; // Change price if items > 100
    $discount1 = 0.05; // Reduce unit price by 5%
    $threshold2 = 21; // Change price if items > 1000
    $discount2 = 0.10; // Reduce unit price by 10%
 
    foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
      if ( $cart_item['quantity'] >= $threshold1 && $cart_item['quantity'] < $threshold2 ) {
         $price = round( $cart_item['data']->get_price() * ( 1 - $discount1 ), 2 );
         $cart_item['data']->set_price( $price );
      } elseif ( $cart_item['quantity'] >= $threshold2 ) {
         $price = round( $cart_item['data']->get_price() * ( 1 - $discount2 ), 2 );
         $cart_item['data']->set_price( $price );
      }    
    }
    /*
     if( $threshold1 > 10 )
        $cart->add_fee( __( 'Quantity discount' ),  $price); // Discount*/

 }


// SPONSOR REDIRECT IF LOGGED IN

 function add_login_check()
{
    if ( is_user_logged_in() && is_page(1462) ) {
        wp_redirect('../../sponsor-portal/');
        exit;
    }
}

add_action('wp', 'add_login_check');

/**
 * @snippet       Translate a String in WooCommerce (English to English)
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 4.0
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */
  
add_filter( 'gettext', 'bbloomer_translate_woocommerce_strings', 999, 3 );
  
function bbloomer_translate_woocommerce_strings( $translated, $untranslated, $domain ) {
 
   if ( ! is_admin() && 'woocommerce' === $domain ) {
 
      switch ( $translated) {
 
         case 'Free shipping coupon' :
 
            $translated = '';
            break;
 
      }
 
   }   
  
   return $translated;
 
}




/* Redirect sponsor after logout 
// **************************************************************
 add_filter( 'logout_url', 'custom_logout_page', 10, 2 );
function custom_logout_page( $logout_url, $redirect ) {
return 'https://emergencyinfoplan.com/login';
}*/



// Remove Menu bar from subscribers 
// **************************************************************

add_action('after_setup_theme', 'remove_admin_bar');
 
function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}


// Limit media library access
// **************************************************************
  
add_filter( 'ajax_query_attachments_args', 'wpb_show_current_user_attachments' );
 
function wpb_show_current_user_attachments( $query ) {
    $user_id = get_current_user_id();
    if ( $user_id && !current_user_can('activate_plugins') && !current_user_can('edit_others_posts
') ) {
        $query['author'] = $user_id;
    }
    return $query;
} 




// Prevent editing certain fields in sponsor profile update
// **************************************************************

// update '1' to the ID of your form
add_filter( 'gform_pre_render_7', 'add_readonly_script' );
function add_readonly_script( $form ) {
    ?>
 
    <script type="text/javascript">
        jQuery(document).ready(function(){
            /* apply only to a input with a class of gf_readonly */
            jQuery("li.gf_readonly input").attr("readonly","readonly");
        });
    </script>
 
    <?php
    return $form;
}



// Remove RankMath from following Pages
// **************************************************************

add_action( 'wp_head', function(){
    if( is_page('1210,1323,1212,1116,1362,1129,804,1209,1477,1365,1462,1564,1347,1351,1353,1373,')) {
        remove_all_actions( 'rank_math/head' );
        add_action( 'wp_head', '_wp_render_title_tag', 2 );
    }
}, 1 );






// WooCommerce Product Page Customization
// *******************************************

// Remove Slider 
add_action( 'after_setup_theme', 'remove_woo_features', 999 );
function remove_woo_features(){
  remove_theme_support( 'wc-product-gallery-slider' );
}

// Remove Zoom on Woocommerce Image 
function remove_image_zoom_support() {
    remove_theme_support( 'wc-product-gallery-zoom' );
}
add_action( 'wp', 'remove_image_zoom_support', 100 );


// Display variation's price even if min and max prices are the same
add_filter('woocommerce_available_variation', function ($value, $object = null, $variation = null) {
  if ($value['price_html'] == '') {
    $value['price_html'] = '<span class="price">' . $variation->get_price_html() . '</span>';
  }
  return $value;
}, 10, 3);


// Display Quantity Set of 10 on sponsor page 
add_action( 'woocommerce_before_add_to_cart_quantity', 'bbloomer_single_category_slug' );
 
function bbloomer_single_category_slug() {
 
if ( has_term( 'sponsor-codes', 'product_cat' ) ) {
echo '<div class="qty">Quantity (Set of 10)</div>';
} 
}


// Add "Price:" before price
add_filter( 'woocommerce_get_price_html', 'cw_change_product_price_display' );
add_filter( 'woocommerce_cart_item_price', 'cw_change_product_price_display' );
function cw_change_product_price_display( $price ) {
    // returning the text before the price
    return '<div class="labelprice">Price:  ' . $price . '</div>';
}

// Add Select Phone Type (what type of sticker do I need?)
add_action( 'woocommerce_before_add_to_cart_form', 'select_intructions_after_title' );
function select_intructions_after_title() {
 echo '<div class="selectphonetype">Select Phone Type: </div>'; 
}


// Hide SKU, Category, Tags
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );


// END WooCommerce Product Page Customization





// Set Email to Username on woocommerce registration at checkout
function my_new_customer_username( $username, $email, $new_user_args, $suffix ) {
    return $email;
}
add_filter( 'woocommerce_new_customer_username', 'my_new_customer_username', 10, 4 );


remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );




// Clear cart on homepage
// *******************************************
add_action( 'woocommerce_add_cart_item_data', 'woocommerce_clear_cart_url' );

function woocommerce_clear_cart_url() {

    global $woocommerce;
    $woocommerce->cart->empty_cart();
} 


add_action( 'woocommerce_before_cart', 'bbloomer_apply_coupon' );
  

/*
// Refer a friend Coupon Generator
// *******************************************


// Early enable customer WC_Session
add_action( 'init', 'force_non_logged_user_wc_session' );
function force_non_logged_user_wc_session()
{
    if (is_user_logged_in() || is_admin())
        return;
    if (isset(WC()->session)) {
        if (!WC()->session->has_session()) {
            WC()->session->set_customer_session_cookie(true);
       }
    }
}
// Utility function that generate a non existing coupon code (as each coupon code required to be unique)
function generated_counpon_code() {
    for ( $i = 0; $i < 1; $i++ ) {
        $coupon_code = strtolower( wp_generate_password( 10, false ) );

        // Check that the generated code doesn't exist yet
        if( coupon_exists( $coupon_code ) ) $i--; // continue
        else break; // Stop the loopp
    }
    return $coupon_code;
}

function coupon_exists( $coupon_code ) {
    global $wpdb;
    return $wpdb->get_var( $wpdb->prepare("SELECT COUNT(ID) FROM $wpdb->posts
        WHERE post_type = 'shop_coupon' AND post_name = '%s'", $coupon_code));
}

// Generate coupon and set code in customer WC session variable
function after_submission ($results_array, $results_id, $qmn_quiz_options, $qmn_array_for_variables) {
    if (isset($_POST['no_thanks_action']) && $_POST['no_thanks_action'] == '1') {
        return;
    } else {
        if (isset($qmn_quiz_options->no_thanks_link) && $qmn_quiz_options->no_thanks_link == 1) {
            // Get a random unique coupon code
            $coupon_code = generated_counpon_code();

            // Get an empty instance of the WC_Coupon Object
            $coupon = new WC_Coupon();

            $coupon->set_code( $coupon_code );
            $coupon->set_discount_type( 'fixed_cart' );
            $coupon->set_description( __('Assessment Discount') );
            $coupon->set_amount( '2' );
            $coupon->set_usage_limit( 1 );
           // $coupon->set_free_shipping( 1 ); // <== Added back
            $coupon->set_individual_use( 0 );

            // Save coupon (data)
            $coupon->save();

            WC()->session->set( 'unique_coupon', $coupon_code );
        }
    }
}

add_action('qsm_quiz_submitted', 'after_submission', 99, 4 );



/*
// Add generated coupon to cart - old
add_action( 'woocommerce_add_to_cart', 'apply_matched_coupons' );
function apply_matched_coupons() {
    if ( is_cart() || is_checkout() ) return;

    // Load coupon code
    $coupon_code = WC()->session->get( 'unique_coupon' );

    if ( $coupon_code && ! WC()->cart->has_discount( $coupon_code ) ) {
        WC()->cart->apply_coupon( $coupon_code ); // Apply coupon code
        WC()->session->__unset( 'unique_coupon' ); // remove session variable 
    }
}
*/
/*
add_action( 'woocommerce_add_to_cart', 'apply_matched_coupons' );

function apply_matched_coupons() {
    // If the current user is a shop admin
  //  if ( current_user_can( 'manage_woocommerce' ) ) return;
    // If the user is on the cart or checkout page
    if ( is_cart() || is_checkout() ) return;

    // Load coupon code
    $coupon_code = WC()->session->get( 'unique_coupon' );


    if ( WC()->cart->has_discount( $coupon_code ) ) return;

    WC()->cart->add_discount( $coupon_code );
}
*/


/** Add Class to WCClever Radio Button Variations **/

add_filter( 'woovr_variation_radio_selector', 'your_woovr_variation_radio_selector', 99, 3 );
function your_woovr_variation_radio_selector( $selector, $product_id, $checked ) {
   return '<div class="woovr-variation-selector"><label><input class="cleverradio radio" type="radio" name="woovr_variation_' . $product_id . '" ' . $checked . '/></label></div>';
}


/**
 * @snippet       WooCommerce Remove "What is PayPal?" @ Checkout
 * @how-to        Get CustomizeWoo.com FREE
 * @sourcecode    https://businessbloomer.com/?p=21186
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 3.5.4
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */
 
add_filter( 'woocommerce_gateway_icon', 'bbloomer_remove_what_is_paypal', 10, 2 );
 
function bbloomer_remove_what_is_paypal( $icon_html, $gateway_id ) {
if( 'paypal' == $gateway_id ) {
   $icon_html = '<img src="/wp-content/plugins/woocommerce/includes/gateways/paypal/assets/images/paypal.png" alt="PayPal Acceptance Mark">';
}
return $icon_html;
}



/**
* @snippet       Save "Terms and Conditions" @ Checkout - WooCommerce
* @how-to        Get CustomizeWoo.com FREE
* @author        Rodolfo Melogli
* @compatible    Woo 4.3
* @donate $9     https://businessbloomer.com/bloomer-armada/
*/
  
// 1. Save T&C as Order Meta
  
add_action( 'woocommerce_checkout_update_order_meta', 'bbloomer_save_terms_conditions_acceptance' );
  
function bbloomer_save_terms_conditions_acceptance( $order_id ) {
if ( $_POST['terms'] ) update_post_meta( $order_id, 'terms', esc_attr( $_POST['terms'] ) );
}
  
// 2. Display T&C @ Single Order Page
  
add_action( 'woocommerce_admin_order_data_after_billing_address', 'bbloomer_display_terms_conditions_acceptance' );
  
function bbloomer_display_terms_conditions_acceptance( $order ) {
if ( get_post_meta( $order->get_id(), 'terms', true ) == 'on' ) {
echo '<p><strong>Terms & Conditions: </strong>accepted</p>';
} else echo '<p><strong>Terms & Conditions: </strong>N/A</p>';
}

// - Settings > CheckoutWC > Premium Features > PHP Snippets (for Growth and Developer plans)

add_filter( 'cfw_promo_code_mobile_heading', function() {
    return __( 'Coupon code', 'checkout-wc' );
} );

add_filter( 'cfw_promo_code_label', function() {
    return __( 'Code/Coupon', 'checkout-wc' );
} );

add_filter( 'cfw_promo_code_placeholder', function() {
    return __( 'Sponsor Code or Coupon', 'checkout-wc' );
} );

add_filter( 'woocommerce_cart_shipping_total', function( $total ) {
    if ( $total == 'Free!' ) {
        $total = 'Free Shipping';
    }
    
    return $total;
} );





// - Settings > CheckoutWC > Premium Features > PHP Snippets (for Growth and Developer plans)

add_action( 'cfw_after_customer_info_tab_login', function() {
    if ( ! is_user_logged_in() ) {
        return;
    }
    
    $billing_fields        = WC()->checkout()->get_checkout_fields( 'billing' );
    $email_field           = $billing_fields['billing_email'];

    cfw_form_field( 'billing_email', $email_field, WC()->checkout()->get_value( 'billing_email' ) );
} );


//

add_filter( 'cfw_billing_shipping_address_heading', function( $heading ) {
   return 'Shipping address';
} );

/* CheckoutWC Modifications */

// Do NOT include the opening php tag.
// Place in your theme's functions.php file

add_filter( 'cfw_billing_shipping_address_heading', function( $heading ) {
   return 'Shipping address';
} );




// Redirect Cart to 404

add_action( 'template_redirect', 'cart_redirect_404' );
function cart_redirect_404(){
    // Redirect to non existing page that will make a 404
    if ( is_cart() ) {
        wp_safe_redirect( home_url('/cart-page/') ); 
        exit();
    }
}



function wc_register_guests( $order_id ) {
  // get all the order data
  $order = new WC_Order($order_id);
  
  //get the user email from the order
  $order_email = $order->billing_email;
    
  // check if there are any users with the billing email as user or email
  $email = email_exists( $order_email );  
  $user = username_exists( $order_email );
  
  // if the UID is null, then it's a guest checkout
  if( $user == false && $email == false ){
    
    // random password with 12 chars
    $random_password = wp_generate_password();
    
    // create new user with email as username & newly created pw
    $user_id = wp_create_user( $order_email, $random_password, $order_email );
    
    //WC guest customer identification
    update_user_meta( $user_id, 'guest', 'yes' );
 
    //user's billing data
    update_user_meta( $user_id, 'billing_address_1', $order->billing_address_1 );
    update_user_meta( $user_id, 'billing_address_2', $order->billing_address_2 );
    update_user_meta( $user_id, 'billing_city', $order->billing_city );
    update_user_meta( $user_id, 'billing_company', $order->billing_company );
    update_user_meta( $user_id, 'billing_country', $order->billing_country );
    update_user_meta( $user_id, 'billing_email', $order->billing_email );
    update_user_meta( $user_id, 'billing_first_name', $order->billing_first_name );
    update_user_meta( $user_id, 'billing_last_name', $order->billing_last_name );
    update_user_meta( $user_id, 'billing_phone', $order->billing_phone );
    update_user_meta( $user_id, 'billing_postcode', $order->billing_postcode );
    update_user_meta( $user_id, 'billing_state', $order->billing_state );
 
    // user's shipping data
    update_user_meta( $user_id, 'shipping_address_1', $order->shipping_address_1 );
    update_user_meta( $user_id, 'shipping_address_2', $order->shipping_address_2 );
    update_user_meta( $user_id, 'shipping_city', $order->shipping_city );
    update_user_meta( $user_id, 'shipping_company', $order->shipping_company );
    update_user_meta( $user_id, 'shipping_country', $order->shipping_country );
    update_user_meta( $user_id, 'shipping_first_name', $order->shipping_first_name );
    update_user_meta( $user_id, 'shipping_last_name', $order->shipping_last_name );
    update_user_meta( $user_id, 'shipping_method', $order->shipping_method );
    update_user_meta( $user_id, 'shipping_postcode', $order->shipping_postcode );
    update_user_meta( $user_id, 'shipping_state', $order->shipping_state );
    
    // link past orders to this newly created customer
    wc_update_new_customer_past_orders( $user_id );
  }
  
}
 
//add this newly created function to the thank you page
add_action( 'woocommerce_thankyou', 'wc_register_guests', 10, 1 );



/* Add to the functions.php file of your theme */
add_filter( 'woocommerce_order_button_text', 'woo_custom_order_button_text' ); 

function woo_custom_order_button_text() {
    return __( 'Finalize Your Order', 'woocommerce' ); 
}
function w3speedup_before_start_optimization($html){
    $html = str_replace(array("@font-face{font-family:'FontAwesomePro';font-style:normal;font-weight:900;font-display:block;src:url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-solid-900.woff2') format('woff2'),url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-solid-900.woff') format('woff'),url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-solid-900.ttf') format('truetype');}","@font-face{font-family:'FontAwesome';font-style:normal;font-weight:900;font-display:block;src:url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-solid-900.woff2') format('woff2'),url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-solid-900.woff') format('woff'),url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-solid-900.ttf') format('truetype');}","@font-face{font-family:'FontAwesomeRegular';font-style:normal;font-weight:400;font-display:block;src:url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-regular-400.woff2') format('woff2'),url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-regular-400.woff') format('woff'),url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-regular-400.ttf') format('truetype');}@font-face{font-family:'FontAwesomePro';font-style:normal;font-weight:400;font-display:block;src:url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-regular-400.woff2') format('woff2'),url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-regular-400.woff') format('woff'),url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-regular-400.ttf') format('truetype');}","@font-face{font-family:'FontAwesomeLight';font-style:normal;font-weight:300;font-display:block;src:url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-light-300.woff2') format('woff2'),url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-light-300.woff') format('woff'),url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-light-300.ttf') format('truetype');}@font-face{font-family:'FontAwesomePro';font-style:normal;font-weight:300;font-display:block;src:url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-light-300.woff2') format('woff2'),url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-light-300.woff') format('woff'),url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-light-300.ttf') format('truetype');}","@font-face{font-family:'FontAwesomeBrands';font-style:normal;font-weight:normal;font-display:block;src:url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-brands-400.woff2') format('woff2'),url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-brands-400.woff') format('woff'),url('https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-brands-400.ttf') format('truetype');}",""),array("","","","",""),$html); 
  return $html;
}
function w3speedup_internal_js_customize($html,$path){
  if(strpos($path,'assets/js/wppopups.js') !== false){   
    $html = str_replace('"use strict";','',$html);  
  }
  return $html;
}
function wpdocs_theme_name_scripts() {
  //wp_dequeue_style('dashicons');
//wp_deregister_style('dashicons');
    wp_enqueue_style( 'fontawesome5', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css' );  
  wp_enqueue_style('fontawesome47','https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css');
  wp_enqueue_style('fonts','/wp-content/themes/pro-child/fonts.css');
}
add_action( 'wp_enqueue_scripts', 'wpdocs_theme_name_scripts' );

function w3speedup_customize_critical_css($critical_css){
$critical_css = str_replace(array(" @font-face{font-family:'FontAwesomePro';font-style:normal;font-weight:900;font-display:block;src:url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-solid-900.woff2) format('woff2'),url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-solid-900.woff) format('woff'),url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-solid-900.ttf) format('truetype');}@font-face{font-family:'FontAwesome';font-style:normal;font-weight:900;font-display:block;src:url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-solid-900.woff2) format('woff2'),url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-solid-900.woff) format('woff'),url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-solid-900.ttf) format('truetype');}@font-face{font-family:'FontAwesomeRegular';font-style:normal;font-weight:400;font-display:block;src:url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-regular-400.woff2) format('woff2'),url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-regular-400.woff) format('woff'),url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-regular-400.ttf) format('truetype');}@font-face{font-family:'FontAwesomePro';font-style:normal;font-weight:400;font-display:block;src:url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-regular-400.woff2) format('woff2'),url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-regular-400.woff) format('woff'),url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-regular-400.ttf) format('truetype');}@font-face{font-family:'FontAwesomeLight';font-style:normal;font-weight:300;font-display:block;src:url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-light-300.woff2) format('woff2'),url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-light-300.woff) format('woff'),url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-light-300.ttf) format('truetype');}@font-face{font-family:'FontAwesomePro';font-style:normal;font-weight:300;font-display:block;src:url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-light-300.woff2) format('woff2'),url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-light-300.woff) format('woff'),url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-light-300.ttf) format('truetype');}@font-face{font-family:'FontAwesomeBrands';font-style:normal;font-weight:normal;font-display:block;src:url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-brands-400.woff2) format('woff2'),url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-brands-400.woff) format('woff'),url(https://www.emergencyinfoplan.com/wp-content/themes/pro/cornerstone/assets/dist/fonts/fa-brands-400.ttf) format('truetype');}","<script type='text/javascript' src='https://emergencyinfoplan.com/wp-content/plugins/shortcodes-ultimate-extra/includes/js/shortcodes/index.js' id='shortcodes-ultimate-extra-js'></script>","id='magnific-popup-js'></script>"), array("","","id='magnific-popup-js'></script><script type='text/javascript' src='https://emergencyinfoplan.com/wp-content/plugins/shortcodes-ultimate-extra/includes/js/shortcodes/index.js' id='shortcodes-ultimate-extra-js'></script>"),$critical_css);


return $critical_css;
}

function w3speedup_inner_js_customize($html){
  if(strpos($html,'$.lockBody') !== false){  
    $html = str_replace(array('$.lockBody()','$.unlockBody();'),array('//jQuery.lockBody()','//$.unlockBody();'),$html);  
  }
  return $html;
}
function w3_no_critical_css($url){
  if(strpos($url,'how-to-determine-your-ios') !== false){  
    return true;  
  }
  return false;
}
add_filter( 'style_loader_src',  'neo_remove_ver_css_js', 9999, 2 );
add_filter( 'script_loader_src', 'neo_remove_ver_css_js', 9999, 2 );

function neo_remove_ver_css_js( $src, $handle ) 
{
    $src = remove_query_arg( 'ver', $src );

    return $src;
}

function wpdocs_dequeue_script() {
    wp_dequeue_script( 'shortcodes-ultimate-extra' );
wp_enqueue_script( 'shortcodes-ultimate-extra', 'https://emergencyinfoplan.com/wp-content/plugins/shortcodes-ultimate-extra/includes/js/shortcodes/index.js?gg=gg', array('magnific-popup') ,true);
}
//add_action( 'wp_print_scripts', 'wpdocs_dequeue_script', 1000 );




// OFFER DISCOUNT AFTER QUIZ SUBMISSION


// Generating dynamically the product "regular price"
add_filter( 'woocommerce_product_get_regular_price', 'custom_dynamic_regular_price', 10, 2 );
add_filter( 'woocommerce_product_variation_get_regular_price', 'custom_dynamic_regular_price', 10, 2 );
function custom_dynamic_regular_price( $regular_price, $product ) {
    if( empty($regular_price) || $regular_price == 0 )
        return $product->get_price();
    else
        return $regular_price;
}

add_action( 'qsm_after_results_page', 'assessmentdiscount', 10, 2 );
  function assessmentdiscount () {

// Generating dynamically the product "sale price"
add_filter( 'woocommerce_product_get_sale_price', 'custom_dynamic_sale_price', 10, 2 );
add_filter( 'woocommerce_product_variation_get_sale_price', 'custom_dynamic_sale_price', 10, 2 );
function custom_dynamic_sale_price( $sale_price, $product ) {
    $rate = 2;
    if( empty($sale_price) || $sale_price == 0 )
        return $product->get_regular_price() - $rate;
    else
        return $sale_price;
};

// Displayed formatted regular price + sale price
add_filter( 'woocommerce_get_price_html', 'custom_dynamic_sale_price_html', 20, 2 );
function custom_dynamic_sale_price_html( $price_html, $product ) {
    if( $product->is_type('variable') ) return $price_html;

    $price_html = wc_format_sale_price( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ), wc_get_price_to_display(  $product, array( 'price' => $product->get_sale_price() ) ) ) . $product->get_price_suffix();

    return $price_html;
}

add_action( 'woocommerce_before_calculate_totals', 'set_cart_item_sale_price', 20, 1 );
function set_cart_item_sale_price( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
        return;

    // Iterate through each cart item
    foreach( $cart->get_cart() as $cart_item ) {
        $price = $cart_item['data']->get_sale_price(); // get sale price
        $cart_item['data']->set_price( $price ); // Set the sale price

      }
  }

}
