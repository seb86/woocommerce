<?php
/**
 * Admin Customers
 *
 * Functions used for displaying customers and their details in the admin.
 *
 * @author   WooThemes
 * @category Admin
 * @package  WooCommerce/Admin/Customers
 * @version  2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Admin_Customers' ) ) :

/**
 * WC_Admin_Customers Class.
 */
class WC_Admin_Customers {

	public function __construct() {
		// Show blank state
		add_action( 'woocommerce_admin_customers_table_top', array( $this, 'maybe_render_no_customers' ), 10, 1 );

		// Set screen options
		add_filter( 'set-screen-option', array( $this, 'set_screen' ), 10, 3 );
	}

	/**
	 * Per page screen option value for the Customers list table.
	 *
	 * @access public
	 * @static
	 * @param  bool|int $status
	 * @param  string   $option
	 * @param  mixed    $value
	 * @return mixed
	 */
	public static function set_screen( $status, $option, $value ) {
		if ( 'wc_customers_per_page' === $option ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Add per page screen option to the Affiliates list table
	 *
	 * @access public
	 * @static
	 */
	public static function screen_option() {
		$screen = get_current_screen();

		// Get out of here if we are not on the customers table page.
		if ( $screen->id !== 'woocommerce_page_wc-customers' ) {
			return;
		}
 
		add_screen_option( 'per_page', array(
			'label'   => __( 'Number of Customers per page:', 'woocommerce' ),
			'option'  => 'wc_customers_per_page',
			'default' => 30,
		) );

		do_action( 'woocommerce_customers_screen_options', $screen );
	}

	/**
	 * Handles output of the customers page in admin.
	 *
	 * @access public
	 */
	public static function output() {
		if ( ! isset( $_REQUEST['mode'] ) ) {

			include_once( 'customers/class-wc-admin-customers-table.php' );
			return self::get_customers();

		} else {

			include_once( 'views/html-admin-page-customer.php' );

		}
	}

	/**
	 * Returns the definitions for the customers to show in admin.
	 *
	 * @access public
	 * @return array
	 */
	public static function get_customers() {
		$customers_table = new WC_Customers_Table();
		$customers_table->prepare_items();
		?>
		<div class="wrap woocommerce wc_customers_wrap">
			<div class="icon32 icon32-customers" id="icon-woocommerce"><br /></div>
			<h1><?php _e( 'Customers', 'woocommerce' ); ?> <a href="<?php echo admin_url( 'admin.php?page=wc-customers&mode=add' ); ?>" class="page-title-action"><?php _e( 'Add Customer', 'woocommerce' ); ?></a></h1>

			<?php do_action( 'woocommerce_admin_customers_table_top', $customers_table ); ?>

			<form id="wc-customers-filter" method="get" action="<?php echo admin_url( 'admin.php?page=wc-customers' ); ?>">
				<?php $customers_table->display(); ?>
				<input type="hidden" name="page" value="wc-customers" />
				<input type="hidden" name="view" value="customers" />
			</form>

			<?php do_action( 'woocommerce_admin_customers_table_bottom', $customers_table ); ?>
		</div>
		<?php
	}

	/**
	 * Maybe display a blank state if you have no customers.
	 *
	 * @access public
	 * @return void
	 */
	public static function maybe_render_no_customers( $customers ) {
		if ( 0 < $customers->total_customers() ) { return; }
	?>
	<div class="woocommerce-BlankState">
		<h2 class="woocommerce-BlankState-message"><?php _e( 'When you receive a new customer, their information will appear here.', 'woocommerce' ); ?></h2>
		<a class="woocommerce-BlankState-cta button-primary button" target="_blank" href="https://docs.woothemes.com/document/managing-customers/?utm_source=blankslate&utm_medium=customer&utm_content=customersdoc&utm_campaign=woocommerceplugin"><?php _e( 'Learn more about customers', 'woocommerce' ); ?></a>

		<style type="text/css">#posts-filter .wp-list-table, #posts-filter .tablenav.top, .wrap .subsubsub  { display: none; } </style>
	</div>
	<?php
	}

	/**
	 * Displays information about a customer and allows the 
	 * admin to edit the customers details.
	 *
	 * @access public
	 * @return void
	 */
	public static function add_customer() {
	} // END add_customer()

	/**
	 * Displays information about a customer and allows the 
	 * admin to edit the customers details.
	 *
	 * @access public
	 * @param  array $customer
	 * @return void
	 */
	public static function edit_customer( $customer ) {
		$customer_id = $customer['customer_id'];

	?>
		<div class="customer_profile">
			<div class="customer_profileimage"><?php echo get_avatar( $customer['email'], 140 ); ?></div>
			<h3><?php echo sprintf( '%s %s', $customer['first_name'], $customer['last_name'] ); ?></h3>
			<div class="customer_email"><?php echo $customer['email']; ?></div>
			<div class="customer_phone"><a href="tel:<?php echo wc_get_customer_meta( $customer_id, 'billing_phone', true ); ?>"><?php echo wc_get_customer_meta( $customer_id, 'billing_phone', true ); ?></a></div>
		</div>

		<div class="customer_ordercounter">
			<h3><?php _e( 'Orders', 'woocommerce' ); ?></h3>
			<span><?php echo wc_get_customer_order_count( $customer_id ); ?></span>
			<br/><a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_status=all&post_type=shop_order&_customer_user=' . esc_html( $customer_id ) ) ); ?>"><?php _e( 'View Customer Orders', 'woocommerce' ); ?></a>
		</div>

		<div class="customer_totalspent">
			<h3><?php _e( 'Total Spent', 'woocommerce' ); ?></h3>
			<span><?php echo wc_price( wc_get_customer_meta( $customer_id, '_money_spent', true ) ); ?></span>
		</div>

		<?php
		// For debugging purposes.
		if ( defined( 'WP_DEBUG' ) ) {

			echo sprintf( '<h3>%s</h3>', __( 'All Customer Meta Data', 'woocommerce' ) );
			$customer_meta = wc_get_customer_meta( $customer_id, '', true );

			$default_customer_meta_keys = wc_default_customer_meta_keys();
			echo '<code>';
			print_r($default_customer_meta_keys);
			echo '</code><br/>';

			foreach ( $customer_meta as $meta_key => $meta_value ) {
				// Custom meta data is returned if it starts with an underscore.
				if ( '_' == $meta_key[0] ) {
					echo $meta_key . ': ' . $meta_value[0] . '<br/>';
				} else {
					echo $default_customer_meta_keys[$meta_key] . ': ' . $meta_value[0] . '<br/>';
				}

			}

		} // END debug

	} // END edit_customer()

}

endif;

return new WC_Admin_Customers();