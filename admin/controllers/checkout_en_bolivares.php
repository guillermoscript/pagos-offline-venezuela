<?php

// namespace Root\inc\controller;

PLUGIN_BASE_PATH2  . 'vendor/autoload.php';
use Goutte\Client;
function calcular_total_a_pagar($sub_total_en_dolares) {
    # code...
    
    $moneda = 'Bs.S';
    $tasa_de_bolivares = '';
    if (get_option( 'tasa_dolar_auto_insert' ) === 'yes') {  
        
        $client = new Client();
        try {
            // Go to the bcv.com website
            $crawler = $client->request('GET', URL_BANCO);
    
            // Get the dolar
            $helper = $crawler->filter('#dolar strong')->each(function ($node) {
                return $node->text()."\n";
            });
            $tasa_de_bolivares =  $helper[0];
        } catch (\Throwable $th) {
            //throw $th;
            $tasa_de_bolivares = get_option( 'tasa_dolar_title' );
        }
    } else {
        $tasa_de_bolivares = get_option( 'tasa_dolar_title' );
    }

    $precio_en_bolivares = $sub_total_en_dolares * floatval($tasa_de_bolivares);

    $precio_sin_iva = floatval($precio_en_bolivares / 1.16);
    $porcentaje_de_impuestos = floatval($precio_en_bolivares - $precio_sin_iva);
    // $porcentaje_de_servicios = ($sub_total_en_dolares / 10);
    
    // $precio_en_dolares_sin_iva = number_format(floatval($sub_total_en_dolares_en_dolares / 1.16),4,',','.');
    // $porcentaje_de_impuestos_dolares = $sub_total_en_dolares_en_dolares - $precio_en_dolares_sin_iva;

    $total = $precio_en_bolivares;

    // $total = $sub_total_en_dolares + $porcentaje_de_servicios + $porcentaje_de_impuestos;
    // $total_en_dolares = $sub_total_en_dolares_en_dolares + $porcentaje_de_servicios_dolares + $porcentaje_de_impuestos_dolares;

    return array(
        'total' => number_format($total,2,',','.'),
        'moneda' => $moneda,
        'tasa_dolar' => $tasa_de_bolivares,
        'sub_total_en_dolares' => $sub_total_en_dolares,
        'precio_sin_iva' => number_format($precio_sin_iva,2,',','.'),
        'porcentaje_de_impuestos' => number_format($porcentaje_de_impuestos,2,',','.')
    );
}

function debounce_add_jscript_checkout() {

    $info_de_los_pago = calcular_total_a_pagar(WC()->cart->subtotal);

    ?>
    <div class="caja-con-facturacion non2">
        <div class="caja-pago">
            <div class="montos align-cent">
                <h2 id="titulo-fact">Total Facturación</h2>
            </div>
            <div class="montos">
                <p>Sub-Total</p>
                <p><?php  echo  $info_de_los_pago['precio_sin_iva'] ?> Bs.S</p>
            </div>
            <div class="montos">
                <p class="flex-abajo">IVA  16%</p>
                <p><?php  echo $info_de_los_pago['porcentaje_de_impuestos'] ?> Bs.S</p>
            </div>                 
            <div class="montos dolares">
                <p>Tasa de cambio - BCV</p>
                <p id="tasa-hoy"><?php  echo $info_de_los_pago['tasa_dolar'] ?> Bs.S</p>
            </div>
            <div class="total-factura">
                <h4>TOTAL</h4>
                <h4 id="precio-total"><?php  echo $info_de_los_pago['total'] ?> Bs.S</h4>
            </div>
        </div>
    </div>
<?php
}