<?php
/**
 * ATBDP Add_Listing class
 *
 * This class is for interacting with Add_Listing, eg, saving data to the database
 *
 * @package     ATBDP
 * @subpackage  inlcudes/classes Add_Listing
 * @copyright   Copyright (c) 2018, AazzTech
 * @since       1.0
 */

// Exit if accessed directly
defined('ABSPATH') || die( 'Direct access is not allowed.' );

if (!class_exists('ATBDP_Add_Listing')):

    /**
     * Class ATBDP_Add_Listing
     */
    class ATBDP_Add_Listing{


        /**
         * @var string
         */
        public $nonce = 'add_listing_nonce';
        /**
         * @var string
         */
        public $nonce_action = 'add_listing_action';


        /**
         * ATBDP_Add_Listing constructor.
         */
        public function __construct()
        {
            // show the attachment of the current users only
            add_filter( 'ajax_query_attachments_args', array($this, 'show_current_user_attachments'), 10, 1 );
            add_action ('wp_loaded', array($this, 'add_listing_to_db'));

        }

        /**
         * It sets the author parameter of the attachment query for showing the attachment of the user only
         * @TODO; add functionality to show all image to the administrator
         * @See; https://wordpress.stackexchange.com/questions/1482/restricting-users-to-view-only-media-library-items-they-have-uploaded
         * @param array $query
         * @return array
         */
        public function show_current_user_attachments( $query = array() ) {
            $user_id = get_current_user_id();
            if( !current_user_can('delete_pages') ){
                if( $user_id ) $query['author'] = $user_id;
            }
            return $query;
        }

        /**
         *
         */
        public function add_listing_to_db() {
            // has the listing for been submitted ?
            if ( !empty( $_POST['add_listing_form'] ) ) {
                // add listing form has been submitted
                if (ATBDP()->helper->verify_nonce($this->nonce, $this->nonce_action )) {
                    // we have data and passed the security
                    // we not need to sanitize post vars to be saved to the database,
                    // because wp_insert_post() does this inside that like : $postarr = sanitize_post($postarr, 'db');;
                    $title= !empty($_POST['listing_title']) ? sanitize_text_field($_POST['listing_title']) : '';
                    $content = !empty($_POST['listing_content']) ? wp_kses($_POST['listing_content'], wp_kses_allowed_html('post')) : '';

                    $info= (!empty($_POST['listing'])) ? aazztech_enc_serialize($_POST['listing']) : aazztech_enc_serialize( array() );

                    $excerpt = !empty($_POST['listing']['excerpt']) ? sanitize_textarea_field($_POST['listing']['excerpt']) : '';

                    $args = array(
                        'post_content' => $content,
                        'post_title' => $title,
                        'post_excerpt' => $excerpt,
                        'post_status' => 'publish',
                        'post_type' => ATBDP_POST_TYPE,
                        'tax_input' =>!empty($_POST['tax_input'])? atbdp_sanitize_array( $_POST['tax_input'] ) : array(),
                        'meta_input'=> array('_listing_info'=>$info),

                    );


                    // is it update post ?
                    if (!empty($_POST['listing_id'])){
                        // update the post
                        $args['ID']= absint($_POST['listing_id']); // set the ID of the post to update the post
                        // Check if the current user is the owner of the post
                        $post = get_post($args['ID']);
                        // update the post if the current user own the listing he is trying to edit. or we and give access to the editor or the admin of the post.
                        if (get_current_user_id() == $post->post_author || current_user_can('edit_others_at_biz_dirs')){
                            // Convert taxonomy input to term IDs, to avoid ambiguity.
                            if ( isset( $args['tax_input'] ) ) {
                                foreach ( (array) $args['tax_input'] as $taxonomy => $terms ) {
                                    // Hierarchical taxonomy data is already sent as term IDs, so no conversion is necessary.
                                    if ( is_taxonomy_hierarchical( $taxonomy ) ) {
                                        continue;
                                    }

                                    /*
                                     * Assume that a 'tax_input' string is a comma-separated list of term names.
                                     * Some languages may use a character other than a comma as a delimiter, so we standardize on
                                     * commas before parsing the list.
                                     */
                                    if ( ! is_array( $terms ) ) {
                                        $comma = _x( ',', 'tag delimiter' );
                                        if ( ',' !== $comma ) {
                                            $terms = str_replace( $comma, ',', $terms );
                                        }
                                        $terms = explode( ',', trim( $terms, " \n\t\r\0\x0B," ) );
                                    }

                                    $clean_terms = array();
                                    foreach ( $terms as $term ) {
                                        // Empty terms are invalid input.
                                        if ( empty( $term ) ) {
                                            continue;
                                        }

                                        $_term = get_terms( $taxonomy, array(
                                            'name' => $term,
                                            'fields' => 'ids',
                                            'hide_empty' => false,
                                        ) );

                                        if ( ! empty( $_term ) ) {
                                            $clean_terms[] = intval( $_term[0] );
                                        } else {
                                            // No existing term was found, so pass the string. A new term will be created.
                                            $clean_terms[] = $term;
                                        }
                                    }

                                    $args['tax_input'][ $taxonomy ] = $clean_terms;
                                }
                            }


                            $post_id = wp_update_post($args);
                        }else{
                            // kick the user out because he is trying to modify the listing of other user.
                            wp_redirect($_SERVER['REQUEST_URI'].'?error=true');
                            exit();
                        }


                    }else{
                        // the post is a new post, so insert it as new post.
                        if (current_user_can('publish_at_biz_dirs')){
                            $post_id = wp_insert_post($args);
                        }
                    }

                    if (!empty($post_id)){
                        // post inserted successfully,
                        // Redirect to avoid duplicate form submissions
                        // if monetization on, redirect to checkout page
// vail if monetization is not active.
                       if (get_directorist_option('enable_monetization')){
                           wp_redirect(ATBDP_Permalink::get_checkout_page_link($post_id));
                           exit;
                       }
                        wp_redirect(get_permalink($post_id));
                        exit;
                    }else{
                        wp_redirect(site_url().'?error=true');
                    }

                }
                exit;
            }
        }

        /**
         *It outputs nonce field to any any form
         * @param bool       $referer Optional. Whether to set the referer field for validation. Default true.
         * @param bool       $echo    Optional. Whether to display or return hidden form field. Default true.
         */
        public function show_nonce_field( $referer = true , $echo = true)
        {
            wp_nonce_field($this->nonce_action, $this->nonce, $referer, $echo);

        }


    }


endif;

