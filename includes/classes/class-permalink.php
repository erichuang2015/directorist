<?php
/**
 * ATBDP Permalink class
 *
 * This class is for interacting with Permalink, eg, receiving link to different page.
 *
 * @package     ATBDP
 * @subpackage  inlcudes/classes Permalink
 * @copyright   Copyright (c) 2018, AazzTech
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined('ABSPATH') ) { die( 'You are not allowed to access this file directly' ); }

if (!class_exists('ATBDP_Permalink')):
    class ATBDP_Permalink{
        /**
         * It returns the link to the custom search archive page of ATBDP
         * @return string
         */
        public static function get_search_result_page_link()
        {

            $link = home_url();
            $id = get_directorist_option('search_result_page'); // get the page id of the search page.
            if( $id ) $link = get_permalink( $id );



            return apply_filters('atbdp_search_result_page_url', $link );
        }

        /**
         * It returns the link to the custom search archive page of ATBDP
         * @return string
         */
        public static function get_dashboard_page_link()
        {

            $link = home_url();

            $id = get_directorist_option('user_dashboard'); // get the page id of the dashboard page.

            if( $id )  $link = get_permalink( $id );

            return apply_filters('atbdp_dashboard_page_url', $link );
        }


        /**
         * It returns the link to the custom search archive page of ATBDP
         * @param array $query_vars [optional] Array of query vars to be added to the registration page url
         * @return string
         */
        public static function get_registration_page_link($query_vars=array())
        {

            $link = home_url();

            $id = get_directorist_option('custom_registration'); // get the page id of the custom registration page.

            if( $id ) $link = get_permalink( $id );

            if (!empty($query_vars) && is_array($query_vars)){
                $link = add_query_arg( $query_vars, $link );
            }

            return apply_filters('atbdp_registration_page_url', $link );
        }


        /**
         * It returns the link to the custom search archive page of ATBDP
         * @return string
         */
        public static function get_add_listing_page_link()
        {
            $link = home_url();
            $id = get_directorist_option('add_listing_page');
            if( $id ) $link = get_permalink( $id );
            return apply_filters('atbdp_add_listing_page_url', $link );
        }


        /**
         * It returns the current page url of the WordPress and you can add any query string to the url
         * @param array $query_args The array of query arguments passed to the current url
         * @return mixed it returns the current url of WordPress
         */
        public static function get_current_page_url($query_args=array()){

            global $wp;

            $current_url = home_url(add_query_arg($query_args, $wp->request));

            return apply_filters('atbdp_current_page_url', $current_url );
        }
        
        /**
         * It returns the link to the custom category archive page of ATBDP
         * @param WP_Term $cat
         * @param string $field
         * @return string
         */
        public static function get_category_archive($cat, $field='slug')
        {
            $link = self::get_search_result_page_link() . "?q=&in_cat={$cat->$field}";
            return apply_filters('atbdp_category_archive_url', $link);

        }

        /**
         * It returns the link to the custom category archive page of ATBDP
         * @param WP_Term $loc
         * @param string $field
         * @return string
         */
        public static function get_location_archive($loc, $field='slug')
        {
            $link = self::get_search_result_page_link() . "?q=&in_loc={$loc->$field}";
            return apply_filters('atbdp_location_archive_url', $link);

        }

        /**
         * It returns the link to the custom tag archive page of ATBDP
         * @param WP_Term $tag
         * @param string $field
         * @return string
         */
        public static function get_tag_archive($tag, $field='slug')
        {
            return self::get_search_result_page_link() . "?q=&in_tag={$tag->$field}";
        }

        /**
         * Generate a permalink for Payment receipt page.
         *
         * @since    3.0.0
         *
         * @param    int       $order_id    Order ID.
         * @return   string                 Payment receipt page URL.
         */
        public static function get_payment_receipt_page_link($order_id) {
            $link = home_url(); // default url
            $id = get_directorist_option('payment_receipt_page');
            if( $id ) {
                $link = get_permalink( $id );

                if( '' != get_option( 'permalink_structure' ) ) {
                    $link = user_trailingslashit( trailingslashit( $link ) . 'order/' . $order_id );
                } else {
                    $link = add_query_arg(
                        array(
                            'atbdp_action' => 'order',
                            'atbdp_order' => $order_id
                        ),
                        $link
                    );
                }
            }

            return apply_filters('atbdp_payment_receipt_page_url', $link);
        }

    } // end ATBDP_Permalink

endif;
