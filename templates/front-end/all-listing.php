<?php
!empty($args['data']) ? extract($args['data']) : array(); // data array contains all required var.
$all_listings = !empty($all_listings) ? $all_listings : new WP_Query;
$all_listing_title = !empty($all_listing_title) ? $all_listing_title : __('All Items', ATBDP_TEXTDOMAIN);
// testing
$send_before_days_date = date( 'Y-m-d H:i:s', strtotime( "+6 days" ) );
//var_dump($send_before_days_date);
// Define the query
$dt = '2018-04-07 09:24:00';
$args = array(
    'post_type'      => ATBDP_POST_TYPE,
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'meta_query'     => array(
        //'relation'    => 'AND',
        array(
            'key'	  => '_listing_status',
            'value'	  => 'post_status',
        ),
        /*array(
            'key'	  => '_expiry_date',
            //$dt            : '2018-04-3 09:24:00';
            // post has time : '2018-04-07 09:24:00'
            'value'	  => $dt,
            'compare' => '<=', // it actually means that _expiry_date <= $dt;
            'type'    => 'DATETIME'
        ),*/
        array(
            'key'	  => '_never_expire',
            'value'	  => 0,
        ),
        // if we are querying post to send notification to the user, then it is good to leave the post that that has send notification meta
    )
);
//$listings  = new WP_Query( $args );
/*var_dump('dumping our posts' );
var_dump( $listings->posts );
var_dump($send_before_days_date);
var_dump($dt);*/
/*foreach ($listings->posts as $list) {
    //var_dump( 'Post ID :     '. $list->ID );
    //var_dump( get_post_meta( $list->ID, '_expiry_date', true ) );

}*/
$is_disable_price = get_directorist_option('disable_list_price');

?>


    <div class="directorist directory_wrapper single_area">
        <div class="header_bar">
            <div class="<?php echo is_directoria_active() ? 'container': 'container-fluid'; ?>">
                <div class="row">
                    <div class="col-md-12">
                        <div class="header_form_wrapper">
                            <div class="directory_title">
                                <h3>
                                    <?php echo esc_html($all_listing_title); ?>
                                </h3>
                                <?php
                                    _e('Total Listing Found: ', ATBDP_TEXTDOMAIN);
                                    if ($paginate){
                                        echo $all_listings->found_posts;
                                    }else{
                                        echo count($all_listings->posts);
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="<?php echo is_directoria_active() ? 'container': 'container-fluid'; ?>">
            <div class="row" data-uk-grid>


                <?php if ( $all_listings->have_posts() ) {
                    while ( $all_listings->have_posts() ) { $all_listings->the_post(); ?>
                        <?php
                        //var_dump(get_post_meta(get_the_ID()));

                        /*RATING RELATED STUFF STARTS*/
                        $reviews = ATBDP()->review->db->count(array('post_id' => get_the_ID()));
                        $average = ATBDP()->review->get_average(get_the_ID());

                        /*RATING RELATED STUFF ENDS*/
                        $info = ATBDP()->metabox->get_listing_info(get_the_ID()); // get all post meta and extract it.
                        extract($info);
                        // get only one parent or high level term object
                        $single_parent = ATBDP()->taxonomy->get_one_high_level_term(get_the_ID(), ATBDP_CATEGORY);
                        $deepest_location = ATBDP()->taxonomy->get_one_deepest_level_term(get_the_ID(), ATBDP_LOCATION);
                        $featured = get_post_meta(get_the_ID(), '_featured', true);
                        $price = get_post_meta(get_the_ID(), '_price', true);



                        ?>

                        <div class="col-md-4 col-sm-6">
                            <div class="single_directory_post">
                                <article class="<?php echo ($featured) ? 'directorist-featured-listings' : ''; ?>">
                                    <figure>
                                        <div class="post_img_wrapper">
                                            <?= (!empty($attachment_id[0])) ? '<img src="'.esc_url(wp_get_attachment_image_url($attachment_id[0],  array(432,400))).'" alt="listing image">' : '' ?>
                                        </div>

                                        <figcaption>
                                            <p><?= !empty($excerpt) ? esc_html(stripslashes($excerpt)) : ''; ?></p>
                                        </figcaption>
                                    </figure>

                                    <div class="article_content">
                                        <div class="content_upper">
                                            <h4 class="post_title">
                                                <a href="<?= esc_url(get_post_permalink(get_the_ID())); ?>"><?php echo esc_html(stripslashes(get_the_title())); ?></a>
                                                <?php
                                                if ($featured){ printf(
                                                    ' <span class="directorist-ribbon featured-ribbon">%s</span>',
                                                    esc_html__('Featured', ATBDP_TEXTDOMAIN)
                                                );}
                                            ?>
                                            </h4>
                                            <?php echo (!empty($tagline)) ? sprintf('<p>%s</p>', esc_html(stripslashes($tagline))) : '';

                                            /**
                                             * Fires after the title and sub title of the listing is rendered
                                             *
                                             *
                                             * @since 1.0.0
                                             */

                                            do_action('atbdp_after_listing_tagline');
                                            atbdp_display_price($price, $is_disable_price);
                                            /**
                                             * Fires after the price of the listing is rendered
                                             *
                                             *
                                             * @since 3.1.0
                                             */
                                            do_action('atbdp_after_listing_price');

                                            ?>
                                          
                                        </div>
                                        <!--it is better to show the gen info if we have data-->
                                        <?php if (!empty($single_parent) || !empty($deepest_location)) { ?>
                                        <div class="general_info">
                                            <ul>
                                                <?php if (!empty($single_parent)){ ?>
                                                <li>
                                                    <p class="info_title"><?php _e('Category:', ATBDP_TEXTDOMAIN);?></p>
                                                    <p class="directory_tag">

                                                        <span class="fa <?= esc_attr(get_cat_icon(@$single_parent->term_id)); ?>" aria-hidden="true"></span>
                                                        <span> <?php if (is_object($single_parent)) { ?>
                                                                <a href="<?= ATBDP_Permalink::get_category_archive($single_parent); ?>">
                                                                <?= esc_html($single_parent->name); ?>
                                                                </a>
                                                            <?php } ?>
                                                        </span>
                                                    </p>
                                                </li>
                                                <?php } if (!empty($deepest_location)){ ?>
                                                <li><p class="info_title"><?php _e('Location:', ATBDP_TEXTDOMAIN);?>
                                                    <span><?php if (is_object($deepest_location)) { ?>
                                                            <a href="<?= ATBDP_Permalink::get_location_archive($deepest_location); ?>">
                                                                <?= esc_html($deepest_location->name); ?>
                                                                </a>
                                                        <?php } ?></span>
                                                    </p>
                                                </li>
                                            <?php } ?>
                                            </ul>
                                        </div>
                                        <?php } ?>


                                        <div class="read_more_area">
                                            <a class="btn btn-default" href="<?= get_post_permalink(get_the_ID()); ?>"><?php _e('Read More', ATBDP_TEXTDOMAIN); ?></a> <?php echo ($featured) ? '<span>'.esc_html__('Featured', ATBDP_TEXTDOMAIN).'</span>' : ''; ?>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>

                    <?php }
                    wp_reset_postdata();
                    } else {?>
                            <p><?php _e('No listing found.', ATBDP_TEXTDOMAIN); ?></p>
                <?php } ?>



            </div> <!--ends .row -->

            <div class="row">
                <div class="col-md-12">
                    <?php
                    echo atbdp_pagination($all_listings, $paged);
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php
?>
<?php //get_footer(); ?>