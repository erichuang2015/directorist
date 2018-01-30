<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( '-1' );
}

/**
 * It creates roles for ATBDP and assign capability to those roles.
 * @since 3.0.0
 *
 * Class ATBDP_Roles
 */
class ATBDP_Roles {


    public function __construct()
    {
        // Add custom ATBDP_Roles & Capabilities once only
        //var_dump(get_option( 'atbdp_roles_mapped' ));
        if( ! get_option( 'atbdp_roles_mapped' ) ) {
            $this->add_caps();
            // Insert atbdp_roles_mapped option to the db to prevent mapping meta cap
            add_option( 'atbdp_roles_mapped', true );
        }

    }

    /**
     * It gets the WP Roles object.
     * 
     * @since 3.0.0
     * @access public
     * @return WP_Roles  It returns an object of WP_Roles Class.
     */
    public function getWpRoles() {
        global $wp_roles;

        if ( !empty($wp_roles) && is_object($wp_roles) ) {
            return $wp_roles;
        } else {
            if ( ! isset( $wp_roles ) ) {
                $wp_roles = new WP_Roles();
            }
        }

        return $wp_roles;
    }

    /**
     * Add new capabilities.
     *
     * @since    3.0.0
     * @access   public
     */
    public function add_caps() {

        $wp_roles = $this->getWpRoles();

        if( is_object( $wp_roles ) ) {
            // Add all the core caps to the administrator so that he can do anything with our custom post types
            $custom_posts_caps = $this->get_core_caps(); // get caps array for our custom post(s)
            // Iterate over the array of post types and caps array and assign the cap to the administrator role.
            foreach( $custom_posts_caps as $single_post_caps ) {
                foreach( $single_post_caps as $cap ) {
                    $wp_roles->add_cap( 'administrator', $cap );
                }
            }

            /*lets add another capability to the admin to check him if he has cap to edit our settings, Though we can use default manage_options caps. However, if a shop manager has manage_options cap, we do not want to let him access to our plugin admin panel, we just want the admin to access the plugin's settings.*/
            $wp_roles->add_cap( 'administrator', 'manage_atbdp_options' );

            // Add the "editor" capabilities
            $wp_roles->add_cap( 'editor', 'edit_at_biz_dirs' );
            $wp_roles->add_cap( 'editor', 'edit_others_at_biz_dirs' );
            $wp_roles->add_cap( 'editor', 'publish_at_biz_dirs' );
            $wp_roles->add_cap( 'editor', 'read_private_at_biz_dirs' );
            $wp_roles->add_cap( 'editor', 'delete_at_biz_dirs' );
            $wp_roles->add_cap( 'editor', 'delete_private_at_biz_dirs' );
            $wp_roles->add_cap( 'editor', 'delete_published_at_biz_dirs' );
            $wp_roles->add_cap( 'editor', 'delete_others_at_biz_dirs' );
            $wp_roles->add_cap( 'editor', 'edit_private_at_biz_dirs' );
            $wp_roles->add_cap( 'editor', 'edit_published_at_biz_dirs' );

            // Add the "author" capabilities
            $wp_roles->add_cap( 'author', 'edit_at_biz_dirs' );
            $wp_roles->add_cap( 'author', 'publish_at_biz_dirs' );
            $wp_roles->add_cap( 'author', 'delete_at_biz_dirs' );
            $wp_roles->add_cap( 'author', 'delete_published_at_biz_dirs' );
            $wp_roles->add_cap( 'author', 'edit_published_at_biz_dirs' );

            // Add the "contributor" capabilities
            $wp_roles->add_cap( 'contributor', 'edit_at_biz_dirs' );
            $wp_roles->add_cap( 'contributor', 'publish_at_biz_dirs' );
            $wp_roles->add_cap( 'contributor', 'delete_at_biz_dirs' );
            $wp_roles->add_cap( 'contributor', 'delete_published_at_biz_dirs' );
            $wp_roles->add_cap( 'contributor', 'edit_published_at_biz_dirs' );

            // Add the "subscriber" capabilities
            $wp_roles->add_cap( 'subscriber', 'edit_at_biz_dirs' );
            $wp_roles->add_cap( 'subscriber', 'publish_at_biz_dirs' );
            $wp_roles->add_cap( 'subscriber', 'delete_at_biz_dirs' );
            $wp_roles->add_cap( 'subscriber', 'delete_published_at_biz_dirs' );
            $wp_roles->add_cap( 'subscriber', 'edit_published_at_biz_dirs' );

        }
    }

    /**
     * Gets the core post type capabilities.
     *
     * @since    3.0.0
     * @access   public
     *
     * @return   array    $capabilities    Core post type capabilities.
     */
    public function get_core_caps() {

        $caps = array();

        $custom_posts = array( 'at_biz_dir' ); // we can add more custom post type here as we will work on the plugin eg. payment.

        foreach( $custom_posts as $cp ) {

            $caps[ $cp ] = array(

                "edit_{$cp}",
                "read_{$cp}",
                "delete_{$cp}",
                "edit_{$cp}s",
                "edit_others_{$cp}s",
                "publish_{$cp}s",
                "read_private_{$cp}s",
                "delete_{$cp}s",
                "delete_private_{$cp}s",
                "delete_published_{$cp}s",
                "delete_others_{$cp}s",
                "edit_private_{$cp}s",
                "edit_published_{$cp}s",

            );
        }

        return $caps;

    }

