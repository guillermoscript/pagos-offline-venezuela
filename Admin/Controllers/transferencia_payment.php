<?php

use Admin\Controllers\RestApiV1;
use Admin\Controllers\ValidationPaymentController;

function wc_offline_gateway_init_transferencia()
{

    class WC_Gateway_Offline_Transferencia extends WC_Payment_Gateway
    {
        public function __construct()
        {
            // The meat and potatoes of our gateway will go here
            $this->id = 'transferencia'; // payment gateway plugin ID
            $this->icon = home_url() . ("/wp-content/plugins/pagos-offline-venezuela/assets/bcv.png"); // URL of the icon that will be displayed on checkout page near your gateway name
            $this->has_fields = true; // in case you need a custom credit card form
            $this->method_title = 'Transferencia Payment';
            $this->method_description = 'Descripcion del transferencia'; // will be displayed on the options page

            // gateways can support subscriptions, refunds, saved payment methods,
            // but in this tutorial we begin with simple payments
            $this->supports = array(
                'products'
            );

            // transferencia account fields shown on the thanks page and in emails.
            $this->account_details = get_option(
                'woocommerce_transferencia_accounts',
                array(
                    array(
                        'nombre'   => $this->get_option('nombre'),
                        'apellido'   => $this->get_option('apellido'),
                        // 'telefono'   => $this->get_option( 'telefono' ),                            
                        'cedula'   => $this->get_option('cedula'),
                        'banco'   => $this->get_option('banco'),
                        'cuenta'   => $this->get_option('cuenta'),
                        'capture'   => $this->get_option('capture'),
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
            // add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'save_account_details'));
            add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);
            add_action('woocommerce_thankyou_transferencia', array($this, 'thankyou_page'));

            // Add the fields to order email
            add_action('woocommerce_email_order_details', array($this, 'transferencia_action_after_email_order_details'), 25, 4);


            // Display field value on the order edit page
            add_action('woocommerce_admin_order_data_after_billing_address', array($this, 'transferencia_my_custom_checkout_field_display_admin_order_meta'), 10, 1);

            // add_action('woocommerce_order_details_after_order_table', [$this, 'tasa_in_order_page']);
            add_filter('woocommerce_get_order_item_totals', [$this,'total_in_bolivares_in_order_table'], 10, 2);
            // You can also register a webhook here
            // add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) );
        }

        public function init_form_fields()
        {

            $this->form_fields = array(
                'enabled' => array(
                    'title'       => 'Activar/Desactivar',
                    'label'       => 'Activar pago transferencia',
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'Esto controla lo que el usuario ve de titulo en el checkout.',
                    'default'     => 'transferencia',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'Esto controla la descripcion que el usuario ve en el checkout.',
                    'default'     => 'Pagos usando transferencia.',
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
                <td class="forminp" id="transferencia_accounts">
                    <div class="wc_input_table_wrapper">
                        <table class="widefat wc_input_table sortable" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="sort">&nbsp;</th>
                                    <th><?php esc_html_e('Nombre', 'woocommerce'); ?></th>
                                    <th><?php esc_html_e('Apellido', 'woocommerce'); ?></th>
                                    <th><?php esc_html_e('Cedula', 'woocommerce'); ?></th>
                                    <th><?php esc_html_e('Banco', 'woocommerce'); ?></th>
                                    <th><?php esc_html_e('N# Cuenta', 'woocommerce'); ?></th>
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
                                            <td><input type="text" value="' . esc_attr(wp_unslash($account['nombre'])) . '" name="transferencia_nombre[' . esc_attr($i) . ']" /></td>
                                            <td><input type="text" value="' . esc_attr(wp_unslash($account['apellido'])) . '" name="transferencia_apellido[' . esc_attr($i) . ']" /></td>
                                            <td><input type="text" value="' . esc_attr(wp_unslash($account['cedula'])) . '" name="transferencia_cedula[' . esc_attr($i) . ']" /></td>
                                            <td><input type="text" value="' . esc_attr(wp_unslash($account['banco'])) . '" name="transferencia_banco[' . esc_attr($i) . ']" /></td>
                                            <td><input type="text" value="' . esc_attr(wp_unslash($account['cuenta'])) . '" name="transferencia_cuenta[' . esc_attr($i) . ']" /></td>
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
                            jQuery('#transferencia_accounts').on('click', 'a.add', function() {

                                var size = jQuery('#transferencia_accounts').find('tbody .account').length;

                                jQuery('<tr class="account">\
                                        <td class="sort"></td>\
                                        <td><input type="text" name="transferencia_nombre[' + size + ']" /></td>\
                                        <td><input type="text" name="transferencia_apellido[' + size + ']" /></td>\
                                        <td><input type="text" name="transferencia_cedula[' + size + ']" /></td>\
                                        <td><input type="text" name="transferencia_banco[' + size + ']" /></td>\
                                        <td><input type="text" name="transferencia_cuenta[' + size + ']" /></td>\
                                    </tr>').appendTo('#transferencia_accounts table tbody');

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
            if (isset($_POST['transferencia_nombre']) && isset($_POST['transferencia_apellido']) && isset($_POST['transferencia_cedula']) && isset($_POST['transferencia_banco']) && isset($_POST['transferencia_cuenta'])) {

                $transferencia_nombres = wc_clean(wp_unslash($_POST['transferencia_nombre']));
                $transferencia_apellidos = wc_clean(wp_unslash($_POST['transferencia_apellido']));
                $transferencia_cedulas = wc_clean(wp_unslash($_POST['transferencia_cedula']));
                $transferencia_bancos = wc_clean(wp_unslash($_POST['transferencia_banco']));
                $transferencia_cuentas = wc_clean(wp_unslash($_POST['transferencia_cuenta']));

                foreach ($transferencia_nombres as $i => $name) {
                    if (!isset($transferencia_nombres[$i])) {
                        continue;
                    }

                    $accounts[] = array(
                        'nombre'   => $transferencia_nombres[$i],
                        'apellido'   => $transferencia_apellidos[$i],
                        'cedula'   => $transferencia_cedulas[$i],
                        'banco'   => $transferencia_bancos[$i],
                        'cuenta'   => $transferencia_cuentas[$i],
                    );
                }
            }
            // phpcs:enable

            update_option('woocommerce_transferencia_accounts', $accounts);
        }

        private function transferencia_details($order_id = '')
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

            $transferencia_accounts = apply_filters('woocommerce_transferencia_accounts', $this->account_details);

            if (!empty($transferencia_accounts)) {
                $account_html = '';
                $has_details  = false;

                // foreach ( $transferencia_accounts as $transferencia_account ) {
                //     $transferencia_account = (object) $transferencia_account;

                //     if ( $transferencia_account->email_cuenta ) {
                //         $account_html .= '<h3 class="wc-transferencia-bank-details-account-name">' . wp_kses_post( wp_unslash( $transferencia_account->email_cuenta ) ) . ':</h3>' . PHP_EOL;
                //     }

                //     $account_html .= '<ul class="wc-transferencia-bank-details order_details transferencia_details">' . PHP_EOL;

                //     // transferencia account fields shown on the thanks page and in emails.
                //     $account_fields = apply_filters(
                //         'woocommerce_transferencia_account_fields',
                //         array(
                //             'nombre' => array(
                //                 'label' => __( 'Nombre', 'woocommerce' ),
                //                 'value' => $transferencia_account->nombre,
                //             ),
                //             'apellido'      => array(
                //                 'label' => __( 'Apellido', 'woocommerce' ),
                //                 'value' => $transferencia_account->apellido,
                //             ),
                //             'cedula'      => array(
                //                 'label' => __( 'Cedula', 'woocommerce' ),
                //                 'value' => $transferencia_account->cedula,
                //             ),
                //             'banco'      => array(
                //                 'label' => __( 'Banco', 'woocommerce' ),
                //                 'value' => $transferencia_account->banco,
                //             ),
                //             'cuenta'      => array(
                //                 'label' => __( 'N# Cuenta', 'woocommerce' ),
                //                 'value' => $transferencia_account->cuenta,
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

                if ($has_details) {
                    echo '<section class="woocommerce-transferencia-bank-details"><h2 class="wc-transferencia-bank-details-heading">' . esc_html__('Our bank details', 'woocommerce') . '</h2>' . wp_kses_post(PHP_EOL . $account_html) . '</section>';
                }
            }
        }


        /**
         * Add content to the WC emails.
         *
         * @param WC_Order $order Order object.
         * @param bool     $sent_to_admin Sent to admin.
         * @param bool     $plain_text Email format: plain text or HTML.
         */
        // public function email_instructions( $order, $sent_to_admin, $plain_text = false ) 
        // {

        //     if ( ! $sent_to_admin && 'bacs' === $order->get_payment_method() && $order->has_status( 'on-hold' ) ) {
        //         if ( $this->instructions ) {
        //             echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) . PHP_EOL );
        //         }
        //         $this->transferencia_details( $order->get_id() );
        //     }

        // }


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
            $this->transferencia_details($order_id);
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

            $bancos = array(
                "Venezuela",
                "Banesco",
                "Provincial",
                "Mercantil",
                "Bod",
                "Bicentenario",
                "Del Tesoro",
                "Bancaribe",
                "Agrícola de Vzla",
                "Mi Banco",
                "Banco Activo",
                "Banco Caroní",
                "Banco Exterior",
                "Banco Plaza",
                "Banco Sofitasa",
                "Banco Venezolano de Crédito",
                "Bancrecer",
                "BanFANB",
                "Bangente",
                "Banplus",
                "BFC Banco Fondo Común",
                "DELSUR",
                "100% Banco",
                "Mi Banco",
                "Nacional de Crédito"

            );
            // value para los select
            $bancos_value     = [
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


            // I will echo() the form, but you can close PHP tags and print it directly in HTML
            $html =  '<fieldset id="wc-' . esc_attr($this->id) . '-cc-form" class="wc-car-movil wc-credit-card-form wc-payment-form" style="background:transparent;">';

            // Add this action hook if you want your custom payment gateway to support it
            do_action('woocommerce_transferencia_form_start', $this->id);

            // I recommend to use inique IDs, because other gateways could already use #ccNo, #expdate, #cvc
            $html .=  '';

            $transferencia_info = get_option('woocommerce_transferencia_accounts');

            $full_name = $transferencia_info[0]['nombre'] . ' ' . $transferencia_info[0]['apellido'];

            $html .=  '

                <div class="form-row form-row-last width-50">
                    <label for="nombre_completo_transferencia">Nombre Completo <span class="required">*</span>
                        <img class="copy" data-id="nombre_completo_transferencia" src=" ' . home_url() . ("/wp-content/plugins/pagos-offline-venezuela/assets/copy-to-clipboard.png") . ' " alt="Copiar">
                    </label>                
                    <input value="'. $full_name. '" readonly  type="text" name="nombre_completo_transferencia" id="nombre_completo_transferencia" >
                </div>

                <div class="form-row form-row-last width-50">
                    <label for="banco_a_pagar_transferencia">Banco Titular <span class="required">*</span>
                        <img class="copy" data-id="banco_a_pagar_transferencia" src=" ' . home_url() . ("/wp-content/plugins/pagos-offline-venezuela/assets/copy-to-clipboard.png") . ' " alt="Copiar">
                    </label>                
                    <input value="'. $transferencia_info[0]['banco'] .'" readonly  type="text" name="banco_a_pagar_transferencia" id="telefono_a_pagar_transferencia" >
                </div>

                <div class="form-row form-row-last width-50">
                    <label for="cedula_a_pagar_transferencia">Cedula Titular <span class="required">*</span>
                        <img class="copy" data-id="cedula_a_pagar_transferencia" src=" ' . home_url() . ("/wp-content/plugins/pagos-offline-venezuela/assets/copy-to-clipboard.png") . ' " alt="Copiar">
                    </label>                
                    <input value="'. $transferencia_info[0]['cedula'] .'" readonly  type="text" name="cedula_a_pagar_transferencia" id="cedula_a_pagar_transferencia" >
                </div>

                <div class="form-row form-row-last width-50">
                    <label for="banco_a_pagar_transferencia">Cuenta Titular <span class="required">*</span>
                        <img class="copy" data-id="banco_a_pagar_transferencia" src=" ' . home_url() . ("/wp-content/plugins/pagos-offline-venezuela/assets/copy-to-clipboard.png") . ' " alt="Copiar">
                    </label>                
                    <input value="'. $transferencia_info[0]['cuenta'] .'" readonly  type="text" name="banco_a_pagar_transferencia" id="banco_a_pagar_transferencia" >
                </div>
                <div class="form-row form-row-wide width-50">
                    <div class="input-group">
                        <label class="label-file" for="comprobante_transferencia">
                            Adjuntar Comprobante
                            <div class="jpg-p">
                                <p class="text-file">jpg,png,pdf</p>
                                <div id="bararea" class="non2">
                                    <div id="bar"></div>
                                </div>
                            </div>
                            <input id="comprobante_transferencia" required class="input-pago-file" type="file" accept="application/pdf,image/png,image/jpeg,image/jpg ,image/jpe,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.wordprocessingml.template,application/vnd.ms-word.document.macroEnabled.12,application/vnd.ms-word.template.macroEnabled.12" name="capture" >
                        </label>
                    </div>
                </div>
                <div class="form-row form-row-wide width-50">
                    <label for="numero_recibo_transferencia">Número del Recibo <span class="required">*</span></label>
                    <input  min="1" type="number" name="numero_recibo_transferencia" id="numero_recibo_transferencia" >
                </div>
                <div class="form-row form-row-wide width-50">
                    <label for="bancos_transferencia">Banco Origen <span class="required">*</span></label>
                    <select id="bancos_transferencia" class="select-width" name="transferencia_banco_select" required>
                    <option value="" selected disabled hidden> 
                        Seleccionar 
                    </option> ';

            foreach ($bancos_value as $key => $value) {
                # code...
                $html .= '<option value=' . $bancos_value[$key] . '>' . $bancos[$key] . '</option>';
            }

            $html .= '
                    </select>
                <input type="hidden" id="capture-comprobante_transferencia" name="id-transferencia-capture">
                <div class="clear"></div>';

            do_action('woocommerce_transferencia_form_end', $this->id);

            $html .=  '<div class="clear"></div></fieldset>';
            echo $html;
        }

        public function validate_fields()
        {
            if (is_checkout() && !(is_wc_endpoint_url('order-pay') || is_wc_endpoint_url('order-received'))) {
                ValidationPaymentController::validate_fields();
                ValidationPaymentController::validate_pago_or_transaction('Transferencia', ['id-transferencia-capture', 'transferencia-select', 'transferencia_banco_select', 'numero_recibo_transferencia']);
            }
        }

        public function process_payment($order_id)
        {

            $order = wc_get_order($order_id);

            if (
                isset($_POST['id-transferencia-capture']) && isset($_POST['transferencia-select'])
                // &&  isset($_POST['fecha-transferencia'])
                && isset($_POST['numero_recibo_transferencia']) && isset($_POST['transferencia_banco_select'])
            ) {

                $total_en_bolivares = RestApiV1::get_rate_of_bf(WC()->cart->get_cart_contents_total(), WC()->cart->get_taxes());
                $order->update_meta_data('_thumbnail_id', $_POST['id-transferencia-capture']);
                $order->update_meta_data('transferencia_seleccionado', $_POST['transferencia-select']);
                $order->update_meta_data('numero_recibo_transferencia', $_POST['numero_recibo_transferencia']);
                $order->update_meta_data('transferencia_banco_select', $_POST['transferencia_banco_select']);
                // $order->update_meta_data( 'fecha-transferencia', $_POST['fecha-transferencia'] );
                $order->update_meta_data('tasa-bolivares', $total_en_bolivares['total']);
                $order->update_meta_data('rate_of_dolar', $total_en_bolivares['rate_of_dolar']);
            }

            // Mark as on-hold (we're awaiting the payment)
            $order->update_status('on-hold', __('Esperando por confirmar pago', 'WC_Gateway_Offline_transferencia'));

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

        public function transferencia_action_after_email_order_details($order, $sent_to_admin, $plain_text, $email)
        {
            if ($tasa = $order->get_meta('tasa-bolivares') && $rate = $order->get_meta('rate_of_dolar')) {
                // The data
                $label = __('Total en bolivares');
                $label2 = __('Tasa en bolivares');

                // The HTML Structure
                $html_output = '<h2>' . __('Extra data') . '</h2>
                <div class="discount-info"><table cellspacing="0" cellpadding="6">
                <tr>
                <th>' . $label . '</th><td>' . $tasa . '</td>
                <th>' . $label2 . '</th><td>' . $rate . '</td>
                </tr>
                </tbody></table></div><br>';

                // The CSS styling
                $styles = '<style>
                    .discount-info table{width: 100%; font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif;
                        color: #737373; border: 2px solid #e4e4e4; margin-bottom:8px;}
                    .discount-info table th, table.tracking-info td{ text-align: left; color: #737373; border: none; padding: 12px;}
                    .discount-info table td{ text-align: left; color: #737373; border: none; padding: 12px; }
                </style>';

                // The Output CSS + HTML
                echo $styles . $html_output;
            }
        }

        public function transferencia_my_custom_checkout_field_display_admin_order_meta($order)
        {
            if ($tasa = $order->get_meta('tasa-bolivares')) {
                echo '<p><strong>' . __('Total en bolivares') . '</strong> ' . $tasa . '</p>';
            }
        }

        public function total_in_bolivares_in_order_table($total_rows, $order)
        {
            if ($tasa = $order->get_meta('tasa-bolivares')) {

                $total_rows['recurr_not1'] = array(
                    'label' => __('Total en Bolivares:', 'woocommerce'),
                    'value'   => 'Bs ' . $tasa 
                );
            }
            if ($rate = $order->get_meta('rate_of_dolar')) {

                $total_rows['recurr_not'] = array(
                    'label' => __('Tasa en Bolivares:', 'woocommerce'),
                    'value'   => 'Bs ' . $rate 
                );
            }
            return $total_rows;
        }
    } // end \WC_Gateway_Offline class
}
