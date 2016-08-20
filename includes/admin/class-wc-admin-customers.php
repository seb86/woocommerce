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

	public static function set_screen( $status, $option, $value ) {
		if ( 'customers_per_page' == $option ) return $value;
	}

	/**
	 * Screen options.
	 *
	 * @access public
	 * @global $customers_page
	 */
	public static function screen_option() {
		global $customers_page;
 
		$screen = get_current_screen();

		// Get out of here if we are not on the customers table page.
		if ( !is_object($screen) || $screen->id != $customers_page ) {
			return;
		}
 
		$args = array(
			'label'   => __( 'Customers', 'woocommerce' ),
			'default' => 20,
			'option'  => 'customers_per_page'
		);

		add_screen_option( 'per_page', $args );
	}

	/**
	 * Handles output of the customers page in admin.
	 *
	 * @access public
	 */
	public static function output() {
		if ( ! isset( $_GET['view-user'] ) ) {

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
			<h1><?php _e( 'Customers', 'woocommerce' ); ?></h1>

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

}

endif;

return new WC_Admin_Customers();