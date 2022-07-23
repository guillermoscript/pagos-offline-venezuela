import {
    stopIt,
    // changeImageIfUSerSelectOtherQr,
    // changeImageIfUSerSelectOtherQrBinance,
    copyToClipboart,
    showQR
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
        addEventIfRadioOfPaymentMethod()
        addEventsToCheckoutButon()
        addTextToInputFileWhenUserClick()
        // if (document.querySelector('#reserve_qr_img img')) {
        //     // changeImageIfUSerSelectOtherQr()
        // }
        // if (document.querySelector('#binance_qr_img img')) {
        //     // changeImageIfUSerSelectOtherQrBinance()
        // }

        document.getElementById('reserve') ? document.getElementById('reserve').addEventListener('click', showQR) : null
        document.getElementById('binance') ? document.getElementById('binance').addEventListener('click', showQR) : null
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

function removeEventsToCheckoutButon() {
    let btnCheckOut = document.getElementById('place_order');
    btnCheckOut.removeEventListener('click', stopIt)
    btnCheckOut.removeEventListener('click', finishCheckout)
}

function addEventIfRadioOfPaymentMethod() {
    let radioPaymentMethod = document.querySelectorAll('.payment_methods input[type="radio"]');
    const paymentMethods = ['pago_movil', 'transferencia', 'zelle', 'reserve', 'binance'];
    radioPaymentMethod.forEach(el => {
        // if (paymentMethods.includes(el.value) ) {
        //     el.addEventListener('click', () => {
        //         document.querySelector('.caja-con-facturacion').classList.remove('non2')
        //     })
        // }

        el.addEventListener('change', () => {
            if (paymentMethods.includes(el.value) ) {
                addEventsToCheckoutButon()
            } else {
                removeEventsToCheckoutButon()
            }
        })
    })
}