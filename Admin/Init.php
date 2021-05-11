<?php

namespace Admin;

/**
* Make all the custom logic from controllers work with this final class.
*
* 
*/
final class Init 
{
    public function register() 
    {
        /* ========================= PAGP MOVIL ======================================= */
                
        require_once PLUGIN_BASE_PATH . 'Admin/Controllers/pago_movil_payment.php';
        require_once PLUGIN_BASE_PATH . 'Admin/Controllers/zelle_payment.php';
        require_once PLUGIN_BASE_PATH . 'Admin/Controllers/transferencia_payment.php';
        require_once PLUGIN_BASE_PATH . 'Admin/Controllers/settings_tab_rate_of_dolar.php';
        require_once PLUGIN_BASE_PATH . 'Admin/Controllers/checkout_en_bolivares.php';

        add_filter( 'woocommerce_payment_gateways', array($this,'add_pago_movil_class') );
        add_action( 'plugins_loaded', 'wc_offline_gateway_init_pago_movil', 11 );

        /* ========================= PAGP MOVIL ======================================= */


        /* ========================= TRANSFERENCIA ======================================= */

        add_filter( 'woocommerce_payment_gateways', array($this,'add_transferencia_class') );
        add_action( 'plugins_loaded', 'wc_offline_gateway_init_transferencia', 11 );

        /* ========================= TRANSFERENCIA ======================================= */


        /* ========================= ZELLE ======================================= */


        add_action( 'template_redirect', 'define_default_payment_gateway' );
        

        add_filter( 'woocommerce_payment_gateways', array($this,'add_zelle_class') );
        add_action( 'plugins_loaded', 'wc_offline_gateway_init_zelle', 11 );

        /* ========================= ZELLE ======================================= */
        
        
        /* ========================= OTROS ======================================= */

        add_filter( 'woocommerce_checkout_fields', array($this,'misha_remove_fields'), 9999 );
        add_filter( 'woocommerce_checkout_fields' , array($this,'misha_checkout_fields_styling'), 9999 );

        
        add_action( 'add_meta_boxes', array($this,'mv_add_meta_boxes') );
        add_filter( 'woocommerce_checkout_fields' , array($this,'custom_override_checkout_fields') );
        add_action( 'woocommerce_admin_order_data_after_shipping_address', array($this,'my_custom_checkout_field_display_admin_order_meta'), 10, 1 );
        /* ========================= OTROS ======================================= */
        
        /* ===================================== SETTIGNS para la tasa =================================================*/
        add_filter( 'woocommerce_get_settings_products', 'rate_of_dolar_all_settings', 10, 2 );

        add_filter( 'woocommerce_get_sections_products', 'rate_of_dolar_add_section' );
        /* ===================================== SETTIGNS para la tasa =================================================*/




        /* ===================================== tabla de la tasa =================================================*/
        add_action('woocommerce_after_checkout_form', 'debounce_add_jscript_checkout');
        /* ===================================== tabla de la tasa =================================================*/




    }

    function my_custom_checkout_field_display_admin_order_meta($order){
        echo '<p><strong>'.__('Cedula del checkout').':</strong> ' . get_post_meta( $order->get_id(), '_billing_cid', true ) . '</p>';
    }
        // Our hooked in function – $fields is passed via the filter!
    function custom_override_checkout_fields( $fields ) {
        $fields['billing']['billing_cid'] = array(
            'label'       => __('Cedula', 'woocommerce'),
            'placeholder' => _x('Cedula de indentidad', 'placeholder', 'woocommerce'),
            'required'    => true,
            'priority'        => 110,
            'class'       => array('form-row-wide'),
            'clear'       => true
        );

        return $fields;
    }

