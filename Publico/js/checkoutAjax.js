import {
    stopIt,
    removeAllHtmlWithThisClass,
    showError,
} from './utils.js';

import {
    validationFinalBank,
    validationAvailebleBanks,
    validationIdn,
    validacionCellphone,
    // validationCheckout,
    validationEmail,
    validationDate,
    validationDateTrans,
    validationName,
    validacionNumeroDeCuenta,
    validationNumberOfTransfer,
    // validacionOtros
} from './validationsCheckout.js';

/**
* Adds the name of the file to the file input wrapper so the user
* can see that the file is ready to be uplodade.
*/
function addTextToInputFileWhenUserClick() {
    document.querySelectorAll('.label-file').forEach(el => {
        el.addEventListener('change', function () {
            this.children[0].children[0].innerText = this.children[1].value.replace('C:\\fakepath\\', '')
        })
    })
}

/**
* this function let the client upload the capture of the payment
* adds a lodaing bar so the client can see that the image or pdf can be uploaded
* and then after the image its uplodaded then do the normal checkout  
*
* @param {string} nonce string nonce so that the ajax can be done.
*/
function sendImage(nonce) {

    let btnCheckOut = document.getElementById('place_order');
    let pagoMovilCheckBox = document.getElementById('payment_method_pago_movil');
    let transferenciaCheckBox = document.getElementById('payment_method_transferencia');
    let clase = '';
    let num = '';

    if (pagoMovilCheckBox.checked === true) {
        clase = 'comprobante_pago_movil'
        num = '2'
    } else if (transferenciaCheckBox.checked === true) {
        clase = 'comprobante_transferencia'
        num = '1'
    }

    let fdata = new FormData();
    fdata.append('file', jQuery('#' + clase)[0].files[0]);
    fdata.append('action', nonce.action);
    fdata.append('nonce', nonce.nonce);

    document.body.insertAdjacentHTML('afterbegin', `
        <div class="blockUI blockOverlay" style="z-index: 1000; border: medium none; margin: 0px; padding: 0px; width: 100%; height: 100%; top: 0px; left: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; opacity: 0.6; cursor: default; position: fixed;"></div>
    `)

    jQuery.ajax({
        method: "POST",
        xhr: function () {

            let xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    jQuery('#bararea' + num).removeClass('non2')
                    let percentComplete = evt.loaded / evt.total;
                    percentComplete = parseInt(percentComplete * 100);
                    console.log(percentComplete);
                    jQuery('#bar' + num).width(percentComplete + '%');

                    if (percentComplete === 100) {
                        console.log('completado');
                        console.log(percentComplete);
                    }
                }
            }, false);
            return xhr;
        },
        url: nonce.url,
        processData: false,
        contentType: false,
        data: fdata
    })
        .done(function (data) {

            let response = JSON.parse(data)

            if (response.error) {
                if (response.type) {
                    console.log(response.type);
                }
                showError(response.error)
                return;
            }
            console.log('Successful AJAX Call! perrooooo / Return Data: ' + response.id);
            btnCheckOut.removeEventListener('click', stopIt)
            // btnCheckOut.removeEventListener('click',sendImage )
            document.getElementById('capture-' + clase).value = response.id
            btnCheckOut.click()
        })
        .fail(function (data) {
            // let err = JSON.parse(data);
            console.log(data);
            console.log('Failed AJAX Call :( / Return Data: ' + data);
        });
}

/**
* Its a guardian class so it will call other function to validate.
*
* @param {string} nonce string pass to the sendImage function so ajax can be done.
*/
function validationContainer(nonce) {

    let btnCheckOut = document.getElementById('place_order');
    let pagoMovilCheckBox = document.getElementById('payment_method_pago_movil');
    let transferenciaCheckBox = document.getElementById('payment_method_transferencia');
    let zelleCheckBox = document.getElementById('payment_method_zelle');
    let claseToValidate = '';

    removeAllHtmlWithThisClass('woocommerce-NoticeGroup')

    if (pagoMovilCheckBox.checked === true) {
        claseToValidate = 'pago_movil'
    } else if (transferenciaCheckBox.checked === true) {
        claseToValidate = 'transferencia'
    } else if (zelleCheckBox.checked === true) {
        if (validationZelle() === true) {
            btnCheckOut.removeEventListener('click', stopIt)
            btnCheckOut.click()
            return
        }
    }

    // probando
    if (validationOfSpecialInputsInForm(claseToValidate) === true) {
        sendImage(nonce)
    } else {
        return
    }

}


function validationZelle() {

    let arrayOfErrors = [];

    if (validationEmail('email-origen') === 'no hay nada') {
        arrayOfErrors.push('¡Error! El campo del correo esta vacio, por favor ingrese un correo.')
    }
    if (document.getElementById('zelle_email').value === '') {
        arrayOfErrors.push('¡Error! No selecciono un correo zelle, por favor seleccione uno.')
    }

    if (validationEmail('email-origen') === 'no aceptado') {
        arrayOfErrors.push('¡Error! El correo de origen no es valido, por favor ingrese uno valido.')
    }

    if (validacionNumeroDeCuenta('reference_number') === 'hay caracteres invalidos') {
        arrayOfErrors.push('¡Error! El numero de referencia no es valido, por favor ingrese uno valido.')
    }

    if (arrayOfErrors.length === 0) {
        return true;
    } else {
        showError(arrayOfErrors)
        return false
    }
}

