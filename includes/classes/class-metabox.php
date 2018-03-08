<?php

if ( !class_exists('ATBDP_Metabox') ):
class ATBDP_Metabox {


    /**
     * Add meta boxes for ATBDP_POST_TYPE and ATBDP_SHORT_CODE_POST_TYPE
     * and Save the meta data
     */
    public function __construct() {
        if ( is_admin() ) {
            add_action('add_meta_boxes_'.ATBDP_POST_TYPE,	array($this, 'listing_info_meta'));

            // edit_post hooks is better than save_post hook for nice checkbox
            // http://wordpress.stackexchange.com/questions/228322/how-to-set-default-value-for-checkbox-in-wordpress


            add_action( 'edit_post', array($this, 'save_post_meta'), 10, 2);


        }

     }

    /**
     * Render Metaboxes for ATBDP_POST_TYPE
     * @param Object $post Current Post Object being displayed
     */
    public function listing_info_meta( $post ) {
        add_meta_box( '_listing_info',
        __( 'Listing Information', ATBDP_TEXTDOMAIN ),
        array($this, 'listing_info'),
        ATBDP_POST_TYPE,
        'normal' );

        add_meta_box( '_listing_gallery',
        __( 'Upload Image for the listing', ATBDP_TEXTDOMAIN ),
        array($this, 'listing_gallery'),
        ATBDP_POST_TYPE,
        'normal' );


    }


    /**
     * @param $post
     */
    public function listing_info( $post ) {
        $lf= get_post_meta($post->ID, '_listing_info', true);
        $listing_info= (!empty($lf))? aazztech_enc_unserialize($lf) : array();
        wp_nonce_field( 'listing_info_action', 'listing_info_nonce' );
        ATBDP()->load_template('add-listing', compact('listing_info') );
    }

    public function listing_gallery( $post )
    {
        $lf= get_post_meta($post->ID, '_listing_info', true);
        $listing_info= (!empty($lf)) ? aazztech_enc_unserialize($lf) : array();
        $attachment_ids= (!empty($listing_info['attachment_id'])) ? $listing_info['attachment_id'] : array();
        ATBDP()->load_template('media-upload', compact('attachment_ids') );
    }


/*
|
|   S
-------
|
|   A
----------
|
|   V
-------------
|
|   I
----------------
|
|   N
-------------------
|
|   G
----------------------*/

    /**
     * Save Meta Data of ATBDP_POST_TYPE
     * @param int       $post_id    Post ID of the current post being saved
     * @param object    $post       Current post object being saved
     */
    public function save_post_meta( $post_id, $post ) {
        if ( ! $this->passSecurity($post_id, $post) )  return;
        $listing_info = (!empty($_POST['listing'])) ? aazztech_enc_serialize($_POST['listing']) : aazztech_enc_serialize(array());
        // save the meta data to the database
        update_post_meta( $post_id, '_listing_info', $listing_info );
    }


    /**
     * Check if the the nonce, revision, auto_save are valid/true and the post type is ATBDP_POST_TYPE
     * @param int       $post_id    Post ID of the current post being saved
     * @param object    $post       Current post object being saved
     *
     * @return bool            Return true if all the above check passes the test.
     */
    private function passSecurity( $post_id, $post ) {
        if ( ATBDP_POST_TYPE == $post->post_type) {
        $is_autosave = wp_is_post_autosave($post_id);
        $is_revision = wp_is_post_revision($post_id);
        $nonce = !empty($_POST['listing_info_nonce']) ? $_POST['listing_info_nonce'] : '';
        $is_valid_nonce = wp_verify_nonce($nonce, 'listing_info_action');

        if ( $is_autosave || $is_revision || !$is_valid_nonce || ( !current_user_can( 'edit_'.ATBDP_POST_TYPE, $post_id )) ) return false;
        return true;
        }
        return false;
    }


    /**
     * It fetches all the information of the listing
     * @param int $id The id of the post whose meta we want to collect
     * @return array It returns the listing information of the given listing/post id.
     */
    public function get_listing_info($id)
    {
        $lf= get_post_meta($id, '_listing_info', true);
        return (!empty($lf)) ? aazztech_enc_unserialize($lf) : array();

    }


}

endif;