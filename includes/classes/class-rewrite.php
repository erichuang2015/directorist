<?php

if ( !class_exists('ATBDP_Rewrite') ):

/**
 * Class ATBDP_Rewrite
 * It handle custom rewrite rules and actions etc.
 */
class ATBDP_Rewrite {

    public function __construct()
    {
        // add the rewrite rules to the init hook
        add_action( 'init', array( $this, 'add_write_rules' ) );
    }

    public function add_write_rules()
    {
        $home = home_url();
        // Checkout page URL Rewrite
        $cp_id = get_directorist_option('checkout_page'); // get the checkout page id
        if( $cp_id ) {
            $link = str_replace( $home, '', get_permalink( $cp_id ) );	// remove the home_url() from the link
            $link = trim( $link, '/' );	// remove slash / from the end and the start

            add_rewrite_rule( "$link/submit/([0-9]{1,})/?$", 'index.php?page_id='.$cp_id.'&atbdp_action=submission&atbdp_listing_id=$matches[1]', 'top' );
            add_rewrite_rule( "$link/promote/([0-9]{1,})/?$", 'index.php?page_id='.$cp_id.'&atbdp_action=promotion&atbdp_listing_id=$matches[1]', 'top' );
            add_rewrite_rule( "$link/([^/]+)/([0-9]{1,})/?$", 'index.php?page_id='.$cp_id.'&atbdp_action=$matches[1]&atbdp_order_id=$matches[2]', 'top' );
        }

        // Payment receipt page
        $prp_id = get_directorist_option('payment_receipt_page'); // get the payment receipt page id.
        if( $prp_id ) {
            $link = str_replace( $home, '', get_permalink( $prp_id ) );
            $link = trim( $link, '/' );

            add_rewrite_rule( "$link/order/([0-9]{1,})/?$", 'index.php?page_id='.$prp_id.'&atbdp_action=order&atbdp_order_id=$matches[1]', 'top' );
        }



        // Rewrite tags (Making custom query var available throughout the application
        // WordPress by default does not understand the unknown query vars. It needs to be registered with WP for using it.
        // by using add_rewrite_tag() or add_query_arg() on init hook or other earlier hook, we can register custom query var eg. atbdp_action and  we can access it later on any other page
        // by using get_query_var( 'atbdp_action' );  anywhere in the page.
        // otherwise, get_query_var() would return and empty string even if the 'atbdp_action' var is available in the query string.
        //
        add_rewrite_tag( '%atbdp_action%', '([^/]+)' );
        add_rewrite_tag( '%atbdp_order_id%', '([0-9]{1,})' );
        add_rewrite_tag( '%atbdp_listing_id%', '([0-9]{1,})' );
    }
} // ends ATBDP_Rewrite

endif;