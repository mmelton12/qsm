@LoicTheAztec - If you are reading this, I wanted to express my reverence and appreciation for your contributions to stackoverflow and myself as well.

To see it in action, you can visit https://staging59.emergencyinfoplan.com - when a user takes the assessment and submits their email, the coupon code is successfully 
generated. I want to reflect that in the product modal (single pack / family pack), by way of a discount notice, but with the code you provided, it's still not
showing up. I wanted to share the entire code so you could better understand... 


// ASSESSMENT DISCOUNT
// =============================================================================


add_action('qsm_quiz_submitted', 'after_submission', 99, 4 );

// Early enable customer WC_Session
add_action( 'init', 'wc_session_enabler' );
function wc_session_enabler() {
    if ( is_user_logged_in() || is_admin() ) 
        return; 
        
    if ( isset(WC()->session) && ! WC()->session->has_session() ) { 
        WC()->session->set_customer_session_cookie( true ); 
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
function after_submission () {
    // Get a random unique coupon code
    $coupon_code = generated_counpon_code();

    // Get an empty instance of the WC_Coupon Object
    $coupon = new WC_Coupon();

    $coupon->set_code( $coupon_code );
    $coupon->set_discount_type( 'fixed_cart' );
    $coupon->set_description( __('Assessment Discount') );
    $coupon->set_amount( '2' );
    $coupon->set_usage_limit( 1 );
    $coupon->set_free_shipping( 1 ); // <== Added back
    $coupon->set_individual_use( 1 );

    // Save coupon (data)
    $coupon->save();

    WC()->session->set( 'unique_coupon', $coupon_code );
}

// Add generated coupon to cart
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



// Add notice to Product Page

add_action( 'woocommerce_before_single_product', 'custom_message_before_single_product', 5 ); // For single product page
function custom_message_before_single_product() {
    $coupon_code      = WC()->session->get( 'unique_coupon' ); // Load coupon code
    $applied_coupons  = WC()->cart->get_applied_coupons(); // Get applied coupons

    if ( $coupon_code && in_array( $coupon_code, $applied_coupons ) ) {
        wc_print_notice( __('You will receive a discount at checkout!', 'woocommerce'), 'notice' );
    }
}
