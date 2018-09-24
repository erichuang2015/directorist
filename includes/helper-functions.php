<?php
// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if (!function_exists('load_dependencies')):
    /**
     * It loads files from a given directory using require_once.
     * @param string|array $files list of the names of file or a single file name to be loaded. Default: all
     * @param string $directory  the location of the files
     * @param string $ext  the ext of the files to be loaded
     * @return resource|bool it requires all the files in a given directory
     */
    function load_dependencies($files = 'all', $directory=ATBDP_CLASS_DIR, $ext='.php')
    {
        if (!file_exists($directory)) return; // vail if the directory does not exist

        switch ($files){
            case is_array($files) && 'all' !== strtolower($files[0]):
                // include one or more file looping through the $files array
                load_some_file($files, $directory);
                break;
            case !is_array($files) && 'all' !== $files:
                //load a single file here
                (file_exists($directory.$files.$ext)) ? require_once $directory.$files.$ext: null;
                break;
            case 'all' == $files || 'all' == strtolower($files[0]):
                // load all php file here
                load_all_files($directory);
                break;
        }

        return false;

    }
endif;


if (!function_exists('load_all_files')):
    /**
     * It loads all files that has the extension named $ext from the $dir
     * @param string $dir Name of the directory
     * @param string $ext Name of the extension of the files to be loaded
     */
    function load_all_files($dir='', $ext='.php'){
        if (!file_exists($dir)) return;
        foreach (scandir($dir) as $file) {
            // require once all the files with the given ext. eg. .php
            if( preg_match( "/{$ext}$/i" , $file ) ) {
                require_once( $dir . $file );
            }
        }
    }
endif;



if (!function_exists('load_some_file')):

    /**
     * It loads one or more files but not all files that has the $ext from the $dir
     * @param string|array $files the array of files that should be loaded
     * @param string $dir Name of the directory
     * @param string $ext Name of the extension of the files to be loaded
     */
    function load_some_file($files=array(), $dir='', $ext='.php')
    {
        if (!file_exists($dir)) return; // vail if directory does not exist

        if(is_array($files)) {  // if the given files is an array then
            $files_to_loads = array_map(function ($i) use($ext){ return $i.$ext; }, $files);// add '.php' to the end of all files
            $found_files = scandir($dir); // get the list of all the files in the given $dir
            foreach ($files_to_loads as $file_to_load) {
                in_array($file_to_load, $found_files) ? require_once $dir.$file_to_load : null;
            }
        }

    }
endif;



if (!function_exists('attc_letter_to_number')):

    /**
     * Calculate the column index (number) of a column header string (example: A is 1, AA is 27, ...).
     *
     * For the opposite, @see number_to_letter().
     *
     * @since 1.0.0
     *
     * @param string $column Column string.
     * @return int $number Column number, 1-based.
     */
    function attc_letter_to_number($column ) {
        $column = strtoupper( $column );
        $count = strlen( $column );
        $number = 0;
        for ( $i = 0; $i < $count; $i++ ) {
            $number += ( ord( $column[ $count - 1 - $i ] ) - 64 ) * pow( 26, $i );
        }
        return $number;
    }

endif;

if (!function_exists('attc_number_to_letter')):

    /**
     * "Calculate" the column header string of a column index (example: 2 is B, AB is 28, ...).
     *
     * For the opposite, @see letter_to_number().
     *
     * @since 1.0.0
     *
     * @param int $number Column number, 1-based.
     * @return string $column Column string.
     */
    function attc_number_to_letter($number ) {
        $column = '';
        while ( $number > 0 ) {
            $column = chr( 65 + ( ( $number - 1 ) % 26 ) ) . $column;
            $number = floor( ( $number - 1 ) / 26 );
        }
        return $column;
    }
endif;

if (!function_exists('padded_var_dump')):

    /**
     * It dumps data to the screen in a div that has margin left 200px.
     * It is good for dumping data in WordPress dashboard
     */
    function padded_var_dump(){
        echo "<div style='margin-left: 200px;'>";
        $args = func_get_args();
        if (count($args)) foreach ($args as $a) { var_dump($a); }
        echo "</div>";
    }
endif;

if (!function_exists('list_file_name')):
    /**
     * It returns a list of names of all files which are not hidden files
     * @param string $path
     * @return array
     */
    function list_file_name($path = __DIR__){
        $file_names = array();
        foreach (new DirectoryIterator($path) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            $file_names[] =  $fileInfo->getFilename();
        }
        return $file_names;
    }

endif;

if (!function_exists('list_file_path')):
    /**
     * It returns a list of path of all files which are not hidden files
     * @param string $path
     * @return array
     */
    function list_file_path($path = __DIR__){
        $file_paths = array();
        foreach (new DirectoryIterator($path) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            $file_paths[] =  $fileInfo->getRealPath();
        }
        return $file_paths;
    }

endif;

if (!function_exists('beautiful_datetime')):
    /**
     * It display a nice date and time
     * @param $datetime
     * @param string $type
     * @param string $separator
     * @return string
     */
    function beautiful_datetime($datetime, $type = 'mysql', $separator = ' ' ) {
        if ( 'mysql' === $type ) {
            return mysql2date( get_option( 'date_format' ), $datetime ) . $separator . mysql2date( get_option( 'time_format' ), $datetime );
        } else {
            return date_i18n( get_option( 'date_format' ), $datetime ) . $separator . date_i18n( get_option( 'time_format' ), $datetime );
        }
    }

endif;

if (!function_exists('aazztech_enc_serialize')) {
    /**
     * It will serialize and then encode the string and return the encoded data
     * @param $data
     * @return string
     */
    function aazztech_enc_serialize($data)
    {
        return (!empty($data)) ? base64_encode(serialize($data)): null;
    }
}

if (!function_exists('aazztech_enc_unserialize')){
    /**
     * It will decode the data and then unserialize the data and return it
     * @param string $data Encoded strings that should be decoded and then unserialize
     * @return mixed
     */
    function aazztech_enc_unserialize($data){
        return (!empty($data)) ? unserialize(base64_decode($data)) : null;
    }
}


if (!function_exists('atbd_get_related_posts')){
    // get related post based on tags or categories
    function atbd_get_related_posts() {
        global $post;
        // get all tags assigned to current post
        $tags = wp_get_post_tags($post->ID);
        $args = array();
        // set args to get related posts based on tags
        if (!empty($tags)) {
            $tag_ids = array();
            foreach($tags as $tag) $tag_ids[] = $tag->term_id;
            $args=array(
                'tag__in' => $tag_ids,
                'post__not_in' => array($post->ID),
                'ignore_sticky_posts' => true,
                'posts_per_page'=>5,
                'orderby'=>'rand',
            );
        }
        else {
            // get all cats assigned to current post
            $cats = get_the_category($post->ID);
            // set the args to get all related posts based on category.
            if ($cats) {
                $cat_ids = array();
                foreach($cats as $cat) $cat_ids[] = $cat->term_id;
                $args=array(
                    'category__in' => $cat_ids,
                    'post__not_in' => array($post->ID),
                    'ignore_sticky_posts' => true,
                    'posts_per_page'=>5,
                    'orderby'=>'rand',
                );
            }
        }
        if(!empty($args)){
            // build the markup and return
            return new WP_Query($args);

        }
        return null;
    }
}

if (!function_exists('atbdp_get_option')){

    /**
     * It retrieves an option from the database if it exists and returns false if it is not exist.
     * It is a custom function to get the data of custom setting page
     * @param string $name The name of the option we would like to get. Eg. map_api_key
     * @param string $group The name of the group where the option is saved. eg. general_settings
     * @param mixed $default    Default value for the option key if the option does not have value then default will be returned
     * @return mixed    It returns the value of the $name option if it exists in the option $group in the database, false otherwise.
     */
    function atbdp_get_option($name, $group, $default=false){
        // at first get the group of options from the database.
        // then check if the data exists in the array and if it exists then return it
        // if not, then return false
        if (empty($name) || empty($group)) {
            if (!empty($default)) return $default;
            return false;
        } // vail if either $name or option $group is empty
        $options_array = (array) get_option($group);
        if (array_key_exists($name, $options_array)) {
            return $options_array[$name];
        }else{
            if (!empty($default)) return $default;
            return false;
        }
    }
}


