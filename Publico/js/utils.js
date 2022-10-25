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


/**
 * Adds the name of the file to the file input wrapper so the user
 * can see that the file is ready to be uplodade.
 */
 function addTextToInputFileWhenUserClick() {
	document.querySelectorAll(".label-file").forEach((el) => {
		el.addEventListener("change", function () {
			this.children[0].children[0].innerText = this.children[1].value.replace(
				"C:\\fakepath\\",
				""
			);
		});
	});
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

function showPreviewOfImageUploaded(event) {
    let output = document.querySelector("[data-id=" + event.target.id + "]");
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function () {
        URL.revokeObjectURL(output.src) // free memory
    }
};

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
    const idToCopy = e.target.getAttribute('data-id');
    const htmlInput = document.getElementById(idToCopy)
    const value = htmlInput.options[htmlInput.selectedIndex].innerText.trim()
    // const value = htmlInput.innerText.trim()
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
    changeImageIfUSerSelectOtherQr,
    changeImageIfUSerSelectOtherQrBinance,
    copyToClipboart,
    showError,
    showPreviewOfImageUploaded,
    addTextToInputFileWhenUserClick
}