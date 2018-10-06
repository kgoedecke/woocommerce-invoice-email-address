<?php
/*
Plugin Name: WooCommerce Checkout Invoice Email Address
Plugin URI:  https://github.com/kgoedecke/woocommerce-invoice-email-address
Description: Allows customers to specify a separate email address for the delivery of invoices during the checkout process. This is in particular helpful if you customer wants to send the invoice directly to the accounting department.
Version:     1.0.0
Author:      HaveALook UG
Author URI:  https://havealooklabs.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: woocommerce-invoice-email-address
Domain Path: /languages
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}

$depends_plugins = array(
	'woocommerce/woocommerce.php',
	'woocommerce-germanized-pro/woocommerce-germanized-pro.php',
);

$plugin_is_activated = true;

foreach ( $depends_plugins as $plugin ) {
	if ( ! is_plugin_active( $plugin ) ) {
		$plugin_is_activated = false;
		break;
	}
}

if ( ! $plugin_is_activated ) {
	add_action( 'admin_notices', 'woo_invoice_email_address_depends_fail' );
	return;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-woocommerce-invoice-email-address-plugin.php' );
add_action( 'plugins_loaded', array( 'WooCommerce_Invoice_Email_Address_Plugin', 'get_instance' ) );
register_activation_hook( __FILE__, array( 'WooCommerce_Invoice_Email_Address_Plugin', 'activate' ) );

/**
 * Show a notice about the depend plugins is not activated.
 *
 * @since 1.0.0
 */
function woo_invoice_email_address_depends_fail() {
	$messages = array();

	$messages[] = sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( 'https://wordpress.org/plugins/woocommerce/' ), esc_html__( 'WooCommerce', 'woocommerce-invoice-email-address' ) );

	$messages[] = sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( 'https://vendidero.de/woocommerce-germanized/' ), esc_html__( 'WooCommerce Germanized Pro', 'woocommerce-invoice-email-address' ) );

	$html_message = sprintf( '<div class="error"><p><strong>%1$s</strong> %2$s %3$s</p></div>', esc_html__( 'WooCommerce Checkout Invoice Email Address', 'woocommerce-invoice-email-address' ), esc_html__( 'plugin are depends from follow plugins:', 'woocommerce-invoice-email-address' ), join( ', ', $messages ) );

	echo wp_kses_post( $html_message );
}
