<?php
namespace Includes;
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    PagosOfflineVenezuela
 * @subpackage PagosOfflineVenezuela/includes
 * @author     Guillermo <guillomarindavila@gmail.com>
 */

use Admin\Api\Routes\Routes;
use Includes\PagosOfflineVenezuelai18n;
use Includes\PagosOfflineVenezuelaLoader;

use Admin\PagosOfflineVenezuelaAdmin;
use Publico\PagosOfflineVenezuelaPublico;

use Admin\Init;



class PagosOfflineVenezuela {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      PagosOfflineVenezuelaLoader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PAGOS_OFFLINE_VENEZUELA_VERSION' ) ) {
			$this->version = PAGOS_OFFLINE_VENEZUELA_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'pagos-offline-venezuela';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - PagosOfflineVenezuelaLoader. Orchestrates the hooks of the plugin.
	 * - PagosOfflineVenezuelai18n. Defines internationalization functionality.
	 * - PagosOfflineVenezuelaAdmin. Defines all hooks for the admin area.
	 * - PagosOfflineVenezuelaPublico. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		// require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pagos-offline-venezuela-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		// require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pagos-offline-venezuela-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		// require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-pagos-offline-venezuela-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		// require_once plugin_dir_path( dirname( __FILE__ ) ) . 'publico/class-pagos-offline-venezuela-public.php';

		$this->loader = new PagosOfflineVenezuelaLoader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the PagosOfflineVenezuelai18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new PagosOfflineVenezuelai18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new PagosOfflineVenezuelaAdmin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new PagosOfflineVenezuelaPublico( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
		$this->register_services();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    PagosOfflineVenezuelaLoader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
	/**
     * guarda las clases en un array para automatizar todo
     * y retorna el array con las clases
     * 
     * 
     * Siempre que quiera inicializar una clase , paso por eso y las agg al array
     * es lo que tengo entendido 
     */
    public static function get_services()
    {
        return [
			init::class,
			Routes::class,
			// RestApiV1::class,
        ];
    }

    /**
     * hace un loop en las clases, inicializa las clases
     * y llama al metodo register si es que existe
     */

    public static function register_services() 
    {
        foreach (self::get_services() as $class ) {
            $services = self::instantiate( $class );
            if ( method_exists( $services, 'register' ) ) {
                $services->register();
            }
        }
    }

    /**
     * inicializa la clase y retorna una nueva instancia de ela clase
     */

    private static function instantiate( $class ) 
    {   
        $service = new $class();
        return $service;
    }

}
