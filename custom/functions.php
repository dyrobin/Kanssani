<?php
/**
 * Functions.php
 *
 * @package  Theme_Customisations
 * @author   WooThemes
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/********************** Google Analytics **********************/
/**
 * Add google analytics script
 */
add_action( 'wp_footer', 'kanssani_ga_script' );
if ( ! function_exists( 'kanssani_ga_script' ) ) {

    function kanssani_ga_script() {
        echo "
            <script>
              (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
              (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
              m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
              })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

              ga('create', 'UA-84358898-1', 'auto');
              ga('send', 'pageview');

            </script>
        ";
    }
}


/************************ Preloader **************************/
/**
 * Add Preloader gif
 */
add_action( 'storefront_before_header', 'kanssani_preloader' );
if ( ! function_exists( 'kanssani_preloader' ) ) {
    
    function kanssani_preloader() {
        echo '<div id="wptime-plugin-preloader"></div>';
    }
}


/************************ Header Part ************************/
/**
 * Add "Log In/Out" item to navigation menu 
 */
add_filter( 'wp_nav_menu_items', 'kanssani_nav_menu_loginout', 10, 2 );
if ( ! function_exists( 'kanssani_nav_menu_loginout' ) ) {

    function kanssani_nav_menu_loginout( $items, $args ) {
        if (is_user_logged_in() && $args->theme_location == 'secondary') {
            $items .= '<li><a href="' . wp_logout_url( get_home_url() ) . '">' 
                        . __('Log Out', 'kanssani') . '</a></li>';
        }
        elseif (!is_user_logged_in() && $args->theme_location == 'secondary') {
            $items .= '<li><a href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '">' 
                        . __('Log In', 'kanssani') . '</a></li>';
        }
        return $items;
    }
}


/************************ Footer Part ************************/
/**
 * Add newsletter form
 */
add_action( 'storefront_footer', 'kanssani_newsletter_form', 15 );
if ( ! function_exists( 'kanssani_newsletter_form' ) ) {

    function kanssani_newsletter_form() {
        if ( class_exists( 'WYSIJA_NL_Widget' ) ) {
            $widgetNL = new WYSIJA_NL_Widget(true);
            echo $widgetNL->widget(array('form' => 1, 'form_type' => 'php'));
        }
    }
}

/**
 * Change copyright text
 */
add_filter( 'storefront_copyright_text', 'kanssani_copyright_text' );
if ( ! function_exists( 'kanssani_copyright_text' ) ) {
    function kanssani_copyright_text( $content ) {
        return 'Copyright &copy; Kanssani ' . date( 'Y' );
    }
}

/**
 * Remove credit link
 */
add_filter( 'storefront_credit_link', '__return_false' );


/************************ Archive Page ************************/
/**
 * Change tag cloud widget arguments
 */
add_filter( 'woocommerce_product_tag_cloud_widget_args', 'kanssani_tag_cloud_widget_args' );
if ( ! function_exists('kanssani_tag_cloud_widget_args') ) {

    function kanssani_tag_cloud_widget_args ( $args ) {
        $args['smallest'] = 5;
        $args['largest'] = 20;

        return $args;
    }
}


/************************ Procudt Page ************************/
/**
 * Change upsell columns 
 */
add_filter( 'woocommerce_upsell_display_args', 'kanssani_upsell_columns' );
if ( ! function_exists( 'kanssani_upsell_columns' ) ) {

    function kanssani_upsell_columns ( $args ) {
        $args['columns'] = 4;

        return $args;
    }
}


/************************ Checkout Page ************************/
/**
 * Add create account note
 */
add_action( 'woocommerce_before_checkout_registration_form', 'kanssani_create_account_note' );
if ( ! function_exists( 'kanssani_create_account_note' ) ) {

    function kanssani_create_account_note( $checkout ) {
        echo '<p class="create-account-notes">' . __( 'Having an account lets you view your recent orders, manage your shipping and billing addresses and edit your profile. If you are a returning customer please login at the top of the page.', 'kanssani' ) . '</p>';
    }
}

/**
 * Checked create account by default
 */
add_filter( 'woocommerce_create_account_default_checked', '__return_true' ); 

/**
 * Change checkout fields
 *  - Remove billing->billing_company field
 *  - Remove shipping->shipping_company field
 *  - Add account->confirm_password  field
 *  - Add order->newsletter field
 */
add_filter( 'woocommerce_checkout_fields' , 'kanssani_checkout_fields' );
add_action( 'woocommerce_after_checkout_validation', 'kanssani_validation_confirm_password' );
add_action( 'woocommerce_checkout_order_processed', 'kanssani_newsletter_checkbox', 10, 2 );

if ( ! function_exists( 'kanssani_checkout_fields' ) ) {
    
    function kanssani_checkout_fields( $fields ) {
        unset($fields['billing']['billing_company']);
        unset($fields['shipping']['shipping_company']);
        $fields['account']['confirm_password'] = array(
            'type'          => 'password',
            'label'         => __('Confirm Password', 'kanssani'),
            'required'      => true,
            'placeholder'   => _x('Confirm Password', 'placeholder', 'kanssani')
        );

        $fields['order']['newsletter'] = array(
            'type'          => 'checkbox',
            'label'         => __('Subscribe to our newsletter', 'kanssani'),
            'default'       => 0
        );

        return $fields;
    }
}

