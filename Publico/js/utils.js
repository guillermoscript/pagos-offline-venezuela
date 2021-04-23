function stopIt(e) {
    e.preventDefault();
    e.stopPropagation();
}

function limpiador(clase) {
    if (document.getElementsByClassName(clase)) {
        for (let i = 0; i < document.getElementsByClassName(clase).length; i++) {
            document.getElementsByClassName(clase)[i].remove();
        }
    }
}


function showError(mensaje) {
    
    limpiador('woocommerce-notices-wrapper')
    
    jQuery('.woocommerce-notices-wrapper').prepend(`
        <div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">
            <ul class="woocommerce-error" role="alert">
            </ul>
        </div>
    `);
    mensaje.forEach(elem => {
        document.getElementsByClassName('woocommerce-error')[0].insertAdjacentHTML('afterbegin','<li>' + elem +'</li>')
    })
    jQuery('html, body').animate({ scrollTop: 0 }, 'slow');

}

export {
    stopIt,
    limpiador,
    showError
}