    function mv_add_other_fields_for_packaging() {
        
        global $post;

        $url = (get_the_post_thumbnail_url( $post->ID, 'full', true ));
        $payment_method = get_post_meta( $post->ID, '_payment_method', true );

        ?>
            <p class="form-row">
                <strong>Metodo de pago:</strong>
                <span><?php echo esc_html( $payment_method  ) ?></span>
            </p>
            <p class="form-row">
                <strong>Telefono:</strong>
                <span><?php echo esc_html( get_post_meta( $post->ID, '_billing_phone', true ) ) ?></span>
            </p>
            <p class="form-row">
                <strong>Cedula:</strong>
                <span><?php echo esc_html( get_post_meta( $post->ID, '_billing_cid', true ) ) ?></span>
            </p>
        <?php
        if($payment_method === 'pago_movil') {

            $pago_movil_info = get_option( 'woocommerce_pago_movil_accounts' );
            $key = get_post_meta( $post->ID, 'pago_movil_seleccionado', true );
            $nombre = esc_attr( wp_unslash( $pago_movil_info[$key]['nombre'] ) );
            $apellido = esc_attr( wp_unslash( $pago_movil_info[$key]['apellido'] ) );
            $telefono = esc_attr( wp_unslash( $pago_movil_info[$key]['telefono'] ) );
            $cedula = esc_attr( wp_unslash( $pago_movil_info[$key]['cedula'] ) );
            $banco = esc_attr( wp_unslash( $pago_movil_info[$key]['banco'] ) );
            
            
            ?>
                <p class="form-row">
                    <strong>Fecha de pago:</strong>
                    <span><?php echo esc_html( get_post_meta( $post->ID, 'fecha-pago-movil', true ) ) ?></span>
                </p>
                <p class="form-row">
                    <strong>Numero recibo:</strong>
                    <span><?php echo esc_html( get_post_meta( $post->ID, 'numero_recibo_movil', true ) ) ?></span>
                </p>
                <p class="form-row">
                    <strong>Banco Origen:</strong>
                    <span><?php echo esc_html( get_post_meta( $post->ID, 'pago_movil_banco_select', true ) ) ?></span>
                </p>
                <p class="form-row">
                    <strong>Banco Destino:</strong>
                    <span><?php echo ( $nombre.' |  '.$apellido.' | '.$telefono.' | '.$cedula.' | '.$banco ) ?></span>
                </p>
                <p class="form-row">
                    <strong>Capture:</strong>
                    <a href="<?php echo esc_html( $url ) ?>">
                        <img src="<?php echo esc_html( $url ) ?>" alt="" srcset="">
                    </a>
                </p>
            <?php
        } else if ($payment_method === 'zelle') {
            
            $zelle_info = get_option( 'woocommerce_zelle_accounts' );
            $key3 = get_post_meta( $post->ID, 'zelle_seleccionado', true );
            $email = esc_attr( wp_unslash( $zelle_info[$key3]['email_cuenta'] ) );

            ?>
                <p class="form-row">
                    <strong>Email Origen:</strong>
                    <span><?php echo esc_html( get_post_meta( $post->ID, 'email_origen', true ) ) ?></span>
                </p>
                <p class="form-row">
                    <strong>Email Destino:</strong>
                    <span><?php echo esc_html( $email ) ?></span>
                </p>
            <?php
        } else if ($payment_method === 'transferencia') {

            $transferencia_info = get_option( 'woocommerce_transferencia_accounts' );
            $key2 = get_post_meta( $post->ID, 'transferencia_seleccionado', true );
            $nombre2 = esc_attr( wp_unslash( $transferencia_info[$key2]['nombre'] ) );
            $apellido2 = esc_attr( wp_unslash( $transferencia_info[$key2]['apellido'] ) );
            $telefono2 = esc_attr( wp_unslash( $transferencia_info[$key2]['telefono'] ) );
            $cedula2 = esc_attr( wp_unslash( $transferencia_info[$key2]['cedula'] ) );
            $banco2 = esc_attr( wp_unslash( $transferencia_info[$key2]['banco'] ) );

            ?>
                <p class="form-row">
                    <strong>Fecha de pago:</strong>
                    <span><?php echo esc_html( get_post_meta( $post->ID, 'fecha-transferencia', true ) ) ?></span>
                </p>
                <p class="form-row">
                    <strong>Numero recibo:</strong>
                    <span><?php echo esc_html( get_post_meta( $post->ID, 'numero_recibo_transferencia', true ) ) ?></span>
                </p>
                <p class="form-row">
                    <strong>Banco Origen:</strong>
                    <span><?php echo esc_html( get_post_meta( $post->ID, 'transferencia_banco_select', true ) ) ?></span>
                </p>
                <p class="form-row">
                    <strong>Banco Destino:</strong>
                    <span><?php echo ( $nombre2.' |  '.$apellido2.' | '.$telefono2.' | '.$cedula2.' | '.$banco2 ) ?></span>
                </p>
                <p class="form-row">
                    <strong>Capture:</strong>
                    <a href="<?php echo esc_html( $url ) ?>">
                        <img src="<?php echo esc_html( $url ) ?>" alt="" srcset="">
                    </a>
                </p>
            <?php
        }
    }

    function mv_add_meta_boxes() {
        add_meta_box( 'mv_other_fields', __('Datos de Pagos','woocommerce'), array($this,'mv_add_other_fields_for_packaging'), 'shop_order', 'side', 'core' );
    }

    function misha_remove_fields( $woo_checkout_fields_array ) {
 
        // she wanted me to leave these fields in checkout
        // unset( $woo_checkout_fields_array['billing']['billing_first_name'] );
        // unset( $woo_checkout_fields_array['billing']['billing_last_name'] );
        // unset( $woo_checkout_fields_array['billing']['billing_phone'] );
        // unset( $woo_checkout_fields_array['billing']['billing_email'] );
        // unset( $woo_checkout_fields_array['order']['order_comments'] ); // remove order notes
     
        // and to remove the billing fields below
        // unset( $woo_checkout_fields_array['billing']['billing_company'] ); // remove company field
        // unset( $woo_checkout_fields_array['billing']['billing_country'] );
        // unset( $woo_checkout_fields_array['billing']['billing_address_1'] );
        unset( $woo_checkout_fields_array['billing']['billing_address_2'] );
        // unset( $woo_checkout_fields_array['billing']['billing_city'] );
        // unset( $woo_checkout_fields_array['billing']['billing_state'] ); // remove state field
        unset( $woo_checkout_fields_array['billing']['billing_postcode'] ); // remove zip code field
     
        return $woo_checkout_fields_array;
    }

    function misha_checkout_fields_styling( $f ) {
 
        $f['billing']['billing_email']['class'][0] = 'form-row-wide';
        $f['billing']['billing_phone']['class'][0] = 'form-row-wide';
 
        $f['billing']['billing_city']['class'][0] = 'form-row-last';
        $f['billing']['billing_state']['class'][0] = 'form-row-first';
     
        return $f;
     
    }

    function define_default_payment_gateway(){
        if( is_checkout() && ! is_wc_endpoint_url() ) {
            // HERE define the default payment gateway ID
            $default_payment_id = 'zelle';

            WC()->session->set( 'chosen_payment_method', $default_payment_id );
        }
    }

    function add_zelle_class( $methods ) {
        $methods[] = 'WC_Gateway_Offline_Zelle'; 
        return $methods;
    }

    function add_transferencia_class( $methods ) {
        $methods[] = 'WC_Gateway_Offline_Transferencia'; 
        return $methods;
    }

    function add_pago_movil_class( $methods ) {
        $methods[] = 'WC_Gateway_Offline_Pago_Movil'; 
        return $methods;
    }
}