if ( ! function_exists( 'kanssani_validation_confirm_password' ) ) {
    
    function kanssani_validation_confirm_password( $posted ) {
        $checkout = WC()->checkout;
        if ( ! is_user_logged_in() && ( $checkout->must_create_account || ! empty( $posted['createaccount'] ) ) ) {
            if ( strcmp( $posted['account_password'], $posted['confirm_password'] ) !== 0 ) {
                wc_add_notice( __( 'Passwords do not match.', 'kanssani' ), 'error' );
            }
        }
    }    
}

if ( ! function_exists( 'kanssani_newsletter_checkbox' ) ) {

    function kanssani_newsletter_checkbox( $order_id, $posted ) {
        if ( $posted['newsletter'] && class_exists('WYSIJA')) {

            $user_data = array(
                'email'     => $posted['billing_email'],
                'firstname' => $posted['billing_first_name'],
                'lastname'  => $posted['billing_last_name'],
                'status'    => 1
            );
 
            $data_subscriber = array(
                'user'      => $user_data,
                // list_id is hard-coded here
                'user_list' => array('list_ids' => array(1))
            );
            
            $helper_user = WYSIJA::get('user','helper');
            $helper_user->addSubscriber($data_subscriber);
        }
    }
}

/**
 * Remove sticky_order_review
 */
add_filter( 'storefront_sticky_order_review', '__return_false');

/**
 * Hide other shipping methods if a free shipping coupon is applied
 */
add_filter( 'woocommerce_package_rates', 'kanssani_hide_other_shipping_methods', 100 );
if ( ! function_exists( 'kanssani_hide_other_shipping_methods' )) {
    
    function kanssani_hide_other_shipping_methods( $rates ) {
        $hide = false;
        $free = array();

        // check if there is a free shipping coupon that has been applied
        $coupons = WC()->cart->get_coupons();
        foreach ( $coupons as $coupon ) {
            if ( $coupon->enable_free_shipping()) {
                $hide = true;
                break;
            }
        }

        // remove other shipping methods if needed
        if ( $hide ) {
            foreach ( $rates as $rate_id => $rate ) {
                if ( 'free_shipping' === $rate->method_id &&
                     'Free Shipping (with Coupon)' === $rate->label ) {
                    $free[ $rate_id ] = $rate;
                    break;
                }
            }
        }

        return ! empty( $free ) ? $free : $rates;
    }
}

/**
 * Add shipping methond notes
 * @todo add __() to each string for language support
 */
add_action( 'woocommerce_after_shipping_rate', 'kanssani_shipping_method_notes', 10, 2 );
if ( ! function_exists( 'kanssani_shipping_method_notes' ) ) {
    
    function kanssani_shipping_method_notes( $method, $index ) {
        echo '<div class="shipping_method_notes '. $method->method_id .'">';
        switch ($method->method_id) {
            case 'free_shipping':
                if ("Free Delivery in Otaniemi" === $method->label)
                    echo 'We guarantee a free delivery within Otaniemi area within 24 hour on weekdays.';
                break;
            case 'local_pickup':
                echo 'Pick-up from office shop at Room 246, Vuorimiehentie 2, Espoo. &nbsp;';
                echo '<a href="https://goo.gl/maps/geGNT9jyZ1v" target="_blank">Show On Map</a>';
                break;
            case 'wb_kirje_shipping_method_sz':
                echo "Delivered to a mailbox/mail slot or, if the item is too large, to the nearest postal outlet.";
                break;
            case 'sz_wb_posti_smartpost_shipping_method':
                echo "Pick-up from a Posti Terminal of your choice. When the parcel arrives, you will receive pick-up instructions by text message.";
                break;
            case 'wb_posti_ovelle_shipping_method_sz':
                echo "Posti brings the products to the desired address and agrees on a delivery time with you.";
                break;
            case 'wb_posti_nouto_shipping_method_sz':
                echo "Items are delivered to your address or picked up from a postal outlet.";
                break;
            default:
                break;
        }
        echo '</div>';
    }
}

/**
 * Change payment gateway icon 
 */
add_filter( 'woocommerce_gateway_icon', 'kanssani_gateway_icon', 10, 2 );
if ( ! function_exists( 'kanssani_gateway_icon' ) ) {

    function kanssani_gateway_icon ( $icon_html, $method_id ) {
        $dom = new DOMDocument();
        if ( $dom->loadHTML( $icon_html ) ) {

            $src_path = "store.kanssani.fi/wp-content/uploads/2016/09/";
            if (is_ssl()) {
                $src_path = "https://" . $src_path;
            } else {
                $src_path = "http://" . $src_path;
            }

            // There should be only one <img>, so break after it
            foreach ( $dom->getElementsByTagName('img') as $item ) {
                switch ( $method_id ) {
                    case 'checkout':
                        $item->setAttribute( 'src', $src_path . 'checkout_icon.png' );
                        break;
                    case 'ppec_paypal':
                        $item->setAttribute( 'src', $src_path . 'paypal_icon.gif' );
                        break;
                    default:
                        break;
                }
                $icon_html = $dom->saveHTML();
                break;
            }
        }

        return $icon_html;
    }
}
