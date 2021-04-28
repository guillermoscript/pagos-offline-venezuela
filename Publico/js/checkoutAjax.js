import { 
    stopIt, 
    limpiador,
    showError,
} from './utils.js';

import {
    validacionBancoFinal,
    validacionBancosDisponibles,
    validacionCedula,
    validacionCellphone,
    // validacionCheckOut,
    validacionCorreo,
    validacionFecha,
    validacionFechaTrans,
    validacionNombre,
    // validacionNumeroDeCuenta,
    validacionNumeroReferencia,
    // validacionOtros
} from './validationsCheckout.js';

function addTextToInputFileWhenUserClick() {
    document.querySelectorAll('.label-file').forEach(el => {
        el.addEventListener('change', function() {
            this.children[0].children[0].innerText = this.children[1].value.replace('C:\\fakepath\\','')
        })
    })
}

function enviarImagen(nonce) {

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

    document.body.insertAdjacentHTML('afterbegin',`
        <div class="blockUI blockOverlay" style="z-index: 1000; border: medium none; margin: 0px; padding: 0px; width: 100%; height: 100%; top: 0px; left: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; opacity: 0.6; cursor: default; position: fixed;"></div>
    `)

    jQuery.ajax({
        method: "POST",
        xhr: function() {
    
            let xhr = new window.XMLHttpRequest();                    
            xhr.upload.addEventListener("progress", function(evt) {
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
    .done(function( data ) {

        let response = JSON.parse(data)

        if (response.error) {
            if (response.type) {
                console.log(response.type);
            }
            showError(response.error)
            return;
        }
        console.log('Successful AJAX Call! perrooooo / Return Data: ' + response.id);
        btnCheckOut.removeEventListener('click',stopIt )
        // btnCheckOut.removeEventListener('click',enviarImagen )
        document.getElementById('capture-' + clase).value = response.id
        btnCheckOut.click()
    })
    .fail(function( data ) {
        // let err = JSON.parse(data);
        console.log(data);
        console.log('Failed AJAX Call :( / Return Data: ' + data);
    }); 
}

function validacionesContainer(nonce) {
        
    let btnCheckOut = document.getElementById('place_order');
    let pagoMovilCheckBox = document.getElementById('payment_method_pago_movil');
    let transferenciaCheckBox = document.getElementById('payment_method_transferencia');
    let zelleCheckBox = document.getElementById('payment_method_zelle');
    let claseValidar = '';

    limpiador('woocommerce-NoticeGroup')
    
    if (pagoMovilCheckBox.checked === true) {
        claseValidar = 'pago_movil'
    } else if (transferenciaCheckBox.checked === true) {
        claseValidar = 'transferencia'
    } else if (zelleCheckBox.checked === true) {
        if (validarZelle() === true) {
            btnCheckOut.removeEventListener('click',stopIt )
            btnCheckOut.click()
            return
        }
    }

    if (validacionFormEspeciales(claseValidar) === true) {
        // btnCheckOut.removeEventListener('click',enviarImagen);            
        // btnCheckOut.addEventListener('click', enviarImagen );

        enviarImagen(nonce)
    } else {
        return
    }

}


function validarZelle() {
    
    let arrayDeErrores = [];

    if (validacionCorreo('email-origen') === 'no hay nada') {
        arrayDeErrores.push('¡Error! El campo del correo esta vacio, por favor ingrese un correo.')
    }
    if (document.getElementById('zelle_email').value === '') {
        arrayDeErrores.push('¡Error! No selecciono un correo zelle, por favor seleccione uno.')
    }

    if (validacionCorreo('email-origen') === 'no aceptado') {
        arrayDeErrores.push('¡Error! El correo de origen no es valido, por favor ingrese uno valido.')
    }

    if (arrayDeErrores.length === 0) {
        return true;
    } else {
        showError(arrayDeErrores)
        return false
    }
}

function validacionFormEspeciales(claseValidar) {
    let arrayDeErrores = [];
    let allowedExtensions = /(\.jpg|\.jpeg|\.pdf|\.png|\.gif)$/i;
    let clase = '';
    let num = '';

    if (claseValidar === 'pago_movil') {
        clase = 'comprobante_pago_movil'
        num = '2'
    } else {
        clase = 'comprobante_transferencia'
        num = '1'
    }

    let fileInput = document.getElementById(clase);
    let filePath = fileInput.value;
    

    if (validacionFechaTrans('fecha_' + claseValidar) === 'fecha menor') {
        arrayDeErrores.push('Error la fecha pago es 20 dias menor a la de hoy, por favor corrijalo, el limite es 20 dias antes de hoy')
    }

    if (validacionFechaTrans('fecha_' + claseValidar) === 'fecha mayor') {
        arrayDeErrores.push('Error la fecha pago es mayor a la de hoy, por favor corrijalo');
    }

    if (validacionFecha('fecha_' + claseValidar) === 'no hay nada') {
        arrayDeErrores.push('¡Error! Seleccione una fecha de pago, por favor.');
    }

    if (validacionNumeroReferencia('numero_recibo_' + claseValidar) === 'caracteres no validos') {
        arrayDeErrores.push('¡Error! Agrege solo numeros en el recibo, por favor.');
    }

    if (validacionBancoFinal('info_' + claseValidar) === 'no hay nada') {
        arrayDeErrores.push('¡Error! Seleccione el banco de destino, por favor.');
    }

    if (validacionBancosDisponibles('bancos_' + claseValidar) === 'no estan en los bancos') {
        arrayDeErrores.push('¡Error! Seleccione el banco de origen, por favor.');
    }

    if (fileInput.value === '') {
        arrayDeErrores.push('¡Error! Adjunte el comprobante, por favor.')
        showError(arrayDeErrores)
        return false
    }

    if (!allowedExtensions.test(filePath)){
        arrayDeErrores.push('No es un tipo de archivo aceptado, por favor use un con alguna de las extenciones: .jpg|.jpeg|.pdf|.png|.gif')
    } 
    
    if (jQuery('#' + clase)[0].files[0].size > 3000000) {
        arrayDeErrores.push('Error el capture es mayor a 3 Megas, por favor corrijalo')
    }


    if (arrayDeErrores.length === 0) {
        return true;
    } else {
        showError(arrayDeErrores)
        return false
    }
}

function validacionCheckOut() {

    let arrayDeErrores = [];

    if (validacionCedula('billing_cid') === 'cantidad no aceptada') {
        arrayDeErrores.push('Error la cantidad de digitos no es aceptada en la Cedula, por favor corrijalo')
        
    } 
    if (validacionCedula('billing_cid') === 'no hay nada') {
        arrayDeErrores.push('Error no hay nada en la cedula, por favor corrijalo')
        
    } 
    if (validacionCedula('billing_cid') === 'hay una letra') {
        arrayDeErrores.push('Error hay letras en la Cedula, por favor corrijalo')
        
    } 

    if (validacionNombre('billing_first_name') === 'hay un Numero') {
        arrayDeErrores.push('Error Hay un Numero en el Nombre')
        
    } 
    if (validacionNombre('billing_first_name') === 'maximo de caracteres permitido') {
        arrayDeErrores.push('Error hay mas caracteres de lo permitido en el nombre')
        
    } 
    if (validacionNombre('billing_first_name') === 'no hay nada') {
        arrayDeErrores.push('Error No hay nada en el Nombre, por favor ingrese su nombre')
    } 

    if (validacionCellphone('billing_phone') === 'no es un numero valido') {
        arrayDeErrores.push('Error No es un numero valido, por favor ingrese un numero de venezuela valido, Ejemplo: 0424 123 4567');
    }
    
    if (validacionCellphone('billing_phone') === 'no estan en los metodos de pago'){
        arrayDeErrores.push('Error No es un metodo disponible el que puso, por favor ingrese uno de los disponibles')
    } 

    if (validacionNombre('billing_last_name') === 'hay un Numero') {
        arrayDeErrores.push('Error Hay un Numero en el Apellido')
        
    } 
    if (validacionNombre('billing_last_name') === 'maximo de caracteres permitido') {
        arrayDeErrores.push('Error hay mas caracteres de lo permitido en el apellido')
        
    } 
    if (validacionNombre('billing_last_name') === 'no hay nada') {
        arrayDeErrores.push('Error No hay nada en el apellido, por favor ingrese su apellido')
        
    } 

    // if (validacionCorreo('billing_email') === 'no hay nada') {
    //     arrayDeErrores.push('Error No hay nada en el Correo, por favor ingrese su correo')
        
    // } 

    // if (validacionCorreo('billing_email') === 'no aceptado') {
    //     arrayDeErrores.push('Error No es aceptado el Correo, por favor ingrese su correo')
        
    // } 

    if (arrayDeErrores.length === 0) {
        return true
    } else {
        showError(arrayDeErrores)
        return false
    }
}



export {
    addTextToInputFileWhenUserClick,
    enviarImagen,
    validacionCheckOut,
    validacionesContainer,
}