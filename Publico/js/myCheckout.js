import {
    stopIt,
} from './utils.js';

import {
    addTextToInputFileWhenUserClick,
    validationContainer,
    validationCheckout,
} from './checkoutAjax.js';

// console.log('AAAAAAAAAAAAAAAAAAAAAA FUCKKKKKKKKKKKKKKKKKKKKKKKKKKKKK');

jQuery(document).ready(() => {
    observerWrapper()
})

function finishCheckout() {
    if (!validationCheckout()) return;
    if (validationContainer(ajax_var)) {
        document.getElementById('place_order').removeEventListener('click',finishCheckout)
    }
}


/**
* Check if the DOM have been modified by woocommerce (the checkout button) by an ajax call
* and if thats true then add events to the checout button.
* 
*/
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
        mutations.forEach(mutation => {
            // console.log(mutation);
            if (mutation.target === document.getElementById('place_order')) {
                if (mutation.addedNodes.length > 0) {
                    // console.log('aaa');
                    addEventsToCheckoutButon()
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

function addEventsToCheckoutButon() {

    let btnCheckOut = document.getElementById('place_order');
    btnCheckOut.addEventListener('click', stopIt)
    btnCheckOut.addEventListener('click', finishCheckout)

}