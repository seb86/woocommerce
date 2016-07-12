<?php
/**
 * Customers Table Class
 *
 * @author   WooThemes
 * @category Admin
 * @package  WooCommerce/Admin/Customers
 * @version  2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * WC_Customers_Table Class
 *
 * Renders the Customers table.
 */
class WC_Customers_Table extends WP_List_Table {

	/**
	 * Get things started
	 *
	 * @see WP_List_Table::__construct()
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => __( 'Customer', 'woocommerce' ),
			'plural'   => __( 'Customers', 'woocommerce' ),
			'ajax'     => false,
		) );
	}

	/**
	 * Retrieve customer’s data from the database.
	 *
	 * @param int $per_page
	 * @param int $page_number
	 * @global $wpdb
	 * @return mixed
	 */
	public function get_customers( $per_page = 20, $page_number = 1 ) {
		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}woocommerce_customers";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";

		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	/**
	 * Returns the count of customers in the database.
	 *
	 * @return null|string
	 */
	public function total_customers() {
		global $wpdb;

		$customer_count = $wpdb->get_var( "SELECT COUNT(*) FROM " . $wpdb->prefix . "woocommerce_customers" );

		return $customer_count;
	}

	/**
	 * Prepare the customers for the table to process.
	 *
	 * @access public
	 * @uses WC_Customers_Table::get_columns()
	 * @uses WP_List_Table::get_sortable_columns()
	 * @uses WC_Customers_Table::get_pagenum()
	 * @uses WC_Customers_Table::get_total_customers()
	 * @return void
	 */
	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = array(); // No hidden columns
		$sortable = $this->get_sortable_columns();

		$per_page     = $this->get_items_per_page( 'customers_per_page', 5 );
		$current_page = $this->get_pagenum();

		$data = $this->get_customers( $per_page, $current_page );
		usort( $data, array( $this, 'sort_data' ) );

		$total_items = $this->total_customers();

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
		) );

		$data = array_slice( $data, ( ($current_page-1) * $per_page ), $per_page );

		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $data;
	}

	/**
	 * Retrieve the table columns.
	 *
	 * @access public
	 * @return array $columns
	 */
	public function get_columns() {
		return apply_filters( 'woocommerce_admin_customer_columns', array(
			'thumb'          => __( 'Profile', 'woocommerce' ),
			'customer_id'    => __( 'Customer ID', 'woocommerce' ),
			'name'           => __( 'Name', 'woocommerce' ),
			'email'          => __( 'Email', 'woocommerce' ),
			'purchase_count' => __( 'Purchases', 'woocommerce' ),
			'registered'     => __( 'Date Registered', 'woocommerce' ),
			'customer_type'  => __( 'Customer Type', 'woocommerce' ),
		) );
	}

	/**
	 * Define the sortable columns.
	 *
	 * @access public
	 * @return array
	 */
	public function get_sortable_columns() {
		return apply_filters( 'woocommerce_admin_customer_sortable_columns', array(
			'thumb'          => array( 'thumb', false ),
			'customer_id'    => array( 'customer_id', true ),
			'name'           => array( 'name', true ),
			'email'          => array( 'email', false ),
			'purchase_count' => array( 'purchase_count', false ),
			'registered'     => array( 'date_registered', true ),
			'customer_type'  => array( 'customer_type', false ),
		) );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @access public
	 * @param array $item Contains all the data of the customers
	 * @param string $column_name The name of the column
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {

			case 'thumb' :
				$value = '<a href="' . esc_url( admin_url( '/admin.php?page=wc-customers&view-user=' . esc_html( $item['customer_id'] ) ) ) . '">' . get_avatar( $item['email'], 32 ) . '</a>';
				break;

			case 'customer_id':
				$value = '#' . $item['customer_id'] . ' - <a href="' . esc_url( admin_url( '/admin.php?page=wc-customers&view-user=' . esc_html( $item['customer_id'] ) ) ) . '">' . esc_html( __( 'View Customer', 'woocommerce' ) ) . '</a>';
				break;

			case 'name':
				$value = '<strong>' . $item[ $column_name ] . '</strong>';
				break;

			case 'email':
			case 'purchase_count':
				$value = $item[ $column_name ];
				break;

			case 'customer_type':
				$value = 'TODO: Add function here!';
				break;

			case 'registered':
				$value = date_i18n( get_option( 'date_format' ), strtotime( $item['registered'] ) );
				break;

			default:
				$value = isset( $item[ $column_name ] ) ? $item[ $column_name ] : null;
				break;
		}

		return apply_filters( 'woocommerce_admin_customers_column_' . $column_name, $value, $item['customer_id'] );
	}

}