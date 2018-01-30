<?php

if (!class_exists('ATBDP_Enqueuer')):
class ATBDP_Enqueuer {
    /**
     * Whether to enable multiple image upload feature on add listing page
     *
     * Default behavior is to not to show multiple image upload feature
     * evaluated to true.
     *
     * @since 1.2.2
     * @access public
     * @var bool
     */
    var $enable_multiple_image = false;

    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        // best hook to enqueue scripts for front-end is 'template_redirect'
        // 'Professional WordPress Plugin Development' by Brad Williams
        add_action( 'wp_enqueue_scripts', array( $this, 'front_end_enqueue_scripts' ), -10 );

        $this->enable_multiple_image = is_multiple_images_active(); // is the MI Extension is installed???

    }



    public function admin_enqueue_scripts( $page ) {
        global $typenow;

        if ( ATBDP_POST_TYPE == $typenow ) {
            // get the map api from the user settings
            $map_api_key = get_directorist_option('map_api_key'); // eg. zaSyBtTwA-Y_X4OMsIsc9WLs7XEqavZ3ocQLQ

            //Register all styles for the admin pages
            /*Public Common Asset: */

            wp_register_style( 'atbdp-font-awesome', ATBDP_PUBLIC_ASSETS . 'css/font-awesome.min.css', false, ATBDP_VERSION);
            wp_register_style( 'sweetalertcss', ATBDP_PUBLIC_ASSETS.'css/sweetalert.min.css', false, ATBDP_VERSION );
            wp_register_style( 'select2style', ATBDP_PUBLIC_ASSETS.'css/select2.min.css', false, ATBDP_VERSION );
            wp_register_style( 'atbdp-bootstrap-style', ATBDP_PUBLIC_ASSETS . 'css/bootstrap.css', false, ATBDP_VERSION);
            wp_register_style( 'atbdp-admin', ATBDP_ADMIN_ASSETS . 'css/style.css', array('atbdp-bootstrap-style', 'atbdp-font-awesome', 'select2style'), ATBDP_VERSION);

            // Scripts.... Right now we do not need bootstrap js in the admin.
            /*if ('yes' !== get_directorist_option('fix_js_conflict')) {
                wp_register_script('atbdp-bootstrap-script', ATBDP_PUBLIC_ASSETS . 'js/bootstrap.min.js', array('jquery'), ATBDP_VERSION, true);
            }*/


            wp_register_script( 'sweetalert', ATBDP_PUBLIC_ASSETS . 'js/sweetalert.min.js', array( 'jquery' ), ATBDP_VERSION, true );
            /*Public Common Asset: */
            wp_register_script( 'select2script', ATBDP_PUBLIC_ASSETS . 'js/select2.min.js', array( 'jquery' ), ATBDP_VERSION, true );


            //Google map needs to be enqueued from google server with a valid API key. So, it is not possible to store map js file locally as this file will be unique for all users based on their MAP API key.

            wp_register_script( 'atbdp-google-map-admin', '//maps.googleapis.com/maps/api/js?key='.$map_api_key.'&libraries=places', false, ATBDP_VERSION, true );
            // make a list of dependency of admin script and then add to this list some other scripts using different conditions to handle different situation.
            $admin_scripts_dependency = array(
                'jquery',
                'atbdp-google-map-admin',
                'wp-color-picker',
                'sweetalert',
                'select2script',
            );
            // We do not need bootstrap js in the admin page
            /*
             if ('yes' !== get_directorist_option('fix_js_conflict')) {
                array_push($admin_scripts_dependency, 'atbdp-bootstrap-script');
            }
            */


            wp_register_script( 'atbdp-admin-script', ATBDP_ADMIN_ASSETS . 'js/main.js', $admin_scripts_dependency , ATBDP_VERSION, true );


            // Styles
            //@todo; later minify the bootstrap
            /*@todo; we can also load scripts and style using very strict checking like loading based on post.php or edit.php etc. For example, sweetalert should be included in the post.php file where the user will add/edit listing */

            /* enqueue all styles*/
            wp_enqueue_style('atbdp-bootstrap-style');
            wp_enqueue_style('atbdp-font-awesome');
            wp_enqueue_style('sweetalertcss');
            wp_enqueue_style('select2style');
            /* WP COLOR PICKER */
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_style('atbdp-admin');


            /* Enqueue all scripts */
            //wp_enqueue_script('atbdp-bootstrap'); // maybe we do not need the bootstrap js in the admin panel at the moment.
            wp_enqueue_script('sweetalert');
            wp_enqueue_script('atbdp-google-map-admin');
            wp_enqueue_script('select2script');
            wp_enqueue_script('atbdp-admin-script');




            // Internationalization text for javascript file especially add-listing.js
            $i18n_text = array(
                'confirmation_text' => __('Are you sure', ATBDP_TEXTDOMAIN),
                'ask_conf_sl_lnk_del_txt' => __('Do you really want to remove this Social Link!', ATBDP_TEXTDOMAIN),
                'confirm_delete' => __('Yes, Delete it!', ATBDP_TEXTDOMAIN),
                'deleted' => __('Deleted!', ATBDP_TEXTDOMAIN),
                'icon_choose_text' => __('Select an icon', ATBDP_TEXTDOMAIN),
                'upload_image' => __('Select or Upload Listing Image', ATBDP_TEXTDOMAIN),
                'choose_image' => __('Use this Image', ATBDP_TEXTDOMAIN),
            );
            // is MI extension enabled and active?
            $active_mi_extension = ($this->enable_multiple_image) ? get_directorist_option('enable_multiple_image', 'no'): 'no'; // yes or no

            $data = array(
                'nonce'             => wp_create_nonce('atbdp_nonce_action_js'),
                'ajaxurl'           => admin_url('admin-ajax.php'),
                'nonceName'         => 'atbdp_nonce_js',
                'AdminAssetPath'    => ATBDP_ADMIN_ASSETS,
                'i18n_text'         => $i18n_text,
                'active_mi_ext'     => $active_mi_extension, // yes or no
            );
            wp_localize_script( 'atbdp-admin-script', 'atbdp_admin_data', $data );
            wp_enqueue_media();

        }

    }

    /**
     * It loads all scripts for front end if the current post type is our custom post type
     * @param bool $force [optional] whether to load the style in the front end forcibly(even if the post type is not our custom post). It is needed for enqueueing file from a inside the short code call
     */
    public function front_end_enqueue_scripts($force=false) {
        global $typenow, $post;
        // get the map api key from the user settings
        $map_api_key = get_directorist_option('map_api_key', null); // eg. zaSyBtTwA-Y_X4OMsIsc9WLs7XEqavZ3ocQLQ
        // Registration of all styles and js for the front end should be done here
        // but the inclusion should be limited to the scope of the page user viewing.
        // This way,  we can just enqueue any styles and scripts in side the shortcode or any other functions.

        //@Todo; make unminified css minified then enqueue them.
        wp_register_style( 'atbdp-bootstrap-style', ATBDP_PUBLIC_ASSETS . 'css/bootstrap.css', false, ATBDP_VERSION);
        wp_register_style( 'atbdp-font-awesome', ATBDP_PUBLIC_ASSETS . 'css/font-awesome.min.css', false, ATBDP_VERSION);
        wp_register_style( 'sweetalertcss', ATBDP_PUBLIC_ASSETS.'css/sweetalert.min.css', false, ATBDP_VERSION );
        wp_register_style( 'select2style', ATBDP_PUBLIC_ASSETS.'css/select2.min.css', false, ATBDP_VERSION );
        wp_register_style( 'atbdp-style', ATBDP_PUBLIC_ASSETS . 'css/style.css', array( 'atbdp-font-awesome', 'atbdp-bootstrap-style'), ATBDP_VERSION);



        // Scripts
        if ( 'yes' !== get_directorist_option( 'fix_js_conflict' )) {
            wp_register_script('atbdp-bootstrap-script', ATBDP_PUBLIC_ASSETS . 'js/bootstrap.min.js', array('jquery'), ATBDP_VERSION, true);
        }

        wp_register_script( 'atbdp-rating', ATBDP_PUBLIC_ASSETS . 'js/jquery.barrating.min.js', array( 'jquery' ), ATBDP_VERSION, true );
        wp_register_script( 'atbdp-uikit', ATBDP_PUBLIC_ASSETS . 'js/uikit.min.js', array( 'jquery' ), ATBDP_VERSION, true );

        wp_register_script( 'atbdp-uikit-grid', ATBDP_PUBLIC_ASSETS . 'js/grid.min.js', array( 'jquery', 'atbdp-uikit' ), ATBDP_VERSION, true );

        wp_register_script( 'sweetalert', ATBDP_PUBLIC_ASSETS . 'js/sweetalert.min.js', array( 'jquery' ), ATBDP_VERSION, true );




        wp_register_script( 'atbdp-google-map-front', '//maps.googleapis.com/maps/api/js?key='.$map_api_key.'&libraries=places', false, ATBDP_VERSION, true );

        wp_register_script( 'atbdp-all-listing', ATBDP_PUBLIC_ASSETS . 'js/all-listing.js', array(
            'jquery',
            'atbdp-google-map-front',
        ), ATBDP_VERSION, true );

        wp_register_script( 'atbdp-user-dashboard', ATBDP_PUBLIC_ASSETS . 'js/user-dashboard.js', array( 'jquery' ), ATBDP_VERSION, true );


        wp_register_script( 'select2script', ATBDP_PUBLIC_ASSETS . 'js/select2.min.js', array( 'jquery' ), ATBDP_VERSION, true );



        // we need select2 js on taxonomy edit screen to let the use to select the fonts-awesome icons ans search the icons easily
        // @TODO; make the styles and the scripts specific to the scripts where they are used specifically. For example. load select2js scripts and styles in

        wp_enqueue_style('select2style');
        wp_enqueue_script('select2script');

        /* Enqueue all styles*/
        //wp_enqueue_style('atbdp-bootstrap-style');
        wp_enqueue_style('atbdp-font-awesome');
        wp_enqueue_style('atbdp-style');

        /* Enqueue all scripts */
        wp_enqueue_script('atbdp-bootstrap-script');
        wp_enqueue_script('atbdp-rating');
        wp_enqueue_script('atbdp-google-map-front');
        wp_enqueue_script('atbdp-uikit');
        wp_enqueue_script('atbdp-uikit-grid');

        $data = array(
            'nonce'       => wp_create_nonce('atbdp_nonce'),
            'PublicAssetPath'  => ATBDP_PUBLIC_ASSETS,
        );
        wp_enqueue_style('wp-color-picker');
        wp_localize_script( 'atbdp-all-listing', 'atbdp_data', $data );


        // enqueue the style and the scripts on the page when the post type is our registered post type.
        if ( (is_object($post) && ATBDP_POST_TYPE == $post->post_type) || $force) {
            $this->common_scripts_styles();
            wp_enqueue_style('sweetalertcss' );
            wp_enqueue_script('sweetalert' );

            wp_enqueue_script( 'atbdp-public-script', ATBDP_PUBLIC_ASSETS . 'js/main.js', array(
                'jquery',
                'atbdp-google-map-front',
            ), ATBDP_VERSION, true );


            wp_enqueue_style('wp-color-picker');

            $data = array(
                'nonce'       => wp_create_nonce('atbdp_nonce_action_js'),
                'ajaxurl'       => admin_url('admin-ajax.php'),
                'nonceName'       => 'atbdp_nonce_js',
                'AdminAssetPath'  => ATBDP_PUBLIC_ASSETS,
            );
            wp_localize_script( 'atbdp-public-script', 'atbdp_public_data', $data );
            wp_enqueue_media();
        }
    }


    public function common_scripts_styles()
    {




    }

    public function add_listing_scripts_styles()
    {
        $this->common_scripts_styles();
        wp_register_style( 'sweetalertcss', ATBDP_ADMIN_ASSETS.'css/sweetalert.min.css', false, ATBDP_VERSION );
        wp_register_script( 'sweetalert', ATBDP_ADMIN_ASSETS . 'js/sweetalert.min.js', array( 'jquery' ), ATBDP_VERSION, true );
        wp_enqueue_style('sweetalertcss');
        wp_enqueue_script('atbdp-bootstrap-script');

        wp_register_script('atbdp_add_listing_js', ATBDP_PUBLIC_ASSETS . 'js/add-listing.js', array(
            'jquery',
            'atbdp-rating',
            'atbdp-google-map-front',
            'sweetalert',
            'jquery-ui-sortable',
            'select2script'
        ), ATBDP_VERSION, true );
        wp_enqueue_script('atbdp_add_listing_js');

        // Internationalization text for javascript file especially add-listing.js
        $i18n_text = array(
            'confirmation_text' => __('Are you sure', ATBDP_TEXTDOMAIN),
            'ask_conf_sl_lnk_del_txt' => __('Do you really want to remove this Social Link!', ATBDP_TEXTDOMAIN),
            'confirm_delete' => __('Yes, Delete it!', ATBDP_TEXTDOMAIN),
            'deleted' => __('Deleted!', ATBDP_TEXTDOMAIN),
            'location_selection' => __('Select a location', ATBDP_TEXTDOMAIN),
            'category_selection' => __('Select a category', ATBDP_TEXTDOMAIN),
            'tag_selection' => __('Select or insert new tags separated by a comma, or space', ATBDP_TEXTDOMAIN),
            'upload_image' => __('Select or Upload Listing Image', ATBDP_TEXTDOMAIN),
            'choose_image' => __('Use this Image', ATBDP_TEXTDOMAIN),
        );

        // is MI extension enabled and active?
        $active_mi_extension = ($this->enable_multiple_image) ? get_directorist_option('enable_multiple_image', 'no'): 'no'; // yes or no
        $data = array(
            'nonce'       => wp_create_nonce('atbdp_nonce_action_js'),
            'ajaxurl'       => admin_url('admin-ajax.php'),
            'nonceName'       => 'atbdp_nonce_js',
            'PublicAssetPath'  => ATBDP_PUBLIC_ASSETS,
            'i18n_text'        => $i18n_text,
            'active_mi_ext'        => $active_mi_extension, // yes or no

        );
        wp_localize_script( 'atbdp_add_listing_js', 'atbdp_add_listing', $data );

        wp_enqueue_media();

    }

    public function user_dashboard_scripts_styles()
    {
        /* Enqueue all styles*/
        wp_enqueue_style('atbdp-bootstrap-style');
        wp_enqueue_style('atbdp-stars');
        wp_enqueue_style('atbdp-font-awesome');
        wp_enqueue_style('atbdp-style');
        wp_enqueue_style('atbdp-responsive');

        /* Enqueue all scripts */
        wp_enqueue_script('atbdp-bootstrap-script');
        wp_enqueue_script('atbdp-rating');
//        wp_enqueue_script('atbdp-google-map-front');
        wp_enqueue_script('atbdp-user-dashboard');


        $data = array(
            'nonce'       => wp_create_nonce('atbdp_nonce'),
            'PublicAssetPath'  => ATBDP_PUBLIC_ASSETS,
        );
        wp_enqueue_style('wp-color-picker');
        wp_localize_script( 'atbdp-user-dashboard', 'atbdp_data', $data );

    }

    public function search_listing_scripts_styles(){
        wp_enqueue_script( 'atbdp_search_listing', ATBDP_PUBLIC_ASSETS . 'js/search-listing.js', array(
            'jquery',
            'select2script',
        ), ATBDP_VERSION, true );

        /*Internationalization*/
        $data = array(
            'i18n_text'        => array(
                'location_selection' => __('Select a location', ATBDP_TEXTDOMAIN),
                'category_selection' => __('Select a category', ATBDP_TEXTDOMAIN),
            )
        );
        wp_localize_script( 'atbdp_search_listing', 'atbdp_search_listing', $data );
    }
}



endif;