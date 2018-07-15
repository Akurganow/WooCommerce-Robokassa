<?php
/*
  Plugin Name: Robokassa Payment Gateway (Saphali)
  Plugin URI: 
  Description: Allows you to use Robokassa payment gateway with the WooCommerce plugin.
  Version: 1.0.5
  Author: Alexander Kurganov, Saphali
  Author URI: http://saphali.com
  Text Domain: robokassa-payment-gateway-saphali
  Domain Path: /languages
 */

//TODO: Выбор платежной системы на стороне магазина

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/* Add a custom payment class to WC
  ------------------------------------------------------------ */
add_action( 'plugins_loaded', 'woocommerce_robokassa' );

function woocommerce_robokassa() {
	load_plugin_textdomain( 'robokassa-payment-gateway-saphali', false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	} // if the WC payment gateway class is not available, do nothing
	if ( class_exists( 'WC_ROBOKASSA' ) ) {
		return;
	}
	if ( class_exists( 'WooCommerce_Payment_Status' ) ) {
		add_filter( 'woocommerce_valid_order_statuses_for_payment',
			array( 'WC_ROBOKASSA', 'valid_order_statuses_for_payment' ), 52, 2 );
	}

	/**
	 * Gateway class load
	 */
	include_once dirname(__FILE__) . '/class-wc-robokassa.php';

	/**
	 * Add the gateway to WooCommerce
	 **/
	function add_robokassa_gateway( $methods ) {
		$methods[] = 'WC_ROBOKASSA';

		return $methods;
	}

	add_filter( 'woocommerce_payment_gateways', 'add_robokassa_gateway' );
}

?>
