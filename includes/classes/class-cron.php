<?php
/**
 * Cron class of Directorist
 *
 * This class is for running scheduled work and interacting with cronjobs
 *
 * @package     Directorist
 * @copyright   Copyright (c) 2018, AazzTech
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) die('What the hell are you doing here accessing this file directly');
if (!class_exists('ATBDP_Cron')):
class ATBDP_Cron {

    public function __construct()
    {
        //init wp schedule
        //add_action('init', array($this, 'schedule_events')); // for testing on local host use init hook

    }

    /**
     * It hooks the schedule events to WordPress Cron tasks
     * @since 3.1.0
     */
    public function schedule_events()
    {
        add_filter( 'cron_schedules', 'example_add_cron_interval' );

        function example_add_cron_interval( $schedules ) {
            $schedules['five_seconds'] = array(
                'interval' => 10,
                'display'  => esc_html__( 'Every Five Seconds' ),
            );

            return $schedules;
        }


        if( ! wp_next_scheduled( 'directorist_hourly_scheduled_events' ) ) {
            wp_schedule_event( time(), 'five_seconds', 'directorist_hourly_scheduled_events' );
        }

        // run the schedules events
        add_action('directorist_hourly_scheduled_events', array($this, 'hourly_scheduled_events'));
        //@todo; Test cron integration using short intervals. also test all cron functions manually before finalizing this class.
    }
    function bl_print_tasks() {
        echo '<pre>'; print_r( _get_cron_array() ); echo '</pre>';
    }


    /**
     * Define actions to execute during the cron event.
     *
     * @since    3.1.0
     * @access   public
     */
    public function hourly_scheduled_events() {
        /*
        $this->update_renewal_status(); // we will send about to expire notification here
        $this->update_expired_status();  // we will send expired notification here
        $this->send_renewal_reminders(); // we will send renewal notification after expiration here
        $this->delete_expired_listings(); // we will delete listings here certain days after expiration here.
        */

    }

    /**
     * Move listings to renewal status (only if applicable).
     *
     * @since    3.1.0
     * @access   private
     */
    private function update_renewal_status() {

        $can_renew       = get_directorist_option('can_renew_listing');
        $renew_email_threshold = get_directorist_option('email_to_expire_day'); // before how many days of expiration, a renewal message should be sent

        if( $can_renew && $renew_email_threshold > 0 ) {
            $renew_email_threshold_date = date( 'Y-m-d H:i:s', strtotime( "+{$renew_email_threshold} days" ) );

            // Define the query
            $args = array(
                'post_type'      => ATBDP_POST_TYPE,
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'meta_query'     => array(
                    array(
                        'key'	  => '_listing_status',
                        'value'	  => 'post_status',
                    ),
                    array(
                        'key'	  => '_expiry_date',
                        'value'	  => $renew_email_threshold_date,
                        'compare' => '<=',
                        'type'    => 'DATETIME'
                    ),
                    array(
                        'key'	  => '_never_expire',
                        'value' => 0,
                    )
                )
            );

            $listings  = new WP_Query( $args ); // get all the post that has post_status only and update their status and fire an email
            if ($listings->found_posts){
                foreach ($listings->posts as $listing){
                    update_post_meta( $listing->ID, '_listing_status', 'renewal' );
                    // hook for dev.
                    do_action('atbdp_status_updated_to_renewal');
                }

            }

        }

    }

    /**
     * Move listings to expired status (only if applicable).
     *
     * @since    3.1.0
     * @access   private
     */
    private function update_expired_status() {

        $can_renew               = get_directorist_option('can_renew_listing');
        $email_renewal_day       = get_directorist_option('email_renewal_day');
        $delete_in_days          = get_directorist_option('delete_expired_listings_after');
        $delete_threshold = $can_renew ? (int) $email_renewal_day + (int) $delete_in_days : $delete_in_days;

        // Define the query
        $args = array(
            'post_type'      => ATBDP_POST_TYPE,
            'posts_per_page' => -1,
            'post_status'    => 'publish', // get expired post with published status
            'meta_query'     => array(
                array(
                    'key'	  => '_expiry_date',
                    'value'	  => current_time( 'mysql' ),
                    'compare' => '<=', // eg. expire date 6 <= current date 7 will return the post
                    'type'    => 'DATETIME'
                ),
                array(
                    'key'	  => '_never_expire',
                    'value' => 0,
                )
            )
        );

        $listings  = new WP_Query( $args );
        if ($listings->found_posts){
            foreach ($listings->posts as $listing){
                // prepare the post meta data
                $metas = array(
                    '_listing_status' => 'expired',
                    '_featured' => 0,
                    '_renewal_reminder_sent' => 0,
                );
                if( $delete_threshold > 0 ) {
                    $metas['_deletion_date'] = date( 'Y-m-d H:i:s', strtotime( "+".$delete_threshold." days" ) );
                }
                wp_update_post( array(
                    'ID'           => $listing->ID,
                    'post_status' => 'private', // update the status to private so that we do not run this func a second time
                    'meta_input' => $metas, // insert all meta data once to reduce update meta query
                ) );
                // Hook for developers
                do_action( 'atbdp_listing_expired', $listing->ID );
            }

        }


    }

    /**
     * Send renewal reminders to expired listings (only if applicable)
     *
     * @since    3.1.0
     * @access   private
     */
    private function send_renewal_reminders() {
        $can_renew              = get_directorist_option('can_renew_listing');
        $email_renewal_day       = get_directorist_option('email_renewal_day');

        if( $can_renew && $email_renewal_day > 0 ) {
            // Define the query
            $args = array(
                'post_type'      => ATBDP_POST_TYPE,
                'posts_per_page' => -1,
                'post_status'    => 'private',
                'meta_query'     => array(
                    array(
                        'key'	  => '_listing_status',
                        'value'	  => 'expired',
                    ),
                    array(
                        'key'	  => '_renewal_reminder_sent',
                        'value'	  => 0,
                    ),
                    array(
                        'key'	  => '_never_expire',
                        'value' => 0,
                    )
                )
            );

            $listings  = new WP_Query( $args );

            // Start the Loop
            if ($listings->found_posts){
                foreach ($listings->posts as $listing){
                    // Send emails
                    $expiration_date = get_post_meta( $listing->ID, '_expiry_date', true );
                    $expiration_date_time = strtotime( $expiration_date );
                    $reminder_date_time = strtotime( "+{$email_renewal_day} days", strtotime( $expiration_date_time ) );

                    if( current_time( 'timestamp' ) > $reminder_date_time ) {
                        do_action('atbdp_send_renewal_reminder', $listing->ID);
                        // once we notify the user, lets update the reminder status so that we do not run this func a second time
                        update_post_meta( $listing->ID, '_renewal_reminder_sent', 1 );
                    }
                }
            }


        }

    }

    /**
     * Delete expired listings (only if applicable)
     *
     * @since    3.1.0
     * @access   private
     */
    private function delete_expired_listings() {

        $delete_in_days = (int) get_directorist_option('delete_expired_listings_after');
        // check if user wanna delete the post.
        //@todo; in future, let the admin decide to delete or hide the post from the public. because direct deletion is not a good idea.
        if( $delete_in_days > 0 ) {

            // Define the query
            $args = array(
                'post_type'      => ATBDP_POST_TYPE,
                'posts_per_page' => -1,
                'post_status'    => 'private',
                'meta_query'     => array(
                    array(
                        'key'	  => '_listing_status',
                        'value'	  => 'expired',
                    ),
                    array(
                        'key'	  => '_deletion_date',
                        'value'	  => current_time( 'mysql' ),
                        'compare' => '<',
                        'type'    => 'DATETIME'
                    ),
                    array(
                        'key'	  => '_never_expire',
                        'value' => 0,
                    )
                )
            );

            $listings  = new WP_Query( $args );

            if( $listings ->found_posts ) {
                foreach ($listings->posts as $listing) {
                    // Delete the listing @todo; Let the admin decide whether to delete the post directly or he wants to move to trash
                    wp_delete_post( $listing->ID, true );
                }
            }
        }
    }
}

endif;