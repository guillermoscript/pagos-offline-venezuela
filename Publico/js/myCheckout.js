import {
    stopIt,
    // changeImageIfUSerSelectOtherQr,
    // changeImageIfUSerSelectOtherQrBinance,
    copyToClipboart,
    changeImageIfUSerSelectOtherQrBinance,
    changeImageIfUSerSelectOtherQr,
} from './utils.js';

import {
    addTextToInputFileWhenUserClick,
    validationContainer,
    validationCheckout,
} from './checkoutAjax.js';

jQuery(document).ready(() => {

    /**
    * Check if the DOM have been modified by woocommerce (the checkout button) by an ajax call
    * and if thats true then add events to the checout button.
    * 
    */
    jQuery('body').on('updated_checkout', () => {
        addEventsToCheckoutButon()
        addTextToInputFileWhenUserClick()
        if (document.querySelector('#reserve_qr_img img')) {
            changeImageIfUSerSelectOtherQr()
        }
        if (document.querySelector('#binance_qr_img img')) {
            changeImageIfUSerSelectOtherQrBinance()
        }
        document.querySelectorAll('.copy').forEach(el => el.addEventListener('click', copyToClipboart))
    })
    // showTotalInBs()
})

function finishCheckout() {
    if (!validationCheckout()) return;
    if (validationContainer(ajax_var)) {
            // setTimeout(() => document.getElementById('place_order').click(), 100)
            document.getElementById('place_order').removeEventListener('click', finishCheckout)
    }
}

// funcion que tla vez ponga pero por ahora no 
// function showTotalInBs() {
//     let pagoMovilCheckBox = document.getElementById('payment_method_pago_movil');
//     let transferenciaCheckBox = document.getElementById('payment_method_transferencia');
//     let zelleCheckBox = document.getElementById('payment_method_zelle');
//     pagoMovilCheckBox.addEventListener('click', () => {
//         document.querySelector('.caja-con-facturacion').classList.remove('non2')
//     })
//     transferenciaCheckBox.addEventListener('click', () => {
//         document.querySelector('.caja-con-facturacion').classList.remove('non2')
//     })
//     zelleCheckBox.addEventListener('click', () => {
//         document.querySelector('.caja-con-facturacion').classList.add('non2')
//     })
// }

function addEventsToCheckoutButon() {
    let btnCheckOut = document.getElementById('place_order');
    btnCheckOut.addEventListener('click', stopIt)
    btnCheckOut.addEventListener('click', finishCheckout)
}