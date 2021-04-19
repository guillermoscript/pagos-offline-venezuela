<?php

namespace Admin\Controllers;
use Goutte\Client;
/**
 * The core plugin Rest Api class.
 *
 * This is used to define all the data that will be register in the REST API.
 *
 * @since      1.0.0
 * @package    PagosOfflineVenezuela
 * @subpackage PagosOfflineVenezuela/includes
 * @author     Guillermo <guillomarindavila@gmail.com>
 */
class RestApiV1 {

    public function register()
    {
        // # code...
        // /* ===================================== REST API CUSTOM ENDPOINTS =================================================*/
        // add_action('woocommerce_after_checkout_form', 'register_to_the_rest_api_total_to_pay');
        // /* ===================================== REST API CUSTOM ENDPOINTS =================================================*/
    }

    /**
	 * Gets the Tasa from the bcv.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
    static function get_tasa() {
        # code...
        
        $tasa_de_bolivares = '';
        if (get_option( 'tasa_dolar_auto_insert' ) === 'yes') {  
            
            $client = new Client();
            try {
                // Go to the bcv.com website
                $crawler = $client->request('GET', URL_BANCO);
        
                // Get the dolar
                $helper = $crawler->filter('#dolar strong')->each(function ($node) {
                    return $node->text()."\n";
                });
                $tasa_de_bolivares =  $helper[0];
            } catch (\Throwable $th) {
                //throw $th;
                $tasa_de_bolivares = get_option( 'tasa_dolar_title' );
            }
        } else {
            $tasa_de_bolivares = get_option( 'tasa_dolar_title' );
        }

        return array(
            'tasa_dolar' => $tasa_de_bolivares,
        );
    }

}