if (!function_exists('get_directorist_option')){

    /**
     * It retrieves an option from the database if it exists and returns false if it is not exist.
     * It is a custom function to get the data of custom setting page
     * @param string $name          The name of the option we would like to get. Eg. map_api_key
     * @param mixed $default        Default value for the option key if the option does not have value then default will be returned
     * @param bool $force_default   Whether to use default value when database return anything other than NULL such as '', false etc
     * @return mixed    It returns the value of the $name option if it exists in the option $group in the database, false otherwise.
     */
    function get_directorist_option($name, $default=false, $force_default = false){
        // at first get the group of options from the database.
        // then check if the data exists in the array and if it exists then return it
        // if not, then return false
        if (empty($name)) { return $default; }
        // get the option from the database and return it if it is not a null value. Otherwise, return the default value
        $options = (array) get_option('atbdp_option');
        $v = (array_key_exists($name, $options))
            ? $v =  $options[sanitize_key($name)]
            : null;
        // use default only when the value of the $v is NULL
        if (is_null($v)) { return $default; }
        if ($force_default){
            // use the default value even if the value of $v is falsy value returned from the database
             if(empty($v)) { return $default; }
        }
        return (isset($v) ) ? $v : $default; // return the data if it is anything but NULL.
    }
}


if (!function_exists('atbdp_yes_to_bool')){
    function atbdp_yes_to_bool($v=false){
        if(empty($v)) return false;
        return ('yes' == trim($v)) ? true : false;
    }
}


if (!function_exists('atbdp_pagination')){
    /**
     * Prints pagination for custom post
     * @param object|WP_Query $custom_post_query
     * @param int $paged
     *
     * @return string
     */
    function atbdp_pagination( $custom_post_query, $paged = 1){
        $navigation = '';
        $largeNumber = 999999999; // we need a large number here
        $links = paginate_links( array(
            'base' => str_replace( $largeNumber, '%#%', esc_url( get_pagenum_link( $largeNumber ) ) ),
            'format' => '?paged=%#%',
            'current' => max( 1, $paged ),
            'total' => $custom_post_query->max_num_pages,
            'prev_text' => apply_filters('atbdp_pagination_prev_text', '<span class="fa fa-chevron-left"></span>'),
            'next_text' => apply_filters('atbdp_pagination_next_text', '<span class="fa fa-chevron-right"></span>'),
        ) );


        if ( $links ) {
            $navigation = _navigation_markup( $links, 'pagination', __( 'Posts navigation', ATBDP_TEXTDOMAIN) );
        }
        return apply_filters('atbdp_pagination', $navigation, $links, $custom_post_query, $paged);
    }
}

