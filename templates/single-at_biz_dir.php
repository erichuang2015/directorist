<?php
$ATBDP = ATBDP();
$lf= get_post_meta($post->ID, '_listing_info', true);
$listing_info = (!empty($lf))? aazztech_enc_unserialize($lf) : array();
$attachment_ids= (!empty($listing_info['attachment_id'])) ? $listing_info['attachment_id'] : array();

$image_links = array(); // define a link placeholder variable
foreach ($attachment_ids as $id){
    $image_links[$id]= wp_get_attachment_image_src($id, 'full')[0]; // store the attachment id and url
}

//$first_image = (!empty($image_links)) ? array_shift($image_links) : '';


extract($listing_info);
/*Code for Business Hour Extensions*/
/*@todo; Make business hour settings compatible to our new settings panel. It is good to prefix all settings of extensions with their prefix*/
$enable_business_hour = get_directorist_option('enable_business_hour', 'no'); // yes or no
$enable_bh_on_page = get_directorist_option('enable_bh_on_page', 'no' ); // yes or no
$text247 = get_directorist_option('text247',  __('Open 24/7', ATBDP_TEXTDOMAIN)); // text for 24/7 type listing
$business_hour_title = get_directorist_option('business_hour_title',  __('Business Hour', ATBDP_TEXTDOMAIN)); // text Business Hour Title
$business_hours = !empty($listing_info['bdbh']) ? atbdp_sanitize_array($listing_info['bdbh']) : array(); // arrays of days and times if exist
$bdbh_settings = !empty($listing_info['bdbh_settings']) ? extract(atbdp_sanitize_array($listing_info['bdbh_settings'])) : array();
/*Code for Business Hour Extensions*/


$manual_lat = (!empty($manual_lat)) ? floatval($manual_lat) : false;
$manual_lng = (!empty($manual_lng)) ? floatval($manual_lng) : false;

/*INFO WINDOW CONTENT*/
$t = get_the_title();
$t = !empty($t)? $t : __('No Title', ATBDP_TEXTDOMAIN);
$tg = !empty($tagline)? esc_html($tagline) : '';
$ad = !empty($address)? esc_html($address) : '';
$image = (!empty($attachment_id[0])) ? "<img src='". esc_url(wp_get_attachment_image_url($attachment_id[0], 'thumbnail'))."'>": '';
$info_content = "<div class='map_info_window'> <h3>{$t}</h3>";
$info_content .= "<p> {$tg} </p>";
$info_content .= $image ; // add the image if available
$info_content .= "<address> {$ad} </address>";
$info_content .= "<a href='http://www.google.com/maps/place/{$manual_lat},{$manual_lng}' target='_blank'> ".__('View On Google Maps', ATBDP_TEXTDOMAIN)."</a></div>";


/*Reviews*/
$reviews = $ATBDP->review->db->get_reviews_by('post_id', $post->ID, 0, 3); // get only 3
$recent_reviews = $ATBDP->review->db->get_all(5);// 5 recent reviews
$average = $ATBDP->review->get_average($reviews);
$reviews_count = $ATBDP->review->db->count(array('post_id' => $post->ID)); // get total review count for this post

$map_zoom_level = get_directorist_option('map_zoom_level', 16);

?>

