<?php

use Admin\Controllers\ValidationPaymentController;

function wc_offline_gateway_init_binance()
{

    class WC_Gateway_Offline_binance extends WC_Payment_Gateway
    {
        public function __construct()
        {
            // The meat and potatoes of our gateway will go here
            $this->id = 'binance'; // payment gateway plugin ID
            $this->icon = home_url( ) .( "/wp-content/plugins/pagos-offline-venezuela/assets/binance.png" ); // URL of the icon that will be displayed on checkout page near your gateway name
            $this->has_fields = true; // in case you need a custom credit card form
            $this->method_title = 'Binance Payment';
            $this->method_description = 'Pagos con Binance'; // will be displayed on the options page

            // gateways can support subscriptions, refunds, saved payment methods,
            // but in this tutorial we begin with simple payments
            $this->supports = array(
                'products'
            );

            // binance account fields shown on the thanks page and in emails.
            $this->account_details = get_option(
                'woocommerce_binance_accounts',
                array(
                    array(
                        'binance_nombre_de_usuario'   => $this->get_option('binance_nombre_de_usuario'),
                        'binance_qr'   => $this->get_option('binance_qr'),
                        'binance_coin'   => $this->get_option('binance_coin'),
                    ),
                )
            );

            // Method with all the options fields
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->enabled = $this->get_option('enabled');
            $this->testmode = 'yes' === $this->get_option('testmode');
            // $this->private_key = $this->testmode ? $this->get_option( 'test_private_key' ) : $this->get_option( 'private_key' );
            // $this->publishable_key = $this->testmode ? $this->get_option( 'test_publishable_key' ) : $this->get_option( 'publishable_key' );

            // This action hook saves the settings
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

            // We need custom JavaScript to obtain a token
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'save_account_details'));
            add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);
            add_action('woocommerce_thankyou_binance', array($this, 'thankyou_page'));

            // registramos para el ajax
            // add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ),11 );

            // Add the fields to order email
            // add_action('woocommerce_email_order_details', array($this,'binance_action_after_email_order_details'), 25, 4 );


            // Display field value on the order edit page
            // add_action('woocommerce_admin_order_data_after_billing_address', array($this, 'binance_my_custom_checkout_field_display_admin_order_meta'), 10, 1);

            // add_action( 'woocommerce_order_details_after_order_table', [$this,'tasa_in_order_page'] );

            // You can also register a webhook here
            // add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) );
        }

        public function init_form_fields()
        {

            $this->form_fields = array(
                'enabled' => array(
                    'title'       => 'Activar/Desactivar',
                    'label'       => 'Activar pago binance',
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'Esto controla lo que el usuario ve de titulo en el checkout.',
                    'default'     => 'Binance',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'Esto controla la descripcion que el usuario ve en el checkout.',
                    'default'     => 'Pagos usando Binance.',
                ),
                'account_details' => array(
                    'type' => 'account_details',
                )
            );
        }

        public function generate_account_details_html()
        {

            ob_start();
?>
            <tr valign="top">
                <th scope="row" class="titledesc"><?php esc_html_e('Account details:', 'woocommerce'); ?></th>
                <td class="forminp" id="binance_accounts">
                    <div class="wc_input_table_wrapper">
                        <table class="widefat wc_input_table sortable" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="sort">&nbsp;</th>
                                    <th><?php esc_html_e('Nombre De Usuario', 'woocommerce'); ?></th>
                                    <th><?php esc_html_e('subir la imagen del QR en medios y luego copiar la url y pegarla aca', 'woocommerce'); ?></th>
                                    <th><?php esc_html_e('Agregar la Moneda o Token que quieres recibir', 'woocommerce'); ?></th>
                                </tr>
                            </thead>
                            <tbody class="accounts">
                                <?php
                                $i = -1;
                                if ($this->account_details) {
                                    foreach ($this->account_details as $account) {
                                        $i++;
                                        echo '<tr class="account">
                                            <td class="sort"></td>
                                            <td><input type="text" value="' . esc_attr(wp_unslash($account['binance_nombre_de_usuario'])) . '" name="binance_nombre_de_usuario[' . esc_attr($i) . ']" /></td>
                                            <td><input type="text" value="' . esc_attr(wp_unslash($account['binance_qr'])) . '" name="binance_qr[' . esc_attr($i) . ']" /></td>
                                            <td><input type="text" value="' . esc_attr(wp_unslash($account['binance_coin'])) . '" name="binance_coin[' . esc_attr($i) . ']" /></td>
                                        </tr>';
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7"><a href="#" class="add button"><?php esc_html_e('+ Add account', 'woocommerce'); ?></a> <a href="#" class="remove_rows button"><?php esc_html_e('Remove selected account(s)', 'woocommerce'); ?></a></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <script type="text/javascript">
                        jQuery(function() {
                            jQuery('#binance_accounts').on('click', 'a.add', function() {

                                var size = jQuery('#binance_accounts').find('tbody .account').length;

                                jQuery('<tr class="account">\
                                        <td class="sort"></td>\
                                        <td><input type="text" name="binance_nombre_de_usuario[' + size + ']" /></td>\
                                        <td><input type="text" name="binance_qr[' + size + ']" /></td>\
                                        <td><input type="text" name="binance_coin[' + size + ']" /></td>\
                                    </tr>').appendTo('#binance_accounts table tbody');

                                return false;
                            });
                        });
                    </script>
                </td>
            </tr>
<?php
            return ob_get_clean();
        }
        /**
         * Save account details table.
         */
        public function save_account_details()
        {

            $accounts = array();

            // phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification -- Nonce verification already handled in WC_Admin_Settings::save()
            if (isset($_POST['binance_nombre_de_usuario']) && isset($_POST['binance_qr']) && isset($_POST['binance_coin'])) {

                $binance_nombre_de_usuario = wc_clean(wp_unslash($_POST['binance_nombre_de_usuario']));
                $binance_qr = wc_clean(wp_unslash($_POST['binance_qr']));
                $binance_coin = wc_clean(wp_unslash($_POST['binance_coin']));

                foreach ($binance_nombre_de_usuario as $i => $name) {
                    if (!isset($binance_nombre_de_usuario[$i])) {
                        continue;
                    }

                    $accounts[] = array(
                        'binance_nombre_de_usuario'   => $binance_nombre_de_usuario[$i],
                        'binance_qr'   => $binance_qr[$i],
                        'binance_coin'   => $binance_coin[$i],
                    );
                }
                // phpcs:enable

                update_option('woocommerce_binance_accounts', $accounts);
            }
        }

        private function binance_details($order_id = '')
        {

            if (empty($this->account_details)) {
                return;
            }

            // Get order and store in $order.
            $order = wc_get_order($order_id);

            // Get the order country and country $locale.
            // $country = $order->get_billing_country();
            // $locale  = $this->get_country_locale();

            // Get sortcode label in the $locale array and use appropriate one.
            // $sortcode = isset( $locale[ $country ]['sortcode']['label'] ) ? $locale[ $country ]['sortcode']['label'] : __( 'Sort code', 'woocommerce' );

            $binance_accounts = apply_filters('woocommerce_binance_accounts', $this->account_details);

            if (!empty($binance_accounts)) {
                $account_html = '';
                $has_details  = false;

                // foreach ( $binance_accounts as $binance_account ) {
                //     $binance_account = (object) $binance_account;

                //     if ( $binance_account->email_cuenta ) {
                //         $account_html .= '<h3 class="wc-binance-bank-details-account-name">' . wp_kses_post( wp_unslash( $binance_account->email_cuenta ) ) . ':</h3>' . PHP_EOL;
                //     }

                //     $account_html .= '<ul class="wc-binance-bank-details order_details binance_details">' . PHP_EOL;

                //     // binance account fields shown on the thanks page and in emails.
                //     $account_fields = apply_filters(
                //         'woocommerce_binance_account_fields',
                //         array(
                //             'nombre' => array(
                //                 'label' => __( 'Nombre', 'woocommerce' ),
                //                 'value' => $binance_account->nombre,
                //             ),
                //             'apellido'      => array(
                //                 'label' => __( 'Apellido', 'woocommerce' ),
                //                 'value' => $binance_account->apellido,
                //             ),
                //             'telefono'      => array(
                //                 'label' => __( 'Telefono', 'woocommerce' ),
                //                 'value' => $binance_account->telefono,
                //             ),
                //             'cedula'      => array(
                //                 'label' => __( 'Cedula', 'woocommerce' ),
                //                 'value' => $binance_account->cedula,
                //             ),
                //             'banco'      => array(
                //                 'label' => __( 'Banco', 'woocommerce' ),
                //                 'value' => $binance_account->banco,
                //             ),
                //         ),
                //         $order_id
                //     );

                //     foreach ( $account_fields as $field_key => $field ) {
                //         if ( ! empty( $field['value'] ) ) {
                //             $account_html .= '<li class="' . esc_attr( $field_key ) . '">' . wp_kses_post( $field['label'] ) . ': <strong>' . wp_kses_post( wptexturize( $field['value'] ) ) . '</strong></li>' . PHP_EOL;
                //             $has_details   = true;
                //         }
                //     }

                //     $account_html .= '</ul>';
                // }

                // if ( $has_details ) {
                //     echo '<section class="woocommerce-binance-bank-details"><h2 class="wc-binance-bank-details-heading">' . esc_html__( 'Our bank details', 'woocommerce' ) . '</h2>' . wp_kses_post( PHP_EOL . $account_html ) . '</section>';
                // }
            }
        }


        /**
         * Output for the order received page.
         *
         * @param int $order_id Order ID.
         */
        public function thankyou_page($order_id)
        {

            if ($this->instructions) {
                echo wp_kses_post(wpautop(wptexturize(wp_kses_post($this->instructions))));
            }
            $this->binance_details($order_id);
        }

        public function payment_fields()
        {
            // ok, let's display some description before the payment form
            if ($this->description) {
                // you can instructions for test mode, I mean test card numbers etc.
                if ($this->testmode) {
                    $this->description .= ' TEST MODE ENABLED. In test mode, you can use the card numbers listed in <a href="#" target="_blank" rel="noopener noreferrer">documentation</a>.';
                    $this->description  = trim($this->description);
                }
                // display the description with <p> tags etc.
                echo wpautop(wp_kses_post($this->description));
            }


            // I will echo() the form, but you can close PHP tags and print it directly in HTML
            $html =  '<fieldset id="wc-' . esc_attr($this->id) . '-cc-form" class="wc-car-movil wc-credit-card-form wc-payment-form" style="background:transparent;">';

            // Add this action hook if you want your custom payment gateway to support it
            do_action('woocommerce_binance_form_start', $this->id);

            // I recommend to use inique IDs, because other gateways could already use #ccNo, #expdate, #cvc
            $html .=  '';

            $binance_info = get_option('woocommerce_binance_accounts');

            foreach ($binance_info as $key => $account) {
                # code...
                $nombre = esc_attr(wp_unslash($account['binance_nombre_de_usuario']));
                $qr = esc_attr(wp_unslash($account['binance_qr']));
                $binance_coin = esc_attr(wp_unslash($account['binance_coin']));
            }
            $html .=  '

                <div class="form-row form-row-wide">
                    <h4 class="account-title">Datos de la cuenta</h4>
                    <div class="account-data">                        
                        <label for="cuenta_binance">Nombre del Beneficiario <span class="required">*</span>
                        </label>                
                        <div>
                            <span id="cuenta_binance" class="copy-text">'.$binance_info[0]['binance_nombre_de_usuario'].'</span>
                            <img class="copy" data-id="cuenta_binance" src=" ' . home_url() . ("/wp-content/plugins/pagos-offline-venezuela/assets/copy-to-clipboard.png") . ' " alt="Copiar">
                        </div>
                    </div>
                    <div class="account-data">                        
                        <label for="binance_qr_img">Nombre del Beneficiario <span class="required">*</span>
                        </label>                
                        <div>
                            <span id="binance_qr_img" class="copy-text">'.$binance_info[0]['binance_coin'].'</span>
                            <img class="copy" data-id="binance_qr_img" src=" ' . home_url() . ("/wp-content/plugins/pagos-offline-venezuela/assets/copy-to-clipboard.png") . ' " alt="Copiar">
                        </div>
                    </div>
                </div>            
                
                <div class="form-row form-row-first width-50">
                    <label for="cuenta_binance">Cuenta Titular <span class="required">*</span>
                        <img class="copy" data-id="cuenta_binance" src=" ' . home_url() . ("/wp-content/plugins/pagos-offline-venezuela/assets/copy-to-clipboard.png") . ' " alt="Copiar">
                    </label>      
                    <input value="'.$binance_info[0]['binance_nombre_de_usuario'].'" readonly  type="text" name="cuenta_binance" id="cuenta_binance" >
                </div>
                
                <div class="form-row form-row-last width-50">
                    <label for="cuenta_binance_token">Moneda/Token <span class="required">*</span>
                        <img class="copy" data-id="cuenta_binance_token" src=" ' . home_url() . ("/wp-content/plugins/pagos-offline-venezuela/assets/copy-to-clipboard.png") . ' " alt="Copiar">
                    </label>      
                    <input value="'.$binance_info[0]['binance_coin'].'" readonly  type="text" name="cuenta_binance_token" id="cuenta_binance_token" >
                </div>

                <div class="form-row form-row-last width-50">
                    <label for="binance_sender_user">Nombre de tu Usuario <span class="required">*</span></label>
                    <input  type="text" name="binance_sender_user" id="binance_sender_user" >
                </div>

                <div class="form-row form-row-wide" id="binance_qr_img" style="display: none;">
                        <img src="'.$binance_info[0]['binance_qr'].'" alt="QR" width="100" height="100" style="width: 100%;">
                </div>

                <div class="form-row form-row-wide width-50">
                    <input  type="Button" value="Ver QR" name="binance" id="binance" >
                </div>

                <div class="form-row form-row-wide width-50">
                    <div class="input-group">
                        <label class="label-file" for="comprobante_binance">
                            Adjuntar Comprobante
                            <div class="jpg-p">
                                <p class="text-file">jpg,png,pdf</p>
                                <div id="bararea4" class="non2">
                                    <div id="bar4"></div>
                                </div>
                            </div>
                            <input id="comprobante_binance" required class="input-pago-file" type="file" accept="application/pdf,image/png,image/jpeg,image/jpg ,image/jpe,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.wordprocessingml.template,application/vnd.ms-word.document.macroEnabled.12,application/vnd.ms-word.template.macroEnabled.12" name="capture" >
                        </label>
                    </div>
                </div>
                <input type="hidden" id="capture-comprobante_binance" name="id-comprobante_binance">
                <div class="clear"></div>';

            do_action('woocommerce_binance_form_end', $this->id);

            $html .=  '<div class="clear"></div></fieldset>';
            echo $html;
        }

        public function validate_fields()
        {
            if (is_checkout() && !(is_wc_endpoint_url('order-pay') || is_wc_endpoint_url('order-received'))) {
                ValidationPaymentController::validate_fields();
                ValidationPaymentController::validate_binance();
                // ValidationPaymentController::validate_pago_or_transaction('Pago movil',['id-pago-movil-capture','binance_select','binance_banco_select','numero_recibo_movil']);
            }
        }

        public function payment_scripts()
        {
            if (!is_cart() && !is_checkout() && !isset($_GET['pay_for_order'])) {
                return;
            }
        }

        public function process_payment($order_id)
        {

            $order = wc_get_order($order_id);

            if (
                isset($_POST['id-comprobante_binance'])
                // && isset($_POST['binance_select'])
                && isset($_POST['binance_sender_user'])
            ) {

                $order->update_meta_data('_thumbnail_id', $_POST['id-comprobante_binance']);
                // $order->update_meta_data('binance_seleccionado', $_POST['binance_select']);
                $order->update_meta_data('binance_sender_user', $_POST['binance_sender_user']);
            }

            // Mark as on-hold (we're awaiting the payment)
            $order->update_status('on-hold', __('Esperando por confirmar pago', 'WC_Gateway_Offline_binance'));

            // Reduce stock levels
            // $order->reduce_order_stock();

            // Remove cart
            WC()->cart->empty_cart();

            // Return thankyou redirect
            return array(
                'result'    => 'success',
                'redirect'  => $this->get_return_url($order)
            );
        }

        /**
         * Add content to the WC emails.
         *
         * @access public
         * @param WC_Order $order
         * @param bool $sent_to_admin
         * @param bool $plain_text
         */
        public function email_instructions($order, $sent_to_admin, $plain_text = false)
        {

            if ($this->instructions && !$sent_to_admin && 'offline' === $order->payment_method && $order->has_status('on-hold')) {
                echo wpautop(wptexturize($this->instructions)) . PHP_EOL;
            }
        }
    } // end \WC_Gateway_Offline class
}
