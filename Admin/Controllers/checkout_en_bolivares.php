<?php

use Admin\Controllers\RestApiV1;

function debounce_add_jscript_checkout() {

    $info_de_los_pago = RestApiV1::get_rate_of_bf(WC()->cart->get_cart_contents_total(),WC()->cart->get_taxes());

    ?>
    <div class="caja-con-facturacion non2">
        <div class="caja-pago">
            <div class="montos align-cent">
                <h2 id="titulo-fact">Total Facturación</h2>
            </div>
            <div class="montos">
                <p>Sub-Total</p>
                <p><?php  echo  $info_de_los_pago['price_without_iva'] ?> Bs.S</p>
            </div>
            <div class="montos">
                <p class="flex-abajo">IVA  16%</p>
                <p><?php  echo $info_de_los_pago['percentage_of_iva'] ?> Bs.S</p>
            </div>                 
            <div class="montos dolares">
                <p>Tasa de cambio - BCV</p>
                <p id="tasa-hoy"><?php  echo $info_de_los_pago['rate_of_dolar'] ?> Bs.S</p>
            </div>
            <div class="total-factura">
                <h4>TOTAL</h4>
                <h4 id="precio-total"><?php  echo $info_de_los_pago['total'] ?> Bs.S</h4>
            </div>
        </div>
    </div>
<?php
}