if (!function_exists('get_fa_icons')){
    function  get_fa_icons() {
        return $iconsFA = array("fa-500px","fa-address-book","fa-address-book-o","fa-address-card","fa-address-card-o","fa-adjust","fa-adn","fa-align-center","fa-align-justify","fa-align-left","fa-align-right","fa-amazon","fa-ambulance","fa-american-sign-language-interpreting","fa-anchor","fa-android","fa-angellist","fa-angle-double-down","fa-angle-double-left","fa-angle-double-right","fa-angle-double-up","fa-angle-down","fa-angle-left","fa-angle-right","fa-angle-up","fa-apple","fa-archive","fa-area-chart","fa-arrow-circle-down","fa-arrow-circle-left","fa-arrow-circle-o-down","fa-arrow-circle-o-left","fa-arrow-circle-o-right","fa-arrow-circle-o-up","fa-arrow-circle-right","fa-arrow-circle-up","fa-arrow-down","fa-arrow-left","fa-arrow-right","fa-arrow-up","fa-arrows","fa-arrows-alt","fa-arrows-h","fa-arrows-v","fa-assistive-listening-systems","fa-asterisk","fa-at","fa-audio-description","fa-backward","fa-balance-scale","fa-ban","fa-bandcamp","fa-bar-chart","fa-barcode","fa-bars","fa-bath","fa-battery-empty","fa-battery-full","fa-battery-half","fa-battery-quarter","fa-battery-three-quarters","fa-bed","fa-beer","fa-behance","fa-behance-square","fa-bell","fa-bell-o","fa-bell-slash","fa-bell-slash-o","fa-bicycle","fa-binoculars","fa-birthday-cake","fa-bitbucket","fa-bitbucket-square","fa-black-tie","fa-blind","fa-bluetooth","fa-bluetooth-b","fa-bold","fa-bolt","fa-bomb","fa-book","fa-bookmark","fa-bookmark-o","fa-braille","fa-briefcase","fa-btc","fa-bug","fa-building","fa-building-o","fa-bullhorn","fa-bullseye","fa-bus","fa-buysellads","fa-calculator","fa-calendar","fa-calendar-check-o","fa-calendar-minus-o","fa-calendar-o","fa-calendar-plus-o","fa-calendar-times-o","fa-camera","fa-camera-retro","fa-car","fa-caret-down","fa-caret-left","fa-caret-right","fa-caret-square-o-down","fa-caret-square-o-left","fa-caret-square-o-right","fa-caret-square-o-up","fa-caret-up","fa-cart-arrow-down","fa-cart-plus","fa-cc","fa-cc-amex","fa-cc-diners-club","fa-cc-discover","fa-cc-jcb","fa-cc-mastercard","fa-cc-paypal","fa-cc-stripe","fa-cc-visa","fa-certificate","fa-chain-broken","fa-check","fa-check-circle","fa-check-circle-o","fa-check-square","fa-check-square-o","fa-chevron-circle-down","fa-chevron-circle-left","fa-chevron-circle-right","fa-chevron-circle-up","fa-chevron-down","fa-chevron-left","fa-chevron-right","fa-chevron-up","fa-child","fa-chrome","fa-circle","fa-circle-o","fa-circle-o-notch","fa-circle-thin","fa-clipboard","fa-clock-o","fa-clone","fa-cloud","fa-cloud-download","fa-cloud-upload","fa-code","fa-code-fork","fa-codepen","fa-codiepie","fa-coffee","fa-cog","fa-cogs","fa-columns","fa-comment","fa-comment-o","fa-commenting","fa-commenting-o","fa-comments","fa-comments-o","fa-compass","fa-compress","fa-connectdevelop","fa-contao","fa-copyright","fa-creative-commons","fa-credit-card","fa-credit-card-alt","fa-crop","fa-crosshairs","fa-css3","fa-cube","fa-cubes","fa-cutlery","fa-dashcube","fa-database","fa-deaf","fa-delicious","fa-desktop","fa-deviantart","fa-diamond","fa-digg","fa-dot-circle-o","fa-download","fa-dribbble","fa-dropbox","fa-drupal","fa-edge","fa-eercast","fa-eject","fa-ellipsis-h","fa-ellipsis-v","fa-empire","fa-envelope","fa-envelope-o","fa-envelope-open","fa-envelope-open-o","fa-envelope-square","fa-envira","fa-eraser","fa-etsy","fa-eur","fa-exchange","fa-exclamation","fa-exclamation-circle","fa-exclamation-triangle","fa-expand","fa-expeditedssl","fa-external-link","fa-external-link-square","fa-eye","fa-eye-slash","fa-eyedropper","fa-facebook","fa-facebook-official","fa-facebook-square","fa-fast-backward","fa-fast-forward","fa-fax","fa-female","fa-fighter-jet","fa-file","fa-file-archive-o","fa-file-audio-o","fa-file-code-o","fa-file-excel-o","fa-file-image-o","fa-file-o","fa-file-pdf-o","fa-file-powerpoint-o","fa-file-text","fa-file-text-o","fa-file-video-o","fa-file-word-o","fa-files-o","fa-film","fa-filter","fa-fire","fa-fire-extinguisher","fa-firefox","fa-first-order","fa-flag","fa-flag-checkered","fa-flag-o","fa-flask","fa-flickr","fa-floppy-o","fa-folder","fa-folder-o","fa-folder-open","fa-folder-open-o","fa-font","fa-font-awesome","fa-fonticons","fa-fort-awesome","fa-forumbee","fa-forward","fa-foursquare","fa-free-code-camp","fa-frown-o","fa-futbol-o","fa-gamepad","fa-gavel","fa-gbp","fa-genderless","fa-get-pocket","fa-gg","fa-gg-circle","fa-gift","fa-git","fa-git-square","fa-github","fa-github-alt","fa-github-square","fa-gitlab","fa-glass","fa-glide","fa-glide-g","fa-globe","fa-google","fa-google-plus","fa-google-plus-official","fa-google-plus-square","fa-google-wallet","fa-graduation-cap","fa-gratipay","fa-grav","fa-h-square","fa-hacker-news","fa-hand-lizard-o","fa-hand-o-down","fa-hand-o-left","fa-hand-o-right","fa-hand-o-up","fa-hand-paper-o","fa-hand-peace-o","fa-hand-pointer-o","fa-hand-rock-o","fa-hand-scissors-o","fa-hand-spock-o","fa-handshake-o","fa-hashtag","fa-hdd-o","fa-header","fa-headphones","fa-heart","fa-heart-o","fa-heartbeat","fa-history","fa-home","fa-hospital-o","fa-hourglass","fa-hourglass-end","fa-hourglass-half","fa-hourglass-o","fa-hourglass-start","fa-houzz","fa-html5","fa-i-cursor","fa-id-badge","fa-id-card","fa-id-card-o","fa-ils","fa-imdb","fa-inbox","fa-indent","fa-industry","fa-info","fa-info-circle","fa-inr","fa-instagram","fa-internet-explorer","fa-ioxhost","fa-italic","fa-joomla","fa-jpy","fa-jsfiddle","fa-key","fa-keyboard-o","fa-krw","fa-language","fa-laptop","fa-lastfm","fa-lastfm-square","fa-leaf","fa-leanpub","fa-lemon-o","fa-level-down","fa-level-up","fa-life-ring","fa-lightbulb-o","fa-line-chart","fa-link","fa-linkedin","fa-linkedin-square","fa-linode","fa-linux","fa-list","fa-list-alt","fa-list-ol","fa-list-ul","fa-location-arrow","fa-lock","fa-long-arrow-down","fa-long-arrow-left","fa-long-arrow-right","fa-long-arrow-up","fa-low-vision","fa-magic","fa-magnet","fa-male","fa-map","fa-map-marker","fa-map-o","fa-map-pin","fa-map-signs","fa-mars","fa-mars-double","fa-mars-stroke","fa-mars-stroke-h","fa-mars-stroke-v","fa-maxcdn","fa-meanpath","fa-medium","fa-medkit","fa-meetup","fa-meh-o","fa-mercury","fa-microchip","fa-microphone","fa-microphone-slash","fa-minus","fa-minus-circle","fa-minus-square","fa-minus-square-o","fa-mixcloud","fa-mobile","fa-modx","fa-money","fa-moon-o","fa-motorcycle","fa-mouse-pointer","fa-music","fa-neuter","fa-newspaper-o","fa-object-group","fa-object-ungroup","fa-odnoklassniki","fa-odnoklassniki-square","fa-opencart","fa-openid","fa-opera","fa-optin-monster","fa-outdent","fa-pagelines","fa-paint-brush","fa-paper-plane","fa-paper-plane-o","fa-paperclip","fa-paragraph","fa-pause","fa-pause-circle","fa-pause-circle-o","fa-paw","fa-paypal","fa-pencil","fa-pencil-square","fa-pencil-square-o","fa-percent","fa-phone","fa-phone-square","fa-picture-o","fa-pie-chart","fa-pied-piper","fa-pied-piper-alt","fa-pied-piper-pp","fa-pinterest","fa-pinterest-p","fa-pinterest-square","fa-plane","fa-play","fa-play-circle","fa-play-circle-o","fa-plug","fa-plus","fa-plus-circle","fa-plus-square","fa-plus-square-o","fa-podcast","fa-power-off","fa-print","fa-product-hunt","fa-puzzle-piece","fa-qq","fa-qrcode","fa-question","fa-question-circle","fa-question-circle-o","fa-quora","fa-quote-left","fa-quote-right","fa-random","fa-ravelry","fa-rebel","fa-recycle","fa-reddit","fa-reddit-alien","fa-reddit-square","fa-refresh","fa-registered","fa-renren","fa-repeat","fa-reply","fa-reply-all","fa-retweet","fa-road","fa-rocket","fa-rss","fa-rss-square","fa-rub","fa-safari","fa-scissors","fa-scribd","fa-search","fa-search-minus","fa-search-plus","fa-sellsy","fa-server","fa-share","fa-share-alt","fa-share-alt-square","fa-share-square","fa-share-square-o","fa-shield","fa-ship","fa-shirtsinbulk","fa-shopping-bag","fa-shopping-basket","fa-shopping-cart","fa-shower","fa-sign-in","fa-sign-language","fa-sign-out","fa-signal","fa-simplybuilt","fa-sitemap","fa-skyatlas","fa-skype","fa-slack","fa-sliders","fa-slideshare","fa-smile-o","fa-snapchat","fa-snapchat-ghost","fa-snapchat-square","fa-snowflake-o","fa-sort","fa-sort-alpha-asc","fa-sort-alpha-desc","fa-sort-amount-asc","fa-sort-amount-desc","fa-sort-asc","fa-sort-desc","fa-sort-numeric-asc","fa-sort-numeric-desc","fa-soundcloud","fa-space-shuttle","fa-spinner","fa-spoon","fa-spotify","fa-square","fa-square-o","fa-stack-exchange","fa-stack-overflow","fa-star","fa-star-half","fa-star-half-o","fa-star-o","fa-steam","fa-steam-square","fa-step-backward","fa-step-forward","fa-stethoscope","fa-sticky-note","fa-sticky-note-o","fa-stop","fa-stop-circle","fa-stop-circle-o","fa-street-view","fa-strikethrough","fa-stumbleupon","fa-stumbleupon-circle","fa-subscript","fa-subway","fa-suitcase","fa-sun-o","fa-superpowers","fa-superscript","fa-table","fa-tablet","fa-tachometer","fa-tag","fa-tags","fa-tasks","fa-taxi","fa-telegram","fa-television","fa-tencent-weibo","fa-terminal","fa-text-height","fa-text-width","fa-th","fa-th-large","fa-th-list","fa-themeisle","fa-thermometer-empty","fa-thermometer-full","fa-thermometer-half","fa-thermometer-quarter","fa-thermometer-three-quarters","fa-thumb-tack","fa-thumbs-down","fa-thumbs-o-down","fa-thumbs-o-up","fa-thumbs-up","fa-ticket","fa-times","fa-times-circle","fa-times-circle-o","fa-tint","fa-toggle-off","fa-toggle-on","fa-trademark","fa-train","fa-transgender","fa-transgender-alt","fa-trash","fa-trash-o","fa-tree","fa-trello","fa-tripadvisor","fa-trophy","fa-truck","fa-try","fa-tty","fa-tumblr","fa-tumblr-square","fa-twitch","fa-twitter","fa-twitter-square","fa-umbrella","fa-underline","fa-undo","fa-universal-access","fa-university","fa-unlock","fa-unlock-alt","fa-upload","fa-usb","fa-usd","fa-user","fa-user-circle","fa-user-circle-o","fa-user-md","fa-user-o","fa-user-plus","fa-user-secret","fa-user-times","fa-users","fa-venus","fa-venus-double","fa-venus-mars","fa-viacoin","fa-viadeo","fa-viadeo-square","fa-video-camera","fa-vimeo","fa-vimeo-square","fa-vine","fa-vk","fa-volume-control-phone","fa-volume-down","fa-volume-off","fa-volume-up","fa-weibo","fa-weixin","fa-whatsapp","fa-wheelchair","fa-wheelchair-alt","fa-wifi","fa-wikipedia-w","fa-window-close","fa-window-close-o","fa-window-maximize","fa-window-minimize","fa-window-restore","fa-windows","fa-wordpress","fa-wpbeginner","fa-wpexplorer","fa-wpforms","fa-wrench","fa-xing","fa-xing-square","fa-y-combinator","fa-yahoo","fa-yelp","fa-yoast","fa-youtube","fa-youtube-play","fa-youtube-square");
    }
}

