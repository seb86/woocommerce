<?php
/**
 * Abstract Customer
 *
 * The WooCommerce customer class handles customer data.
 *
 * @class    WC_Customer
 * @version  2.7.0
 * @package  WooCommerce/Classes
 * @category Class
 * @author   WooThemes
 */
abstract class WC_Abstract_Customer {

	/** @public int Customer ID. */
	public $id = 0;

	/** @public int WordPress User ID. */
	public $user_id = 0;

	/** @public string Customer email address. */
	public $email = '';

	/** @public string Customer Name. */
	public $name = '';

	/** @public date Date of First Registration/Purchase. */
	public $registered = '';

	/** @public array Customers Orders. */
	public $order_ids = '';

	/** @public int Completed Purchase Count. */
	public $purchase_count = '';

	/** @public int Total amount the customer has spent. */
	public $total_spent = 0;

	/** @public string Guest Key. */
	public $guest_key = '';

	/** @public array Customer Meta data. */
	public $customer_meta = array();

	/**
	 * Get the customer if ID is passed, otherwise the customer is new and empty.
	 * This class should NOT be instantiated, but the get_customer function or new WC_Customer_Factory.
	 * should be used. It is possible, but the aforementioned are preferred and are the only 
	 * methods that will be maintained going forward.
	 *
	 * @access public
	 * @param  int|object|WC_Customer $customer Customer to init.
	 */
	public function __construct( $customer = 0 ) {
		$this->init( $customer );
	}

	/**
	 * Init/load the customer object. Called from the constructor.
	 *
	 * @access protected
	 * @param  int|object|WC_Customer $customer Customer to init.
	 */
	protected function init( $customer ) {
		if ( is_numeric( $customer ) ) {
			$this->id = absint( $customer );
			$this->get_customer( $this->id );
		} elseif ( $customer instanceof WC_Customer ) {
			$this->id = absint( $customer->id );
			$this->get_customer( $this->id );
		} elseif ( isset( $customer->ID ) ) {
			$this->id = absint( $customer->ID );
			$this->get_customer( $this->id );
		}
	}

	/**
	 * Return the customer ID.
	 *
	 * @since 2.7.0
	 * @return int customer ID
	 */
	public function get_customer_id() {
		return $this->id;
	}

	/**
	 * Return the customer email.
	 *
	 * @since  2.7.0
	 * @return string customer email
	 */
	public function get_customer_email() {
		return $this->email;
	}

	/**
	 * Return the customer name.
	 *
	 * @since  2.7.0
	 * @return string customer name
	 */
	public function get_customer_name() {
		return $this->name;
	}

	/**
	 * Return the customer date when they registered 
	 * or made the first purchase.
	 *
	 * @since  2.7.0
	 * @return string customer date
	 */
	public function get_customer_date() {
		return $this->registered;
	}

	/**
	 * Return the customer orders IDs.
	 *
	 * @since  2.7.0
	 * @return array customer order ID
	 */
	public function get_customer_order_ids() {
		return $this->order_ids;
	}

	/**
	 * Return the customer completed purchase count.
	 *
	 * @since  2.7.0
	 * @return string customer purchase count
	 */
	public function get_customer_purchase_count() {
		return $this->purchase_count;
	}

	/**
	 * Return the customer total amount spent.
	 *
	 * @since  2.7.0
	 * @return string customer total amount
	 */
	public function get_customer_total_spent() {
		return $this->total_spent;
	}

	/**
	 * Return the customer guest key.
	 *
	 * @since  2.7.0
	 * @return string customer guest key
	 */
	public function get_customer_guest_key() {
		return $this->guest_key;
	}

	/**
	 * Return the customer meta data.
	 *
	 * @access protected
	 * @since  2.7.0
	 * @param  int|object|WC_Customer $customer Customer to init.
	 * @return array $customer_meta
	 */
	protected function get_customer_meta( $customer ) {
		$default_meta_keys = array(
			'billing_address_1',
			'billing_address_2',
			'billing_city',
			'billing_postcode',
			'billing_country',
			'billing_state',
			'shipping_address_1',
			'shipping_address_2',
			'shipping_city',
			'shipping_postcode',
			'shipping_country',
			'shipping_state',
			'is_vat_exempt',
			'calculated_shipping',
		);

		// For each default meta key, get the customer meta.
		foreach ( $default_meta_keys as $meta ) {
			$customer_meta[$meta] = wc_get_customer_meta( $meta, $customer->ID, true );
		}

		/**
		 * Returns the customer meta data. Can be filtered by 
		 * third party plugins to return custom customer meta data.
		 */
		return apply_filters( 'woocommerce_customer_meta', $customer_meta, $customer );
	}

}