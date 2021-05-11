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
    validationContainer(ajax_var2)
}

function addEventsToCheckoutButton() {

    let btnCheckOut = document.getElementById('place_order');
    btnCheckOut.addEventListener('click',stopIt )
    btnCheckOut.addEventListener('click', getNonceAndRunValidation)
 
}