if (!function_exists('get_fa_icons_full')){
    function get_fa_icons_full() {
        return array(
            'fa-glass'                               => 'f000',
            'fa-music'                               => 'f001',
            'fa-search'                              => 'f002',
            'fa-envelope-o'                          => 'f003',
            'fa-heart'                               => 'f004',
            'fa-star'                                => 'f005',
            'fa-star-o'                              => 'f006',
            'fa-user'                                => 'f007',
            'fa-film'                                => 'f008',
            'fa-th-large'                            => 'f009',
            'fa-th'                                  => 'f00a',
            'fa-th-list'                             => 'f00b',
            'fa-check'                               => 'f00c',
            'fa-times'                               => 'f00d',
            'fa-search-plus'                         => 'f00e',
            'fa-search-minus'                        => 'f010',
            'fa-power-off'                           => 'f011',
            'fa-signal'                              => 'f012',
            'fa-cog'                                 => 'f013',
            'fa-trash-o'                             => 'f014',
            'fa-home'                                => 'f015',
            'fa-file-o'                              => 'f016',
            'fa-clock-o'                             => 'f017',
            'fa-road'                                => 'f018',
            'fa-download'                            => 'f019',
            'fa-arrow-circle-o-down'                 => 'f01a',
            'fa-arrow-circle-o-up'                   => 'f01b',
            'fa-inbox'                               => 'f01c',
            'fa-play-circle-o'                       => 'f01d',
            'fa-repeat'                              => 'f01e',
            'fa-refresh'                             => 'f021',
            'fa-list-alt'                            => 'f022',
            'fa-lock'                                => 'f023',
            'fa-flag'                                => 'f024',
            'fa-headphones'                          => 'f025',
            'fa-volume-off'                          => 'f026',
            'fa-volume-down'                         => 'f027',
            'fa-volume-up'                           => 'f028',
            'fa-qrcode'                              => 'f029',
            'fa-barcode'                             => 'f02a',
            'fa-tag'                                 => 'f02b',
            'fa-tags'                                => 'f02c',
            'fa-book'                                => 'f02d',
            'fa-bookmark'                            => 'f02e',
            'fa-print'                               => 'f02f',
            'fa-camera'                              => 'f030',
            'fa-font'                                => 'f031',
            'fa-bold'                                => 'f032',
            'fa-italic'                              => 'f033',
            'fa-text-height'                         => 'f034',
            'fa-text-width'                          => 'f035',
            'fa-align-left'                          => 'f036',
            'fa-align-center'                        => 'f037',
            'fa-align-right'                         => 'f038',
            'fa-align-justify'                       => 'f039',
            'fa-list'                                => 'f03a',
            'fa-outdent'                             => 'f03b',
            'fa-indent'                              => 'f03c',
            'fa-video-camera'                        => 'f03d',
            'fa-picture-o'                           => 'f03e',
            'fa-pencil'                              => 'f040',
            'fa-map-marker'                          => 'f041',
            'fa-adjust'                              => 'f042',
            'fa-tint'                                => 'f043',
            'fa-pencil-square-o'                     => 'f044',
            'fa-share-square-o'                      => 'f045',
            'fa-check-square-o'                      => 'f046',
            'fa-arrows'                              => 'f047',
            'fa-step-backward'                       => 'f048',
            'fa-fast-backward'                       => 'f049',
            'fa-backward'                            => 'f04a',
            'fa-play'                                => 'f04b',
            'fa-pause'                               => 'f04c',
            'fa-stop'                                => 'f04d',
            'fa-forward'                             => 'f04e',
            'fa-fast-forward'                        => 'f050',
            'fa-step-forward'                        => 'f051',
            'fa-eject'                               => 'f052',
            'fa-chevron-left'                        => 'f053',
            'fa-chevron-right'                       => 'f054',
            'fa-plus-circle'                         => 'f055',
            'fa-minus-circle'                        => 'f056',
            'fa-times-circle'                        => 'f057',
            'fa-check-circle'                        => 'f058',
            'fa-question-circle'                     => 'f059',
            'fa-info-circle'                         => 'f05a',
            'fa-crosshairs'                          => 'f05b',
            'fa-times-circle-o'                      => 'f05c',
            'fa-check-circle-o'                      => 'f05d',
            'fa-ban'                                 => 'f05e',
            'fa-arrow-left'                          => 'f060',
            'fa-arrow-right'                         => 'f061',
            'fa-arrow-up'                            => 'f062',
            'fa-arrow-down'                          => 'f063',
            'fa-share'                               => 'f064',
            'fa-expand'                              => 'f065',
            'fa-compress'                            => 'f066',
            'fa-plus'                                => 'f067',
            'fa-minus'                               => 'f068',
            'fa-asterisk'                            => 'f069',
            'fa-exclamation-circle'                  => 'f06a',
            'fa-gift'                                => 'f06b',
            'fa-leaf'                                => 'f06c',
            'fa-fire'                                => 'f06d',
            'fa-eye'                                 => 'f06e',
            'fa-eye-slash'                           => 'f070',
            'fa-exclamation-triangle'                => 'f071',
            'fa-plane'                               => 'f072',
            'fa-calendar'                            => 'f073',
            'fa-random'                              => 'f074',
            'fa-comment'                             => 'f075',
            'fa-magnet'                              => 'f076',
            'fa-chevron-up'                          => 'f077',
            'fa-chevron-down'                        => 'f078',
            'fa-retweet'                             => 'f079',
            'fa-shopping-cart'                       => 'f07a',
            'fa-folder'                              => 'f07b',
            'fa-folder-open'                         => 'f07c',
            'fa-arrows-v'                            => 'f07d',
            'fa-arrows-h'                            => 'f07e',
            'fa-bar-chart'                           => 'f080',
            'fa-twitter-square'                      => 'f081',
            'fa-facebook-square'                     => 'f082',
            'fa-camera-retro'                        => 'f083',
            'fa-key'                                 => 'f084',
            'fa-cogs'                                => 'f085',
            'fa-comments'                            => 'f086',
            'fa-thumbs-o-up'                         => 'f087',
            'fa-thumbs-o-down'                       => 'f088',
            'fa-star-half'                           => 'f089',
            'fa-heart-o'                             => 'f08a',
            'fa-sign-out'                            => 'f08b',
            'fa-linkedin-square'                     => 'f08c',
            'fa-thumb-tack'                          => 'f08d',
            'fa-external-link'                       => 'f08e',
            'fa-sign-in'                             => 'f090',
            'fa-trophy'                              => 'f091',
            'fa-github-square'                       => 'f092',
            'fa-upload'                              => 'f093',
            'fa-lemon-o'                             => 'f094',
            'fa-phone'                               => 'f095',
            'fa-square-o'                            => 'f096',
            'fa-bookmark-o'                          => 'f097',
            'fa-phone-square'                        => 'f098',
            'fa-twitter'                             => 'f099',
            'fa-facebook'                            => 'f09a',
            'fa-github'                              => 'f09b',
            'fa-unlock'                              => 'f09c',
            'fa-credit-card'                         => 'f09d',
            'fa-rss'                                 => 'f09e',
            'fa-hdd-o'                               => 'f0a0',
            'fa-bullhorn'                            => 'f0a1',
            'fa-bell'                                => 'f0f3',
            'fa-certificate'                         => 'f0a3',
            'fa-hand-o-right'                        => 'f0a4',
            'fa-hand-o-left'                         => 'f0a5',
            'fa-hand-o-up'                           => 'f0a6',
            'fa-hand-o-down'                         => 'f0a7',
            'fa-arrow-circle-left'                   => 'f0a8',
            'fa-arrow-circle-right'                  => 'f0a9',
            'fa-arrow-circle-up'                     => 'f0aa',
            'fa-arrow-circle-down'                   => 'f0ab',
            'fa-globe'                               => 'f0ac',
            'fa-wrench'                              => 'f0ad',
            'fa-tasks'                               => 'f0ae',
            'fa-filter'                              => 'f0b0',
            'fa-briefcase'                           => 'f0b1',
            'fa-arrows-alt'                          => 'f0b2',
            'fa-users'                               => 'f0c0',
            'fa-link'                                => 'f0c1',
            'fa-cloud'                               => 'f0c2',
            'fa-flask'                               => 'f0c3',
            'fa-scissors'                            => 'f0c4',
            'fa-files-o'                             => 'f0c5',
            'fa-paperclip'                           => 'f0c6',
            'fa-floppy-o'                            => 'f0c7',
            'fa-square'                              => 'f0c8',
            'fa-bars'                                => 'f0c9',
            'fa-list-ul'                             => 'f0ca',
            'fa-list-ol'                             => 'f0cb',
            'fa-strikethrough'                       => 'f0cc',
            'fa-underline'                           => 'f0cd',
            'fa-table'                               => 'f0ce',
            'fa-magic'                               => 'f0d0',
            'fa-truck'                               => 'f0d1',
            'fa-pinterest'                           => 'f0d2',
            'fa-pinterest-square'                    => 'f0d3',
            'fa-google-plus-square'                  => 'f0d4',
            'fa-google-plus'                         => 'f0d5',
            'fa-money'                               => 'f0d6',
            'fa-caret-down'                          => 'f0d7',
            'fa-caret-up'                            => 'f0d8',
            'fa-caret-left'                          => 'f0d9',
            'fa-caret-right'                         => 'f0da',
            'fa-columns'                             => 'f0db',
            'fa-sort'                                => 'f0dc',
            'fa-sort-desc'                           => 'f0dd',
            'fa-sort-asc'                            => 'f0de',
            'fa-envelope'                            => 'f0e0',
            'fa-linkedin'                            => 'f0e1',
            'fa-undo'                                => 'f0e2',
            'fa-gavel'                               => 'f0e3',
            'fa-tachometer'                          => 'f0e4',
            'fa-comment-o'                           => 'f0e5',
            'fa-comments-o'                          => 'f0e6',
            'fa-bolt'                                => 'f0e7',
            'fa-sitemap'                             => 'f0e8',
            'fa-umbrella'                            => 'f0e9',
            'fa-clipboard'                           => 'f0ea',
            'fa-lightbulb-o'                         => 'f0eb',
            'fa-exchange'                            => 'f0ec',
            'fa-cloud-download'                      => 'f0ed',
            'fa-cloud-upload'                        => 'f0ee',
            'fa-user-md'                             => 'f0f0',
            'fa-stethoscope'                         => 'f0f1',
            'fa-suitcase'                            => 'f0f2',
            'fa-bell-o'                              => 'f0a2',
            'fa-coffee'                              => 'f0f4',
            'fa-cutlery'                             => 'f0f5',
            'fa-file-text-o'                         => 'f0f6',
            'fa-building-o'                          => 'f0f7',
            'fa-hospital-o'                          => 'f0f8',
            'fa-ambulance'                           => 'f0f9',
            'fa-medkit'                              => 'f0fa',
            'fa-fighter-jet'                         => 'f0fb',
            'fa-beer'                                => 'f0fc',
            'fa-h-square'                            => 'f0fd',
            'fa-plus-square'                         => 'f0fe',
            'fa-angle-double-left'                   => 'f100',
            'fa-angle-double-right'                  => 'f101',
            'fa-angle-double-up'                     => 'f102',
            'fa-angle-double-down'                   => 'f103',
            'fa-angle-left'                          => 'f104',
            'fa-angle-right'                         => 'f105',
            'fa-angle-up'                            => 'f106',
            'fa-angle-down'                          => 'f107',
            'fa-desktop'                             => 'f108',
            'fa-laptop'                              => 'f109',
            'fa-tablet'                              => 'f10a',
            'fa-mobile'                              => 'f10b',
            'fa-circle-o'                            => 'f10c',
            'fa-quote-left'                          => 'f10d',
            'fa-quote-right'                         => 'f10e',
            'fa-spinner'                             => 'f110',
            'fa-circle'                              => 'f111',
            'fa-reply'                               => 'f112',
            'fa-github-alt'                          => 'f113',
            'fa-folder-o'                            => 'f114',
            'fa-folder-open-o'                       => 'f115',
            'fa-smile-o'                             => 'f118',
            'fa-frown-o'                             => 'f119',
            'fa-meh-o'                               => 'f11a',
            'fa-gamepad'                             => 'f11b',
            'fa-keyboard-o'                          => 'f11c',
            'fa-flag-o'                              => 'f11d',
            'fa-flag-checkered'                      => 'f11e',
            'fa-terminal'                            => 'f120',
            'fa-code'                                => 'f121',
            'fa-reply-all'                           => 'f122',
            'fa-star-half-o'                         => 'f123',
            'fa-location-arrow'                      => 'f124',
            'fa-crop'                                => 'f125',
            'fa-code-fork'                           => 'f126',
            'fa-chain-broken'                        => 'f127',
            'fa-question'                            => 'f128',
            'fa-info'                                => 'f129',
            'fa-exclamation'                         => 'f12a',
            'fa-superscript'                         => 'f12b',
            'fa-subscript'                           => 'f12c',
            'fa-eraser'                              => 'f12d',
            'fa-puzzle-piece'                        => 'f12e',
            'fa-microphone'                          => 'f130',
            'fa-microphone-slash'                    => 'f131',
            'fa-shield'                              => 'f132',
            'fa-calendar-o'                          => 'f133',
            'fa-fire-extinguisher'                   => 'f134',
            'fa-rocket'                              => 'f135',
            'fa-maxcdn'                              => 'f136',
            'fa-chevron-circle-left'                 => 'f137',
            'fa-chevron-circle-right'                => 'f138',
            'fa-chevron-circle-up'                   => 'f139',
            'fa-chevron-circle-down'                 => 'f13a',
            'fa-html5'                               => 'f13b',
            'fa-css3'                                => 'f13c',
            'fa-anchor'                              => 'f13d',
            'fa-unlock-alt'                          => 'f13e',
            'fa-bullseye'                            => 'f140',
            'fa-ellipsis-h'                          => 'f141',
            'fa-ellipsis-v'                          => 'f142',
            'fa-rss-square'                          => 'f143',
            'fa-play-circle'                         => 'f144',
            'fa-ticket'                              => 'f145',
            'fa-minus-square'                        => 'f146',
            'fa-minus-square-o'                      => 'f147',
            'fa-level-up'                            => 'f148',
            'fa-level-down'                          => 'f149',
            'fa-check-square'                        => 'f14a',
            'fa-pencil-square'                       => 'f14b',
            'fa-external-link-square'                => 'f14c',
            'fa-share-square'                        => 'f14d',
            'fa-compass'                             => 'f14e',
            'fa-caret-square-o-down'                 => 'f150',
            'fa-caret-square-o-up'                   => 'f151',
            'fa-caret-square-o-right'                => 'f152',
            'fa-eur'                                 => 'f153',
            'fa-gbp'                                 => 'f154',
            'fa-usd'                                 => 'f155',
            'fa-inr'                                 => 'f156',
            'fa-jpy'                                 => 'f157',
            'fa-rub'                                 => 'f158',
            'fa-krw'                                 => 'f159',
            'fa-btc'                                 => 'f15a',
            'fa-file'                                => 'f15b',
            'fa-file-text'                           => 'f15c',
            'fa-sort-alpha-asc'                      => 'f15d',
            'fa-sort-alpha-desc'                     => 'f15e',
            'fa-sort-amount-asc'                     => 'f160',
            'fa-sort-amount-desc'                    => 'f161',
            'fa-sort-numeric-asc'                    => 'f162',
            'fa-sort-numeric-desc'                   => 'f163',
            'fa-thumbs-up'                           => 'f164',
            'fa-thumbs-down'                         => 'f165',
            'fa-youtube-square'                      => 'f166',
            'fa-youtube'                             => 'f167',
            'fa-xing'                                => 'f168',
            'fa-xing-square'                         => 'f169',
            'fa-youtube-play'                        => 'f16a',
            'fa-dropbox'                             => 'f16b',
            'fa-stack-overflow'                      => 'f16c',
            'fa-instagram'                           => 'f16d',
            'fa-flickr'                              => 'f16e',
            'fa-adn'                                 => 'f170',
            'fa-bitbucket'                           => 'f171',
            'fa-bitbucket-square'                    => 'f172',
            'fa-tumblr'                              => 'f173',
            'fa-tumblr-square'                       => 'f174',
            'fa-long-arrow-down'                     => 'f175',
            'fa-long-arrow-up'                       => 'f176',
            'fa-long-arrow-left'                     => 'f177',
            'fa-long-arrow-right'                    => 'f178',
            'fa-apple'                               => 'f179',
            'fa-windows'                             => 'f17a',
            'fa-android'                             => 'f17b',
            'fa-linux'                               => 'f17c',
            'fa-dribbble'                            => 'f17d',
            'fa-skype'                               => 'f17e',
            'fa-foursquare'                          => 'f180',
            'fa-trello'                              => 'f181',
            'fa-female'                              => 'f182',
            'fa-male'                                => 'f183',
            'fa-gratipay'                            => 'f184',
            'fa-sun-o'                               => 'f185',
            'fa-moon-o'                              => 'f186',
            'fa-archive'                             => 'f187',
            'fa-bug'                                 => 'f188',
            'fa-vk'                                  => 'f189',
            'fa-weibo'                               => 'f18a',
            'fa-renren'                              => 'f18b',
            'fa-pagelines'                           => 'f18c',
            'fa-stack-exchange'                      => 'f18d',
            'fa-arrow-circle-o-right'                => 'f18e',
            'fa-arrow-circle-o-left'                 => 'f190',
            'fa-caret-square-o-left'                 => 'f191',
            'fa-dot-circle-o'                        => 'f192',
            'fa-wheelchair'                          => 'f193',
            'fa-vimeo-square'                        => 'f194',
            'fa-try'                                 => 'f195',
            'fa-plus-square-o'                       => 'f196',
            'fa-space-shuttle'                       => 'f197',
            'fa-slack'                               => 'f198',
            'fa-envelope-square'                     => 'f199',
            'fa-wordpress'                           => 'f19a',
            'fa-openid'                              => 'f19b',
            'fa-university'                          => 'f19c',
            'fa-graduation-cap'                      => 'f19d',
            'fa-yahoo'                               => 'f19e',
            'fa-google'                              => 'f1a0',
            'fa-reddit'                              => 'f1a1',
            'fa-reddit-square'                       => 'f1a2',
            'fa-stumbleupon-circle'                  => 'f1a3',
            'fa-stumbleupon'                         => 'f1a4',
            'fa-delicious'                           => 'f1a5',
            'fa-digg'                                => 'f1a6',
            'fa-pied-piper-pp'                       => 'f1a7',
            'fa-pied-piper-alt'                      => 'f1a8',
            'fa-drupal'                              => 'f1a9',
            'fa-joomla'                              => 'f1aa',
            'fa-language'                            => 'f1ab',
            'fa-fax'                                 => 'f1ac',
            'fa-building'                            => 'f1ad',
            'fa-child'                               => 'f1ae',
            'fa-paw'                                 => 'f1b0',
            'fa-spoon'                               => 'f1b1',
            'fa-cube'                                => 'f1b2',
            'fa-cubes'                               => 'f1b3',
            'fa-behance'                             => 'f1b4',
            'fa-behance-square'                      => 'f1b5',
            'fa-steam'                               => 'f1b6',
            'fa-steam-square'                        => 'f1b7',
            'fa-recycle'                             => 'f1b8',
            'fa-car'                                 => 'f1b9',
            'fa-taxi'                                => 'f1ba',
            'fa-tree'                                => 'f1bb',
            'fa-spotify'                             => 'f1bc',
            'fa-deviantart'                          => 'f1bd',
            'fa-soundcloud'                          => 'f1be',
            'fa-database'                            => 'f1c0',
            'fa-file-pdf-o'                          => 'f1c1',
            'fa-file-word-o'                         => 'f1c2',
            'fa-file-excel-o'                        => 'f1c3',
            'fa-file-powerpoint-o'                   => 'f1c4',
            'fa-file-image-o'                        => 'f1c5',
            'fa-file-archive-o'                      => 'f1c6',
            'fa-file-audio-o'                        => 'f1c7',
            'fa-file-video-o'                        => 'f1c8',
            'fa-file-code-o'                         => 'f1c9',
            'fa-vine'                                => 'f1ca',
            'fa-codepen'                             => 'f1cb',
            'fa-jsfiddle'                            => 'f1cc',
            'fa-life-ring'                           => 'f1cd',
            'fa-circle-o-notch'                      => 'f1ce',
            'fa-rebel'                               => 'f1d0',
            'fa-empire'                              => 'f1d1',
            'fa-git-square'                          => 'f1d2',
            'fa-git'                                 => 'f1d3',
            'fa-hacker-news'                         => 'f1d4',
            'fa-tencent-weibo'                       => 'f1d5',
            'fa-qq'                                  => 'f1d6',
            'fa-weixin'                              => 'f1d7',
            'fa-paper-plane'                         => 'f1d8',
            'fa-paper-plane-o'                       => 'f1d9',
            'fa-history'                             => 'f1da',
            'fa-circle-thin'                         => 'f1db',
            'fa-header'                              => 'f1dc',
            'fa-paragraph'                           => 'f1dd',
            'fa-sliders'                             => 'f1de',
            'fa-share-alt'                           => 'f1e0',
            'fa-share-alt-square'                    => 'f1e1',
            'fa-bomb'                                => 'f1e2',
            'fa-futbol-o'                            => 'f1e3',
            'fa-tty'                                 => 'f1e4',
            'fa-binoculars'                          => 'f1e5',
            'fa-plug'                                => 'f1e6',
            'fa-slideshare'                          => 'f1e7',
            'fa-twitch'                              => 'f1e8',
            'fa-yelp'                                => 'f1e9',
            'fa-newspaper-o'                         => 'f1ea',
            'fa-wifi'                                => 'f1eb',
            'fa-calculator'                          => 'f1ec',
            'fa-paypal'                              => 'f1ed',
            'fa-google-wallet'                       => 'f1ee',
            'fa-cc-visa'                             => 'f1f0',
            'fa-cc-mastercard'                       => 'f1f1',
            'fa-cc-discover'                         => 'f1f2',
            'fa-cc-amex'                             => 'f1f3',
            'fa-cc-paypal'                           => 'f1f4',
            'fa-cc-stripe'                           => 'f1f5',
            'fa-bell-slash'                          => 'f1f6',
            'fa-bell-slash-o'                        => 'f1f7',
            'fa-trash'                               => 'f1f8',
            'fa-copyright'                           => 'f1f9',
            'fa-at'                                  => 'f1fa',
            'fa-eyedropper'                          => 'f1fb',
            'fa-paint-brush'                         => 'f1fc',
            'fa-birthday-cake'                       => 'f1fd',
            'fa-area-chart'                          => 'f1fe',
            'fa-pie-chart'                           => 'f200',
            'fa-line-chart'                          => 'f201',
            'fa-lastfm'                              => 'f202',
            'fa-lastfm-square'                       => 'f203',
            'fa-toggle-off'                          => 'f204',
            'fa-toggle-on'                           => 'f205',
            'fa-bicycle'                             => 'f206',
            'fa-bus'                                 => 'f207',
            'fa-ioxhost'                             => 'f208',
            'fa-angellist'                           => 'f209',
            'fa-cc'                                  => 'f20a',
            'fa-ils'                                 => 'f20b',
            'fa-meanpath'                            => 'f20c',
            'fa-buysellads'                          => 'f20d',
            'fa-connectdevelop'                      => 'f20e',
            'fa-dashcube'                            => 'f210',
            'fa-forumbee'                            => 'f211',
            'fa-leanpub'                             => 'f212',
            'fa-sellsy'                              => 'f213',
            'fa-shirtsinbulk'                        => 'f214',
            'fa-simplybuilt'                         => 'f215',
            'fa-skyatlas'                            => 'f216',
            'fa-cart-plus'                           => 'f217',
            'fa-cart-arrow-down'                     => 'f218',
            'fa-diamond'                             => 'f219',
            'fa-ship'                                => 'f21a',
            'fa-user-secret'                         => 'f21b',
            'fa-motorcycle'                          => 'f21c',
            'fa-street-view'                         => 'f21d',
            'fa-heartbeat'                           => 'f21e',
            'fa-venus'                               => 'f221',
            'fa-mars'                                => 'f222',
            'fa-mercury'                             => 'f223',
            'fa-transgender'                         => 'f224',
            'fa-transgender-alt'                     => 'f225',
            'fa-venus-double'                        => 'f226',
            'fa-mars-double'                         => 'f227',
            'fa-venus-mars'                          => 'f228',
            'fa-mars-stroke'                         => 'f229',
            'fa-mars-stroke-v'                       => 'f22a',
            'fa-mars-stroke-h'                       => 'f22b',
            'fa-neuter'                              => 'f22c',
            'fa-genderless'                          => 'f22d',
            'fa-facebook-official'                   => 'f230',
            'fa-pinterest-p'                         => 'f231',
            'fa-whatsapp'                            => 'f232',
            'fa-server'                              => 'f233',
            'fa-user-plus'                           => 'f234',
            'fa-user-times'                          => 'f235',
            'fa-bed'                                 => 'f236',
            'fa-viacoin'                             => 'f237',
            'fa-train'                               => 'f238',
            'fa-subway'                              => 'f239',
            'fa-medium'                              => 'f23a',
            'fa-y-combinator'                        => 'f23b',
            'fa-optin-monster'                       => 'f23c',
            'fa-opencart'                            => 'f23d',
            'fa-expeditedssl'                        => 'f23e',
            'fa-battery-full'                        => 'f240',
            'fa-battery-three-quarters'              => 'f241',
            'fa-battery-half'                        => 'f242',
            'fa-battery-quarter'                     => 'f243',
            'fa-battery-empty'                       => 'f244',
            'fa-mouse-pointer'                       => 'f245',
            'fa-i-cursor'                            => 'f246',
            'fa-object-group'                        => 'f247',
            'fa-object-ungroup'                      => 'f248',
            'fa-sticky-note'                         => 'f249',
            'fa-sticky-note-o'                       => 'f24a',
            'fa-cc-jcb'                              => 'f24b',
            'fa-cc-diners-club'                      => 'f24c',
            'fa-clone'                               => 'f24d',
            'fa-balance-scale'                       => 'f24e',
            'fa-hourglass-o'                         => 'f250',
            'fa-hourglass-start'                     => 'f251',
            'fa-hourglass-half'                      => 'f252',
            'fa-hourglass-end'                       => 'f253',
            'fa-hourglass'                           => 'f254',
            'fa-hand-rock-o'                         => 'f255',
            'fa-hand-paper-o'                        => 'f256',
            'fa-hand-scissors-o'                     => 'f257',
            'fa-hand-lizard-o'                       => 'f258',
            'fa-hand-spock-o'                        => 'f259',
            'fa-hand-pointer-o'                      => 'f25a',
            'fa-hand-peace-o'                        => 'f25b',
            'fa-trademark'                           => 'f25c',
            'fa-registered'                          => 'f25d',
            'fa-creative-commons'                    => 'f25e',
            'fa-gg'                                  => 'f260',
            'fa-gg-circle'                           => 'f261',
            'fa-tripadvisor'                         => 'f262',
            'fa-odnoklassniki'                       => 'f263',
            'fa-odnoklassniki-square'                => 'f264',
            'fa-get-pocket'                          => 'f265',
            'fa-wikipedia-w'                         => 'f266',
            'fa-safari'                              => 'f267',
            'fa-chrome'                              => 'f268',
            'fa-firefox'                             => 'f269',
            'fa-opera'                               => 'f26a',
            'fa-internet-explorer'                   => 'f26b',
            'fa-television'                          => 'f26c',
            'fa-contao'                              => 'f26d',
            'fa-500px'                               => 'f26e',
            'fa-amazon'                              => 'f270',
            'fa-calendar-plus-o'                     => 'f271',
            'fa-calendar-minus-o'                    => 'f272',
            'fa-calendar-times-o'                    => 'f273',
            'fa-calendar-check-o'                    => 'f274',
            'fa-industry'                            => 'f275',
            'fa-map-pin'                             => 'f276',
            'fa-map-signs'                           => 'f277',
            'fa-map-o'                               => 'f278',
            'fa-map'                                 => 'f279',
            'fa-commenting'                          => 'f27a',
            'fa-commenting-o'                        => 'f27b',
            'fa-houzz'                               => 'f27c',
            'fa-vimeo'                               => 'f27d',
            'fa-black-tie'                           => 'f27e',
            'fa-fonticons'                           => 'f280',
            'fa-reddit-alien'                        => 'f281',
            'fa-edge'                                => 'f282',
            'fa-credit-card-alt'                     => 'f283',
            'fa-codiepie'                            => 'f284',
            'fa-modx'                                => 'f285',
            'fa-fort-awesome'                        => 'f286',
            'fa-usb'                                 => 'f287',
            'fa-product-hunt'                        => 'f288',
            'fa-mixcloud'                            => 'f289',
            'fa-scribd'                              => 'f28a',
            'fa-pause-circle'                        => 'f28b',
            'fa-pause-circle-o'                      => 'f28c',
            'fa-stop-circle'                         => 'f28d',
            'fa-stop-circle-o'                       => 'f28e',
            'fa-shopping-bag'                        => 'f290',
            'fa-shopping-basket'                     => 'f291',
            'fa-hashtag'                             => 'f292',
            'fa-bluetooth'                           => 'f293',
            'fa-bluetooth-b'                         => 'f294',
            'fa-percent'                             => 'f295',
            'fa-gitlab'                              => 'f296',
            'fa-wpbeginner'                          => 'f297',
            'fa-wpforms'                             => 'f298',
            'fa-envira'                              => 'f299',
            'fa-universal-access'                    => 'f29a',
            'fa-wheelchair-alt'                      => 'f29b',
            'fa-question-circle-o'                   => 'f29c',
            'fa-blind'                               => 'f29d',
            'fa-audio-description'                   => 'f29e',
            'fa-volume-control-phone'                => 'f2a0',
            'fa-braille'                             => 'f2a1',
            'fa-assistive-listening-systems'         => 'f2a2',
            'fa-american-sign-language-interpreting' => 'f2a3',
            'fa-deaf'                                => 'f2a4',
            'fa-glide'                               => 'f2a5',
            'fa-glide-g'                             => 'f2a6',
            'fa-sign-language'                       => 'f2a7',
            'fa-low-vision'                          => 'f2a8',
            'fa-viadeo'                              => 'f2a9',
            'fa-viadeo-square'                       => 'f2aa',
            'fa-snapchat'                            => 'f2ab',
            'fa-snapchat-ghost'                      => 'f2ac',
            'fa-snapchat-square'                     => 'f2ad',
            'fa-pied-piper'                          => 'f2ae',
            'fa-first-order'                         => 'f2b0',
            'fa-yoast'                               => 'f2b1',
            'fa-themeisle'                           => 'f2b2',
            'fa-google-plus-official'                => 'f2b3',
            'fa-font-awesome'                        => 'f2b4'
        );
    }
}

