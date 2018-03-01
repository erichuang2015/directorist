<?php
/**
 * Gateway
 *
 * @package       directorist
 * @subpackage    directorist/includes/gateways
 * @copyright     Copyright 2018. AazzTech
 * @license       https://www.gnu.org/licenses/gpl-3.0.en.html GNU Public License
 * @since         3.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * ATBDP_Gateway Class
 *
 * @since    3.0.0
 * @access   public
 */

class ATBDP_Gateway{
    public function __construct()
    {
        add_filter('atbdp_extension_settings_submenus', array($this, 'gateway_settings_submenu'), 10, 1);

    }


    /**
     * It register the gateway settings submenu
     * @param array $submenus
     * @return array            It returns gateway submenu
     */
    function gateway_settings_submenu($submenus){
        $submenus['gateway_submenu'] =  array(
            'title' => __('Gateways Settings', ATBDP_TEXTDOMAIN),
            'name' => 'gateway_general',
            'icon' => 'font-awesome:fa-money',
            'controls' => apply_filters('atbdp_gateway_settings_controls', array(
                'gateways' => array(
                    'type' => 'section',
                    'title' => __('Gateway General Settings', ATBDP_TEXTDOMAIN),
                    'description' => __('You can Customize Gateway-related settings here. You can enable or disable any gateways here. Here, ON means Enabled, and OFF means disabled. After switching any option, Do not forget to save the changes.', ATBDP_TEXTDOMAIN),
                    'fields' => $this->get_gateway_settings_fields(),
                ),
            )),
        );
        //padded_var_dump($submenus);
        return $submenus;
    }

    function get_gateway_settings_fields(){
        return apply_filters('atbdp_gateway_settings_fields', array(
                array(
                    'type' => 'toggle',
                    'name' => 'enable_offline_payment',
                    'label' => __('Enable Offline Payment', ATBDP_TEXTDOMAIN),
                    'description' => __('Choose whether you want to accept offline Payment or not. Default is ON.', ATBDP_TEXTDOMAIN),
                    'default' => 1,
                ),

                array(
                    'type' => 'toggle',
                    'name' => 'gateway_test_mode',
                    'label' => __('Enable Test Mode', ATBDP_TEXTDOMAIN),
                    'description' => __('If you enable Test Mode, then no real transaction will occur. If you want to test the payment system of your website then you can set this option enabled.', ATBDP_TEXTDOMAIN),
                    'default' => 1,
                ),
                array(
                    'type' => 'checkimage',
                    'name' => 'offline_gateways',
                    'label' => __('Offline Gateways', ATBDP_TEXTDOMAIN),
                    'description' => __('Select the type of offline payment you want to accept based on your business model or listing type. Default is Bank Transfer.', ATBDP_TEXTDOMAIN),
                    'items' => apply_filters('atbdp_offline_gateways', array(
                        array(
                            'value' => 'bank_transfer',
                            'label' => __('Bank Transfer', ATBDP_TEXTDOMAIN),
                            'img' => esc_url(ATBDP_ADMIN_ASSETS . 'images/bank_icon.png'),

                        ),
                        array(
                            'value' => 'cash_on_delivery',
                            'label' => __('Cash On Delivery', ATBDP_TEXTDOMAIN),
                            'img' => esc_url(ATBDP_ADMIN_ASSETS . 'images/cash_icon.png'),

                        ),
                    )),

                    'default' => array(
                        'bank_transfer',
                    ),
                ),
                /*@todo; think whether it is good to list online payments here or in separate tab when a new payment gateway is added*/


                array(
                    'type' => 'notebox',
                    'name' => 'payment_currency_note',
                    'label' => __('Note About This Currency Settings:', ATBDP_TEXTDOMAIN),
                    'description' => __('This currency settings lets you customize how you would like to accept payment from your user/customer and how to display pricing on the order form/history.', ATBDP_TEXTDOMAIN),
                    'status' => 'info',
                ),
                array(
                    'type' => 'textbox',
                    'name' => 'payment_currency',
                    'label' => __( 'Currency Name', ATBDP_TEXTDOMAIN ),
                    'description' => __( 'Enter the Name of the currency eg. USD or GBP etc.', ATBDP_TEXTDOMAIN ),
                    'default' => 'USD',
                    'validation' => 'required',
                ),
                /*@todo; lets user use space as thousand separator in future. @see: https://docs.oracle.com/cd/E19455-01/806-0169/overview-9/index.html
                */
                array(
                    'type' => 'textbox',
                    'name' => 'payment_thousand_separator',
                    'label' => __( 'Thousand Separator', ATBDP_TEXTDOMAIN ),
                    'description' => __( 'Enter the currency thousand separator. Eg. , or . etc.', ATBDP_TEXTDOMAIN ),
                    'default' => ',',
                    'validation' => 'required',
                ),

                array(
                    'type' => 'textbox',
                    'name' => 'payment_decimal_separator',
                    'label' => __('Decimal Separator', ATBDP_TEXTDOMAIN),
                    'description' => __('Enter the currency decimal separator. Eg. "." or ",". Default is "."', ATBDP_TEXTDOMAIN),
                    'default' => '.',
                ),
                array(
                    'type' => 'select',
                    'name' => 'payment_currency_position',
                    'label' => __('Currency Position', ATBDP_TEXTDOMAIN),
                    'description' => __('Select where you would like to show the currency symbol. Default is before. Eg. $5', ATBDP_TEXTDOMAIN),
                    'default' => array(
                        'before',
                    ),
                    'items' => array(
                        array(
                            'value' => 'before',
                            'label' => __('$5 - Before', ATBDP_TEXTDOMAIN),
                        ),
                        array(
                            'value' => 'after',
                            'label' => __('After - 5$', ATBDP_TEXTDOMAIN),
                        ),
                    ),
                ),

            )
        );
    }


}


