<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://netkiub.com
 * @since             1.0.0
 * @package           PagosOfflineVenezuela
 *
 * @wordpress-plugin
 * Plugin Name:       Pagos Offline Venezuela
 * Plugin URI:        https://netkiub.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Guillermo
 * Author URI:        https://netkiub.com
 * GitHub Plugin URI: https://github.com/guillermoscript/pagos-offline-venezuela
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pagos-offline-venezuela
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;




if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

define('PLUGIN_BASE_PATH2', plugin_dir_path( __FILE__ ));
define('URL_BANCO', 'http://www.bcv.org.ve/');
define('REST_API_NAMESPACE', 'pagos-offline-venezuela');
define('REST_API_V1', 'v1');
define('REST_API_V2', 'v2');

use Admin\Init;
use Includes\PagosOfflineVenezuelaActivator;
use Includes\PagosOfflineVenezuelaDeactivator;
use Includes\PagosOfflineVenezuela;

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PAGOS_OFFLINE_VENEZUELA_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pagos-offline-venezuela-activator.php
 */
function activate_pagos_offline_venezuela() {
	// require_once plugin_dir_path( __FILE__ ) . 'includes/class-pagos-offline-venezuela-activator.php';
	PagosOfflineVenezuelaActivator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pagos-offline-venezuela-deactivator.php
 */
function deactivate_pagos_offline_venezuela() {
	// require_once plugin_dir_path( __FILE__ ) . 'includes/class-pagos-offline-venezuela-deactivator.php';
	PagosOfflineVenezuelaDeactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pagos_offline_venezuela' );
register_deactivation_hook( __FILE__, 'deactivate_pagos_offline_venezuela' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
// require plugin_dir_path( __FILE__ ) . 'includes/class-pagos-offline-venezuela.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pagos_offline_venezuela() {

	$plugin = new PagosOfflineVenezuela();
	$plugin->run();

}
run_pagos_offline_venezuela();

add_action( 'wp_ajax_nopriv_recibir_imagen', 'recibir_imagen');
add_action( 'wp_ajax_recibir_imagen', 'recibir_imagen');

function recibir_imagen() {
    # code...
    // comprobando el nonce 
    $nonce = sanitize_text_field( $_POST['nonce'] );

    if ( ! wp_verify_nonce( $nonce, 'my-ajaxxx-nonce' ) ) {
        die ( 'Busted!!!');
    }

    // estas cosas se requieren,lo vi en internet
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    // $i = 0;

    foreach ( $_FILES as $image ) {
        // $image = $_FILES['file'];
        // if a files was upload
        if ( $image['size'] < 3000000 ) {
            // if it is an image
            if ( preg_match('/(jpg|jpeg|png|gif|pdf)$/', $image['type']) ) {

                $override = array('test_form' => false);
                // save the file, and store an array, containing its location in $file
                // guardamos la imagne en la carpeta uploads
                $file = wp_handle_upload( $image, $override );
                $wp_upload_dir = wp_upload_dir();

                if ( $file && ! isset( $file['error'] ) ) {

                    // aqui es cuando hacemos la imagne un post para que este en la pagina de medios
                    $file_title_for_media_library = $title;

                    $attachment = array(
                        "guid" => $file['url'],
                        "post_mime_type" => $file['type'],
                        "post_title" => addslashes( $file_title_for_media_library ),
                        "post_content" => "",
                        "post_status" => "draft",
                        "post_author" => 1
                    );
                    $id = wp_insert_attachment( $attachment, $file['file'],0);

                    $attach_data = wp_generate_attachment_metadata( $id, $file['file'] );
                    wp_update_attachment_metadata( $id, $attach_data );

                    // add_option( 'capture', $order_id );

                    // $i++;
                    echo json_encode(['succes' => 'yey' , 'id' => $id]);
                    wp_die();
                    return ;
                    
                } else {
                    echo json_encode(['error' => 'error al guardar la imagen', 'type' => $file['error']]);
                    return ;
                }                
            } else { 
                echo json_encode(['error' => 'Error, tipo no aceptado']);
                return ;
            }
        } else {
            echo json_encode(['error' => 'Error, tamano mas grande del permitido']);
            return ;
        }
    }
}
