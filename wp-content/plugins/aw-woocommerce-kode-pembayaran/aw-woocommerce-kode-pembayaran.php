<?php
/**
 *
 * @link            https://www.agenwebsite.com
 * @since           1.0.0
 * @package         AW WooCommerce Kode Pembayaran
 *
 * @wordpress-plugin
 * Plugin Name:     AW WooCommerce Kode Pembayaran ( Free Version )
 * Plugin URI:      https://www.agenwebsite.com/products/woocommerce-tiki-shipping
 * Description:     Plugin untuk menambahkan kode pembayaran di WooCommerce agar memudahkan penjual untuk mengecek transfer dari pembeli.
 * Version:         1.1.4
 * Author:          AgenWebsite
 * Author URI:      https://www.agenwebsite.com
 * License:         GPL-2.0+
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.txt
 * WC requires at least: 4.0.0
 * WC tested up to: 3.9.1
 */

if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if( !class_exists( 'wc_kode_pembayaran' ) ):

class wc_kode_pembayaran{

    /**
     * Constructor
     *
     * @return void
     * @since 1.1.2
     */
    public function __construct(){
        $this->defines();
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Defines
     *
     * @return void
     * @since 1.1.2
     */
    public function defines(){
        define('KODE_PEMBAYARAN_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
    }

    /**
     * Includes
     * Load requires files
     *
     * @return void
     * @since 1.1.2
     */
    public function includes(){
        require_once('includes/aw-tools.php');
        require_once('includes/class.admin-settings.php');

        new KodePembayaranSettings();
    }

    /**
     * Init Hooks
     * Hook and filter for run this plugin
     *
     * @return void
     * @since 1.1.2
     */
    public function init_hooks(){
        add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts_admin' ) );
        add_action( 'woocommerce_cart_calculate_fees', array( $this, 'add_kode' ) );
        add_filter( 'plugin_action_links_'.plugin_basename(__FILE__), array( $this, 'links' ), 10, 4 );        
        add_action( 'woocommerce_thankyou', array($this, 'remove_cookie'), 10, 1 );
    }

    /**
     * Load Scripts admin
     * Register and enqueue script admin
     *
     * @return void
     * @since 1.1.2
     */
    public function load_scripts_admin(){
        wp_enqueue_style( 'kode-pembayaran-admin', plugin_dir_url(__FILE__) . '/assets/css/admin.style.css' );
    }

    /**
     * Link
     * Add link settings
     *
     * @return void
     * @since 1.1.2
     */
    public function links($actions, $plugin_file, $plugin_data, $context){
        array_unshift($actions, '<a href="'.admin_url( 'admin.php?page=woocommerce-kode-pembayaran' ).'">Settings</a>');
        array_unshift($actions, '<a href="https://www.agenwebsite.com/products/woocommerce-kode-pembayaran" target="new">' . __( 'Buy Full Version', 'agenwebsite' ) . '</a>');
        return $actions;
    }

    /**
     * Get Option
     *
     * @return void
     * @since 1.1.2
     */
    private function get_option( $slug ){
        $option_name = sprintf( 'wc_kode_pembayaran_%s', $slug );
        $options = get_option($option_name);
        return $options;
    }

    /**
     * Add Kode
     * Main function. to add kode pembayaran to table checkout
     *
     * @return void
     * @since 1.1.2
     */
    public function add_kode(){
		global $woocommerce;

		$enable = $this->get_option('enable');
		$title = $this->get_option('judul');

		if ( $enable == 1 && $woocommerce->cart->subtotal != 0){
			if(! is_cart()){
                $min_number = 100;
                $max_number = 999;

                if(!isset($_COOKIE['kodepembayaran'])){
                    $cost = setcookie('kodepembayaran', rand( $min_number, $max_number), time()+3600, COOKIEPATH, COOKIE_DOMAIN, false);
                }

                if(isset($_COOKIE['kodepembayaran'])){
                    $cost = $_COOKIE['kodepembayaran'];
                }

				if($cost != 0)
					$woocommerce->cart->add_fee( __($title, 'woocommerce'), $cost);
			}
		}
    }

    /**
     * Remove cookies
     * Ketika proses order sudah selesai maka perlu dihapus cookies kode acak
     *
     * @return void
     * @since 1.1.3
     */
    public function remove_cookie(){
        setcookie('kodepembayaran', "", time()-3600, COOKIEPATH, COOKIE_DOMAIN, false);
    }

}

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    // Run Class Kode Pembayaran
    new wc_kode_pembayaran();

}

endif;

