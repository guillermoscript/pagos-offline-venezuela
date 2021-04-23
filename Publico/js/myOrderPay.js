import {
    addTextToInputFileWhenUserClick,
    validacionesContainer,
    // observerWrapper,
} from './checkoutAjax.js';


import { 
    stopIt, 
    // limpiador,
    // showError,
} from './utils.js';

jQuery(document).ready(() => {
    agregarEventosAlBotonCheckout()
    addTextToInputFileWhenUserClick()
}) 

function getNonceAndRunValidation() {
    validacionesContainer(ajax_var2)
}

function agregarEventosAlBotonCheckout() {

    let btnCheckOut = document.getElementById('place_order');
    btnCheckOut.addEventListener('click',stopIt )
    btnCheckOut.addEventListener('click', getNonceAndRunValidation)
 
}
