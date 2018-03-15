<?php
defined('ABSPATH') || die( 'Direct access is not allowed.' );
if ( !class_exists('ATBDP_Email') ):
class ATBDP_Email {

    public static function notify_admin_order_created($listing_id, $order_id)
    {
        $disabled_email = get_directorist_option('disable_email_notification');
        if ($disabled_email) return; //vail if email notification is off
        $notify_admin = get_directorist_option('notify_admin', array());
        if( in_array( 'order_created', $notify_admin ) ) return; // vail if order created notification to admin off
        $listing = get_post($listing_id);
        $user = get_userdata($listing->post_author);

        $replacement = array(
             $user->display_name,
             $user->user_login,
             get_bloginfo( 'name' ),
             $listing_id,
             $listing->post_title,
             $order_id,
             admin_url( "edit.php?post_type=atbdp_orders" ),
             ATBDP_Order::get_order_details( $order_id )
        );
        $placeholders = array(
            '==NAME==',
            '==USERNAME==',
            '==SITE_NAME==',
            '==LISTING_ID==',
            '==LISTING_TITLE==',
            '==ORDER_ID==',
            '==ORDER_RECEIPT_URL==',
            '==ORDER_DETAILS==',
        );
        $admin_email_lists = get_directorist_option('admin_email_lists'); /*@todo; add this settings to the option page*/
        $to = self::get_admin_email_list( $admin_email_lists );

        $subject = __( '[==SITE_NAME==] You have a new order on your website', ATBDP_TEXTDOMAIN );
        $subject = str_replace($placeholders, $replacement, $subject );

        $message = <<<KAMAL
"Dear Administrator,<br /><br />
You have received a new order<br /><br />
This notification was for the order #==ORDER_ID== on the website ==SITE_NAME==.<br />
You can access the order details directly by clicking on the link below after logging in your back end:<br />
==ORDER_RECEIPT_URL==<br /><br />
==ORDER_DETAILS==<br /><br />

This email is sent automatically for information purpose only. Please do not respond to this.."
KAMAL;

        $message = str_replace($placeholders, $replacement, $message );
        // work on them..... tomorrow.
        $headers = self::atbdp_get_email_headers( $admin_email_lists );

        //self::send_mail( $to, $subject, $message, $headers );


    }

    /**
     * Get the list of emails to send admin notification
     *
     * @since    3.1.0
     *
     * @param	 array|string    $email_lists         Email Lists.
     * @return	 string|array    $to                    Array or comma-separated list of email addresses to send message.
     */
    function get_admin_email_list( $email_lists = array() ) {

        $to = '';
        if( empty( $email_lists ) ) {
            $email_lists = get_directorist_option('admin_email_lists');
            $to = explode( ",", $email_lists );
            $to = array_map( 'trim', $to );
            $to = array_filter( $to );
        }

        if( empty( $to ) ) {
            $to = get_bloginfo( 'admin_email' );
        }

        return $to;

    }



} // ends class
endif;
