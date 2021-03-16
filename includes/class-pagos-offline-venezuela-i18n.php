<?php
namespace base\includes;
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://netkiub.com
 * @since      1.0.0
 *
 * @package    Pagos_Offline_Venezuela
 * @subpackage Pagos_Offline_Venezuela/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Pagos_Offline_Venezuela
 * @subpackage Pagos_Offline_Venezuela/includes
 * @author     Guillermo <guillomarindavila@gmail.com>
 */
class Pagos_Offline_Venezuela_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'pagos-offline-venezuela',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
