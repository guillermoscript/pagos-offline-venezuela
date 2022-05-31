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
    static function get_rate_of_bf($sub_total_in_dolars, $taxes) {
        # code...
        
        $currency = 'Bs.S';
        
        $rate_in_bolivares = RestApiV1::get_tasa();
        
        $total_taxes_with_discount = 0;
        // Add each taxes to $total_taxes_with_discount
        foreach($taxes as $tax) $total_taxes_with_discount += $tax;

        $sub_total_in_dolars += $total_taxes_with_discount;

        $price_in_bolivares = $sub_total_in_dolars * floatval($rate_in_bolivares['rate_of_dolar']);

        $price_without_iva = floatval($price_in_bolivares / 1.16);
        $percentage_of_iva = floatval($price_in_bolivares - $price_without_iva);

        $total = $price_in_bolivares;

        return array(
            'total' => number_format($total,2,',','.'),
            'currency' => $currency,
            'rate_of_dolar' => $rate_in_bolivares['rate_of_dolar'],
            'sub_total_in_dolars' => $sub_total_in_dolars,
            'price_without_iva' => number_format($price_without_iva,2,',','.'),
            'percentage_of_iva' => number_format($percentage_of_iva,2,',','.')
        );
    }

    static function get_tasa() {
        # code...
        
        $rate_in_bolivares = '';
        if (get_option( 'rate_of_dolar_auto_insert' ) === 'yes') {  
            
            $client = new Client();
            try {
                // Go to the bcv.com website
                $crawler = $client->request('GET', BANK_URL);
        
                // Get the dolar
                $helper = $crawler->filter('#dolar strong')->each(function ($node) {
                    return $node->text()."\n";
                });
                $rate_in_bolivares =  $helper[0];
                // $rate = "4,77050000 ";
                // $rate = str_replace(',','.',$rate);
                $rate_in_bolivares = str_replace(',','.',$rate_in_bolivares);
                $rate_in_bolivares = floatval($rate_in_bolivares);
            } catch (\Throwable $th) {
                //throw $th;
                $rate_in_bolivares = get_option( 'rate_of_dolar_title' );
            }
        } else {
            $rate_in_bolivares = get_option( 'rate_of_dolar_title' );
        }

        return array(
            'rate_of_dolar' => $rate_in_bolivares,
        );
    }
}