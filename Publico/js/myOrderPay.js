import {
    addTextToInputFileWhenUserClick,
    validationContainer,
    // observerWrapper,
} from './checkoutAjax.js';


import {
    stopIt,
    // changeImageIfUSerSelectOtherQr,
    // changeImageIfUSerSelectOtherQrBinance,
    copyToClipboart
    // removeAllHtmlWithThisClass,
    // showError,
} from './utils.js';

jQuery(document).ready(() => {

    /**
    * Check if the DOM have been modified by woocommerce (the checkout button) by an ajax call
    * and if thats true then add events to the checout button.
    * 
    */
    jQuery('body').on('updated_checkout', () => {

        addEventsToCheckoutButton()
        addTextToInputFileWhenUserClick()
        // if (document.querySelector('#reserve_qr_img img')) {
        //     changeImageIfUSerSelectOtherQr()
        // }
        // if (document.querySelector('#binance_qr_img img')) {
        //     changeImageIfUSerSelectOtherQrBinance()
        // }

        document.querySelectorAll('.copy').forEach(el => el.addEventListener('click',copyToClipboart))

    })
})

function getNonceAndRunValidation() {
    if (!validationCheckout()) return;
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
