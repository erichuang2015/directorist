<?php
$ATBDP = ATBDP();
$listings =  $ATBDP->user->current_user_listings();
$uid = get_current_user_id();
$c_user = get_userdata($uid);

$u_website= $c_user->user_url;
$avatar= get_user_meta($uid, 'avatar', true);
$u_phone= get_user_meta($uid, 'phone', true);
$u_pro_pic= get_user_meta($uid, 'pro_pic', true);
$u_address= get_user_meta($uid, 'address', true);




?>
<div class="directorist directory_wrapper single_area">
    <div class="<?php echo is_directoria_active() ? 'container': 'container-fluid'; ?>">
        <div class="row">
            <div class="col-md-12">

                <div class="add_listing_title">
                    <h2><?php _e('My Dashboard', ATBDP_TEXTDOMAIN); ?></h2>
                </div> <!--ends add_listing_title-->

                <div class="dashboard_wrapper">
                    <div class="dashboard_nav">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active" >
                                <a href="#my_listings" aria-controls="my_listings" role="tab" data-toggle="tab">
                                    <?php $list_found = ($listings->found_posts > 0) ? $listings->found_posts : '0';
                                    printf(__('My Listing (%s)', ATBDP_TEXTDOMAIN), $list_found); ?>
                                </a>
                            </li>
                            <li role="presentation" ><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><?php _e('My Profile', ATBDP_TEXTDOMAIN);?></a></li>
                        </ul>

                        <div class="nav_button">
                            <a href="<?= esc_url(ATBDP_Permalink::get_add_listing_page_link()); ?>" class="<?= atbdp_directorist_button_classes(); ?>"><?php _e('Submit New Listing', ATBDP_TEXTDOMAIN); ?></a>
                            <a href="<?= esc_url(wp_logout_url());?>" class="<?= atbdp_directorist_button_classes(); ?>"><?php _e('Log Out', ATBDP_TEXTDOMAIN); ?></a>
                        </div>
                    </div> <!--ends dashboard_nav-->

                    <!-- Tab panes -->
                    <div class="tab-content row">
                        <div role="tabpanel" class="tab-pane active" data-uk-grid id="my_listings">
                            <?php if ($listings->have_posts()) {
                                foreach ($listings->posts as $post) {
                                    /*RATING RELATED STUFF STARTS*/
                                    $reviews = $ATBDP->review->db->count(array('post_id' => $post->ID));
                                    $average = $ATBDP->review->get_average($post->ID);
                                    /*RATING RELATED STUFF ENDS*/
                                    $info = $ATBDP->metabox->get_listing_info($post->ID); // get all post meta and extract it.
                                    extract($info);
                                    // get only one parent or high level term object
                                    $single_parent = $ATBDP->taxonomy->get_one_high_level_term($post->ID, ATBDP_CATEGORY);
                                    ?>
                                    <div class="col-lg-4 col-sm-6" id="listing_id_<?= $post->ID; ?>">
                                        <div class="single_directory_post">
                                            <article>
                                                <figure>
                                                    <div class="post_img_wrapper">
                                                        <img src="<?= (!empty($attachment_id[0]))  ? wp_get_attachment_image_url($attachment_id[0], array(432,400)) : '' ?>" alt="Image">
                                                    </div>
                                                </figure> <!--ends figure-->

                                                <div class="article_content">
                                                    <div class="content_upper">
                                                        <h4 class="post_title"><a href="<?= get_post_permalink($post->ID); ?>"><?= !empty($post->post_title)? esc_html(stripslashes($post->post_title)): ''; ?></a></h4>                                                        <p><?= (!empty($tagline)) ? esc_html(stripslashes($tagline)): '' ?></p>
                                                        <?php

                                                        /**
                                                         * Fires after the title and sub title of the listing is rendered on the single listing page
                                                         *
                                                         *
                                                         * @since 1.0.0
                                                         */

                                                        do_action('atbdp_after_listing_tagline');

                                                        ?>
                                                    </div> <!--ends .content_upper-->

                                                    <div class="db_btn_area">
                                                        <!--@TODO; get the add listing page url from plugin option-->
                                                        <a href="<?= esc_url(site_url('add-listing/?listing_id='. $post->ID)); ?>" id="edit_listing" class="directory_edit_btn btn btn-default"><?php _e('Edit Listing', ATBDP_TEXTDOMAIN); ?></a>
                                                        <a href="#" id="remove_listing" data-listing_id="<?= $post->ID; ?>" class="directory_remove_btn btn btn-default"><?php _e('Delete', ATBDP_TEXTDOMAIN); ?></a>
                                                    </div> <!--ends .db_btn_area-->
                                                </div> <!--ends .article_content-->
                                            </article> <!--ends article-->
                                        </div> <!--ends .single_directory_post-->
                                    </div> <!--ends . col-lg-3 col-sm-6-->
                                    <?php
                                }
                            }else{
                                esc_html_e('Looks like you have not created any listing yet!', ATBDP_TEXTDOMAIN);
                                ?>

                            <?php
                            }

                           // echo atbdp_pagination($listings, $paged);

                            ?>

                        </div> <!--ends #my_listings-->
                        <div role="tabpanel" class="tab-pane " id="profile">
                            <form action="#" id="user_profile_form" method="post">
                                <div class="col-md-4">
                                    <div class="user_pro_img_area">
                                            <div class="user_img" id="profile_pic_container">
                                                <div class="cross" id="remove_pro_pic"><span class="fa fa-times"></span></div>
                                                <div class="choose_btn">
                                                    <input type="hidden" name="user[pro_pic]" id="pro_pic" value="<?= !empty($u_pro_pic) ? esc_url($u_pro_pic) : ''; ?>">
                                                    <label for="pro_pic" id="upload_pro_pic"><?php _e('Change', ATBDP_TEXTDOMAIN); ?></label>
                                                </div> <!--ends .choose_btn-->
                                                <img src="<?= !empty($u_pro_pic) ? esc_url($u_pro_pic) : esc_url(ATBDP_PUBLIC_ASSETS.'images/no-image.jpg'); ?>" id="pro_img" alt="">
                                            </div> <!--ends .user_img-->
                                    </div> <!--ends .user_pro_img_area-->
                                </div> <!--ends .col-md-4-->

                                <div class="col-md-8">
                                    <div class="profile_title"><h4><?php _e('My Profile', ATBDP_TEXTDOMAIN); ?></h4></div>

                                    <div class="user_info_wrap">
                                            <!--hidden inputs-->
                                            <input type="hidden" name="ID" value="<?= get_current_user_id(); ?>">
                                            <!--Full name-->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="full_name">Full Name</label>
                                                    <input class="directory_field" type="text" name="user[full_name]" value="<?= !empty($c_user->display_name)? esc_attr($c_user->display_name):'';?>" placeholder="Enter your full name">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="user_name">User Name</label>
                                                    <input class="directory_field" id="user_name" type="text" disabled="disabled" name="user[user_name]" value="<?= !empty($c_user->user_login)? esc_attr($c_user->user_login):'';?>"> <?php _e('(username can not be changed)', ATBDP_TEXTDOMAIN); ?>
                                                </div>
                                            </div> <!--ends .row-->
                                            <!--First Name-->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="first_name"><?php _e('First Name', ATBDP_TEXTDOMAIN); ?></label>
                                                    <input class="directory_field" id="first_name" type="text" name="user[first_name]" value="<?= !empty($c_user->first_name)? esc_attr($c_user->first_name):'';?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="last_name"><?php _e('Last Name', ATBDP_TEXTDOMAIN); ?></label>
                                                    <input class="directory_field" id="last_name" type="text" name="user[last_name]" value="<?= !empty($c_user->last_name)? esc_attr($c_user->last_name):'';?>">
                                                </div>
                                            </div> <!--ends .row-->
                                            <!--Email-->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="req_email"><?php _e('Email (required)', ATBDP_TEXTDOMAIN); ?></label>
                                                    <input class="directory_field" id="req_email" type="text" name="user[user_email]" value="<?= !empty($c_user->user_email)? esc_attr($c_user->user_email):'';?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="phone"><?php _e('Cell Number', ATBDP_TEXTDOMAIN); ?></label>
                                                    <input class="directory_field" type="tel" name="user[phone]" value="<?= !empty($u_phone)? esc_attr($u_phone):'';?>" placeholder="Enter your phone number">
                                                </div>
                                            </div> <!--ends .row-->
                                            <!--Website-->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="website"><?php _e('Website', ATBDP_TEXTDOMAIN); ?></label>
                                                    <input class="directory_field" id="website" type="text" name="user[website]" value="<?= !empty($u_website) ? esc_url($u_website):'';?>" >
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="address"><?php _e('Address', ATBDP_TEXTDOMAIN); ?></label>
                                                    <input class="directory_field" id="address" type="text" name="user[address]" value="<?= !empty($u_address)? esc_attr($u_address):'';?>">
                                                </div>
                                            </div> <!--ends .row-->

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label for="current_pass"><?php _e('Current Password', ATBDP_TEXTDOMAIN); ?></label>
                                                    <input class="directory_field" type="password" name="user[current_pass]" id="current_pass" value="" placeholder="Your Current Password" >
                                                </div>
                                            </div> <!--ends .row-->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="new_pass"><?php _e('New Password', ATBDP_TEXTDOMAIN); ?></label>
                                                    <input class="directory_field" type="password" name="user[new_pass]" value="" placeholder="Enter a new password" >
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="confirm_pass"><?php _e('Confirm Password', ATBDP_TEXTDOMAIN); ?></label>
                                                    <input class="directory_field" type="password" name="user[confirm_pass]" value="" placeholder="Confirm your new password" >
                                                </div>
                                            </div><!--ends .row-->
                                            <button type="submit" class="btn btn-primary" id="update_user_profile"><?php _e('Save Changes', ATBDP_TEXTDOMAIN); ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
    </div>
</div>