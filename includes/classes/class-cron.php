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
        add_action('wp', array($this, 'schedule_events'));
    }

    /**
     * It hooks the schedule events to WordPress Cron tasks
     * @since 3.1.0
     */
    public function schedule_events()
    {
        /*If we want we can define custom recurrence, for testing*/
        /*
         * example
        add_filter('cron_schedules', function (){
             return array(
                'per_minute'     => array( 'interval' => MINUTE_IN_SECONDS,      'display' => __( 'Once Every Minute' ) ),
            );
        });
        */

        if( ! wp_next_scheduled( 'directorist_hourly_scheduled_events' ) ) {
            wp_schedule_event( current_time( 'timestamp' ), 'hourly', 'directorist_hourly_scheduled_events' );
        }

        // run the schedules events
        add_action('directorist_hourly_scheduled_events', array($this, 'hourly_scheduled_events'));
        //@todo; Test cron integration using short intervals. also test all cron functions manually before finalizing this class.
    }


    /**
     * Define actions to execute during the cron event.
     *
     * @since    3.1.0
     * @access   public
     */
    public function hourly_scheduled_events() {
        var_dump('Our Custom cron is working.....');
        /*$this->update_renewal_status();
        $this->update_expired_status();
        $this->send_renewal_reminders();
        $this->delete_expired_listings();*/

    }

    /**
     * Move listings to renewal status (only if applicable).
     *
     * @since    3.1.0
     * @access   private
     */
    private function update_renewal_status() {

        $can_renew       = get_directorist_option('can_renew_listing');
        $renew_email_threshold = get_directorist_option('email_renewal_day'); // before how many days of expiration, a renewal message should be sent

        if( $can_renew && $renew_email_threshold > 0 ) {
            $renew_email_threshold_date = date( 'Y-m-d H:i:s', strtotime( "+{$renew_email_threshold} days" ) );

            // Define the query
            $args = array(
                'post_type'      => ATBDP_POST_TYPE,
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'meta_query'     => array(
                    'relation'    => 'AND',
                    array(
                        'key'	  => '_listing_status',
                        'value'	  => 'post_status',
                        'compare' => '='
                    ),
                    array(
                        'key'	  => '_expiry_date',
                        'value'	  => $renew_email_threshold_date,
                        'compare' => '<',
                        'type'    => 'DATETIME'
                    ),
                    array(
                        'key'	  => '_never_expire',
                        'compare' => 0,
                    )
                )
            );

            $listings  = new WP_Query( $args );
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
        $delete_in_days = get_directorist_option('delete_expired_listings_after');
        if( $can_renew ) {
            $delete_threshold = (int) $email_renewal_day + (int) $delete_in_days;
        } else {
            $delete_threshold = (int) $delete_in_days;
        }

        // Define the query
        $args = array(
            'post_type'      => ATBDP_POST_TYPE,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                'relation'   => 'AND',
                array(
                    'key'	  => '_expiry_date',
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
                    'post_status' => 'private',
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
                    'relation'    => 'AND',
                    array(
                        'key'	  => '_listing_status',
                        'value'	  => 'expired',
                        'compare' => '='
                    ),
                    array(
                        'key'	  => '_renewal_reminder_sent',
                        'value'	  => 0,
                        'compare' => '='
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

        $can_renew       = get_directorist_option('can_renew_listing');
        $delete_in_days = get_directorist_option('delete_expired_listings_after');
        $email_renewal_day = get_directorist_option('email_renewal_day'); // before how many days of expiration, a renewal message should be sent
        if( $can_renew ) {
            $delete_threshold = (int) $email_renewal_day + (int) $delete_in_days;
        } else {
            $delete_threshold = (int) $delete_in_days;
        }

        if( $delete_threshold > 0 ) {

            // Define the query
            $args = array(
                'post_type'      => ATBDP_POST_TYPE,
                'posts_per_page' => -1,
                'post_status'    => 'private',
                'meta_query'     => array(
                    'relation'    => 'AND',
                    array(
                        'key'	  => '_listing_status',
                        'value'	  => 'expired',
                        'compare' => '='
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
                    // Delete the listing
                    wp_delete_post( $listing->ID, true );
                }
            }
        }
    }
}

endif;