    /**
     * Filter a user's capabilities depending on specific context and/or privilege.
     *
     * @since    3.0.0
     * @access   public
     *
     * @param    array     $caps       Returns the user's actual capabilities.
     * @param    string    $cap        Capability name.
     * @param    int       $user_id    The user ID.
     * @param    array     $args       Adds the context to the cap. Typically the object ID.
     * @return   array                 Actual capabilities for meta capability.
     */
    public function meta_caps( $caps, $cap, $user_id, $args ) {
        // If editing, deleting, or reading a listing, get the post and post type object.
        if( 'edit_at_biz_dir' == $cap || 'delete_at_biz_dir' == $cap || 'read_at_biz_dir' == $cap ) {
            $post = get_post( $args[0] );
            $post_type = get_post_type_object( $post->post_type );
            // Set an empty array for the caps.
            $caps = array();
        }

        // If editing a listing, assign the required capability.
        if( 'edit_at_biz_dir' == $cap ) {
            if( $user_id == $post->post_author )
                $caps[] = $post_type->cap->edit_at_biz_dirs;
            else
                $caps[] = $post_type->cap->edit_others_at_biz_dirs;
        }

        // If deleting a listing, assign the required capability.
        else if( 'delete_at_biz_dir' == $cap ) {
            if( $user_id == $post->post_author )
                $caps[] = $post_type->cap->delete_at_biz_dirs;
            else
                $caps[] = $post_type->cap->delete_others_at_biz_dirs;
        }

        // If reading a private listing, assign the required capability.
        else if( 'read_at_biz_dir' == $cap ) {
            if( 'private' != $post->post_status )
                $caps[] = 'read';
            elseif ( $user_id == $post->post_author )
                $caps[] = 'read';
            else
                $caps[] = $post_type->cap->read_private_at_biz_dirs;
        }

        // Return the capabilities required by the user.
        return $caps;

    }

    /**
     * Remove core post type capabilities (called on uninstall).
     *
     * @since    3.0.0
     * @access   public
     */
    public function remove_caps() {

        global $wp_roles;

        if( class_exists( 'WP_Roles' ) ) {
            if( ! isset( $wp_roles ) ) {
                $wp_roles = new WP_Roles();
            }
        }

        if( is_object( $wp_roles ) ) {

            // Remove the "administrator" Capabilities
            $capabilities = $this->get_core_caps();

            foreach( $capabilities as $cap_group ) {
                foreach( $cap_group as $cap ) {
                    $wp_roles->remove_cap( 'administrator', $cap );
                }
            }


            $wp_roles->remove_cap( 'administrator', 'manage_acadp_options' );

            // Remove the "editor" capabilities
            $wp_roles->remove_cap( 'editor', 'edit_at_biz_dirs' );
            $wp_roles->remove_cap( 'editor', 'edit_others_at_biz_dirs' );
            $wp_roles->remove_cap( 'editor', 'publish_at_biz_dirs' );
            $wp_roles->remove_cap( 'editor', 'read_private_at_biz_dirs' );
            $wp_roles->remove_cap( 'editor', 'delete_at_biz_dirs' );
            $wp_roles->remove_cap( 'editor', 'delete_private_at_biz_dirs' );
            $wp_roles->remove_cap( 'editor', 'delete_published_at_biz_dirs' );
            $wp_roles->remove_cap( 'editor', 'delete_others_at_biz_dirs' );
            $wp_roles->remove_cap( 'editor', 'edit_private_at_biz_dirs' );
            $wp_roles->remove_cap( 'editor', 'edit_published_at_biz_dirs' );

            // Remove the "author" capabilities
            $wp_roles->remove_cap( 'author', 'edit_at_biz_dirs' );
            $wp_roles->remove_cap( 'author', 'publish_at_biz_dirs' );
            $wp_roles->remove_cap( 'author', 'delete_at_biz_dirs' );
            $wp_roles->remove_cap( 'author', 'delete_published_at_biz_dirs' );
            $wp_roles->remove_cap( 'author', 'edit_published_at_biz_dirs' );

            // Remove the "contributor" capabilities
            $wp_roles->remove_cap( 'contributor', 'edit_at_biz_dirs' );
            $wp_roles->remove_cap( 'contributor', 'publish_at_biz_dirs' );
            $wp_roles->remove_cap( 'contributor', 'delete_at_biz_dirs' );
            $wp_roles->remove_cap( 'contributor', 'delete_published_at_biz_dirs' );
            $wp_roles->remove_cap( 'contributor', 'edit_published_at_biz_dirs' );
            //@todo; check if it is better to add -upload_media- cap to contributor and subscriber here so that they can upload listing image using WordPress default featured image uploader.
            // Remove the "subscriber" capabilities
            $wp_roles->remove_cap( 'subscriber', 'edit_at_biz_dirs' );
            $wp_roles->remove_cap( 'subscriber', 'publish_at_biz_dirs' );
            $wp_roles->remove_cap( 'subscriber', 'delete_at_biz_dirs' );
            $wp_roles->remove_cap( 'subscriber', 'delete_published_at_biz_dirs' );
            $wp_roles->remove_cap( 'subscriber', 'edit_published_at_biz_dirs' );

        }
    }
}
