<?php
/**
 * Admin View: Page - Customer
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$customer_id = ''; // Start a new customer until customer has been identified.

// Are we adding or editing a customer?
$current_mode = ! empty( $_REQUEST['mode'] ) ? sanitize_title( $_REQUEST['mode'] ) : 'add';

// If we are editing a customer then fetch the customers details.
if ( ! empty( $_REQUEST['customer_id'] ) && $mode = 'edit' ) {
	$customer = wc_get_customer( $_REQUEST['customer_id'] );

	$customer_id = $customer['customer_id'];
}
// If the customer ID was not set then it's safe to assume we are adding a new customer.
else {
	$current_mode = 'add';
}

$modes = array(
	'add'  => __( 'Add Customer', 'woocommerce' ),
	'edit' => sprintf( __( 'Customer ID: #%s', 'woocommerce' ), $customer_id )
);
?>
<div class="wrap woocommerce wc_customer_wrap">
	<div class="icon32 icon32-customers-customer" id="icon-woocommerce"><br /></div>
	<h1 class="screen-reader-text"><?php echo esc_html( $modes[ $current_mode ] ); ?></h1>

	<?php
	switch( $current_mode ) {
		case 'add':
			WC_Admin_Customers::add_customer();
			break;

		case 'edit':
			WC_Admin_Customers::edit_customer( $customer );
			break;
	}
	?>

</div>
