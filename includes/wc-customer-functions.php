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
		'billing_first_name',
		'billing_last_name',
		'billing_company',
		'billing_phone',
		'billing_email',
		'billing_address_1',
		'billing_address_2',
		'billing_city',
		'billing_postcode',
		'billing_country',
		'billing_state',
		'shipping_first_name',
		'shipping_last_name',
		'shipping_company',
		'shipping_address_1',
		'shipping_address_2',
		'shipping_city',
		'shipping_postcode',
		'shipping_country',
		'shipping_state',
		'paying_customer',
		'_money_spent',
		'_order_count',
		'last_updated',
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
function wc_get_customer_id( $user_id = 0 ) {
	$logged_in_user = get_current_user_id();

	if ( isset( $user_id ) && $user_id > 0 ) {
		return wc_get_customer( $user_id, 'customer_id' );
	} else {
		return wc_get_customer( $logged_in_user, 'customer_id' );
	}

	return false;
}

/**
 * Returns the customer email if the user/guest is a customer.
 *
 * @param  string $user_email WP User or Guest customer email address
 * @return int
 */
function wc_get_customer_email( $user_email = '' ) {
	$logged_in_user = get_current_user_id();

	if ( isset( $user_email ) ) {
		return wc_get_customer( '', 'email' );
	} else {
		return wc_get_customer( $logged_in_user, 'email' );
	}

	return false;
}

/**
 * Returns the customer field if customer exists.
 *
 * @global object $wpdb    WP Database
 * @param  int    $user_id
 * @param  string $field
 * @return int|bool|string|date|array
 */
function wc_get_customer( $user_id = 0, $field = '' ) {
	// If field is not set then return
	if ( ! isset( $field ) ) return false;

	global $wpdb;

	$table = $wpdb->prefix . 'woocommerce_customers';

	$results = $wpdb->get_var( $wpdb->prepare( "SELECT `{$field}` FROM `{$table}` WHERE `user_id` = %s", $user_id ) );

	if ( isset( $results ) ) {
		return $results;
	}

	return false;
}

/**
 * Checks if a customer has already been assigned to a user.
 *
 * @param  int|string $user_value
 * @return bool
 */
function wc_check_customer_has_user( $user_value ) {
	// Check if customer has user by ID
	if ( is_int( $user_value ) ) {

		if ( wc_get_customer_id( $user_value ) ) {
			return true;
		}

	}
	// Check if guest customer has email
	else if( is_string( $user_value ) ) {

		if ( wc_get_customer_email( $user_value ) ) {
			return true;
		}

	}

	return false;
}

/**
 * Create a new customer.
 *
 * @global object   $wpdb       WP Database
 * @param  int      $user_id
 * @param  string   $email
 * @param  string   $first_name
 * @param  string   $last_name
 * @param  string   $guest_key
 * @return int|bool $customer_id Return customer ID
 */
function wc_create_new_customer( $user_id = '', $email = '', $first_name = '', $last_name = '', $guest_key = '' ) {
	global $wpdb;

	// First check that a customer was not already created and assigned to a user.
	if ( wc_check_customer_has_user( $user_id ) ) {
		return new WP_Error( 'customer_has_user_id', __( 'A customer has already been assigned to a user.', 'woocommerce' ) );
	}

	// Now check that a customer with the email was not already registered.
	if ( wc_check_customer_has_user( $user_email ) ) {
		return new WP_Error( 'customer_has_email', __( 'A customer already exists with this email address.', 'woocommerce' ) );
	}

	// All clear, create new customer.
	$compacted = compact( 'user_id', 'email', 'first_name', 'last_name', 'registered', 'guest_key' );
	$data = wp_unslash( $compacted );

	$wpdb->insert( $wpdb->customers, $data );
	$customer_id = (int) $wpdb->insert_id;

	// Update the user role to "Customer" if user id was provided.
	if ( isset( $user_id ) ) {
		wp_update_user( array( 'ID' => $user_id, 'role' => 'customer' ) );
	}

	return $customer_id;
}