if (!function_exists('get_cat_icon')){
    function get_cat_icon($term_id){
        $icon= get_term_meta($term_id, 'category_icon', true);
        return !empty($icon) ? $icon : '';
    }
}

if (!function_exists('atbdp_sanitize_array')){
    /**
     * It sanitize a multi-dimensional array
     * @param array &$array The array of the data to sanitize
     * @return mixed
     */
    function atbdp_sanitize_array(&$array ) {

        foreach ($array as &$value) {
            if( !is_array($value) ) {
                // sanitize if value is not an array
                $value = sanitize_text_field($value);
            }else {
                // go inside this function again
                atbdp_sanitize_array($value);
            }
        }
        return $array;
    }
}

if (!function_exists('is_directoria_active')){
    /**
     * It checks if the Directorist theme is installed currently.
     * @return bool It returns true if the directorist theme is active currently. False otherwise.
     */
    function  is_directoria_active(){
        return wp_get_theme()->get_stylesheet() === 'directoria';
    }
}

if (!function_exists('is_multiple_images_active')){
    /**
     * It checks if the Directorist Multiple images Extension is active and enabled
     * @return bool It returns true if the Directorist Multiple images Extension is active and enabled
     */
    function  is_multiple_images_active(){
        $enable = get_directorist_option('enable_multiple_image', 0);
        $active = in_array( 'directorist-multiple-image/bd-multiple-image.php', (array) get_option( 'active_plugins', array() ) ) ;

        return ((1==$enable) && $active); // plugin is active and enabled
    }
}


