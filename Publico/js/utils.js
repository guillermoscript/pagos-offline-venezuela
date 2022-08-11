function stopIt(e) {
    e.preventDefault();
    e.stopPropagation();
}

function removeAllHtmlWithThisClass(clase) {
    if (document.getElementsByClassName(clase)) {
        for (let i = 0; i < document.getElementsByClassName(clase).length; i++) {
            document.getElementsByClassName(clase)[i].remove();
        }
    }
}

function showError(mensaje) {

    jQuery('.woocommerce-notices-wrapper').prepend(`
        <div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">
            <ul class="woocommerce-error" role="alert">
            </ul>
        </div>
    `);

    mensaje.forEach(elem => {
        document.getElementsByClassName('woocommerce-error')[0].insertAdjacentHTML('afterbegin', '<li>' + elem + '</li>')
    })
    jQuery('html, body').animate({ scrollTop: 0 }, 'slow');

}

function changeImageIfUSerSelectOtherQr() {
    let qr = document.querySelector('#reserve_qr_img img')
    let selectInput = document.querySelector('#info_reserve')
    selectInput.addEventListener('change', (e) => {
        let image = selectInput.options[selectInput.options.selectedIndex].dataset.qr
        qr.src = image
        qr.parentElement.style.display = 'block'
    });
}

function showQR(e) {
    let target = e.target.id;
    let qr = document.querySelector(`#${target}_qr_img img`)
    qr.parentElement.style.display = 'block'
}

function changeImageIfUSerSelectOtherQrBinance() {
    let qr = document.querySelector('#binance_qr_img img')
    let selectInput = document.querySelector('#info_binance')
    selectInput.addEventListener('change', (e) => {
        let image = selectInput.options[selectInput.options.selectedIndex].dataset.qr
        qr.src = image
        qr.parentElement.style.display = 'block'
    });
}

const copyToClipboard = str => {
    if (navigator && navigator.clipboard && navigator.clipboard.writeText)
        return navigator.clipboard.writeText(str);
    return Promise.reject('The Clipboard API is not available.');
};

function copyToClipboart(e) {
    /* Get the text field */

    console.log(e.target.getAttribute('data-id'))
    const idToCopy = e.target.getAttribute('data-id');
    const htmlInput = document.getElementById(idToCopy)
    // const value = htmlInput.options[htmlInput.selectedIndex].innerText.trim()
    const value = htmlInput.innerText.trim()
    // let copyText = document.getElementById("myInput");

    /* Select the text field */
    // htmlInput.select();
    // htmlInput.setSelectionRange(0, 99999); /* For mobile devices */

    /* Copy the text inside the text field */
    navigator.clipboard.writeText(value);

    /* Alert the copied text */
    alert("Texto copiado: " + value);
}

function myFunction() {
    const idToCopy = e.target.getAttribute('data-id');
    const htmlInput = document.getElementById(idToCopy)
    // const value = htmlInput.options[htmlInput.selectedIndex].innerText.trim()
    const value = htmlInput.innerText.trim()
    // htmlInput.select();
    // htmlInput.setSelectionRange(0, 99999); /* For mobile devices */
    /* Copy selected text into clipboard */
    copyToClipboard(value)
        .then(() => {
            alert('texto copiado');
        })
        .catch(err => {
            alert(err);
        });
}


export {
    stopIt,
    removeAllHtmlWithThisClass,
    // changeImageIfUSerSelectOtherQr,
    // changeImageIfUSerSelectOtherQrBinance,
    copyToClipboart,
    showError,
    showQR
}