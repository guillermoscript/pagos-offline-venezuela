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
    static function get_rate_of_bf($sub_total_en_dolares) {
        # code...
        
        $moneda = 'Bs.S';
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

        $precio_en_bolivares = $sub_total_en_dolares * floatval($tasa_de_bolivares);

        $precio_sin_iva = floatval($precio_en_bolivares / 1.16);
        $porcentaje_de_impuestos = floatval($precio_en_bolivares - $precio_sin_iva);

        $total = $precio_en_bolivares;

        return array(
            'total' => number_format($total,2,',','.'),
            'moneda' => $moneda,
            'tasa_dolar' => $tasa_de_bolivares,
            'sub_total_en_dolares' => $sub_total_en_dolares,
            'precio_sin_iva' => number_format($precio_sin_iva,2,',','.'),
            'porcentaje_de_impuestos' => number_format($porcentaje_de_impuestos,2,',','.')
        );
    }
}