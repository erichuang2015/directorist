<?php
if ( !class_exists('ATBDP_Settings_Manager' ) ):
class ATBDP_Settings_Manager {

    public function __construct()
    {
        // the safest hook to use is after_setup_theme, since Vafpress Framework may exists in Theme or Plugin
        add_action( 'after_setup_theme', array($this, 'display_plugin_settings') );
    }

    /**
     * It displays the settings page of the plugin using VafPress framework
     * @since 3.0.0
     * @return void
     */
    public function display_plugin_settings()
    {
         $atbdp_options = array(
             //'is_dev_mode' => true,
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
                'logo' => esc_url(ATBDP_ADMIN_ASSETS . 'images/settings_icon.png'),
                'menus' => $this->get_settings_menus(),
            ),
        );

        // initialize the option page
        new VP_Option($atbdp_options);
    }

    /**
     * Get all the menus for the Settings Page
     * @since 3.0.0
     * @return array It returns an array of Menus
     */
    function get_settings_menus(){
        return apply_filters('atbdp_settings_menus', array(
            /*Main Menu 1*/
            'general_menu' => array(
                'title' => __('General settings', ATBDP_TEXTDOMAIN),
                'name' => 'menu_1',
                'icon' => 'font-awesome:fa-magic',
                'menus' => $this->get_general_settings_submenus(),
            ),
            /*Main Menu 2*/
            'permalink_menu' => array(
                'name' => 'permalinks',
                'title' => __('Permalinks settings', ATBDP_TEXTDOMAIN),
                'icon' => 'font-awesome:fa-link',
                'controls' => apply_filters('atbdp_permalink_settings_controls', array(
                    'permalink_section' => array(
                        'type' => 'section',
                        'title' => __('Slugs & Permalinks', ATBDP_TEXTDOMAIN),
                        'fields' => $this->get_permalink_settings_fields()
                    ), // ends section
                ) ),
            ),
            /*Main Menu 3*/
            'search' => array(
                'name' => 'search',
                'title' => __('Search settings', ATBDP_TEXTDOMAIN),
                'icon' => 'font-awesome:fa-search',
                'controls' => apply_filters('atbdp_search_settings_controls', array(
                    'search_section' => array(
                        'type' => 'section',
                        'title' => __('Search Settings', ATBDP_TEXTDOMAIN),
                        'description' => __('You can Customize Listing Search related settings here. After switching any option, Do not forget to save the changes.', ATBDP_TEXTDOMAIN),
                        'fields' => $this->get_search_settings_fields(),
                    ), // ends 'search_settings' section
                ) ),
            ),
            /*Main Menu 4*/
            'listings' => array(
                'name' => 'listings',
                'title' => __('Listings settings', ATBDP_TEXTDOMAIN),
                'icon' => 'font-awesome:fa-list',
                'controls' => apply_filters('atbdp_listings_settings_controls', array(
                    'search_section' => array(
                        'type' => 'section',
                        'title' => __('Listings Settings', ATBDP_TEXTDOMAIN),
                        'description' => __('You can Customize Listings related settings here. After switching any option, Do not forget to save the changes.', ATBDP_TEXTDOMAIN),
                        'fields' => $this->get_listings_settings_fields(),
                    ), // ends 'search_settings' section
                )),
            ),
            /*Main Menu 5*/
            'pages' => array(
                'name' => 'pages',
                'title' => __('Pages, links & views', ATBDP_TEXTDOMAIN),
                'icon' => 'font-awesome:fa-line-chart',
                'controls' => apply_filters('atbdp_pages_settings_controls', array(
                    'search_section' => array(
                        'type' => 'section',
                        'title' => __('Pages, links & views Settings', ATBDP_TEXTDOMAIN),
                        'description' => __('You can Customize Listings related settings here. After switching any option, Do not forget to save the changes.', ATBDP_TEXTDOMAIN),
                        'fields' => $this->get_pages_settings_fields(),
                    ), // ends 'pages' section
                )),
            ),
            /*Lets make the following extension menu customization by the extensions. Apply a filter on it*/
            'extensions_menu' => array(
                'title' => __('Extensions Settings', ATBDP_TEXTDOMAIN),
                'name' => 'menu_1',
                'icon' => 'font-awesome:fa-magic',
                'menus' => $this->get_extension_settings_submenus(),
            ),
        ));
    }

    /**
     * Get all the pages in an array where each page is an array of key:value:id and key:label:name
     * 
     * Example : array(
     *                  array('value'=> 1, 'label'=> 'page_name'),
     *                  array('value'=> 50, 'label'=> 'page_name'),
     *          )
     * @since 3.0.0
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

    /**
     * Get all the submenus for the General Settings menu
     * @since 3.0.0
     * @return array It returns an array of submenus
     */
    function get_general_settings_submenus(){
        return apply_filters('atbdp_general_settings_submenus', array(
            'submenu_1' => array(
                'title' => __( 'Home', ATBDP_TEXTDOMAIN),
                'name' => 'submenu_1',
                'icon' => 'font-awesome:fa-home',
                'controls' => apply_filters('atbdp_general_settings_controls', array(
                    'general_section' => array(
                        'type'          => 'section',
                        'title'         => __('General Settings', ATBDP_TEXTDOMAIN),
                        'description'   => __('You can Customize General settings here. After switching any option, Do not forget to save the changes.', ATBDP_TEXTDOMAIN),
                        'fields'        => $this->get_general_settings_fields(),
                    ), // ends general settings section
                )),
            ),

        ) );
    }

    /**
     * Get all the submenus for the extension menu
     * @since 3.0.0
     * @return array It returns an array of submenus
     */
    function get_extension_settings_submenus(){

        return apply_filters('atbdp_extension_settings_submenus', array(
            'submenu_1' => array(
                'title' => __('Extensions General', ATBDP_TEXTDOMAIN),
                'name' => 'extensions_switch',
                'icon' => 'font-awesome:fa-home',
                'controls' => apply_filters('atbdp_extension_settings_controls', array(
                    'extensions' => array(
                        'type' => 'section',
                        'title' => __('Extensions General Settings', ATBDP_TEXTDOMAIN),
                        'description' => __('You can Customize Extensions-related settings here. You can enable or disable any extensions here. Here, ON means Enabled, and OFF means disabled. After switching any option, Do not forget to save the changes.', ATBDP_TEXTDOMAIN),
                        'fields' => $this->get_extension_settings_fields(),
                    ),
                )),
            ),
        ));
    }

    /**
     * Get all the settings fields for the general settings section
     * @since 3.0.0
     * @return array
     */
    function get_general_settings_fields(){
        /*ADAPTED FOR BACKWARD COMPATIBILITY*/
        $fix_b_js = atbdp_get_option('fix_js_conflict', 'atbdp_general', 'no'); // fix bootstrap js conflict

        return apply_filters('atbdp_general_settings_fields', array(
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
                        'description' => __( 'You can adjust the zoom level of the map. 0 means 100% zoom-out. 22 means 100% zoom-in. Minimum Zoom Allowed = 1. Max Zoom Allowed = 22. Default is 16. ', ATBDP_TEXTDOMAIN ),
                        'min' => '1',
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
                )
        );
    }

    /**
     * Get all the settings fields for the permalink settings section
     * @since 3.0.0
     * @return array
     */
    function get_permalink_settings_fields(){
        return apply_filters('atbdp_permalink_settings_fields', array(
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

            )
        );
    }

    /**
     * Get all the settings fields for the search settings section
     * @since 3.0.0
     * @return array
     */
    function get_search_settings_fields(){
        return apply_filters('atbdp_search_settings_fields', array(
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

            )
        );
    }

    /**
     * Get all the settings fields for the listings settings section
     * @since 3.0.0
     * @return array
     */
    function get_listings_settings_fields(){
        // BACKWARD COMPATIBILITY:  OLD SETTINGS DATA that should be adapted by using them as default value, will be removed in future
        $s_p_cat = atbdp_get_option('show_popular_category', 'atbdp_general', 'yes');
        $e_p_list = atbdp_get_option('enable_pop_listing', 'atbdp_general', 'yes');
        $e_r_list = atbdp_get_option('enable_rel_listing', 'atbdp_general', 'yes');

        return apply_filters('atbdp_listings_settings_fields', array(
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


            )
        );
    }

    /**
     * Get all the settings fields for the pages settings section
     * @since 3.0.0
     * @return array
     */
    function get_pages_settings_fields(){
        return apply_filters('atbdp_pages_settings_fields', array(
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
            )
        );
    }

    /**
     * Get all the settings fields for the extension settings section
     * @since 3.0.0
     * @return array
     */
    function get_extension_settings_fields(){
        /*BACKWARD Compatibility: $e_review. It may be removed in future release*/
        $e_review = atbdp_get_option('enable_review', 'atbdp_general', 'yes');

        return apply_filters('atbdp_extension_settings_fields', array(
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
            )
        );
    }



} // ends ATBDP_Settings_Manager
endif;