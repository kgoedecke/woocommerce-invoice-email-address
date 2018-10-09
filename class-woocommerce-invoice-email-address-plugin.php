<?php
if ( ! class_exists( 'WooCommerce_Invoice_Email_Address_Plugin' ) ) {
	class WooCommerce_Invoice_Email_Address_Plugin {

		/**
		 * A reference to an instance of this class.
		 * 
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance;

		/**
		 * Returns an instance of this class.
		 * 
		 * @since  1.0.0
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {

			if ( null == self::$instance ) {
				self::$instance = new WooCommerce_Invoice_Email_Address_Plugin();
			}

			return self::$instance;
		}

		/**
		 * Initializes the plugin by setting filters and administration functions.
		 * 
		 * @since 1.0.0
		 */
		private function __construct() {
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			
			add_filter( 'woocommerce_checkout_fields', array( $this, 'add_invoice_email_field' ) );
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_order_meta' ), 11, 2 );

			add_filter( 'woocommerce_email_recipient_customer_invoice', array( $this, 'add_invoice_recipient' ), 11, 2 );
		}

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since 1.0.0
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain(
				'woocommerce-invoice-email-address',
				false,
				dirname( plugin_basename( __FILE__ ) ) . '/languages/'
			);
		}

		/**
		 * Adds a Invoice Email Address field to checkout page.
		 *
		 * @since 1.0.0
		 * @param array $fields
		 */
		public function add_invoice_email_field( $fields ) {

			if ( is_user_logged_in() ) {
				$fields['billing']['_invoice_email'] = array(
					'label'       => esc_html__( 'Invoice Email Address', 'woocommerce-invoice-email-address' ),
					'required'    => true,
					'class'       => array( 'form-row-wide' ),
					'clear'       => true,
					'priority'    => 115,
				);
			}
		
			return $fields;
		}

		/**
		 * Save invoice email. to the order meta.
		 *
		 * @since 1.0.0
		 * @param int   $order_id
		 * @param array $data
		 */
		public function update_order_meta( $order_id, $data ) {

			if ( ! empty( $_POST['_invoice_email'] ) ) {
				update_post_meta( $order_id, '_invoice_email', sanitize_email( $_POST['_invoice_email'] ) );
			}
		}

		/**
		 * Adds a new recipient.
		 *
		 * @param string $recipient
		 * @param WC_Order $order
		 * @return void
		 */
		public function add_invoice_recipient( $recipient, $order ) {
			$invoice_email = get_post_meta( $order->get_id(), '_invoice_email', true );
			$invoice_email = sanitize_email( $invoice_email );
	
			if ( ! empty( $invoice_email ) ) {
				$recipient .= ",{$invoice_email}";
			}
		
			return $recipient;
		}

		/**
		 * Fired when the plugin is activated.
		 * 
		 * Important: if WooCommerce Germanized Pro plugin's follow options are off, 
		 * than current plugin wouldn't send invoices.
		 *
		 * @since 1.0.0
		 */
		public static function activate() {
			$pre = apply_filters( 'woo_invoice_email_address/pre_update_options', true );

			if ( true !== $pre ) {
				return;
			}

			if ( get_option( 'woocommerce_gzdp_invoice_auto' ) != 'yes' ) {
				update_option( 'woocommerce_gzdp_invoice_auto', 'yes' );
			}

			if ( get_option( 'woocommerce_gzdp_invoice_auto_email' ) != 'yes' ) {
				update_option( 'woocommerce_gzdp_invoice_auto_email', 'yes' );
			}
		}
	}
}
