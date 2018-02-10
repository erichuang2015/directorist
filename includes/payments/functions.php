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
        'created'   => __( "Created", ATBDP_POST_TYPE ),
        'pending'   => __( "Pending", ATBDP_POST_TYPE ),
        'completed' => __( "Completed", ATBDP_POST_TYPE ),
        'failed'    => __( "Failed", ATBDP_POST_TYPE ),
        'cancelled' => __( "Cancelled", ATBDP_POST_TYPE ),
        'refunded'  => __( "Refunded", ATBDP_POST_TYPE )
    );

    return apply_filters( 'atbdp_payment_statuses', $statuses );

}