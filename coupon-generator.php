

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
