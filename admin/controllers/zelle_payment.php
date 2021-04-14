<?php

use base\admin\controllers\ValidationPaymentController;

function wc_offline_gateway_init_zelle() {

    class WC_Gateway_Offline_Zelle extends WC_Payment_Gateway
    {
        public function __construct() 
        {
            // The meat and potatoes of our gateway will go here
            $this->id = 'zelle'; // payment gateway plugin ID
            $this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
            $this->has_fields = true; // in case you need a custom credit card form
            $this->method_title = 'Zelle Payment';
            $this->method_description = 'Descripcion del pago zelle'; // will be displayed on the options page
        
            // gateways can support subscriptions, refunds, saved payment methods,
            // but in this tutorial we begin with simple payments
            $this->supports = array(
                'products'
            );

            // zelle account fields shown on the thanks page and in emails.
            $this->account_details = get_option(
                'woocommerce_zelle_accounts',
                array(
                    array(
                        'email_cuenta'   => $this->get_option( 'email_cuenta' ),                            
                        'name_zelle'      => $this->get_option( 'name_zelle' ),
                    ),
                )
            );
        
            // Method with all the options fields
            $this->init_form_fields();
        
            // Load the settings.
            $this->init_settings();
            $this->title = $this->get_option( 'title' );
            $this->description = $this->get_option( 'description' );
            $this->enabled = $this->get_option( 'enabled' );
            $this->testmode = 'yes' === $this->get_option( 'testmode' );
            // $this->private_key = $this->testmode ? $this->get_option( 'test_private_key' ) : $this->get_option( 'private_key' );
            // $this->publishable_key = $this->testmode ? $this->get_option( 'test_publishable_key' ) : $this->get_option( 'publishable_key' );
        
            // This action hook saves the settings
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        
            // We need custom JavaScript to obtain a token
            // add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'save_account_details' ) );
            add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
            add_action( 'woocommerce_thankyou_zelle', array( $this, 'thankyou_page' ) );
        
            // You can also register a webhook here
            // add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) );
        }

        public function init_form_fields() 
        {

            $this->form_fields = array(
                'enabled' => array(
                    'title'       => 'Activar/Desactivar',
                    'label'       => 'Activar pago Zelle',
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'Esto controla lo que el usuario ve de titulo en el checkout.',
                    'default'     => 'Zelle',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'Esto controla la descripcion que el usuario ve en el checkout.',
                    'default'     => 'Pagos usando zelle.',
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
                <th scope="row" class="titledesc"><?php esc_html_e( 'Account details:', 'woocommerce' ); ?></th>
                <td class="forminp" id="zelle_accounts">
                    <div class="wc_input_table_wrapper">
                        <table class="widefat wc_input_table sortable" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="sort">&nbsp;</th>
                                    <th><?php esc_html_e( 'Email', 'woocommerce' ); ?></th>
                                    <th><?php esc_html_e( 'Nombre', 'woocommerce' ); ?></th>
                                </tr>
                            </thead>
                            <tbody class="accounts">
                                <?php
                                $i = -1;
                                if ( $this->account_details ) {
                                    foreach ( $this->account_details as $account ) {
                                        $i++;
    
                                        echo '<tr class="account">
                                            <td class="sort"></td>
                                            <td><input type="email" value="' . esc_attr( wp_unslash( $account['email_cuenta'] ) ) . '" name="zelle_email_cuenta[' . esc_attr( $i ) . ']" /></td>
                                            <td><input type="text" value="' . esc_attr( wp_unslash( $account['name_zelle'] ) ) . '" name="zelle_name_zelle[' . esc_attr( $i ) . ']" /></td>
                                        </tr>';
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3"><a href="#" class="add button"><?php esc_html_e( '+ Add account', 'woocommerce' ); ?></a> <a href="#" class="remove_rows button"><?php esc_html_e( 'Remove selected account(s)', 'woocommerce' ); ?></a></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <script type="text/javascript">
                        jQuery(function() {
                            jQuery('#zelle_accounts').on( 'click', 'a.add', function(){
    
                                var size = jQuery('#zelle_accounts').find('tbody .account').length;
    
                                jQuery('<tr class="account">\
                                        <td class="sort"></td>\
                                        <td><input type="text" name="zelle_email_cuenta[' + size + ']" /></td>\
                                        <td><input type="text" name="zelle_name_zelle[' + size + ']" /></td>\
                                    </tr>').appendTo('#zelle_accounts table tbody');
    
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
            if ( isset( $_POST['zelle_email_cuenta'] ) && isset( $_POST['zelle_name_zelle'] )) {

                $email_cuentas   = wc_clean( wp_unslash( $_POST['zelle_email_cuenta'] ) );
                $name_zelles      = wc_clean( wp_unslash( $_POST['zelle_name_zelle'] ) );

                foreach ( $email_cuentas as $i => $name ) {
                    if ( ! isset( $email_cuentas[ $i ] ) ) {
                        continue;
                    }

                    $accounts[] = array(
                        'email_cuenta'   => $email_cuentas[ $i ],
                        'name_zelle'      => $name_zelles[ $i ],
                    );
                }
            }
            // phpcs:enable

            update_option( 'woocommerce_zelle_accounts', $accounts );
        }

        private function zelle_details( $order_id = '' )
        {

            if ( empty( $this->account_details ) ) {
                return;
            }

            // Get order and store in $order.
            $order = wc_get_order( $order_id );

            // Get the order country and country $locale.
            // $country = $order->get_billing_country();
            // $locale  = $this->get_country_locale();

            // Get sortcode label in the $locale array and use appropriate one.
            // $sortcode = isset( $locale[ $country ]['sortcode']['label'] ) ? $locale[ $country ]['sortcode']['label'] : __( 'Sort code', 'woocommerce' );

            $zelle_accounts = apply_filters( 'woocommerce_zelle_accounts', $this->account_details );

            if ( ! empty( $zelle_accounts ) ) {
                $account_html = '';
                $has_details  = false;

                // foreach ( $zelle_accounts as $zelle_account ) {
                //     $zelle_account = (object) $zelle_account;

                //     if ( $zelle_account->email_cuenta ) {
                //         $account_html .= '<h3 class="wc-zelle-bank-details-account-name">' . wp_kses_post( wp_unslash( $zelle_account->email_cuenta ) ) . ':</h3>' . PHP_EOL;
                //     }

                //     $account_html .= '<ul class="wc-zelle-bank-details order_details zelle_details">' . PHP_EOL;

                //     // zelle account fields shown on the thanks page and in emails.
                //     $account_fields = apply_filters(
                //         'woocommerce_zelle_account_fields',
                //         array(
                //             'email_cuenta' => array(
                //                 'label' => __( 'Cuenta Zelle', 'woocommerce' ),
                //                 'value' => $zelle_account->email_cuenta,
                //             ),
                //             'name_zelle'      => array(
                //                 'label' => __( 'Nombre', 'woocommerce' ),
                //                 'value' => $zelle_account->name_zelle,
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

                if ( $has_details ) {
                    echo '<section class="woocommerce-zelle-bank-details"><h2 class="wc-zelle-bank-details-heading">' . esc_html__( 'Our bank details', 'woocommerce' ) . '</h2>' . wp_kses_post( PHP_EOL . $account_html ) . '</section>';
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
        //         $this->zelle_details( $order->get_id() );
        //     }

        // }

            
        /**
         * Output for the order received page.
         *
         * @param int $order_id Order ID.
         */
        public function thankyou_page( $order_id )
        {

            if ( $this->instructions ) {
                echo wp_kses_post( wpautop( wptexturize( wp_kses_post( $this->instructions ) ) ) );
            }
            $this->zelle_details( $order_id );

        }

        public function payment_fields() 
        {
            // ok, let's display some description before the payment form
            if ( $this->description ) {
                // you can instructions for test mode, I mean test card numbers etc.
                if ( $this->testmode ) {
                    $this->description .= ' TEST MODE ENABLED. In test mode, you can use the card numbers listed in <a href="#" target="_blank" rel="noopener noreferrer">documentation</a>.';
                    $this->description  = trim( $this->description );
                }
                // display the description with <p> tags etc.
                echo wpautop( wp_kses_post( $this->description ) );
            }
        
            // I will echo() the form, but you can close PHP tags and print it directly in HTML
            $html =  '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-car-movil wc-credit-card-form wc-payment-form" style="background:transparent;">';
        
            // Add this action hook if you want your custom payment gateway to support it
            do_action( 'woocommerce_zelle_form_start', $this->id );
        
            // I recommend to use inique IDs, because other gateways could already use #ccNo, #expdate, #cvc
            $html .=  '<div class="form-row form-row-wide"><label for="zelle_email">Email Zelle <span class="required">*</span></label>
            
            <select id="zelle_email" name="zelle-select" required>
            <option value="" selected disabled hidden> 
                Seleccionar 
            </option> ';

            $zelle_info = get_option( 'woocommerce_zelle_accounts' );

            foreach ($zelle_info as $key => $account) {
                # code...
                $email_cuenta = esc_attr( wp_unslash( $account['email_cuenta'] ) );
                // $name_zelle = esc_attr( wp_unslash( $account['name_zelle'] ) );
                $html .= '
                    <option value="'.$key.'"> 
                        '.$email_cuenta.' 
                    </option> 
                ';
            }  
            $html .=  '</select>
                </div>
                <div class="form-row form-row-wide">
                    <label for="comprobante_transferencia">Correo origen<span class="required">*</span></label>
                    <input type="email" name="email_origen" id="email-origen" required>
                </div>
                <div class="clear"></div>';
        
            do_action( 'woocommerce_zelle_form_end', $this->id );
        
            $html .=  '<div class="clear"></div></fieldset>';
            echo $html;
        }

        public function validate_fields()
        {
 
            ValidationPaymentController::validate_fields();
         
        }

        public function process_payment( $order_id )
        {
    
            $order = wc_get_order( $order_id );

            if (isset($_POST['zelle-select']) && isset($_POST['email_origen'])) {
                $order->update_meta_data( 'zelle_seleccionado', $_POST['zelle-select'] );
                $order->update_meta_data( 'email_origen', $_POST['email_origen'] );
            }
                    
            // Mark as on-hold (we're awaiting the payment)
            $order->update_status( 'on-hold', __( 'Esperando por confirmar pago', 'WC_Gateway_Offline_Zelle' ) );
                    
            // Reduce stock levels
            // $order->reduce_order_stock();
                    
            // Remove cart
            WC()->cart->empty_cart();
                    
            // Return thankyou redirect
            return array(
                'result'    => 'success',
                'redirect'  => $this->get_return_url( $order )
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
        public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
                
            if ( $this->instructions && ! $sent_to_admin && 'offline' === $order->payment_method && $order->has_status( 'on-hold' ) ) {
                echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
            }
        }

    } 
}