<?php
defined('ABSPATH') || die( 'Direct access is not allowed.' );
if ( !class_exists('ATBDP_Email') ):
/**
 * Class ATBDP_Email
 */
class ATBDP_Email {

    public function replace_content($content, $order_id=0, $listing_id=0, $user=null)
    {
        /*@todo; we can check for order id or listing id if provided or not in order to limit db query for option value*/
        $listing_id         = get_post_meta( $order_id, 'listing_id', true );
        $post_author_id     = get_post_field( 'post_author', $listing_id );
        $user               = get_userdata( $post_author_id );
        $site_name          = get_option('blogname');
        $site_url           = site_url();
        $l_title            = get_the_title( $listing_id );
        $listing_url        = get_permalink( $listing_id );
        $date_format        = get_option( 'date_format' );
        $time_format        = get_option( 'time_format' );
        $current_time       = current_time( 'timestamp' );
        $exp_date           = ''; // @todo; add expiration date later
        $renewal_link       = ''; // @todo; add renewal link later
        $cat_name           = ''; // @todo; add cat name later
        $find_replace =  array(
            '==NAME=='                  => !empty($user->display_name) ? $user->display_name : '' ,
            '==USERNAME=='              => !empty($user->user_login) ? $user->user_login : '' ,
            '==SITE_NAME=='             => $site_name,
            '==SITE_LINK=='             => sprintf( '<a href="%s">%s</a>', $site_url, $site_name ),
            '==SITE_URL=='              => sprintf( '<a href="%s">%s</a>', $site_url, $site_url ),
            '==EXPIRATION_DATE=='       => $exp_date,
            '==CATEGORY_NAME=='         => $cat_name,
            '==RENEWAL_LINK=='          => $renewal_link,
            '==LISTING_ID=='            => $listing_id,
            '==LISTING_TITLE=='         => $l_title,
            '==LISTING_LINK=='          => sprintf( '<a href="%s">%s</a>', $listing_url, $l_title ),
            '==LISTING_URL=='           => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_url ),
            '==ORDER_ID=='              => $order_id,
            '==ORDER_RECEIPT_URL=='     => ATBDP_Permalink::get_payment_receipt_page_link($order_id),
            '==ORDER_DETAILS=='         => ATBDP_Order::get_order_details( $order_id ),
            '==TODAY=='                 => date_i18n( $date_format, $current_time ),
            '==NOW=='                   => date_i18n( $date_format . ' ' . $time_format, $current_time ),
        );
        // find and replace and return
        return strtr($content, $find_replace);
    }


    /**
     * Get the list of emails to send admin notification
     *
     * @since    3.1.0
     *
     * @param	 array|string    $email_lists           [optional] Email Lists.
     * @return	 string|array    $to                    Array or comma-separated list of email addresses to send message. Default admin_email
     */
    public function get_admin_email_list( $email_lists = '' ) {
        return !empty($email_lists)
            ? $email_lists
            : array_map('trim',
                explode(',', get_directorist_option('admin_email_lists', get_bloginfo( 'admin_email' )))
            );
    }

    /**
     * It sends an email
     *
     * @since 3.1.0
     * @param string|array  $to         Array or comma-separated list of email addresses to send message.
     * @param string        $subject    Email's Subject
     * @param string        $message    Email's body
     * @param string        $headers    Email's Header
     * @return bool         It returns true if mail is sent successfully. False otherwise.
     */
    public function send_mail($to, $subject, $message, $headers)
    {
        add_filter( 'wp_mail_content_type', array($this, 'html_content_type')); // set content type to html
        $sent = wp_mail( $to, html_entity_decode( $subject ), $message, $headers );
        /*@todo; check if we really need to remove the filter, as the above filter change the content type only when we call this function.*/
        remove_filter( 'wp_mail_content_type', array($this, 'html_content_type')); // remove content type from html
        return $sent;
    }


    /**
     * It returns content type 'text/html'
     * @return string
     */
    public function html_content_type()
    {
        return 'text/html'; // default is 'text/plain'; @pluggable.php @line 418
    }


    /**
     * Get the email header eg. From: and Reply-to:
     * @param array $data [optional] The array of name and the reply to email
     * @return string It returns the header of the email that contains From: $name and Reply to: $email
     */
    public function get_email_headers($data = array())
    {
        // get the data from the db
        $name = !empty($data['name']) ? sanitize_text_field($data['name']) : get_directorist_option('email_from_name', get_option('blogname'));
        $email = !empty($data['email']) ? sanitize_email($data['email']) : get_directorist_option('email_from_email', get_option('admin_email'));
        // build the header for email and return it @todo; is it better to trim here? test on free time.
        return "From: {$name} <{$email}>\r\nReply-To: {$email}\r\n";
    }

    /**
     * Send an email to notify the admin that an oder has been created
     *
     * @since 3.1.0
     * @param int $listing_id
     * @param int $order_id
     * @return bool Whether the email was sent successfully or not.
     */
    public function notify_admin_order_created($listing_id, $order_id)
    {
        /*@todo; think if it is better to assign disabled_email_notification to the class prop*/
        $disabled_email = get_directorist_option('disable_email_notification');
        if ($disabled_email) return false; //vail if email notification is off

        $notify_admin = get_directorist_option('notify_admin', array());
        if( ! in_array( 'order_created', $notify_admin ) ) return false; // vail if order created notification to admin off
        $listing = get_post($listing_id);
        $user = get_userdata($listing->post_author);
        $find_replace = array(
            '==NAME=='                  => $user->display_name,
            '==USERNAME=='              => $user->user_login,
            '==SITE_NAME=='             => get_option('blogname'),
            '==LISTING_ID=='            => $listing_id,
            '==LISTING_TITLE=='         => $listing->post_title,
            '==ORDER_ID=='              => $order_id,
            '==ORDER_RECEIPT_URL=='     => admin_url( "edit.php?post_type=atbdp_orders" ),
            '==ORDER_DETAILS=='         => ATBDP_Order::get_order_details( $order_id ),
        );
        $to = $this->get_admin_email_list( );

        $subject = __( '[==SITE_NAME==] You have a new order on your website', ATBDP_TEXTDOMAIN );
        //strtr is better than str_replace in this case: @see https://stackoverflow.com/questions/8177296/when-to-use-strtr-vs-str-replace
        $subject = strtr( $subject, $find_replace );

        $message = <<<KAMAL
Dear Administrator,

You have received a new order

This notification was for the order <strong> #==ORDER_ID== </strong> on the website <strong>==SITE_NAME==</strong>.
You can access the order details directly by clicking on the link below after logging in your back end:

==ORDER_RECEIPT_URL==

Here is the order summery:

==ORDER_DETAILS==

This email is sent automatically for information purpose only. Please do not respond to this.
KAMAL;

        $message = strtr( nl2br($message), $find_replace );

        $headers = $this->get_email_headers();
        return $this->send_mail( $to, $subject, $message, $headers );

    }

    /**
     * It notifies the listing owner via email when his order is created
     * @param int $listing_id   The listing ID
     * @param int $order_id     The Order ID
     * @param bool $offline     Whether the order is made using online payment or offline payment
     */
    public function notify_owner_order_created($listing_id, $order_id, $offline=false)
    {
        $disabled_email = get_directorist_option('disable_email_notification');
        if ($disabled_email) return; //vail if email notification is off
        $notify_user = get_directorist_option('notify_user', array());
        if(! in_array( 'order_created', $notify_user ) ) return; // vail if order created notification to owner off
        $post_author_id = get_post_field( 'post_author', $listing_id );
        $user           = get_userdata( $post_author_id );
        $to = $user->user_email;// get listing owner email
        // Send email according to the type of the payment that user used during checkout. get email template from the db.
        $offline = (!empty($offline)) ? 'offline' : '';
        $subject = $this->replace_content(get_directorist_option("email_sub_{$offline}_new_order"), $order_id, $listing_id, $user);
        $message = $this->replace_content(get_directorist_option("email_tmpl_{$offline}_new_order"), $order_id, $listing_id, $user);

        $headers = $this->get_email_headers( );
        $this->send_mail( $to, $subject, $message, $headers );
    }

    public function notify_owner_order_completed()
    {
        $disabled_email = get_directorist_option('disable_email_notification');
        if ($disabled_email) return; //vail if email notification is off
        $notify_user = get_directorist_option('notify_user', array());
        if(! in_array( 'order_created', $notify_user ) ) return; // vail if order created notification to owner off
        $post_author_id = get_post_field( 'post_author', $listing_id );
        $user           = get_userdata( $post_author_id );
        $to = $user->user_email;// get listing owner email
        // Send email according to the type of the payment that user used during checkout. get email template from the db.
        $offline = (!empty($offline)) ? 'offline' : '';
        $subject = $this->replace_content(get_directorist_option("email_sub_{$offline}_new_order"), $order_id, $listing_id, $user);
        $message = $this->replace_content(get_directorist_option("email_tmpl_{$offline}_new_order"), $order_id, $listing_id, $user);

        $headers = $this->get_email_headers( );
        $this->send_mail( $to, $subject, $message, $headers );
    }



} // ends class
endif;
