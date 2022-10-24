<?php

use Admin\Controllers\RestApiV1;

function debounce_add_jscript_checkout() {

    $uri = $_SERVER['REQUEST_URI'];
    // get order id from url
    $order_id = preg_match('/order-pay/',$uri) ? explode('order-pay/', $uri)[1] : null;
    // $order = wc_get_order(  );
    if ($order_id) {
        $order = wc_get_order(explode('/', $order_id)[0]);
        $sub_total_in_dolars = $order->get_subtotal();
        $taxes = $order->get_taxes();
    } else {
        $sub_total_in_dolars = WC()->cart->get_cart_contents_total();
        $taxes = WC()->cart->get_taxes();
    }
    $info_de_los_pago = RestApiV1::get_rate_of_bf($sub_total_in_dolars, $taxes);

    ?>
    <div class="caja-con-facturacion">
        <div class="caja-pago">
            <div class="montos align-cent">
                <h2 id="titulo-fact">Total Facturación</h2>
            </div>
            <div class="montos">
                <p>Sub-Total</p>
                <p><?php  echo  $info_de_los_pago['price_without_iva'] ?> Bs</p>
            </div>
            <div class="montos">
                <p class="flex-abajo">IVA  16%</p>
                <p><?php  echo $info_de_los_pago['percentage_of_iva'] ?> Bs</p>
            </div>                 
            <div class="montos dolares">
                <p>Tasa de cambio - BCV</p>
                <p id="tasa-hoy"><?php  echo $info_de_los_pago['rate_of_dolar'] ?> Bs</p>
            </div>
            <div class="total-factura">
                <h4>TOTAL</h4>
                <h4 id="precio-total"><?php  echo $info_de_los_pago['total'] ?> Bs</h4>
            </div>
        </div>
    </div>
<?php
}