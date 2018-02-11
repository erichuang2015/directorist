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


