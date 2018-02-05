<?php
if ( !class_exists('ATBDP_Settings_Manager' ) ):
class ATBDP_Settings_Manager {

    public function __construct()
    {
        // the safest hook to use, since Vafpress Framework may exists in Theme or Plugin
        add_action( 'after_setup_theme', array($this, 'display_plugin_settings') );
    }


    /**
     * It displays the settings page of the plugin using vafpress framework
     */
    public function display_plugin_settings()
    {
        //Adapted for old settings for migration. Get old settings and Lets convert boolean like value to boolean
        // it should only be one time process only before user save the settings for the first time only. Once user save the settings for the first time the the settings should use the data from the newly saved data from the database instead of using data from the database. @todo; And it should be tested before resealing. In short, make sure default data from old db settings is used only once.
        // OLD SETTINGS DATA that should be
        $s_p_cat = atbdp_get_option('show_popular_category', 'atbdp_general', 'yes');
        $e_p_list = atbdp_get_option('enable_pop_listing', 'atbdp_general', 'yes');
        $e_r_list = atbdp_get_option('enable_rel_listing', 'atbdp_general', 'yes');
        $e_review = atbdp_get_option('enable_review', 'atbdp_general', 'yes');
        $fix_b_js = atbdp_get_option('fix_js_conflict', 'atbdp_general', 'no'); // fix bootstrap js conflict



        /*Create a list of fields array of extension on off and then use it through a filter so that we can hook in to this array from an extension plugin.*/
        $ext_general_fields= array(
            array(
                'type' => 'toggle',
                'name' => 'enable_review',
                'label' => __('Enable Reviews & Rating', ATBDP_TEXTDOMAIN),
                'description' => __('Choose whether you want to display reviews form on Single listing details page or not. Default is ON.', ATBDP_TEXTDOMAIN),
                'default' => atbdp_yes_to_bool($e_review),
            ),

            array(
                'type' => 'toggle',
                'name' => 'enable_owner_review',
                'label' => __('Enable Owner Review', ATBDP_TEXTDOMAIN),
                'description' => __('Choose whether you want to allow a listing OWNER to post a review on his/her OWN listing on Single listing details page or not. Default is ON.', ATBDP_TEXTDOMAIN),
                'default' => 1,
            ),

            array(
                'type' => 'slider',
                'name' => 'review_num',
                'label' => __('Number of Reviews', ATBDP_TEXTDOMAIN),
                'description' => __( 'Enter how many reviews you would like to show on your website. Eg. 5. Default is 5. It will work if Review option is enabled.', ATBDP_TEXTDOMAIN),
                'min' => '1',
                'max' => '20',
                'step' => '1',
                'default' => '5',
                'validation' => 'numeric|minlength[1]',
            ),
        );


        /**
         * Allow modification of The list of extensions on/off fields array lists. An extension can add its on off switch by adding new array of using the following format.
         *
         * @since 4.9.0
         *
         * @param array $ext_general_fields     The array of the list of settings fields under the general section of Extensions
         */
        $ext_general_fields_array = apply_filters('atbdp_settings_ext_general_fields', $ext_general_fields);

        /* Create a list of submenus and use it through a filter so that we can add new item from our extensions.*/
        $extensions_submenu_array = array(
            'submenu_1' => array(
                'title' => __('Extensions General', ATBDP_TEXTDOMAIN),
                'name' => 'extensions_switch',
                'icon' => 'font-awesome:fa-home',
                'controls' => array(
                    'extensions' => array(
                        'type' => 'section',
                        'title' => __('Extensions General Settings', ATBDP_TEXTDOMAIN),
                        'description' => __('You can Customize Extensions-related settings here. You can enable or disable any extensions here. Here, ON means Enabled, and OFF means disabled. After switching any option, Do not forget to save the changes.', ATBDP_TEXTDOMAIN),
                        'fields' => $ext_general_fields_array,
                    ),
                ),
            ),
        );


        /**
         * Allow modification of The list of extensions submenu array lists. An extension can add its settings arrays/fields to the submenu of the extension array.
         *
         * @since 2.0.0
         *
         * @param array $extensions_submenu_array  The array of submenus of extension menu
         */
        $extensions_submenu = apply_filters('atbdp_setting_ext_submenu', $extensions_submenu_array);
        $atbdp_options = array(
//            'is_dev_mode' => true,
            'option_key' => 'atbdp_option',
            'page_slug' => 'aazztech_settings',
            'menu_page' => 'edit.php?post_type=at_biz_dir',
            'use_auto_group_naming' => true,
            'use_util_menu' => true,
            'minimum_role' => 'manage_options',
            'layout' => 'fixed',
            'page_title' => __('Directory settings', ATBDP_TEXTDOMAIN),
            'menu_label' => __('Directory settings', ATBDP_TEXTDOMAIN),
            'template' => array(
                'title' => __('Directory Settings', ATBDP_TEXTDOMAIN),
                'logo' => ATBDP_ADMIN_ASSETS . 'images/settings_icon.png',
                'menus' => array(
                    /*Main Menu 1*/
                    'general_menu' => array(
                        'title' => __('General settings', ATBDP_TEXTDOMAIN),
                        'name' => 'menu_1',
                        'icon' => 'font-awesome:fa-magic',
                        'menus' => array(
                            'submenu_1' => array(
                                'title' => __( 'Home', ATBDP_TEXTDOMAIN),
                                'name' => 'submenu_1',
                                'icon' => 'font-awesome:fa-home',
                                'controls' => array(
                                    'general_section' => array(
                                        'type'          => 'section',
                                        'title'         => __('General Settings', ATBDP_TEXTDOMAIN),
                                        'description'   => __('You can Customize General settings here. After switching any option, Do not forget to save the changes.', ATBDP_TEXTDOMAIN),
                                        'fields'        => array(
                                            array(
                                                'type' => 'textbox',
                                                'name' => 'map_api_key',
                                                'label' => __( 'Google Map API key', ATBDP_TEXTDOMAIN ),
                                                'description' => sprintf(__( 'You need to enter your google map api key in order to display google map. You can find your map api key and detailed information %s. or you can search in google', ATBDP_TEXTDOMAIN ), '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank"> <strong style="color: red;">here</strong> </a>'),
                                                'default' => atbdp_get_option('map_api_key', 'atbdp_general'),
                                                'validation' => 'required',
                                            ),

                                            array(
                                                'type' => 'slider',
                                                'name' => 'map_zoom_level',
                                                'label' => __( 'Google Map Zoom Level', ATBDP_TEXTDOMAIN ),
                                                'description' => __( 'You can adjust the zoom level of the map. 0 means 100% zoom-out. 22 means 100% zoom-in. Default is 16. ', ATBDP_TEXTDOMAIN ),
                                                'min' => '0',
                                                'max' => '22',
                                                'step' => '1',
                                                'default' => '16',
                                                'validation' => 'required',
                                            ),

                                            array(
                                                'type' => 'toggle',
                                                'name' => 'fix_js_conflict',
                                                'label' => __('Fix Conflict with Bootstrap JS', ATBDP_TEXTDOMAIN),
                                                'description' => __('If you use a theme that uses Bootstrap Framework especially Bootstrap JS, then Check this setting to fix any conflict with theme bootstrap js.', ATBDP_TEXTDOMAIN),
                                                'default' => atbdp_yes_to_bool($fix_b_js),
                                            ),
                                        ),
                                    ), // ends general settings section
                                ),
                            ),

                        ),
                    ),
                    /*Main Menu 2*/
                    'permalink_menu' => array(
                        'name' => 'permalinks',
                        'title' => __('Permalinks settings', ATBDP_TEXTDOMAIN),
                        'icon' => 'font-awesome:fa-link',
                        'controls' => array(


                            'title_slugs' => array(
                                'type' => 'section',
                                'title' => __('Slugs & Permalinks', ATBDP_TEXTDOMAIN),
                                'fields' => array(

                                    array(
                                        'type' => 'notebox',
                                        'label' => __('Notice about slugs:', ATBDP_TEXTDOMAIN),
                                        'description' => __('Slugs must contain only alpha-numeric characters, underscores or dashes. All slugs must be unique and different.', ATBDP_TEXTDOMAIN),
                                        'status' => 'warning',
                                    ),
                                    array(
                                        'type' => 'textbox',
                                        'name' => 'atbdp_listing_slug',
                                        'label' => __('Listing slug', ATBDP_TEXTDOMAIN),
                                        'default' => 'at_biz_dir',
                                        'validation' => 'required',
                                    ),
                                    array(
                                        'type' => 'textbox',
                                        'name' => 'atbdp_cat_slug',
                                        'label' => __('Category slug', ATBDP_TEXTDOMAIN),
                                        'default' => ATBDP_CATEGORY,
                                        'validation' => 'required',
                                    ),
                                    array(
                                        'type' => 'textbox',
                                        'name' => 'atbdp_loc_slug',
                                        'label' => __('Location slug', ATBDP_TEXTDOMAIN),
                                        'default' => ATBDP_LOCATION,
                                        'validation' => 'required',
                                    ),
                                    array(
                                        'type' => 'textbox',
                                        'name' => 'atbdp_tag_slug',
                                        'label' => __('Tag slug', ATBDP_TEXTDOMAIN),
                                        'default' => ATBDP_TAGS,
                                        'validation' => 'required',
                                    ),

                                    array(
                                        'type' => 'notebox',
                                        'label' => __('Tips & Troubleshooting:', ATBDP_TEXTDOMAIN),
                                        'description' => __('NOTE: If changing this option does not work, then do not worry. Just go to "WordPress Dashboard>Settings>Permalinks" and just click save. It should work fine now.', ATBDP_TEXTDOMAIN),
                                        'status' => 'info',
                                    ),

                                ),
                            ),
                        ),
                    ),
                    /*Main Menu 3*/
                    'search' => array(
                        'name' => 'search',
                        'title' => __('Search settings', ATBDP_TEXTDOMAIN),
                        'icon' => 'font-awesome:fa-search',
                        'controls' => array(
                            'search_section' => array(
                                'type' => 'section',
                                'title' => __('Search Settings', ATBDP_TEXTDOMAIN),
                                'description' => __('You can Customize Listing Search related settings here. After switching any option, Do not forget to save the changes.', ATBDP_TEXTDOMAIN),
                                'fields' => array(

                                    array(
                                        'type' => 'textbox',
                                        'name' => 'search_title',
                                        'label' => __('Search Bar Title', ATBDP_TEXTDOMAIN),
                                        'description' => __( 'Enter the title for search bar on Home Page. Eg. Find the Best Places to Be', ATBDP_TEXTDOMAIN ),
                                        'default' => atbdp_get_option('search_title', 'atbdp_general'),
                                    ),

                                    array(
                                        'type' => 'textbox',
                                        'name' => 'search_subtitle',
                                        'label' => __('Search Bar Sub-title', ATBDP_TEXTDOMAIN),
                                        'description' => __( 'Enter the sub-title for search bar on Home Page. Eg. All the top locations – from restaurants and clubs, to cinemas, galleries, and more..', ATBDP_TEXTDOMAIN ),
                                        'default' => atbdp_get_option('search_subtitle', 'atbdp_general'),
                                    ),
                                    array(
                                        'type' => 'textbox',
                                        'name' => 'search_placeholder',
                                        'label' => __('Search Bar Placeholder', ATBDP_TEXTDOMAIN),
                                        'description' => __( 'Enter the placeholder text for the search field. Eg. What are you looking for?', ATBDP_TEXTDOMAIN ),
                                        'default' => atbdp_get_option('search_placeholder', 'atbdp_general'),
                                    ),
                                    array(
                                        'type' => 'slider',
                                        'name' => 'search_posts_num',
                                        'label' => __('Number of Search Results', ATBDP_TEXTDOMAIN),
                                        'description' => __( 'Enter how many listings you would like to show on your listing search result page. Eg. 6. Default is 6', ATBDP_TEXTDOMAIN),
                                        'min' => '1',
                                        'max' => '100',
                                        'step' => '1',
                                        'default' => atbdp_get_option('search_posts_num', 'atbdp_general'),
                                        'validation' => 'numeric|minlength[1]',
                                    ),

                                ),
                            ), // ends 'search_settings' section



                        ),
                    ),
                    /*Main Menu 4*/
                    'listings' => array(
                        'name' => 'listings',
                        'title' => __('Listings settings', ATBDP_TEXTDOMAIN),
                        'icon' => 'font-awesome:fa-list',
                        'controls' => array(
                            'search_section' => array(
                                'type' => 'section',
                                'title' => __('Listings Settings', ATBDP_TEXTDOMAIN),
                                'description' => __('You can Customize Listings related settings here. After switching any option, Do not forget to save the changes.', ATBDP_TEXTDOMAIN),
                                'fields' => array(

                                    array(
                                        'type' => 'textbox',
                                        'name' => 'all_listing_title',
                                        'label' => __('Title for all listing page', ATBDP_TEXTDOMAIN),
                                        'description' => __( 'Enter a title for the page where all listings will be shown using the shortcode [all_listing] . Eg. All Listings/ Items.', ATBDP_TEXTDOMAIN ),
                                        'default' => atbdp_get_option('all_listing_title', 'atbdp_general'),
                                    ),
                                    array(
                                        'type' => 'slider',
                                        'name' => 'all_listing_page_items',
                                        'label' => __('Number of Listings on All listing page', ATBDP_TEXTDOMAIN),
                                        'description' => __( 'Set how many listings you would like to show on the All Listings page. Eg. 6. Default is 6', ATBDP_TEXTDOMAIN),
                                        'min' => '1',
                                        'max' => '30',
                                        'step' => '1',
                                        'default' => '6',
                                        'validation' => 'numeric|minlength[1]',
                                    ),
                                    array(
                                        'type' => 'toggle',
                                        'name' => 'show_popular_category',
                                        'label' => __('Show popular category on the search page', ATBDP_TEXTDOMAIN),
                                        'description' => __('You can show popular category on search page or you can hide it here.', ATBDP_TEXTDOMAIN),
                                        'default' => atbdp_yes_to_bool($s_p_cat),
                                    ),

                                    array(
                                        'type' => 'textbox',
                                        'name' => 'popular_cat_title',
                                        'label' => __('Popular Category Title', ATBDP_TEXTDOMAIN),
                                        'description' => __( 'Enter the title for popular category on listing search page eg. Browse by popular categories', ATBDP_TEXTDOMAIN ),
                                        'default' => __('Browse by popular categories', ATBDP_TEXTDOMAIN),
                                    ),

                                    array(
                                        'type' => 'slider',
                                        'name' => 'popular_cat_num',
                                        'label' => __('Number of Popular Category', ATBDP_TEXTDOMAIN),
                                        'description' => __( 'Set how many popular categories you would like to show on your listing main search page. Eg. 10. Default is 10', ATBDP_TEXTDOMAIN),
                                        'min' => '1',
                                        'max' => '30',
                                        'step' => '1',
                                        'default' => '10',
                                        'validation' => 'numeric|minlength[1]',
                                    ),

                                    array(
                                        'type' => 'toggle',
                                        'name' => 'enable_pop_listing',
                                        'label' => __('Enable popular listings on Single Listing page', ATBDP_TEXTDOMAIN),
                                        'description' => __('Choose whether you want to display popular listings on Single listing details page or not. Default is ON.', ATBDP_TEXTDOMAIN),
                                        'default' => atbdp_yes_to_bool($e_p_list),
                                    ),

                                    array(
                                        'type' => 'slider',
                                        'name' => 'pop_listing_num',
                                        'label' => __('Number of Popular Listings', ATBDP_TEXTDOMAIN),
                                        'description' => __( 'Set how many popular listings you would like to show on your website. Eg. 5. Default is 5.', ATBDP_TEXTDOMAIN),
                                        'min' => '1',
                                        'max' => '30',
                                        'step' => '1',
                                        'default' => '5',
                                        'validation' => 'numeric|minlength[1]',
                                    ),


                                    array(
                                        'type' => 'toggle',
                                        'name' => 'enable_rel_listing',
                                        'label' => __('Enable related listings on Single Listing page', ATBDP_TEXTDOMAIN),
                                        'description' => __('Choose whether you want to display related listings on Single listing details page or not. Default is ON.', ATBDP_TEXTDOMAIN),
                                        'default' => atbdp_yes_to_bool($e_r_list),
                                    ),

                                    array(
                                        'type' => 'slider',
                                        'name' => 'rel_listing_num',
                                        'label' => __('Number of Related Listings', ATBDP_TEXTDOMAIN),
                                        'description' => __( 'Set how many related listings you would like to show on your website. Eg. 2. Default is 2.', ATBDP_TEXTDOMAIN),
                                        'min' => '1',
                                        'max' => '10',
                                        'step' => '1',
                                        'default' => '2',
                                        'validation' => 'numeric|minlength[1]',
                                    ),


                                ),
                            ), // ends 'search_settings' section



                        ),
                    ),
                    /*Main Menu 5*/
                    'pages' => array(
                        'name' => 'pages',
                        'title' => __('Pages, links & views', ATBDP_TEXTDOMAIN),
                        'icon' => 'font-awesome:fa-line-chart',
                        'controls' => array(
                            'search_section' => array(
                                'type' => 'section',
                                'title' => __('Pages, links & views Settings', ATBDP_TEXTDOMAIN),
                                'description' => __('You can Customize Listings related settings here. After switching any option, Do not forget to save the changes.', ATBDP_TEXTDOMAIN),
                                'fields' => array(

                                    array(
                                        'type' => 'select',
                                        'name' => 'add_listing_page',
                                        'label' => __('Add Listing Page ID', ATBDP_TEXTDOMAIN),
                                        'items' => $this->get_pages_vl_arrays(), // eg. array( array('value'=> 123, 'label'=> 'page_name') );
                                        'description' => sprintf(__( 'Select your add listing page ( where you used %s shortcode ) ID here', ATBDP_TEXTDOMAIN ), '<strong style="color: #ff4500;">[add_listing]</strong>'),
                                        'default' => atbdp_get_option('add_listing_page', 'atbdp_general'),
                                        'validation' => 'required|numeric',

                                    ),

                                    array(
                                        'type' => 'select',
                                        'name' => 'all_listing_page',
                                        'label' => __( 'Add All Listings Page ID', ATBDP_TEXTDOMAIN ),
                                        'items' => $this->get_pages_vl_arrays(),
                                        'description' => sprintf(__( 'Select your All Listings  page ( where you used %s shortcode ) ID here.', ATBDP_TEXTDOMAIN ), '<strong style="color: #ff4500;">[all_listing]</strong>'),

                                        'default' => atbdp_get_option('all_listing_page', 'atbdp_general'),
                                        'validation' => 'required|numeric',
                                    ),

                                    array(
                                        'type' => 'select',
                                        'name' => 'user_dashboard',
                                        'label' =>  __( 'Add dashboard page ID', ATBDP_TEXTDOMAIN ),
                                        'items' => $this->get_pages_vl_arrays(),
                                        'description' => sprintf(__( 'Select your add dashboard page ( where you used %s shortcode ) ID here', ATBDP_TEXTDOMAIN ), '<strong style="color: #ff4500;">[user_dashboard]</strong>'),
                                        'default' => atbdp_get_option('user_dashboard', 'atbdp_general'),
                                        'validation' => 'required|numeric',

                                    ),

                                    array(
                                        'type' => 'select',
                                        'name' => 'custom_registration',
                                        'label' =>  __(  'Add registration page ID', ATBDP_TEXTDOMAIN ),
                                        'items' => $this->get_pages_vl_arrays(),
                                        'description' => sprintf(__( 'Select your registration page ( where you used %s  shortcode ) ID here', ATBDP_TEXTDOMAIN ), '<strong style="color: #ff4500;">[custom_registration]</strong>'),
                                        'default' => atbdp_get_option('custom_registration', 'atbdp_general'),
                                        'validation' => 'required|numeric',

                                    ),

                                    array(
                                        'type' => 'select',
                                        'name' => 'search_listing',
                                        'label' =>  __( 'Add Listing Search page ID', ATBDP_TEXTDOMAIN ),
                                        'items' => $this->get_pages_vl_arrays(),
                                        'description' => sprintf(__( 'Select your Listing Search page ( where you used %s shortcode ) ID here. This is generally used in a home page.', ATBDP_TEXTDOMAIN ), '<strong style="color: #ff4500;">[search_listing]</strong>'),
                                        'default' => atbdp_get_option('search_listing', 'atbdp_general'),
                                        'validation' => 'required|numeric',
                                    ),

                                    array(
                                        'type' => 'select',
                                        'name' => 'search_result_page',
                                        'label' =>  __( 'Add Listing Search Result page ID', ATBDP_TEXTDOMAIN ),
                                        'items' => $this->get_pages_vl_arrays(),
                                        'description' => sprintf(__( 'Please Select your Listing Search Result page ( where you used %s shortcode ) ID here. This page is used to show listing search results but this page is generally should be excluded from the menu.', ATBDP_TEXTDOMAIN ),'<strong style="color: #ff4500;">[search_result]</strong>'),
                                        'default' => atbdp_get_option('search_result_page', 'atbdp_general'),
                                        'validation' => 'required|numeric',
                                    ),
                                ),
                            ), // ends 'search_settings' section
                        ),
                    ),
                    /*Lets make the following extension menu customization by the extensions. Apply a filter on it*/
                    'extensions_menu' => array(
                        'title' => __('Extensions Settings', ATBDP_TEXTDOMAIN),
                        'name' => 'menu_1',
                        'icon' => 'font-awesome:fa-magic',
                        'menus' => $extensions_submenu
                    ),
                ),
            ),

        );

        // initialize the option page
        new VP_Option($atbdp_options);

    }

    /**
     * Get all the pages in an array where each page is an array of key:value:id and key:label:name
     * 
     * Example : array(
     *                  array('value'=> 1, 'label'=> 'page_name'),
     *                  array('value'=> 50, 'label'=> 'page_name'),
     *          )
     * @return array page names with key value pairs in a multi-dimensional array
     */
    function get_pages_vl_arrays() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[] = array( 'value'=>$page->ID, 'label'=> $page->post_title);
            }
        }

        return $pages_options;
    }



} // ends ATBDP_Settings_Manager
endif;