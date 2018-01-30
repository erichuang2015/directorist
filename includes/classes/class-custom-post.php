<?php

if ( ! defined('ABSPATH') ) { die( ATBDP_ALERT_MSG ); }

if(!class_exists('ATBDP_Custom_Post')):

    /**
     * Class ATBDP_Custom_Post
     */
    class ATBDP_Custom_Post {
        public function __construct() {
            // Add the listing post type and taxonomies
            add_action( 'init', array( $this, 'register_new_post_types' ) );



            // add new columns for ATBDP_SHORT_CODE_POST_TYPE
            add_filter('manage_'.ATBDP_POST_TYPE.'_posts_columns', array($this, 'add_new_listing_columns'));
            add_action('manage_'.ATBDP_POST_TYPE.'_posts_custom_column', array($this, 'manage_listing_columns'), 10, 2);
            /*make column sortable*/
            add_filter( 'manage_edit-'.ATBDP_POST_TYPE.'_sortable_columns', array($this, 'make_sortable_column'), 10, 1 );



            add_filter( 'enter_title_here', array($this, 'change_title_text') );

        }




        /**
         * This function will register our custom post(s)
         * Initiate registrations of post types and taxonomies.
         *
         */
        public function register_new_post_types() {
            $this->register_post_type();
        }

        /**
         * Register the custom post type.
         *
         * @link http://codex.wordpress.org/Function_Reference/register_post_type
         */
        protected function register_post_type() {
            // Args for ATBDP_POST_TYPE, here any constant may not be available because this function will be called from the
            // register_activation_hook .
            $labels = array(
                'name'                => _x( 'Directory listings', 'Plural Name of Directory listing', 'directorist' ),
                'singular_name'       => _x( 'Directory listing', 'Singular Name of Directory listing', 'directorist' ),
                'menu_name'           => __( 'Directory listings', 'directorist' ),
                'name_admin_bar'      => __( 'Directory listing', 'directorist' ),
                'parent_item_colon'   => __( 'Parent Directory listing:', 'directorist' ),
                'all_items'           => __( 'All listings', 'directorist' ),
                'add_new_item'        => __( 'Add New listing', 'directorist' ),
                'add_new'             => __( 'Add New listing', 'directorist' ),
                'new_item'            => __( 'New listing', 'directorist' ),
                'edit_item'           => __( 'Edit listing', 'directorist' ),
                'update_item'         => __( 'Update listing', 'directorist' ),
                'view_item'           => __( 'View listing', 'directorist' ),
                'search_items'        => __( 'Search listing', 'directorist' ),
                'not_found'           => __( 'No listings found', 'directorist' ),
                'not_found_in_trash'  => __( 'Not listings found in Trash', 'directorist' ),
            );

            $args = array(
                'label'               => __( 'Directory Listing', 'directorist' ),
                'description'         => __( 'Directory listings', 'directorist' ),
                'labels'              => $labels,
                'supports'            => array('title', 'editor'),
                'taxonomies'          => array(),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => current_user_can( 'manage_options' ) ? true : false, // show the menu only to the admin
                'show_in_menu'        => true,
                'menu_position'       => 20,
                'menu_icon'			  => ATBDP_ADMIN_ASSETS.'images/menu_icon.png',
                'show_in_admin_bar'   => true,
                'show_in_nav_menus'   => true,
                'can_export'          => true,
                'has_archive'         => false,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'capability_type'     => ATBDP_POST_TYPE,
                'map_meta_cap'        => true, // set this true, otherwise, even admin will not be able to edit this post. WordPress will map cap from edit_post to edit_at_biz_dir etc,

            );

            // get the rewrite slug from the user settings, if exist use it.
            $slug = get_directorist_option('atbdp_listing_slug', 'at_biz_dir');// default value is the post type at_biz_dir .
            if (!empty($slug)) {
                $args['rewrite'] = array(
                                        'slug'=> $slug,
                                    );
            }



            register_post_type( ATBDP_POST_TYPE, $args );

            // the flush_rewrite_rules() should never be called on every init hook every time a page loads.
            // Rather we should use it only once at the time of the plugin activation.
            //flush_rewrite_rules();
        }


        public function add_new_listing_columns($columns){
            $columns = array();
            $columns['cb']   = '<input type="checkbox" />';
            $columns['title']   = __('Listing Name', ATBDP_TEXTDOMAIN);
            $columns['atbdp_list_2']   = __('Location', ATBDP_TEXTDOMAIN);
            $columns['atbdp_list_3']   = __('Categories', ATBDP_TEXTDOMAIN);
            $columns['atbdp_list_4']   = __('Author', ATBDP_TEXTDOMAIN);
            $columns['date']   = __('Created at', ATBDP_TEXTDOMAIN);
            return $columns;
        }
        public function manage_listing_columns( $column_name, $post_id ) {
            /* global $ATBDP;
            $g_info = get_post_meta( $post_id, 'general' , true); // return serialized and encoded string of array value
            $general_info = (!empty($g_info) ? unserialize( base64_decode( $g_info )) : array());
            $post_link = admin_url( 'post.php?post='.$post_id.'&action=edit'); */
            /*@TODO; Next time we can add image column too. */
            switch($column_name){
                case 'atbdp_list_1':
                    break;
                case 'atbdp_list_2':
                    $terms = wp_get_post_terms( $post_id, ATBDP_LOCATION );
                    if (!empty( $terms ) && is_array( $terms )){
                        foreach ( $terms as $term ){
                            // link the tax term to the search page with custom query string so that plugin can show correct data from database
                            ?>
                            <a href="<?= ATBDP_Permalink::get_location_archive( $term); ?>">
                                <span class="fa <?= get_cat_icon( $term->term_id ); ?>" aria-hidden="true" ></span>
                                <p><?= $term->name; ?></p>
                            </a>
                            <?php
                        }
                    }
                    break;
                case 'atbdp_list_3':
                    $cats = wp_get_post_terms( $post_id, ATBDP_CATEGORY );
                    if (!empty( $cats ) && is_array( $cats )){
                        foreach ( $cats as $c ) {
                    ?>
                    <a href="<?= ATBDP_Permalink::get_category_archive( $c ); ?>">
                        <span class="fa <?= get_cat_icon( $c->term_id ); ?>" aria-hidden="true"></span>
                        <p><?= $c->name; ?></p>
                    </a>
                    <?php
                        }
                    }
                    break;
                case 'atbdp_list_4':
                    the_author_posts_link();
                    break;

                default:
                    break;

            }
        }

        public function make_sortable_column( $columns)
        {
            $columns['atbdp_list_4'] = 'author';
            return $columns;

        }




        /**
         * Change the placeholder of title input box
         * @param string $title Name of the Post Type
         *
         * @return string Returns modified title
         */
        public function change_title_text( $title ){
           global $typenow;
            if ( ATBDP_POST_TYPE == $typenow ) {
                $title = esc_html__('Enter your listing name', ATBDP_TEXTDOMAIN);
            }
            return $title;

        }




    }

endif;