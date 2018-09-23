<?php
/**
 * User class of Directorist
 *
 * This class is for interacting database table
 *
 * @package     Directorist
 * @subpackage  Classes/User
 * @copyright   Copyright (c) 2018, AazzTech
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) die('What the hell are you doing here accessing this file directly');
if (!class_exists('ATBDP_User')):
class ATBDP_User {
    
    
    
    
    public function __construct()
    {
        add_action('wp_loaded', array($this, 'handle_user_registration'));
        //add_action('init', array($this, 'activate_user'));
        add_filter('pre_get_posts', array($this,'restrict_listing_to_the_author'));
        // allow contributor upload images for now. @todo; later it will be better to add custom rules and capability
        add_action('plugins_loaded', array($this, 'user_functions_ready_hook'));// before we add custom image uploading, lets use WordPress default image uploading by letting subscriber and contributor upload imaging capability
        add_filter( 'login_redirect', array($this, 'redirect_non_admin_to_dashboard'), 10, 3 );

    }


    public function user_functions_ready_hook()
    {
        //Allow Contributors/Subscriber to Add Media
        $user = wp_get_current_user();
        if ( (in_array( 'contributor', (array) $user->roles ) || in_array( 'subscriber', (array) $user->roles )) && !current_user_can('upload_files') ) {
            add_action('init', array($this, 'allow_contributor_uploads'));
        }



    }


    public function allow_contributor_uploads()
    {
        /*@todo; this is the temporary solution to let user upload image and listing. After the releasing the plugin, we need to work with custom rules and permission.*/
        $contributor = get_role('contributor');
        $subscriber = get_role('subscriber');

        $contributor->add_cap('upload_files');
        $subscriber->add_cap('upload_files');
    }


    /**
     * If a user trying to login is a subscriber, send him to his dashboard on the front end.
     * @param $redirect_to
     * @param $requested_redirect_to
     * @param $user
     * @return string
     */
    function redirect_non_admin_to_dashboard($redirect_to, $requested_redirect_to, $user ) {
        global $user;
        if( ! isset( $user->ID ) ) {
            return $redirect_to;
        }

        /*@todo; redirect non admin users to their front-end dashboard. */
        if ( ! in_array( 'administrator', (array) $user->roles ) ) {
                $redirect_to  = ATBDP_Permalink::get_dashboard_page_link();
        }
        return wp_validate_redirect( $redirect_to, home_url() ); // return a safe redirect url between the site.
    }


    public function activate_user()
    {
        $user_id = filter_input( INPUT_GET, 'user', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
        if ( $user_id ) {
            // get user meta activation hash field
            $code = get_user_meta( $user_id, 'has_to_be_activated', true );
            $key = filter_input( INPUT_GET, 'key' );
            if ( $code == $key ) {
                delete_user_meta( $user_id, 'has_to_be_activated' );
                wp_safe_redirect(wp_login_url());
            }
        }
    }
    public function registration_form( ) {
        ATBDP()->load_template('front-end/user-registration-form');
    }

    /**
     * It registers a user. It is a private function, All the vars this function uses will be passed into it after proper validation and sanitization
     * @param $username
     * @param $password
     * @param $email
     * @param $website
     * @param $first_name
     * @param $last_name
     * @param $bio
     * @return bool|int|WP_Error
     */
    private function complete_registration($username, $password, $email, $website, $first_name, $last_name, $bio) {
        global $reg_errors, $username, $password, $email, $website, $first_name, $last_name,  $bio;
        $reg_errors = new WP_Error;
        if ( 1 > count( $reg_errors->get_error_messages() ) ) {
            $userdata = array(
                'user_login'    =>   $username,
                'user_email'    =>   $email,
                'user_pass'     =>   $password,
                'user_url'      =>   $website,
                'first_name'    =>   $first_name,
                'last_name'     =>   $last_name,
                'description'   =>   $bio,
            );
            return wp_insert_user( $userdata ); // return inserted user id or a WP_Error
        }
        return false;
    }

    public function registration_validation( $username, $password, $email, $website, $first_name, $last_name, $bio )  {
        global $reg_errors;
        $reg_errors = new WP_Error;
        if ( empty( $username ) || empty( $password ) || empty( $email ) ) {
            $reg_errors->add('field', __('Required form field is missing. Please fill all required fields.', ATBDP_TEXTDOMAIN));
        }

        if (!empty( $username ) && 4 > strlen( $username ) ) {
            $reg_errors->add( 'username_length', __('Username too short. At least 4 characters is required', ATBDP_TEXTDOMAIN) );
        }

        if ( username_exists( $username ) )
            $reg_errors->add('user_name', __('Sorry, that username already exists!', ATBDP_TEXTDOMAIN));


        if ( ! validate_username( $username ) ) {
            $reg_errors->add( 'username_invalid', __('Sorry, the username you entered is not valid', ATBDP_TEXTDOMAIN) );
        }

        if ( ! empty( $password ) && 5 > strlen( $password ) ) {
            $reg_errors->add( 'password', __('Password length must be greater than 5', ATBDP_TEXTDOMAIN) );
        }

        if ( !is_email( $email ) ) {
            $reg_errors->add( 'email_invalid', __('Email is not valid', ATBDP_TEXTDOMAIN) );
        }
        if ( email_exists( $email ) ) {
            $reg_errors->add( 'email', __('Email Already in use', ATBDP_TEXTDOMAIN) );
        }
        if ( ! empty( $first_name ) ) {
            if (!is_string($first_name)) {
                $reg_errors->add('First Name', __('First Name must be letters or combination of letters and number', ATBDP_TEXTDOMAIN));
            }
        }
        if ( ! empty( $last_name ) ) {
            if (!is_string($last_name)) {
                $reg_errors->add('Last Name', __('Last Name must be letters or combination of letters and number', ATBDP_TEXTDOMAIN));
            }
        }

        if ( ! empty( $website ) ) {
            if ( ! filter_var( $website, FILTER_VALIDATE_URL ) ) {
                $reg_errors->add( 'website', __('Website is not a valid URL', ATBDP_TEXTDOMAIN) );
            }
        }
        // if we have errors then returns a string of error message.
        $e = $reg_errors->get_error_messages(); // save the errors in a placeholder var as we can not use function inside empty() until php 5.5.
        if ( is_wp_error( $reg_errors ) && !empty($e) ) {
            //@todo; errors should be kept in the session using a helper function so that we can get all the errors after redirection
            $err_msg = '';
            foreach ( $reg_errors->get_error_messages() as $error ) {
                $err_msg .= '<div>';
                $err_msg .= '<strong>ERROR</strong>:';
                $err_msg .= $error . '<br/>';
                $err_msg .= '</div>';

            }
            return apply_filters('atbdp_registration_error', $err_msg);

        }
        return 'passed';



    }


    public function handle_user_registration() {
        // if the form is submitted then save the form
        if ( isset($_POST['atbdp_user_submit'] ) ) {
            $username = !empty($_POST['username']) ? $_POST[ 'username' ] : '';
                $password = !empty($_POST['password']) ? $_POST[ 'password' ] : '';
                $email = !empty($_POST['email']) ? $_POST[ 'email' ] : '';
                $website = !empty($_POST['website']) ? $_POST[ 'website' ] : '';
                $first_name = !empty($_POST['fname']) ? $_POST[ 'fname' ] : '';
                $last_name = !empty($_POST['lname']) ? $_POST[ 'lname' ] : '';
                $bio = !empty($_POST['bio']) ? $_POST[ 'bio' ] : '';
            // validate all the inputs
            $validation = $this->registration_validation( $username, $password, $email, $website, $first_name, $last_name, $bio );
            if ('passed' !== $validation){

                wp_safe_redirect(ATBDP_Permalink::get_registration_page_link(array('errors' => true)));

                exit();
            }

            // sanitize user form input
            global $username, $password, $email, $website, $first_name, $last_name, $bio;
            $username   =   sanitize_user( $_POST['username'] );
            $password   =   sanitize_text_field( $_POST['password'] );
            $email      =   sanitize_email( $_POST['email'] );
            $website    =   esc_url_raw( $_POST['website'] );
            $first_name =   sanitize_text_field( $_POST['fname'] );
            $last_name  =   sanitize_text_field( $_POST['lname'] );
            $bio        =   sanitize_textarea_field( $_POST['bio'] );

            // call @function complete_registration to create the user
            // only when no WP_error is found
            $user_id = $this->complete_registration($username, $password, $email, $website, $first_name, $last_name, $bio);
            if ($user_id && !is_wp_error( $user_id )) {
                // user has been created successfully, now work on activation process
                wp_new_user_notification($user_id, null, 'both'); // send activation to the user and the admin
                wp_safe_redirect(ATBDP_Permalink::get_registration_page_link(array('success' => true)));

                exit();
            } else {
                wp_safe_redirect(ATBDP_Permalink::get_registration_page_link(array('errors' => true)));

                exit();
            }


        }

    }

    public function user_dashboard()
    {
        // load user dashboard on the front end
        ATBDP()->load_template('front-end/user-dashboard');
    }

    /**
     * It returns all the listing of the current user
     * @return WP_Query   it returns an object of the WP_Query class with the items/listings on success and false on failure.

     */
    public function current_user_listings()
    {
        return ATBDP()->listing->db->get_listing_by_user(); // it returns all the listing of the current user.
    }

    /**
     * It updates the user profile and meta data
     * @param array $data the user data to update.
     * @return bool It returns true on success and false on failure
     */
    public function update_profile($data)
    {
        /**
       * Sample data
       * array (size=3)
        'user' =>
          array (size=10)
            'full_name' =>  'Kamal Ahmed' ,
            'first_name' =>  'Kamal' ,
            'last_name' =>  'Ahmed' ,
            'user_email' =>  'kamalacca@gmail.com' ,
            'phone' =>  '1111-1111-1111' ,
            'website' =>  '' ,
            'address' =>  '' ,
            'current_pass' =>  '' ,
            'new_pass' =>  '' ,
            'confirm_pass' =>  '' ,
        'action' =>  'update_user_profile' ,
        'atbdp_nonce_js' =>  'b49cc5b8dd' ,
      */

        $userdata = array();
        // we need to sanitize the data and then save it.
        $ID = !empty($data['ID']) ? absint($data['ID']) : get_current_user_id();
        $userdata['ID'] = $ID;
        $userdata['display_name'] = !empty($data['full_name']) ? sanitize_text_field(trim($data['full_name'])) : '';
       $userdata['user_email'] = !empty($data['user_email']) ? sanitize_email($data['user_email'] ): '';
       $userdata['user_url'] = !empty($data['website']) ? esc_url_raw(trim($data['website'] )): '';
        $phone = !empty($data['phone']) ? sanitize_text_field(trim($data['phone'] )): '';
        $pro_pic = !empty($data['pro_pic']) ? esc_url_raw(trim($data['pro_pic'] )): '';
        $first_name = !empty($data['first_name']) ? sanitize_text_field(trim($data['first_name'])) : '';
        $last_name = !empty($data['last_name']) ? sanitize_text_field(trim($data['last_name'] )): '';
       $address = !empty($data['address']) ? sanitize_text_field(trim($data['address'] )): '';
       $current_pass = !empty($data['current_pass']) ? sanitize_text_field(trim($data['current_pass'] )): ''; // match with with the hash in DB

       $new_pass = !empty($data['new_pass']) ? sanitize_text_field(trim($data['new_pass'] )): '';
       $confirm_pass = !empty($data['confirm_pass']) ? sanitize_text_field(trim($data['confirm_pass'] )): '';

       $user = get_userdata($ID); // get current user data to check if the provided pass match current usr pass

        //@TODO: add functionality to alert user that his password has not been changed if he did not insert
       if (( wp_hash_password( $current_pass) === $user->user_pass ) && ( $new_pass === $confirm_pass )){
           // user entered correct current password and his new password is valid, so lets set it to data
           $userdata['user_pass'] = $new_pass;
       }
        // now lets save the data to the db
        $uid = wp_update_user($userdata);

        update_user_meta( $ID, 'address', $address );
        update_user_meta( $ID, 'first_name', $first_name );
        update_user_meta( $ID, 'last_name', $last_name );
        update_user_meta( $ID, 'phone', $phone );
        update_user_meta( $ID, 'pro_pic', $pro_pic );

        if (!is_wp_error($uid)) return true;

        return false; // failed to save data, so return false

    }

    /**
     * It prevent the user from showing other posts/listings on dashboard if he is not an admin
     * @param Object|WP_Query $query
     * @return Object|WP_Query
     */
    public function restrict_listing_to_the_author($query)
    {
        global $pagenow, $post_type;


        if( ATBDP_POST_TYPE == $post_type && 'edit.php' == $pagenow && $query->is_admin && !current_user_can( 'edit_others_'.ATBDP_POST_TYPE.'s' ) ){
            global $user_ID;
            $query->set('author', $user_ID );
        }


        return $query;
    }


    } // ends ATBDP_User


endif; // if (!class_exists('ATBDP_User')):