if (!function_exists('is_business_hour_active')){
    /**
     * It checks if the Directorist Business Hour Extension is active and enabled
     * @return bool It returns true if the Directorist Business Hour Extension is active and enabled
     */
    function  is_business_hour_active(){
        $enable = get_directorist_option('enable_business_hour');
        $active = in_array( 'directorist-business-hour/bd-business-hour.php', (array) get_option( 'active_plugins', array() ) ) ;
        return ($enable && $active); // plugin is active and enabled
    }
}

if (!function_exists('is_empty_v')){
    /**
     * It checks if the value of the given data ( array or string etc ) is empty
     * @param array $value The value to check if it is empty
     * @return bool It returns true if the value of the given data is empty, and false otherwise.
     */
    function is_empty_v($value) {
        if (!is_array($value)) return empty($value);
        foreach($value as $key => $val) {
            if (!empty($val))
                return false;
        }
        return true;
    }
}

if (!function_exists('atbdp_get_paged_num')){
    /**
     * Get current page number for the pagination.
     *
     * @since    1.0.0
     *
     * @return    int    $paged    The current page number for the pagination.
     */
    function atbdp_get_paged_num() {

        global $paged;

        if( get_query_var('paged') ) {
            $paged = get_query_var('paged');
        } else if( get_query_var('page') ) {
            $paged = get_query_var('page');
        } else {
            $paged = 1;
        }

        return absint( $paged );

    }


}

