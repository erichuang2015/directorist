<?php
global $wp_query;
!empty($args['data']) ? extract($args['data']) : array();
$listings = !empty($listings) ? $listings : array();
// Pagination fix
$temp_query = $wp_query;
$wp_query   = NULL;
$wp_query   = $listings;
?>

    <div class="directorist directory_wrapper single_area">
        <div class="header_bar">
                <div class="row">
                    <div class="col-md-12">
                        <div class="header_form_wrapper">
                            <div class="directory_title">
                                <h3>
                                    <?php
                                    // show appropriate text for the search
                                    // possible search system

                                    if (!empty($s_string) && !empty($in_cat) && !empty($in_loc)){
                                        //8. with everything
                                        printf(__('Search Result for: "%s" in "%s" Category in "%s" Location', ATBDP_TEXTDOMAIN), $s_string, $in_cat, $in_loc);

                                    }elseif(empty($s_string) && empty($in_cat) && empty($in_loc)){
                                        //1. empty q, loc and cat X
                                     _e('Showing Result from all categories and locations.', ATBDP_TEXTDOMAIN);

                                    }elseif(!empty($s_string) && !empty($in_loc) && empty($in_cat)){
                                        //3. only q and loc X
                                        printf(__('Showing Result for: "%s" in "%s" Location', ATBDP_TEXTDOMAIN), $s_string, $in_loc);
                                    }elseif(!empty($s_string) && !empty($in_cat) && empty($in_loc)){
                                        //4. only q and cat X
                                        printf(__('Showing Result for: "%s" in "%s" Category', ATBDP_TEXTDOMAIN), $s_string, $in_cat);

                                    }elseif(empty($s_string) && !empty($in_cat) && empty($in_loc)){
                                        //5. only cat
                                        printf(__('Showing Result in: "%s" Category', ATBDP_TEXTDOMAIN), $in_cat);

                                    }elseif(empty($s_string) && empty($in_cat) && !empty($in_loc)){
                                        //5. only loc
                                        printf(__('Showing Result in: "%s" Location', ATBDP_TEXTDOMAIN), $in_loc);

                                    }elseif(empty($s_string) && !empty($in_cat) && !empty($in_loc)){
                                        //6. only cat and loc
                                        printf(__('Showing Result in: "%s" Category and "%s" Location', ATBDP_TEXTDOMAIN),  $in_cat, $in_loc);

                                    }else{
                                        //2. only q X
                                        printf(__('Search Result for: "%s" from All categories and locations', ATBDP_TEXTDOMAIN), $s_string);
                                    }

                                    ?>
                                </h3>
                                <p><?php _e('Total Listing Found: ', ATBDP_TEXTDOMAIN); echo $listings->found_posts; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <!--maybe we should removed the parent container so that it can match perfectly -->
            <div class="row" data-uk-grid>


                <?php if ( !empty($listings) ) {
                    while ( $listings->have_posts() ) {
                        $listings->the_post();
                        /*RATING RELATED STUFF STARTS*/
                        $reviews = ATBDP()->review->db->count(array('post_id' => get_the_ID()));
                        $average = ATBDP()->review->get_average(get_the_ID());

                        /*RATING RELATED STUFF ENDS*/
                        $info = ATBDP()->metabox->get_listing_info(get_the_ID()); // get all post meta and extract it.
                        extract($info);
                        // get only one parent or high level term object
                        $single_parent = ATBDP()->taxonomy->get_one_high_level_term(get_the_ID(), ATBDP_CATEGORY);
                        ?>

                        <div class="col-md-4 col-sm-6">
                            <div class="single_directory_post">
                                <article>
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
                                            </h4>
                                            <p><?= (!empty($tagline)) ? esc_html(stripslashes($tagline)) : ''; ?></p>
                                            <?php

                                            /**
                                             * Fires after the title and sub title of the listing is rendered on the single listing page
                                             *
                                             *
                                             * @since 1.0.0
                                             */

                                            do_action('atbdp_after_listing_tagline');

                                            ?>
                                          
                                        </div>

                                        <div class="general_info">
                                            <ul>
                                                <!--Category Icons should be replaced later -->
                                                <li>
                                                    <p class="info_title"><?php echo __('Category:', ATBDP_TEXTDOMAIN);?></p>
                                                    <p class="directory_tag">

                                                        <span class="fa <?= esc_attr(get_cat_icon(@$single_parent->term_id)); ?>" aria-hidden="true"></span>
                                                        <span> <?php if (is_object($single_parent)) { ?>
                                                                <a href="<?= esc_url(ATBDP_Permalink::get_category_archive($single_parent)); ?>">
                                                                <?= esc_html($single_parent->name); ?>
                                                                </a>
                                                            <?php } else {
                                                               _e('Others', ATBDP_TEXTDOMAIN);
                                                            } ?>
                                                        </span>
                                                    </p>
                                                </li>
                                                <li>
                                                    <p class="info_title"><?php _e('Location:', ATBDP_TEXTDOMAIN);?>
                                                    <span><?= !empty($address) ? esc_html(stripslashes($address)) : ''; ?></span>
                                                    </p>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="read_more_area">
                                            <a class="btn btn-default" href="<?= esc_url(get_post_permalink(get_the_ID())); ?>"><?php _e('Read More', ATBDP_TEXTDOMAIN); ?></a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>

                    <?php }

                    } else {?>
                            <p><?php _e('No listing found.', ATBDP_TEXTDOMAIN); ?></p>
                <?php } ?>



            </div> <!--ends .row -->

            <div class="row">
                <div class="col-md-12">
                    <?php
                    the_posts_pagination(
                            array('mid_size'  => 2,
                            'prev_text' => '<span class="fa fa-chevron-left"></span>',
                            'next_text' => '<span class="fa fa-chevron-right"></span>',
                        ));
                    //atbdp_pagination($listings, $paged);
                    wp_reset_postdata();
                    $wp_query   = NULL;
                    $wp_query   = $temp_query;
                    ?>
                </div>
            </div>
    </div>
<?php
?>
<?php include __DIR__.'/style.php'; ?>
