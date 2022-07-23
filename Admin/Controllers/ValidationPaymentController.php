<?php

namespace Admin\Controllers;
/**
 * The core plugin Rest Api class.
 *
 * This is used to validate all the data that will enter in the billing inputs.
 *
 * @since      1.0.0
 * @package    PagosOfflineVenezuela
 * @subpackage PagosOfflineVenezuela/includes
 * @author     Guillermo <guillomarindavila@gmail.com>
 */
class ValidationPaymentController
{
    public static function validate_fields()
    {

        $array_errors = [];

        if( empty( $_POST[ 'billing_first_name' ]) ) {
            wc_add_notice(  'El nombre es requerido', 'error' );
            $array_errors[] = false;
        }

        if( empty( $_POST[ 'billing_last_name' ]) ) {
            wc_add_notice(  'El apellido es requerido', 'error' );
            $array_errors[] = false;
        }

        if( empty( $_POST[ 'billing_country' ]) ) {
            wc_add_notice(  'El pais es requerido', 'error' );
            $array_errors[] = false;
        }

        if( empty( $_POST[ 'billing_phone' ]) ) {
            wc_add_notice(  'El celular es requerido', 'error' );
            $array_errors[] = false;
        }

        if( !preg_match('/(^(\+58\s?)?(\d{3}|\d{4})([\s\-]?\d{3})([\s\-]?\d{4})$)/',  $_POST[ 'billing_phone' ] ) ) {
            // if( !preg_match('/\d/',  $_POST[ 'billing_phone' ] ) && strlen($_POST['billing_phone']) > 7 || strlen($_POST['billing_phone']) < 7 ) {
            wc_add_notice(  'El Número no esta en el formato aceptado', 'error' );
            $array_errors[] = false;
        }

        if( empty( $_POST[ 'billing_cid' ]) ) {
            wc_add_notice(  'La Cédula es requerida', 'error' );
            $array_errors[] = false;
        }

        if( !preg_match('/^\d+$/',  $_POST[ 'billing_cid' ] ) ) {
            
            wc_add_notice(  'La Cédula no esta en el formato aceptado', 'error' );
            $array_errors[] = false;
        }

        if (empty($array_errors)) {
            return true;
        } else {
            return false;
        }
    }

    public static function validate_zelle()
    {
        # code...
        $array_errors = [];
        $inputs = ['email_origen','zelle-select','reference_number','zelle_sender_name'];
        if (!isset($_POST[$inputs[0]]) && !empty($_POST[$inputs[0]])) {
            wc_add_notice(  '¡Error! El campo del correo esta vacio, por favor ingrese un correo.', 'error' );
            $array_errors[] = false;
        } 
        if (!isset($_POST[$inputs[1]]) && !empty($_POST[$inputs[1]])) {
            wc_add_notice(  '¡Error! No selecciono un correo zelle, por favor seleccione uno.', 'error' );
            $array_errors[] = false;
        } 

        if (!isset($_POST[$inputs[2]]) && !empty($_POST[$inputs[2]])) {
            wc_add_notice(  '¡Error! No Coloco el numero de referencia, por favor seleccione uno.', 'error' );
            $array_errors[] = false;
        } 
        
        if (!ctype_alnum($_POST[$inputs[2]])) {
            wc_add_notice(  '¡Error! El numero de referencia no es valido, por favor ingrese uno valido.', 'error' );
            $array_errors[] = false;
        } 

        if (!isset($_POST[$inputs[3]]) && !empty($_POST[$inputs[3]])) {
            wc_add_notice(  '¡Error! No Coloco el nombre, por favor seleccione uno.', 'error' );
            $array_errors[] = false;
        } 

        if (!preg_match("/^([a-zA-Z' ]+)$/",$_POST[$inputs[3]])) {
            wc_add_notice(  '¡Error! El nombre de origen no es valido, por favor ingrese uno valido.', 'error' );
            $array_errors[] = false;
        }

        if (filter_var($_POST[$inputs[0]],FILTER_VALIDATE_EMAIL) === false) {
            wc_add_notice(  '¡Error! El correo de origen no es valido, por favor ingrese uno valido.', 'error' );
            $array_errors[] = false;
        }

        $count_de_cuenta = 0;
        $zelle_info = get_option( 'woocommerce_zelle_accounts' );

        foreach ($zelle_info as $key => $account) {
            # code...
            if ( $_POST[$inputs[1]] === strval($key) ) {
                $count_de_cuenta++;
            }
            if ($count_de_cuenta === 0) {
                wc_add_notice('Error el email que selecciono no es uno de los mostrados', 'error' );
                $array_errors[] = false;
            }
        }  

        if (empty($array_errors)) {
            return true;
        } else {
            return false;
        }
    }

    public static function validate_reserve()
    {
        if (!isset($_POST['id-comprobante_binance'])) {
        
            wc_add_notice(  '¡Error! El campo del capture esta vacio, por favor ingrese su comprobante.', 'error' );
            $array_errors[] = false;
        }

        if (!isset($_POST['reserve_select'] )) {

            wc_add_notice(  '¡Error! El campo de cuenta de reserve esta vacio, por favor ingrese uno.', 'error' );
            $array_errors[] = false;
        }

        if (!isset($_POST['reserve_sender_user'])) {

            wc_add_notice(  '¡Error! El campo de su usuario Reserve esta vacio, por favor ingrese uno.', 'error' );
            $array_errors[] = false;
        }
        if (empty($array_errors)) {
            return true;
        } else {
            return false;
        }
        
    }