if (!function_exists('valid_js_nonce')) {
    /**
     * It checks if the nonce is set and valid
     * @return bool it returns true if the nonce is valid and false otherwise
     */
    function valid_js_nonce()
    {
        if ( !empty($_POST['atbdp_nonce_js']) && (wp_verify_nonce($_POST['atbdp_nonce_js'], 'atbdp_nonce_action_js')))
            return true;
        return false;
    }
}

if (!function_exists('atbdp_get_featured_settings_array')) {
    /**
     * It fetch all the settings related to featured listing.
     * @return array it returns an array of settings related to featured listings.
     */
    function atbdp_get_featured_settings_array()
    {
        return array(
            'active'        => get_directorist_option('enable_featured_listing'),
            'label'         => get_directorist_option('featured_listing_title'),
            'desc'          => get_directorist_option('featured_listing_desc'),
            'price'         => get_directorist_option('featured_listing_price'),
            'show_ribbon'   => get_directorist_option('show_featured_ribbon'),
        );
    }
}

if (!function_exists('atbdp_only_logged_in_user')){

    /**
     * It informs a user to logged in and returns false if the user is not logged in.
     * if a user is not logged in.
     * @param string $message
     * @return bool It returns true if a user is logged in and false otherwise. Besides, it display a message to non-logged in users
     */
    function atbdp_is_user_logged_in($message=''){
        if (!is_user_logged_in()) {
            // user not logged in;
            $error_message = (empty($message))
                ? sprintf(
                    __('You need to be logged in to view the content of this page. You can login %s.', ATBDP_TEXTDOMAIN),
                    "<a href='" . wp_login_url() . "'> " . __('Here', ATBDP_TEXTDOMAIN) . "</a>"
                )
                : $message;
            ?>
            <section class="directory_wrapper single_area">
                <div class="<?php echo is_directoria_active() ? 'container' : ' container-fluid'; ?>">
                    <div class="row">
                        <div class="col-md-12">
                            <?php ATBDP()->helper->show_login_message($error_message); ?>
                        </div>
                    </div>
                </div> <!--ends container-fluid-->
            </section>
            <?php
            return false;
        }
        return true;
    }
}

