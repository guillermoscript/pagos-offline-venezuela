<?php
namespace base\publico;
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://netkiub.com
 * @since      1.0.0
 *
 * @package    Pagos_Offline_Venezuela
 * @subpackage Pagos_Offline_Venezuela/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Pagos_Offline_Venezuela
 * @subpackage Pagos_Offline_Venezuela/public
 * @author     Guillermo <guillomarindavila@gmail.com>
 */
class Pagos_Offline_Venezuela_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pagos_Offline_Venezuela_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pagos_Offline_Venezuela_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pagos-offline-venezuela-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pagos_Offline_Venezuela_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pagos_Offline_Venezuela_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( is_checkout() && ! isset( $_GET['pay_for_order'] ) )  {

			wp_enqueue_script( 'checkOutAjax', plugin_dir_url( __FILE__ ) . 'js/checkOutAjax.js', array( 'jquery' ), $this->version, false );
			wp_localize_script( 'checkOutAjax', 'ajax_var', array(
				'url'    => admin_url( 'admin-ajax.php' ),
				'nonce'  => wp_create_nonce( 'my-ajaxxx-nonce' ),
				'action' => 'recibir_imagen'
			) );
			
			add_filter( 'script_loader_tag', function ( $tag, $handle, $src ) {
				switch ( $handle ) {
					case 'checkOutAjax':
						return '<script type="module" src="' . esc_url( $src ) . '"></script>';
						break;
			
					default:
						return $tag;
						break;
				}
			},10,3);

		}

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pagos-offline-venezuela-public.js', array( 'jquery' ), $this->version, false );

	}

}
