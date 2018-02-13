<?php
/**
 * Directorist Payment Functions
 *
 * @package     Directorist
 * @subpackage  Payment
 * @copyright   Copyright (c) 2018, AazzTech
 * @license     http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @since       3.0.0
 */


/*
 * Placeholder functions to work as blue prints. These functions to be extended with features as we work on.*/
/*IT WORKS LIKE AN INTERFACE*/

/**
 * @param string $status
 * @return string
 */
function atbdp_get_payment_status($status = ''){
    return $status;
}

/**
 * @param $payment_id
 * @param $status
 */
function atbdp_update_payment_status($payment_id, $status){

}

function atbdp_purchase_form_required_fields(){
    return array();
}

/**
 * Get all the payment statuses.
 *
 * @since    3.0.0
 *
 * @return   array    $statuses    A list of available payment status.
 */
function atbdp_get_payment_statuses() {

    $statuses = array(
        'created'   => __( "Created", ATBDP_TEXTDOMAIN ),
        'pending'   => __( "Pending", ATBDP_TEXTDOMAIN ),
        'completed' => __( "Completed", ATBDP_TEXTDOMAIN ),
        'failed'    => __( "Failed", ATBDP_TEXTDOMAIN ),
        'cancelled' => __( "Cancelled", ATBDP_TEXTDOMAIN ),
        'refunded'  => __( "Refunded", ATBDP_TEXTDOMAIN )
    );

    return apply_filters( 'atbdp_payment_statuses', $statuses );

}

/**
 * Get order bulk actions array.
 *
 * @since    3.0.0
 *
 * @return   array    $actions    An array of bulk list of order history status.
 */
function atbdp_get_payment_bulk_actions() {

    $actions = array(
        'set_to_created'   => __( "Set Status to Created", ATBDP_TEXTDOMAIN ),
        'set_to_pending'   => __( "Set Status to Pending", ATBDP_TEXTDOMAIN ),
        'set_to_completed' => __( "Set Status to Completed", ATBDP_TEXTDOMAIN ),
        'set_to_failed'    => __( "Set Status to Failed", ATBDP_TEXTDOMAIN ),
        'set_to_cancelled' => __( "Set Status to Cancelled", ATBDP_TEXTDOMAIN ),
        'set_to_refunded'  => __( "Set Status to Refunded", ATBDP_TEXTDOMAIN )
    );

    return apply_filters( 'atbdp_order_bulk_actions', $actions );

}



/**
 * Returns a nicely formatted amount.
 *
 * @since    3.0.0
 *
 * @param    string     $amount               Price amount to format
 * @param    bool       $decimals             Whether to use decimals or not. Useful when set to false for non-currency numbers.
 * @param    array      $currency_settings    Currency Settings. If we do not provide currency settings
 *                                            then it uses the general currency settings used to display formatted pricing
 *                                            on the front end. However, we can provide new currency settings array
 *                                            with 4 items currency name, thousand and decimal separators and
 *                                            the position of the currency symbol.
 *
 * @return   string     $amount               Newly formatted amount or Price Not Available
 */
function atbdp_format_amount( $amount, $decimals = true, $currency_settings = array() ) {

    if( empty( $currency_settings ) ) {
        $currency_settings = get_option( 'atbdp_currency_settings' );
    }

    //var_dump($currency_settings);

    $currency = ! empty( $currency_settings[ 'currency' ] ) ? $currency_settings[ 'currency' ] : 'USD';
    $thousands_sep = ! empty( $currency_settings[ 'thousands_separator' ] ) ? $currency_settings[ 'thousands_separator' ] : ',';
    $decimal_sep = ! empty( $currency_settings[ 'decimal_separator' ] ) ? $currency_settings[ 'decimal_separator' ] : '.';

    // Format the amount
    if( $decimal_sep == ',' && false !== ( $sep_found = strpos( $amount, $decimal_sep ) ) ) {
        $whole = substr( $amount, 0, $sep_found );
        $part = substr( $amount, $sep_found + 1, ( strlen( $amount ) - 1 ) );
        $amount = $whole . '.' . $part;
    }

    // Strip , from the amount (if set as the thousands separator)
    if( $thousands_sep == ',' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
        $amount = str_replace( ',', '', $amount );
    }

    // Strip ' ' from the amount (if set as the thousands separator)
    if( $thousands_sep == ' ' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
        $amount = str_replace( ' ', '', $amount );
    }

    if( empty( $amount ) ) {
        $amount = 0;
    }

    if( $decimals ) {
        $decimals  = atbdp_currency_decimal_count( 2, $currency );
    } else {
        $decimals = 0;
    }

    $formatted = number_format( $amount, $decimals, $decimal_sep, $thousands_sep );

    return apply_filters( 'atbdp_format_amount', $formatted, $amount, $decimals, $decimal_sep, $thousands_sep );

}

/**
 * Returns a nicely formatted currency amount.
 *
 * @since    3.0.0
 *
 * @param    string    $amount      Price amount to format
 * @param    bool       $decimals   Whether or not to use decimals. Useful when set to false for non-currency numbers.
 * @return   string                 Newly formatted amount or Price Not Available
 */
function atbdp_format_payment_amount( $amount, $decimals = true ) {

    return atbdp_format_amount( $amount, $decimals, atbdp_get_payment_currency_settings() );

}


/**
 * Get the directory's payment currency settings.
 *
 * @since    1.5.4
 * @return   array    $currency_settings    Currency settings array that contains currency name, thousand and decimal separators
 */
function atbdp_get_payment_currency_settings() {

    $payment_currency = get_directorist_option( 'payment_currency', get_directorist_option('g_currency', 'USD') ); // get the currency settings related to the payment
    $payment_thousand_separator = get_directorist_option( 'payment_thousand_separator', get_directorist_option('g_thousand_separator', ',') );
    $payment_decimal_separator = get_directorist_option( 'payment_decimal_separator', get_directorist_option('g_decimal_separator', '.') );
    $payment_currency_position = get_directorist_option( 'payment_currency_position', get_directorist_option('g_currency_position', 'before') );



        $currency_settings = array(
            'currency'            => !empty($payment_currency) ? $payment_currency : 'USD',
            'thousands_separator' => ! empty( $payment_thousand_separator ) ? $payment_thousand_separator : ',',
            'decimal_separator'   => ! empty( $payment_decimal_separator  ) ? $payment_decimal_separator  : '.',
            'position'            => !empty($payment_currency_position) ? $payment_currency_position : 'before'
        );



    return $currency_settings; // return the currency settings array

}


/**
 * Set the number of decimal places per currency
 *
 * @since    3.0.0
 *
 * @param    int       $decimals    Number of decimal places.
 * @param    string    $currency    Payment currency.
 * @return   int       $decimals
 */
function atbdp_currency_decimal_count( $decimals = 2, $currency = 'USD' ) {

    switch( $currency ) {
        case 'RIAL' :
        case 'JPY' :
        case 'TWD' :
        case 'HUF' :
            $decimals = 0;
            break;
    }

    return apply_filters( 'atbdp_currency_decimal_count', $decimals, $currency );

}

