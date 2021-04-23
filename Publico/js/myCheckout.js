import { 
    stopIt, 
} from './utils.js';

import {
    addTextToInputFileWhenUserClick,
    validacionesContainer,
} from './checkoutAjax.js';


import {
    validacionCheckOut,
} from './validationsCheckout.js';


jQuery(document).ready(() => {
    observerWrapper()
}) 

function finishCheckout (e){
  if(!validacionCheckOut()) return;
    validacionesContainer(ajax_var)
}

function observerWrapper() {
    // target element that we will observe
    const target = document.getElementsByClassName('checkout')[0];

    // config object
    const config = {
        characterData: true,
        childList: true,
        subtree: true
    };

    // subscriber function
    function subscriber(mutations) {
        mutations.forEach( mutation => {
            console.log(mutation);
            if (mutation.target === document.getElementById('place_order')) {
                if (mutation.addedNodes.length > 0) {
                    agregarEventosAlBotonCheckout()
                    addTextToInputFileWhenUserClick()
                }
            }
            if (mutation.addedNodes[0] === document.querySelector('.woocommerce-NoticeGroup.woocommerce-NoticeGroup-checkout')) {
                document.querySelector('.blockUI.blockOverlay').remove()
            }
        })      
        
    }

    // instantiating observer
    const observer = new MutationObserver(subscriber);

    // observing target
    observer.observe(target, config);
}

function agregarEventosAlBotonCheckout() {

    let btnCheckOut = document.getElementById('place_order');
    btnCheckOut.addEventListener('click',stopIt )
    btnCheckOut.addEventListener('click', finishCheckout)
 
}