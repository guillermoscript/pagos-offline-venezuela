import {
    stopIt,
    // changeImageIfUSerSelectOtherQr,
    // changeImageIfUSerSelectOtherQrBinance,
    copyToClipboart,
    changeImageIfUSerSelectOtherQrBinance,
    changeImageIfUSerSelectOtherQr,
    showPreviewOfImageUploaded,
    addTextToInputFileWhenUserClick
} from './utils.js';

import {
    validationContainer,
} from './checkoutAjax.js';

import {
    validationCheckout
} from './guardianFunctions.js';

import {
    sanitizeMethods
} from './validationsCheckout.js';

jQuery(document).ready(() => {

    /**
    * Check if the DOM have been modified by woocommerce (the checkout button) by an ajax call
    * and if thats true then add events to the checout button.
    * 
    */
    jQuery('body').on('updated_checkout', () => {
        if (document.querySelector('#reserve_qr_img img')) {
            changeImageIfUSerSelectOtherQr()
        }
        if (document.querySelector('#binance_qr_img img')) {
            changeImageIfUSerSelectOtherQrBinance()
        }

        runFunctionAfterUpdate()
    })
    // showTotalInBs()

    runFunctionAfterUpdate()
})

function runFunctionAfterUpdate() {

    document.querySelectorAll('.copy').forEach(el => el.addEventListener('click', copyToClipboart))
    const inputFiles = document.querySelectorAll(".upload-file");
    inputFiles.forEach(input => {
        input.addEventListener("change", showPreviewOfImageUploaded);
    });
    addTextToInputFileWhenUserClick()
    addEventsToCheckoutButon()

    const namesInputs = document.querySelectorAll("[data-validate='names']");
    const emailInputs = document.querySelectorAll("[data-validate='email']");
    const phoneInputs = document.querySelectorAll("[data-validate='phone']");
    const numbersInputs = document.querySelectorAll("[data-validate='numbers']");

    sanitizeMethods.names(...namesInputs);
    sanitizeMethods.email(...emailInputs);
    sanitizeMethods.phone(...phoneInputs);
    sanitizeMethods.numbers(...numbersInputs);
}

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