function validationOfSpecialInputsInForm(claseToValidate) {
    let arrayOfErrors = [];
    let allowedExtensions = /(\.jpg|\.jpeg|\.pdf|\.png|\.gif)$/i;
    let clase = '';
    let num = '';

    if (claseToValidate === 'pago_movil') {
        clase = 'comprobante_pago_movil'
        num = '2'
    } else {
        clase = 'comprobante_transferencia'
        num = '1'
    }

    let fileInput = document.getElementById(clase);
    let filePath = fileInput.value;


    if (validationDateTrans('fecha_' + claseToValidate) === 'fecha menor') {
        arrayOfErrors.push('Error la fecha pago es 20 dias menor a la de hoy, por favor corrijalo, el limite es 20 dias antes de hoy')
    }

    if (validationDateTrans('fecha_' + claseToValidate) === 'fecha mayor') {
        arrayOfErrors.push('Error la fecha pago es mayor a la de hoy, por favor corrijalo');
    }

    if (validationDate('fecha_' + claseToValidate) === 'no hay nada') {
        arrayOfErrors.push('¡Error! Seleccione una fecha de pago, por favor.');
    }

    if (validationNumberOfTransfer('numero_recibo_' + claseToValidate) === 'caracteres no validos') {
        arrayOfErrors.push('¡Error! Agrege solo numeros en el recibo, por favor.');
    }

    if (validationFinalBank('info_' + claseToValidate) === 'no hay nada') {
        arrayOfErrors.push('¡Error! Seleccione el banco de destino, por favor.');
    }

    if (validationAvailebleBanks('bancos_' + claseToValidate) === 'no estan en los bancos') {
        arrayOfErrors.push('¡Error! Seleccione el banco de origen, por favor.');
    }

    if (fileInput.value === '') {
        arrayOfErrors.push('¡Error! Adjunte el comprobante, por favor.')
        showError(arrayOfErrors)
        return false
    }

    if (!allowedExtensions.test(filePath)) {
        arrayOfErrors.push('No es un tipo de archivo aceptado, por favor use un con alguna de las extenciones: .jpg|.jpeg|.pdf|.png|.gif')
    }

    if (jQuery('#' + clase)[0].files[0].size > 3000000) {
        arrayOfErrors.push('Error el capture es mayor a 3 Megas, por favor corrijalo')
    }


    if (arrayOfErrors.length === 0) {
        return true;
    } else {
        showError(arrayOfErrors)
        return false
    }
}

function validationCheckout() {

    let arrayOfErrors = [];

    if (validationIdn('billing_cid') === 'cantidad no aceptada') {
        arrayOfErrors.push('Error la cantidad de digitos no es aceptada en la Cedula, por favor corrijalo')

    }
    if (validationIdn('billing_cid') === 'no hay nada') {
        arrayOfErrors.push('Error no hay nada en la cedula, por favor corrijalo')

    }
    if (validationIdn('billing_cid') === 'hay una letra') {
        arrayOfErrors.push('Error hay letras en la Cedula, por favor corrijalo')

    }

    if (validationName('billing_first_name') === 'hay un Nmero') {
        arrayOfErrors.push('Error Hay un Número en el Nombre')

    }
    if (validationName('billing_first_name') === 'maximo de caracteres permitido') {
        arrayOfErrors.push('Error hay mas caracteres de lo permitido en el nombre')

    }
    if (validationName('billing_first_name') === 'no hay nada') {
        arrayOfErrors.push('Error No hay nada en el Nombre, por favor ingrese su nombre')
    }

    if (validacionCellphone('billing_phone') === 'no es un numero valido') {
        arrayOfErrors.push('Error No es un numero valido, por favor ingrese un numero de venezuela valido, Ejemplo: 0424 123 4567');
    }

    if (validacionCellphone('billing_phone') === 'no estan en los metodos de pago') {
        arrayOfErrors.push('Error No es un metodo disponible el que puso, por favor ingrese uno de los disponibles')
    }

    if (validationName('billing_last_name') === 'hay un Numero') {
        arrayOfErrors.push('Error Hay un Número en el Apellido')

    }
    if (validationName('billing_last_name') === 'maximo de caracteres permitido') {
        arrayOfErrors.push('Error hay mas caracteres de lo permitido en el apellido')

    }
    if (validationName('billing_last_name') === 'no hay nada') {
        arrayOfErrors.push('Error No hay nada en el apellido, por favor ingrese su apellido')

    }

    // if (validationEmail('billing_email') === 'no hay nada') {
    //     arrayOfErrors.push('Error No hay nada en el Correo, por favor ingrese su correo')

    // } 

    // if (validationEmail('billing_email') === 'no aceptado') {
    //     arrayOfErrors.push('Error No es aceptado el Correo, por favor ingrese su correo')

    // } 

    if (arrayOfErrors.length === 0) {
        return true
    } else {
        showError(arrayOfErrors)
        return false
    }
}



export {
    addTextToInputFileWhenUserClick,
    sendImage,
    validationCheckout,
    validationContainer,
}