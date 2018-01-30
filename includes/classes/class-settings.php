<?php

if ( !class_exists('ATBDP_Settings' ) ):
    class ATBDP_Settings {

        private $settings_api;

        function __construct(ATBDP_Settings_API $setting_api) {
            $this->settings_api = $setting_api;

            add_action( 'admin_init', array($this, 'admin_init') );
            add_action( 'admin_menu', array($this, 'admin_menu') );
        }

        function admin_init() {

            //set the settings
            $this->settings_api->set_sections( $this->get_settings_sections() );
            $this->settings_api->set_fields( $this->get_settings_fields() );
            //initialize settings
            $this->settings_api->admin_init();
        }

        function admin_menu() {
            add_submenu_page('edit.php?post_type=at_biz_dir', __('Directory Settings', ATBDP_TEXTDOMAIN), __('Directory Settings', ATBDP_TEXTDOMAIN), 'manage_options', 'aazztech-business-directory', array($this, 'plugin_page'));
        }

        function get_settings_sections() {
            /**
             * This action lets user add new settings to the plugin settings menu
             *
             */
            return apply_filters('atbdp_settings_sections', array(
                array(
                    'id'    => 'atbdp_general',
                    'title' => __( 'General Settings', ATBDP_TEXTDOMAIN )
                )
            ));
        }

        /**
         * Returns all the settings fields
         *
         * @return array settings fields
         */
        function get_settings_fields() {

            $settings_fields = array(
                'atbdp_general' => array(
                    array(
                        'name'              => 'map_api_key',
                        'label'             => __( 'Google Map API key', ATBDP_TEXTDOMAIN ),
                        'desc'              => __( 'You need to enter your google map api key in order to display google map. You can find your map api key and detailed information <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank"> <strong style="color: red;">here</strong> </a>. or you can search in google', ATBDP_TEXTDOMAIN ),
                        'placeholder'       => __( 'Enter your Google Map API key', ATBDP_TEXTDOMAIN ),
                        'type'              => 'text',
                        'default'           => '',
                        'sanitize_callback' => 'sanitize_text_field'
                    ),

                    array(
                        'name'              => 'search_title',
                        'label'             => __( 'Search Bar Title', ATBDP_TEXTDOMAIN ),
                        'desc'              => __( 'Enter the title for search bar on Home Page. Eg. Find the Best Places to Be', ATBDP_TEXTDOMAIN ),
                        'placeholder'       => __( 'Search Bar Title', ATBDP_TEXTDOMAIN ),
                        'type'              => 'text',
                        'default'           => __('Find the Best Places to Be', ATBDP_TEXTDOMAIN),
                        'sanitize_callback' => 'sanitize_text_field'
                    ),

                    array(
                        'name'              => 'search_subtitle',
                        'label'             => __( 'Search Bar Sub-title', ATBDP_TEXTDOMAIN ),
                        'desc'              => __( 'Enter the sub-title for search bar on Home Page. Eg. All the top locations – from restaurants and clubs, to cinemas, galleries, and more..', ATBDP_TEXTDOMAIN ),
                        'placeholder'       => __( 'Search Bar Sub-title', ATBDP_TEXTDOMAIN ),
                        'type'              => 'text',
                        'default'           => __('All the top locations – from restaurants and clubs, to cinemas, galleries, and more..', ATBDP_TEXTDOMAIN),
                        'sanitize_callback' => 'sanitize_text_field'
                    ),

                    array(
                        'name'              => 'search_placeholder',
                        'label'             => __( 'Search Bar Placeholder', ATBDP_TEXTDOMAIN ),
                        'desc'              => __( 'Enter the placeholder text for the search field. Eg. What are you looking for?', ATBDP_TEXTDOMAIN ),
                        'placeholder'       => __( 'Search field placeholder', ATBDP_TEXTDOMAIN ),
                        'type'              => 'text',
                        'default'           => __( 'What are you looking for?', ATBDP_TEXTDOMAIN ),
                        'sanitize_callback' => 'sanitize_text_field'
                    ),

                    array(
                        'name'              => 'all_listing_title',
                        'label'             => __( 'Title for All listing page', ATBDP_TEXTDOMAIN ),
                        'desc'              => __( 'Enter a title for the page where all listings will be shown using the shortcode [all_listing] .', ATBDP_TEXTDOMAIN ),
                        'placeholder'       => __( 'Eg. All Items', ATBDP_TEXTDOMAIN ),
                        'type'              => 'text',
                        'default'           => __( 'All Items', ATBDP_TEXTDOMAIN ),
                        'sanitize_callback' => 'sanitize_text_field'
                    ),

                    array(
                        'name'  => 'show_popular_category',
                        'label' => __( 'Show popular category on the search page', ATBDP_TEXTDOMAIN ),
                        'desc'  => __( 'You can show popular category on search page or you can hide it here.', ATBDP_TEXTDOMAIN ),
                        'type'    => 'radio',
                        'options' => array(
                            'yes' => 'Yes',
                            'no'  => 'No'
                        ),
                        'default' => 'yes',
                    ),

                    array(
                        'name'              => 'popular_cat_title',
                        'label'             => __( 'Popular Category Title', ATBDP_TEXTDOMAIN ),
                        'desc'              => __( 'Enter the title for popular category on listing search page eg. Browse by popular categories', ATBDP_TEXTDOMAIN ),
                        'placeholder'       => __( 'Eg. Browse by popular categories', ATBDP_TEXTDOMAIN ),
                        'type'              => 'text',
                        'default'           => __( 'Browse by popular categories', ATBDP_TEXTDOMAIN ),
                        'sanitize_callback' => 'sanitize_text_field'
                    ),


                    array(
                        'name'              => 'popular_cat_num',
                        'label'             => __( 'Number of Popular Category', ATBDP_TEXTDOMAIN ),
                        'desc'              => __( 'Enter how many popular categories you would like to show on your listing search page. Eg. 10. Default is 10', ATBDP_TEXTDOMAIN ),
                        'placeholder'       => __( 'eg. 10', ATBDP_TEXTDOMAIN ),
                        'min'               => 1,
                        'max'               => 100,
                        'step'              => '1',
                        'type'              => 'number',
                        'default'           => 10,
                        'sanitize_callback' => 'floatval'
                    ),



                    array(
                        'name'  => 'enable_pop_listing',
                        'label' => __( 'Enable popular listings on single page', ATBDP_TEXTDOMAIN ),
                        'desc'  => __( 'Choose whether you want to display popular listings on listing details page or not. Default is Yes.', ATBDP_TEXTDOMAIN ),
                        'type'    => 'radio',
                        'options' => array(
                            'yes' => __( 'Yes, Show popular listings', ATBDP_TEXTDOMAIN ),
                            'no'  => __( 'No, Do not show popular listings', ATBDP_TEXTDOMAIN ),
                        ),
                        'default' => 'yes',
                    ),


                    array(
                        'name'              => 'pop_listing_num',
                        'label'             => __( 'Number of Popular Listings', ATBDP_TEXTDOMAIN ),
                        'desc'              => __( 'Enter how many popular listings you would like to show on your website. Eg. 5. Default is 5.', ATBDP_TEXTDOMAIN ),
                        'placeholder'       => __( '5', ATBDP_TEXTDOMAIN ),
                        'min'               => 1,
                        'max'               => 5,
                        'step'              => '1',
                        'type'              => 'number',
                        'default'           => 5,
                        'sanitize_callback' => 'floatval'
                    ),

                    array(
                        'name'  => 'enable_rel_listing',
                        'label' => __( 'Enable related listings', ATBDP_TEXTDOMAIN ),
                        'desc'  => __( 'Choose whether you want to display related listings on listing details page or not. Default is Yes.', ATBDP_TEXTDOMAIN ),
                        'type'    => 'radio',
                        'options' => array(
                            'yes' => __('Yes, Show related listings', ATBDP_TEXTDOMAIN),
                            'no'  => __('No, Do not show related listings', ATBDP_TEXTDOMAIN),
                        ),
                        'default' => 'yes',
                    ),


                    array(
                        'name'              => 'rel_listing_num',
                        'label'             => __( 'Number of Related Listings', ATBDP_TEXTDOMAIN ),
                        'desc'              => __( 'Enter how many related listings you would like to show on your website. Eg. 2. Default is 2.', ATBDP_TEXTDOMAIN ),
                        'placeholder'       => __( '2', ATBDP_TEXTDOMAIN ),
                        'min'               => 1,
                        'max'               => 5,
                        'step'              => '1',
                        'type'              => 'number',
                        'default'           => 2,
                        'sanitize_callback' => 'floatval'
                    ),

                    array(
                        'name'  => 'enable_review',
                        'label' => __( 'Enable Reviews', ATBDP_TEXTDOMAIN ),
                        'desc'  => __( 'Choose whether you want to display reviews on listing details page or not. Default is Yes.', ATBDP_TEXTDOMAIN ),
                        'type'    => 'radio',
                        'options' => array(
                            'yes' => __('Yes, Show reviews', ATBDP_TEXTDOMAIN),
                            'no'  => __('No, Do not show reviews', ATBDP_TEXTDOMAIN),
                        ),
                        'default' => 'yes',
                    ),
                    array(
                        'name'              => 'review_num',
                        'label'             => __( 'Number of Reviews', ATBDP_TEXTDOMAIN ),
                        'desc'              => __( 'Enter how many reviews you would like to show on your website. Eg. 5. Default is 5.', ATBDP_TEXTDOMAIN ),
                        'placeholder'       => __( '5', ATBDP_TEXTDOMAIN ),
                        'min'               => 1,
                        'max'               => 30,
                        'step'              => '1',
                        'type'              => 'number',
                        'default'           => 5,
                        'sanitize_callback' => 'floatval'
                    ),


                    array(
                        'name'              => 'search_posts_num',
                        'label'             => __( 'Number of Listings to show on search result page', ATBDP_TEXTDOMAIN ),
                        'desc'              => __( 'Enter how many listings you would like to show on your listing search result page. Eg. 6. Default is 6', ATBDP_TEXTDOMAIN ),
                        'placeholder'       => __( '6', ATBDP_TEXTDOMAIN ),
                        'min'               => 1,
                        'max'               => 100,
                        'step'              => '1',
                        'type'              => 'number',
                        'default'           => 6,
                        'sanitize_callback' => 'intval'
                    ),


                    array(
                        'name'  => 'fix_js_conflict',
                        'label' => __( 'Fix Conflict with Bootstrap JS', ATBDP_TEXTDOMAIN ),
                        'desc'  => __( 'If you use a theme that uses Bootstrap Framework especially Bootstrap JS, then Check this setting to fix any conflict with theme bootstrap js.', ATBDP_TEXTDOMAIN ),
                        'type'    => 'radio',
                        'options' => array(
                            'yes' => 'Yes',
                            'no'  => 'No'
                        ),
                        'default' => 'no',
                    ),


                    array(
                        'name'              => 'add_listing_page',
                        'label'             => __( 'Add listing page ID', ATBDP_TEXTDOMAIN ),
                        'desc'              => __( 'Select your add listing page ( where you used <strong>[add_listing]</strong> shortcode ) ID here', ATBDP_TEXTDOMAIN ),
                        'placeholder'       => __( 'Add listing page ID', ATBDP_TEXTDOMAIN ),
                        'type'              => 'select',
                        'default'           => '',
                        'options'           => $this->get_pages(),
                    ),


                    array(
                        'name'              => 'user_dashboard',
                        'label'             => __( 'Add dashboard page ID', ATBDP_TEXTDOMAIN ),
                        'desc'              => __( 'Select your add dashboard page ( where you used <strong>[user_dashboard]</strong> shortcode ) ID here', ATBDP_TEXTDOMAIN ),
                        'placeholder'       => __( 'Add dashboard page ID', ATBDP_TEXTDOMAIN ),
                        'type'              => 'select',
                        'default'           => '',
                        'options'           => $this->get_pages(),
                    ),

                    array(
                        'name'              => 'custom_registration',
                        'label'             => __( 'Add registration page ID', ATBDP_TEXTDOMAIN ),
                        'desc'              => __( 'Select your registration page ( where you used <strong>[custom_registration]</strong>  shortcode ) ID here', ATBDP_TEXTDOMAIN ),
                        'placeholder'       => __( 'Add registration page ID', ATBDP_TEXTDOMAIN ),
                        'type'              => 'select',
                        'default'           => '',
                        'options'           => $this->get_pages(),
                    ),

                    array(
                        'name'              => 'search_listing',
                        'label'             => __( 'Add Listing Search page ID', ATBDP_TEXTDOMAIN ),
                        'desc'              => __( 'Select your Listing Search page ( where you used <strong>[search_listing]</strong> shortcode ) ID here. This is generally used in a home page.', ATBDP_TEXTDOMAIN ),
                        'placeholder'       => __( 'Listing Search page ID', ATBDP_TEXTDOMAIN ),
                        'type'              => 'select',
                        'default'           => '',
                        'options'           => $this->get_pages(),
                    ),

                    array(
                        'name'              => 'search_result_page',
                        'label'             => __( 'Add Listing Search Result page ID', ATBDP_TEXTDOMAIN ),
                        'desc'              => __( 'Please Select your Listing Search Result page ( where you used <strong>[search_result]</strong> shortcode ) ID here. This page is used to show listing search results but this page is generally should be excluded from the menu.', ATBDP_TEXTDOMAIN ),
                        'placeholder'       => __( 'Listing Search Result page ID', ATBDP_TEXTDOMAIN ),
                        'type'              => 'select',
                        'default'           => '',
                        'options'           => $this->get_pages(),
                    ),

                    array(
                        'name'              => 'all_listing_page',
                        'label'             => __( 'Add The page ID of All Listings Page', ATBDP_TEXTDOMAIN ),
                        'desc'              => __( 'Select your All Listings  page ( where you used <strong>[all_listing]</strong> shortcode ) ID here.', ATBDP_TEXTDOMAIN ),
                        'placeholder'       => __( 'Enter All Listings page ID', ATBDP_TEXTDOMAIN ),
                        'type'              => 'select',
                        'default'           => '',
                        'options'           => $this->get_pages(),
                    ),





                )
            );

            /**
             * This action lets user add new settings fields to the plugin settings fields
             */
            return apply_filters('atbdp_settings_fields', $settings_fields);
        }

        function plugin_page() {
            echo '<div class="wrap">';
            settings_errors();
            $this->settings_api->show_navigation();
            $this->settings_api->show_forms();

            echo '</div>';
        }

        /**
         * Get all the pages
         *
         * @return array page names with key value pairs
         */
        function get_pages() {
            $pages = get_pages();
            $pages_options = array();
            if ( $pages ) {
                foreach ($pages as $page) {
                    $pages_options[$page->ID] = $page->post_title;
                }
            }

            return $pages_options;
        }

        /**
         * Get all the pages in an array where each page is an array of key:value:id and key:label:name
         *
         * @return array page names with key value pairs in a multi-dimensional array
         */
        function get_pages_vl_arrays() {
            $pages = get_pages();
            $pages_options = array();
            if ( $pages ) {
                foreach ($pages as $page) {
                    $pages_options[] = array('value'=>$page->ID, 'label'=> $page->post_title);
                }
            }

            return $pages_options;
        }

    }
endif;
