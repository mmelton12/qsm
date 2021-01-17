<?php

// Generating dynamically the product "regular price"
  add_filter( 'woocommerce_product_get_regular_price', 'custom_dynamic_regular_price', 10, 2 );
  add_filter( 'woocommerce_product_variation_get_regular_price', 'custom_dynamic_regular_price', 10, 2 );
  function custom_dynamic_regular_price( $regular_price, $product ) {
      if( empty($regular_price) || $regular_price == 0 )
          return $product->get_price();
      else
          return $regular_price;
  }

   
  function assessmentdiscount () {

  // Generating dynamically the product "sale price"
  function custom_dynamic_sale_price( $sale_price, $product ) {
      
  add_filter( 'woocommerce_product_get_sale_price', 'custom_dynamic_sale_price', 10, 2 );
  add_filter( 'woocommerce_product_variation_get_sale_price', 'custom_dynamic_sale_price', 10, 2 );

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

  // Displayed formatted regular price + sale price
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