    public static function validate_binance()
    {
        if (!isset($_POST['id-comprobante_binance'])) {
        
            wc_add_notice(  '¡Error! El campo del capture esta vacio, por favor ingrese su comprobante.', 'error' );
            $array_errors[] = false;
        }

        if (!isset($_POST['binance_select'] )) {

            wc_add_notice(  '¡Error! El campo de cuenta de Binance esta vacio, por favor ingrese uno.', 'error' );
            $array_errors[] = false;
        }

        if (!isset($_POST['binance_sender_user'])) {

            wc_add_notice(  '¡Error! El campo de su usuario Binance esta vacio, por favor ingrese uno.', 'error' );
            $array_errors[] = false;
        }
        if (empty($array_errors)) {
            return true;
        } else {
            return false;
        }
        
    }


    public static function validate_pago_movil()
    {
        $array_errors = [];
        $inputs = ['telefono_movil'];
        if (!isset($_POST[$inputs[0]]) && !empty($_POST[$inputs[0]])) {
            wc_add_notice(  '¡Error! El campo del telefono esta vacio, por favor ingrese un correo.', 'error' );
            $array_errors[] = false;
        }

        if( !preg_match('/(^(\+58\s?)?(\d{3}|\d{4})([\s\-]?\d{3})([\s\-]?\d{4})$)/',  $_POST[ $inputs[0] ] ) ) {
            // if( !preg_match('/\d/',  $_POST[ 'billing_phone' ] ) && strlen($_POST['billing_phone']) > 7 || strlen($_POST['billing_phone']) < 7 ) {
            wc_add_notice(  'El Número del pago movil no esta en el formato aceptado', 'error' );
            $array_errors[] = false;
        }

        if (empty($array_errors)) {
            return true;
        } else {
            return false;
        }
    }

    public static function validate_pago_or_transaction($class,$inputs)
    {
        # code...
        $array_errors = [];
        $bancos_value = [
            "Venezuela",
            "Banesco",
            "Provincial",
            "Mercantil",
            "Bod",
            "Bicentenario",
            "DelTesoro",
            "Bancaribe",
            "AgrícoladeVzla",
            "MiBanco",
            "BancoActivo",
            "BancoCaroní",
            "BancoExterior",
            "BancoPlaza",
            "BancoSofitasa",
            "BancoVenezolanodeCrédito",
            "Bancrecer",
            "BanFANB",
            "Bangente",
            "Banplus",
            "BFCBancoFondoComún",
            "DELSUR",
            "100%Banco",
            "MiBanco",
            "NacionaldeCrédito"
        ];
    

        if ((!isset($_POST[$inputs[0]]) && !empty($_POST[$inputs[0]])) || $_POST[$inputs[0]] === '' ) {
            wc_add_notice(  '¡Error! Adjunte el comprobante, por favor.', 'error' );
            $array_errors[] = false;
        }

        if ((!isset($_POST[$inputs[1]]) && !empty($_POST[$inputs[1]])) || $_POST[$inputs[1]] === '' ) {
            wc_add_notice(  '¡Error! Seleccione el banco de destino, por favor.', 'error' );
            $array_errors[] = false;
        }

        if ((!isset($_POST[$inputs[2]]) && !empty($_POST[$inputs[2]])) || $_POST[$inputs[2]] === '' ) {
            wc_add_notice(  '¡Error! Seleccione el banco de origen, por favor.', 'error' );
            $array_errors[] = false;
        }

        if ((!isset($_POST[$inputs[3]]) && !empty($_POST[$inputs[3]])) || $_POST[$inputs[3]] === '' ) {
            wc_add_notice(  '¡Error! Agrege solo numeros en el recibo, por favor.', 'error' );
            $array_errors[] = false;
        }


        // $count_de_cuenta = 0;

        // if ($class === 'Pago Movil') {
        //     $pago_movil_info = get_option( 'woocommerce_pago_movil_accounts' );
            
        //     foreach ($pago_movil_info as $key => $account) {
        //         # code...
        //         if ( $_POST[$inputs[1]] === strval($key) ) {
        //             $count_de_cuenta++;
        //         }
        //     }  
        //     if ($count_de_cuenta === 0) {
        //         wc_add_notice(  'Error no es una de las cuentas disponibles de ' . $class, 'error' );
        //         $array_errors[] = false;
        //     }
        // } else {
        //     $transferencia_info = get_option( 'woocommerce_transferencia_accounts' );

        //     foreach ($transferencia_info as $key => $account) {
        //         # code...
                
        //         if ( $_POST[$inputs[1]] === strval($key) ) {
        //             $count_de_cuenta++;
        //         }
        //     } 
    
        //     if ($count_de_cuenta === 0) {
        //         wc_add_notice(  'Error no es una de las cuentas disponibles de ' . $class , 'error' );
        //         $array_errors[] = false;
        //     }
        // }


        if ( array_search($_POST[$inputs[2]],$bancos_value) === false ) {
            wc_add_notice(  'Error el banco que selecciono no es uno de los disponibles', 'error' );
            $array_errors[] = false;
        }

        if (empty($array_errors)) {
            return true;
        } else {
            return false;
        }

    }
}