import {
    validationContainer,
    // observerWrapper,
} from './checkoutAjax.js';


import {
    stopIt,
    changeImageIfUSerSelectOtherQr,
    changeImageIfUSerSelectOtherQrBinance,
    copyToClipboart,
    showPreviewOfImageUploaded,
    addTextToInputFileWhenUserClick
    // removeAllHtmlWithThisClass,
    // showError,
} from './utils.js';


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

    runFunctionAfterUpdate()
})

function runFunctionAfterUpdate() {

    document.querySelectorAll('.copy').forEach(el => el.addEventListener('click', copyToClipboart))
    const inputFiles = document.querySelectorAll(".upload-file");
    inputFiles.forEach(input => {
        input.addEventListener("change", showPreviewOfImageUploaded);
    });
    addTextToInputFileWhenUserClick()
    addEventsToCheckoutButton()

    const namesInputs = document.querySelectorAll("[data-validate='names']");
    const emailInputs = document.querySelectorAll("[data-validate='email']");
    const phoneInputs = document.querySelectorAll("[data-validate='phone']");
    const numbersInputs = document.querySelectorAll("[data-validate='numbers']");

    sanitizeMethods.names(...namesInputs);
    sanitizeMethods.email(...emailInputs);
    sanitizeMethods.phone(...phoneInputs);
    sanitizeMethods.numbers(...numbersInputs);
}

function getNonceAndRunValidation() {
    if (validationContainer(ajax_var2)) {
        document.getElementById('place_order').removeEventListener('click', finishCheckout)
    }
    // validationContainer(ajax_var2)
}

function addEventsToCheckoutButton() {

    let btnCheckOut = document.getElementById('place_order');
    btnCheckOut.addEventListener('click', stopIt)
    btnCheckOut.addEventListener('click', getNonceAndRunValidation)

}
