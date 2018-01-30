<?php

if ( !class_exists('ATBDP_Rewrite') ):

/**
 * Class ATBDP_Rewrite
 * It handle custom rewrite rules and actions etc.
 */
class ATBDP_Rewrite {

    public function __construct()
    {
        // add the rewrite rules to the init hook
        add_action( 'init', array( $this, 'add_write_rules' ) );
    }

    public function add_write_rules()
    {

    }
} // ends ATBDP_Rewrite

endif;