if (!function_exists('atbdp_get_months')){
    /**
     * Get an array of translatable month names
     * @since    3.1.0
     * @return array
     */
    function atbdp_get_months(){
        return array(
            __( "Jan", ATBDP_TEXTDOMAIN ),
            __( "Feb", ATBDP_TEXTDOMAIN ),
            __( "Mar", ATBDP_TEXTDOMAIN ),
            __( "Apr", ATBDP_TEXTDOMAIN ),
            __( "May", ATBDP_TEXTDOMAIN ),
            __( "Jun", ATBDP_TEXTDOMAIN ),
            __( "Jul", ATBDP_TEXTDOMAIN ),
            __( "Aug", ATBDP_TEXTDOMAIN ),
            __( "Sep", ATBDP_TEXTDOMAIN ),
            __( "Oct", ATBDP_TEXTDOMAIN ),
            __( "Nov", ATBDP_TEXTDOMAIN ),
            __( "Dec", ATBDP_TEXTDOMAIN )
        );
    }
}

if (!function_exists('calc_listing_expiry_date')){
    /**
     * Calculate listing expiry date from the given date
     *
     * @since    3.1.0
     *
     * @param    string   $start_date    Date from which the expiry date should be calculated.
     * @return   string   $date          It returns expiry date in the mysql date format
     */
    function calc_listing_expiry_date( $start_date = NULL ) {

        $exp_days = get_directorist_option('listing_expire_in_days', 999, 999);
        // Current time
        $start_date = !empty($start_date) ? $start_date : current_time( 'mysql' );
        // Calculate new date
        $date = new DateTime( $start_date );
        $date->add( new DateInterval( "P{$exp_days}D" ) ); // set the interval in days
        return $date->format( 'Y-m-d H:i:s' );

    }
}

if (!function_exists('get_date_in_mysql_format')){
    /**
     * It converts a date array to MySQL date format (Y-m-d H:i:s).
     *
     * @since    3.1.0
     *
     * @param    array    $date    Array of date values.
        eg. array(
                'year'  => 0,
                'month' => 0,
                'day'   => 0,
                'hour'  => 0,
                'min'   => 0,
                'sec'   => 0
        );
     * @return   string   $date    Formatted MySQL date string.
     */
    function get_date_in_mysql_format( $date ) {

        $defaults = array(
            'year'  => 0,
            'month' => 0,
            'day'   => 0,
            'hour'  => 0,
            'min'   => 0,
            'sec'   => 0
        );
        $date = wp_parse_args($date, $defaults  );

        $year = (int) $date['year'];
        $year = str_pad( $year, 4, '0', STR_PAD_RIGHT );

        $month = (int) $date['month'];
        $month = max( 1, min( 12, $month ) );

        $day = (int) $date['day'];
        $day = max( 1, min( 31, $day ) );

        $hour = (int) $date['hour'];
        $hour = max( 1, min( 24, $hour ) );

        $min = (int) $date['min'];
        $min = max( 0, min( 59, $min ) );

        $sec = (int) $date['sec'];
        $sec = max( 0, min( 59, $sec ) );

        return sprintf( '%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $min, $sec );

    }
}

if (!function_exists('atbdp_parse_mysql_date')){
    /**
     * Parse MySQL date format.
     *
     * @since    3.1.0
     *
     * @param    string    $date    MySQL date string.
     * @return   array     $date    Array of date values.
     */
    function atbdp_parse_mysql_date( $date ) {

        $date = preg_split( '([^0-9])', $date );

        return array(
            'year'  => $date[0],
            'month' => $date[1],
            'day'   => $date[2],
            'hour'  => $date[3],
            'min'   => $date[4],
            'sec'   => $date[5]
        );

    }
}

if (!function_exists('currency_has_decimal')){
    /**
     * Check if currency has decimals.
     * @param  string $currency
     * @return bool
     */
     function currency_has_decimals( $currency ) {
        if ( in_array( $currency, array( 'RIAL', 'SAR', 'HUF', 'JPY', 'TWD' ) ) ) {
            return false;
        }

        return true;
    }
}

/**
 * Print formatted Price inside a p tag
 *
 * @param int|string $price The price amount to display
 * @param bool $disable_price whether displaying price is enabled or disabled
 * @param string $currency The name of the currency
 * @param string $symbol currency symbol
 * @param string $c_position currency position
 * @param bool $echo Whether to Print value or to Return value. Default is printing value.
 * @return mixed
 */
function atbdp_display_price($price='', $disable_price=false, $currency='', $symbol='', $c_position='', $echo=true){
    if (empty($price) || $disable_price) return null; // vail if the price is empty or price display is disabled.

        $before = ''; $after = '';
        if(empty($c_position)){
            $c_position = get_directorist_option('g_currency_position');
        }
        if(empty($currency)){
            $currency = get_directorist_option('g_currency', 'USD');
        }
        if(empty($symbol)){
            $symbol = atbdp_currency_symbol($currency);
        }

        ('after' == $c_position) ? $after = $symbol : $before = $symbol;
        $price = $before.atbdp_format_amount($price).$after;
        $p = sprintf("<p class='listing_price'>%s: %s</p>", __('Price', ATBDP_TEXTDOMAIN), $price );
        if ($echo){ echo $p; }else{ return $p; }

}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $var Data to sanitize.
 * @return string|array
 */
function directorist_clean( $var ) {
    if ( is_array( $var ) ) {
        return array_map( 'directorist_clean', $var );
    } else {
        return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
    }
}