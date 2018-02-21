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

        if( get_option('permalink_structure') ) {

            $link = get_directorist_option('search_result_page'); // get the page id of the search page.

            if( $link > 0 ) {
                $link = get_permalink( $link );
            }

        }

        return $link;
    }

    /**
     * It returns the link to the custom search archive page of ATBDP
     * @return string
     */
    public static function get_dashboard_page_link()
    {

        $link = home_url();

        if( get_option('permalink_structure') ) {

            $link = get_directorist_option('user_dashboard'); // get the page id of the dashboard page.

            if( $link > 0 ) {
                $link = get_permalink( $link );
            }

        }

        return $link;
    }


    /**
     * It returns the link to the custom search archive page of ATBDP
     * @return string
     */
    public static function get_add_listing_page_link()
    {

        $link = home_url();

        if( get_option('permalink_structure') ) {

            $link = get_directorist_option('add_listing_page'); // get the page id of the add listing page.

            if( $link > 0 ) {
                $link = get_permalink( $link );
            }

        }

        return $link;
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
        return self::get_search_result_page_link() . "?q=&in_cat={$cat->$field}";
    }

    /**
     * It returns the link to the custom category archive page of ATBDP
     * @param WP_Term $loc
     * @param string $field
     * @return string
     */
    public static function get_location_archive($loc, $field='slug')
    {
        return self::get_search_result_page_link() . "?q=&in_loc={$loc->$field}";
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




} // end ATBDP_Permalink 






endif;