<section class="directorist directory_wrapper">
        <div class="row">
                <div class="col-md-8 col-sm-8">
                    <div class="single_listing_info">
                        <?php if (!empty($image_links)){
                            ?>
                            <div class="directory_image_galler_wrap">
                                <div class="directory_image_gallery">
                                    <?php foreach ($image_links as $image_link){ ?>
                                        <div class="single_image">
                                            <img src="<?= esc_url($image_link); ?>" alt="Details Image">
                                        </div>
                                        <?php
                                        // do not output more than one image if the MI extension is not active
                                        if (!is_multiple_images_active()) break;
                                    } ?>
                                </div>
                                <?php if (count($image_links) > 1 && is_multiple_images_active()){ ?>
                                    <span class="prev fa fa-angle-left"></span>
                                    <span class="next fa fa-angle-right"></span>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <div class="listing_detail">
                            <div class="listing_title">
                                <h2><?php esc_html_e($post->post_title); ?></h2>
                                <p class="sub_title"><?= (!empty($tagline)) ? esc_html(stripslashes($tagline)) : ''; ?></p>
                            </div>
                            <?php
                            /**
                             * Fires after the title and sub title of the listing is rendered on the single listing page
                             * @param array $listing_info The meta information of the current listing
                             *
                             * @since 1.0.0
                             */

                            do_action('atbdp_after_listing_tagline', $listing_info);
                            ?>


                            <ul class="directory_tags">
                                <?php
                                $cats = get_the_terms($post->ID, ATBDP_CATEGORY);
                                if (!empty($cats)) {
                                    foreach ($cats as $cat) {

                                        ?>
                                        <li>
                                            <p class="directory_tag">
                                                <span class="fa <?= esc_attr(get_cat_icon($cat->term_id)); ?>" aria-hidden="true"></span>
                                                <span>
                                                    <a href="<?= ATBDP_Permalink::get_category_archive($cat); ?>" >
                                                                <?= $cat->name; ?>
                                                    </a>
                                                </span>
                                            </p>
                                        </li>
                                    <?php  }
                                }
                                ?>

                            </ul>

                            <div class="about_detail">
                                <?php echo wp_kses($post->post_content, wp_kses_allowed_html('post')); ?>
                            </div>

                            <div class="director_social_wrap">
                                <p><?php _e('Share this post',ATBDP_TEXTDOMAIN); ?></p>
                                <ul>


                                    <li><a href="https://www.facebook.com/share.php?u=<?php echo
                                        get_the_permalink(); ?>&title=<?php echo get_the_title(); ?>" target="_blank"><span class="fa fa-facebook"></span></a></li>

                                    <li><a href="http://twitter.com/intent/tweet?status=<?php echo get_the_title(); ?>+<?php echo get_the_permalink(); ?>" target="_blank"><span class="fa fa-twitter"></span></a></li>
                                    <li><a  href="https://plus.google.com/share?url=<?php echo get_the_permalink(); ?>" target="_blank"><span class="fa fa-google-plus"></span></a></li>

                                    <li><a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo get_the_permalink(); ?>&title=<?php echo get_the_title(); ?>" target="_blank"><span class="fa fa-linkedin"></span></a></li>

                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php

                    if (is_business_hour_active() && $enable_bh_on_page && (!is_empty_array($business_hours) || !empty($enable247hour)) ) {
                    ?>
                    <div class="row">
                        <div class="col-md-5">



                            <!-- Opening/Business hour Information section-->
                            <div class="opening_hours">
                                <div class="directory_are_title">
                                    <h4><span class="fa fa-calendar-o"></span><?php echo esc_html($business_hour_title); ?></h4>
                                </div>
                                <div class="directory_open_hours">
                                    <?php
                                    // if 24 hours 7 days open then show it only, otherwise, show the days and its opening time.
                                    if (!empty($enable247hour)) {
                                        echo '<p>'. esc_html($text247) . '</p>';
                                    } else {
                                        BD_Business_Hour()->show_business_hour($business_hours); // show the business hour in an unordered list
                                    } ?>
                                </div> <!--ends .directory_open_hours -->
                            </div> <!--ends. .opening hours-->
                        </div> <!--ends. .col-md-5-->

                        <div class="col-md-7">
                            <?php } ?><!-- Contact Information section-->
                            <div class="directory_contact_area">
                                <div class="directory_are_title">
                                    <h4><span class="fa fa-envelope-o"></span><?php _e('Contact Information', ATBDP_TEXTDOMAIN); ?></h4>
                                </div>

                                <ul>
                                    <?php if (!empty($address)) { ?>
                                        <li><span class="info_title"><?php _e('Address', ATBDP_TEXTDOMAIN); ?></span><span class="info"><?= esc_html($address); ?></span></li>
                                    <?php }?>

                                    <?php if (!empty($phone)) { ?>
                                        <!-- In Future, We will have to use a loop to print more than 1 number-->
                                        <li><span class="info_title"><?php _e('Phone', ATBDP_TEXTDOMAIN); ?></span><span class="info"><?= esc_html( $phone[0]); ?></span></li>
                                    <?php }?>

                                    <?php if (!empty($email)) { ?>
                                        <li><span class="info_title"><?php _e('Email', ATBDP_TEXTDOMAIN); ?></span><span class="info"><?= esc_html($email); ?></span></li>
                                    <?php }?>

                                    <?php if (!empty($website)) { ?>
                                        <li><span class="info_title"><?php _e('Website', ATBDP_TEXTDOMAIN); ?></span><a href="<?= esc_url($website); ?>" class="info"><?= esc_html($website); ?></a></li>
                                    <?php }?>

                                </ul>
                                <?php if (!empty($social) && is_array($social)) {?>
                                    <div class="director_social_wrap">
                                        <p><?php _e('Social Link', ATBDP_TEXTDOMAIN); ?></p>
                                        <ul>
                                            <?php foreach ($social as $link) {
                                                $n = esc_attr($link['id']);
                                                $l = esc_url($link['url']);
                                                echo "<li><a href='{$l}'><span class='fa fa-{$n}'></span></a></li>";
                                                ?>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                <?php } ?>
                            </div>  <!--ends .directory_contact_area -->
                            <!--We need to close the row and col div when we have business hour enabled. We used negative checking so that they can show by default if the setting is not set by the user after adding the plugin.-->
                            <?php if (is_business_hour_active() && $enable_bh_on_page && (!is_empty_array($business_hours) || !empty($enable247hour)) ) {
                            ?>
                        </div> <!--ends. .col-md-7 ddflkdsjflkdsjfl-->
                    </div> <!-- ends .row-->
                <?php } ?>

                    <?php if (!empty($manual_lat) && !empty($manual_lng)){
                        echo '<div id="gmap"></div>';
                    } ?>
                    <!--Google map section-->

                    <?php
                    /**
                     * Fires after the Map is rendered on single listing page
                     *
                     *
                     * @since 1.0.0
                     *
                     * @param object|WP_post $post The current post object which is our listing post
                     * @param array $listing_info The meta information of the current listing
                     */

                    do_action('atbdp_after_map', $post, $listing_info);
                    ?>


                    <?php

                    /**
                     * Fires after the single listing is rendered on single listing page
                     *
                     *
                     * @since 1.0.0
                     *
                     * @param object|WP_post $post The current post object which is our listing post
                     * @param array $listing_info The meta information of the current listing
                     */

                    do_action('atbdp_after_single_listing', $post, $listing_info);
                    ?>
                </div>




                <!--SIDE BAR -->
                <div class="col-md-4 col-sm-4">
                    <div class="directory_user_area">
                        <div class="directory_are_title">
                            <h4><?php _e('Submit Your Item', ATBDP_TEXTDOMAIN); ?></h4>
                        </div>

                        <div class="directory_user_area_form">
                            <a href="<?= esc_url(ATBDP_Permalink::get_add_listing_page_link()); ?>" class="directory_btn btn btn-default"><?php _e('Submit New Listing', ATBDP_TEXTDOMAIN); ?></a>

                            <?php
                            if (!is_user_logged_in()){
                                wp_login_form();
                                wp_register();
                            }

                            ?>
                        </div>
                    </div> <!--ends . directory_user_area-->


                    <?php
                    /**
                     * Fires after the side bar login from is rendered on single listing page
                     *
                     *
                     * @since 1.0.0
                     *
                     * @param object|WP_post $post The current post object which is our listing post
                     * @param array $listing_info The meta information of the current listing
                     */

                    do_action('atbdp_after_sidebar_login_form', $post, $listing_info);

                    ?>

                </div> <!--ends .col-md-4 col-sm-4-->


            <?php
            include ATBDP_TEMPLATES_DIR.'sidebar-listing.php';
            ?>
        </div>
</section>

<script>

    jQuery(document).ready(function ($) {

        // show map if lat long is NOT empty
        <?php if (!empty($manual_lat) && !empty($manual_lng)){ ?>
        // initialize all vars here to avoid hoisting related misunderstanding.
        var  map, info_window, saved_lat_lng, info_content;
        saved_lat_lng = {lat:<?= (!empty($manual_lat)) ? floatval($manual_lat) : false ?>, lng: <?= (!empty($manual_lng)) ? floatval($manual_lng) : false ?> }; // default is London city
        info_content = "<?= $info_content; ?>";

        // create an info window for map
        info_window = new google.maps.InfoWindow({
            content: info_content,
            maxWidth: 400
        });


        function initMap() {
            /* Create new map instance*/
            map = new google.maps.Map(document.getElementById('gmap'), {
                zoom: <?php echo !empty($map_zoom_level) ? intval($map_zoom_level) : 16; ?>,
                center: saved_lat_lng
            });
            var marker = new google.maps.Marker({
                map: map,
                position:  saved_lat_lng
            });
            marker.addListener('click', function() {
                info_window.open(map, marker);
            });


        }


        initMap();
        <?php } ?>


        //Convert address tags to google map links -
        $('address').each(function () {
            var link = "<a href='http://maps.google.com/maps?q=" + encodeURIComponent( $(this).text() ) + "' target='_blank'>" + $(this).text() + "</a>";
            $(this).html(link);
        });

    }); // ends jquery ready function.


</script>



