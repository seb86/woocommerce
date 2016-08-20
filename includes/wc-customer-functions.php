<?php
/**
 * WooCommerce Customer Functions
 *
 * Functions for customer specific things.
 *
 * @author   WooThemes
 * @category Core
 * @package  WooCommerce/Functions
 * @version  2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Add customer meta data field for a customer.
 *
 * @param  int    $customer_id (Required) The customer ID is for
 * @param  string $meta_key    (Required) The meta key
 * @param  mixed  $meta_value  (Required) The meta value
 * @param  bool   $unique      (Optional) Default false.
 *                             Whether the specified metadata key should be unique for the object.
 *                             If true, and the object already has a value for the specified metadata key,
 *                             no change will be made.
 * @return mixed               Will be an array if $single is false. Will be value of meta data field if $single is true.
 */
function wc_add_customer_meta( $customer_id, $meta_key, $meta_value, $unique = false ) {
	return add_metadata( 'customer', $customer_id, $meta_key, $meta_value, $unique );
}

/**
 * Returns the customer meta data of a specific field.
 *
 * @param  int    $customer_id (Required) The customer ID is for
 * @param  string $meta_key    (Optional) The meta key to retrieve. By default, returns data for all keys.
 * @param  bool   $single      (Optional) Whether to return a single value.
 * @return mixed               Will be an array if $single is false. Will be value of meta data field if $single is true.
 */
function wc_get_customer_meta( $customer_id = 0, $meta_key = '', $single = false ) {
	return get_metadata( 'customer', $customer_id, $meta_key, $single );
}

/**
 * Update customer meta data field for a customer.
 *
 * @param  int      $customer_id (Required) The customer ID is for
 * @param  string   $meta_key    (Required) The meta key
 * @param  mixed    $meta_value  (Required) The meta value to update
 * @param  mixed    $prev_value  (Required) The previous meta value to change
 * @return int|bool
 */
function wc_update_customer_meta( $customer_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( 'customer', $customer_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Delete customer meta data field for a customer.
 *
 * @param  int      $customer_id (Required) The customer ID is for
 * @param  string   $meta_key    (Required) The meta key
 * @return int|bool
 */
function wc_delete_customer_meta( $customer_id, $meta_key ) {
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

	// If the meta key is not a default customer meta key then delete it.
	if ( ! in_array( $meta_key, $default_meta_keys ) ) {
		return delete_metadata( 'customer', $customer_id, $meta_key );
	}

	return false;
}

/**
 * Returns the customer ID if the user is a customer.
 *
 * @param  int $user_id The WordPress user ID
 * @return int
 */
function wc_get_customer_id( $user_id = 0; ) {
	$logged_in_user = get_current_user_id();

	if ( isset( $user_id ) && $user_id > 0 ) {
		return wc_get_customer( $user_id, 'customer_id' );
	} else {
		return wc_get_customer( $logged_in_user, 'customer_id' );
	}

	return false;
}

/**
 * Returns the customer ID if the user is a customer.
 *
 * @global $wpdb
 * @param  int    $user_id
 * @param  string $field
 * @return int|string|date|array
 */
function wc_get_customer( $user_id = 0, $field = '' ) {
	if ( isset( $user_id ) && $user_id > 0 ) {

		// If field is not set then return
		if ( ! isset( $field ) ) return false;

		global $wpdb;

		$table = $wpdb->prefix . 'woocommerce_customers';

		$results = $wpdb->get_var( $wpdb->prepare( "SELECT `{$field}` FROM `{$table}` WHERE `user_id` = %s", $user_id ) );

		return $results;
	}
}
