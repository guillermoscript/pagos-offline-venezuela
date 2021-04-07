<?php

namespace base\admin\controllers\api\routes;

use base\admin\controllers\RestApiV1;
/**
 * The core plugin Rest Api Routes class.
 *
 * This is used to define all the Routes that will be register in the REST API.
 *
 * @since      1.0.0
 * @package    Pagos_Offline_Venezuela
 * @subpackage Pagos_Offline_Venezuela/includes
 * @author     Guillermo <guillomarindavila@gmail.com>
 */
class Routes {

    public function register()
    {
        # code...
        /* ===================================== REST API CUSTOM ENDPOINTS =================================================*/
        add_action('woocommerce_after_checkout_form', [$this,'register_to_the_rest_api_total_to_pay']);
        /* ===================================== REST API CUSTOM ENDPOINTS =================================================*/
    }

    public function register_to_the_rest_api_total_to_pay() {
        register_rest_route( REST_API_NAMESPACE . REST_API_V1, '/total-to-pay/', array(
            'methods' => 'GET',
            'callback' => RestApiV1::get_tasa(),
        ));
    }
}