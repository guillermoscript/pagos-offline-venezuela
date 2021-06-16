import {
    addTextToInputFileWhenUserClick,
    validationContainer,
    // observerWrapper,
} from './checkoutAjax.js';


import { 
    stopIt, 
    // removeAllHtmlWithThisClass,
    // showError,
} from './utils.js';

jQuery(document).ready(() => {
    addEventsToCheckoutButton()
    addTextToInputFileWhenUserClick()
}) 

function getNonceAndRunValidation() {
    if (!validationCheckout()) return;
    if (validationContainer(ajax_var2)) {
        document.getElementById('place_order').removeEventListener('click',finishCheckout)
    }
    // validationContainer(ajax_var2)
}

function addEventsToCheckoutButton() {

    let btnCheckOut = document.getElementById('place_order');
    btnCheckOut.addEventListener('click',stopIt )
    btnCheckOut.addEventListener('click', getNonceAndRunValidation)
 
}
