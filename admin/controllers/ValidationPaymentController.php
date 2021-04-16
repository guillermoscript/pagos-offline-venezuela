<?php

namespace base\admin\controllers;
/**
 * The core plugin Rest Api class.
 *
 * This is used to validate all the data that will enter in the billing inputs.
 *
 * @since      1.0.0
 * @package    Pagos_Offline_Venezuela
 * @subpackage Pagos_Offline_Venezuela/includes
 * @author     Guillermo <guillomarindavila@gmail.com>
 */
class ValidationPaymentController
{
    public static function validate_fields()
    {

        $array_errores = [];

        if( empty( $_POST[ 'billing_first_name' ]) ) {
            wc_add_notice(  'El nombre es requerido', 'error' );
            $array_errores[] = false;
        }

        if( empty( $_POST[ 'billing_last_name' ]) ) {
            wc_add_notice(  'El apellido es requerido', 'error' );
            $array_errores[] = false;
        }

        if( empty( $_POST[ 'billing_country' ]) ) {
            wc_add_notice(  'El pais es requerido', 'error' );
            $array_errores[] = false;
        }

        if( empty( $_POST[ 'billing_phone' ]) ) {
            wc_add_notice(  'El celular es requerido', 'error' );
            $array_errores[] = false;
        }

        // if( !preg_match('/(^(\+58\s?)?(\(\d{3}\)|\d{4})([\s\-]?\d{3})([\s\-]?\d{4})$)/',  $_POST[ 'billing_phone' ] ) ) {
        if( !preg_match('/(\(\d{3}\)|\d{4})([\s\-]?\d{3})([\s\-]?\d{4})$)/',  $_POST[ 'billing_phone' ] ) ) {
            // if( !preg_match('/\d/',  $_POST[ 'billing_phone' ] ) && strlen($_POST['billing_phone']) > 7 || strlen($_POST['billing_phone']) < 7 ) {
            wc_add_notice(  'El Numero no esta en el formato aceptado', 'error' );
            $array_errores[] = false;
        }

        if( !preg_match('/^\d+$/',  $_POST[ 'billing_cid' ] ) ) {
            
            wc_add_notice(  'La cedula no esta en el formato aceptado', 'error' );
            $array_errores[] = false;
        }
        

        if( empty( $_POST[ 'billing_cid' ]) ) {
            wc_add_notice(  'La cedula es requerida', 'error' );
            $array_errores[] = false;
        }

        if (empty($array_errores)) {
            return true;
        } else {
            return false;
        }
        
    }
}