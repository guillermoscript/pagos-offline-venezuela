import { stopIt, limpiador} from './utils';

jQuery(window).load((e) => {
    e.preventDefault();
    document.querySelector('.col-1').append(document.querySelector('.caja-con-facturacion'))

    setInterval(() => {
        if (document.getElementById('payment_method_pago_movil').checked || document.getElementById('payment_method_transferencia').checked) {
            document.querySelector('.caja-con-facturacion').classList.remove('non2')
        } else if (document.getElementById('payment_method_zelle').checked) {
            document.querySelector('.caja-con-facturacion').classList.add('non2')
        }
    },200)
})


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
jQuery(document).ready(() => {
    observerWrapper()
    // document.querySelector('.woocommerce-privacy-policy-text p').innerText = '';
    // document.querySelector('.woocommerce-privacy-policy-text p').insertAdjacentHTML('afterbegin', `
    // Sus datos personales se utilizarán para procesar su pedido, apoyar su experiencia a través de este sitio web, y para otros fines descritos en nuestro 
    // <a href="https://netkiub.com/privacy-policy/" class="woocommerce-privacy-policy-link" target="_blank">política de privacidad</a>`);
}) 


function validarZelle() {
    
    let arrayDeErrores = [];

    if (validacionCorreo('email-origen') === 'no hay nada') {
        arrayDeErrores.push('Error No hay nada en el Correo, por favor ingrese su correo')
    }

    if (validacionCorreo('email-origen') === 'no aceptado') {
        arrayDeErrores.push('Error No es aceptado el Correo de origen zelle, por favor ingrese su correo')
    }

    if (arrayDeErrores.length === 0) {
        return true;
    } else {
        showError(arrayDeErrores)
        return false
    }
}

function validacionesContainer() {
        
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

    if (validacionCheckOut() === true) {
        if (validacionFormEspeciales(claseValidar) === true) {
            // btnCheckOut.removeEventListener('click',enviarImagen);
            btnCheckOut.addEventListener('click', enviarImagen );
        } else {
            return
        }
    } else {
        return
    }
}

function agregarEventosAlBotonCheckout() {

    let btnCheckOut = document.getElementById('place_order');
    btnCheckOut.addEventListener('click',stopIt )
    btnCheckOut.addEventListener('click', validacionesContainer)
 
}

function enviarImagen()  {

    let btnCheckOut = document.getElementById('place_order');
    let pagoMovilCheckBox = document.getElementById('payment_method_pago_movil');
    let transferenciaCheckBox = document.getElementById('payment_method_transferencia');
    let allowedExtensions = /(\.jpg|\.jpeg|\.pdf|\.png|\.gif)$/i;
    let clase = '';
    let num = '';
    let arrayDeErrores = [];
    
    if (pagoMovilCheckBox.checked === true) {
        clase = 'comprobante_pago_movil'
        num = '1'
    } else if (transferenciaCheckBox.checked === true) {
        clase = 'comprobante_transferencia'
        num = '2'
    }

    let fileInput = document.getElementById(clase);
    let filePath = fileInput.value;        

    if (jQuery('#' + clase)[0].files[0] === undefined) {
        arrayDeErrores.push(['Hace falta el Capture, por favor ingrese la imagen'])
        return;
    }

    if (!allowedExtensions.test(filePath)){
        arrayDeErrores.push('No es un tipo de archivo aceptado, por favor use un con alguna de las extenciones: .jpg|.jpeg|.pdf|.png|.gif')
    } 
    
    if (jQuery('#' + clase)[0].files[0].size > 3000000) {
        arrayDeErrores.push('Error el capture es mayor a 3 Megas, por favor corrijalo')
        showError(arrayDeErrores)
        return
    }
    
    if (fileInput.value === '') {
        arrayDeErrores.push('Error No a puesto nada en el capture, por favor corrijalo')
    }

    let fdata = new FormData();
    fdata.append('file', jQuery('#' + clase)[0].files[0]);
    fdata.append('action', ajax_var.action);
    fdata.append('nonce', ajax_var.nonce);

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
                    jQuery('#bar').width(percentComplete + '%');
            
                    if (percentComplete === 100) {
                        console.log('completado');
                        console.log(percentComplete);
                    }
                }
            }, false);
            return xhr;
        },
        url: ajax_var.url,
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
        btnCheckOut.removeEventListener('click',enviarImagen )
        document.getElementById('capture-' + clase).value = response.id
        btnCheckOut.click()
    })
    .fail(function( data ) {
        // let err = JSON.parse(data);
        console.log(data);
        console.log('Failed AJAX Call :( / Return Data: ' + data);
    }); 
}

function validacionFormEspeciales(claseValidar) {
    let arrayDeErrores = [];

    if (validacionFechaTrans('fecha_' + claseValidar) === 'fecha menor') {
        arrayDeErrores.push('Error la fecha pago es 20 dias menor a la de hoy, por favor corrijalo, el limite es 20 dias antes de hoy')
    }

    if (validacionFechaTrans('fecha_' + claseValidar) === 'fecha mayor') {
        arrayDeErrores.push('Error la fecha pago es mayor a la de hoy, por favor corrijalo');
    }

    if (validacionFecha('fecha_' + claseValidar) === 'no hay nada') {
        arrayDeErrores.push('Error no hay nada en la fecha pago, por favor corrijalo');
    }

    if (validacionNumeroReferencia('numero_recibo_' + claseValidar) === 'caracteres no validos') {
        arrayDeErrores.push('Error hay caracteres no validos en el numero de recibo, por favor corrijalo');
    }

    if (validacionBancoFinal('info_' + claseValidar) === 'no hay nada') {
        arrayDeErrores.push('Error No hay nada en el banco final, por favor corrijalo');
    }

    if (validacionBancosDisponibles('bancos_' + claseValidar) === 'no estan en los bancos') {
        arrayDeErrores.push('Error no es un banco de origen disponible, por favor corrijalo');
    }


    if (arrayDeErrores.length === 0) {
        return true;
    } else {
        showError(arrayDeErrores)
        return false
    }
}


function validacionBancoFinal(id) {
    let input = document.getElementById(id);
    if (input.value === '') return 'no hay nada';
    return input.value
}

function showError(mensaje) {
    
    jQuery('.checkout.woocommerce-checkout').prepend(`
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

function validacionFecha(id) {

    let input = document.getElementById(id)
    let date = new Date();
    let month = date.getMonth() + 1;
    let today = date.getDate();
    let year = date.getFullYear();
    if (input.value === '') {
        return 'no hay nada'
    }
    if (new Date(input.value.split('-')) < new Date(`${year}-${month}-${today}`)) {
        return 'fecha menor'
    } else {
        return input.value
    }
}


function validacionFechaTrans(id) {

    let input = document.getElementById(id)
    let date = new Date();
    let month = date.getMonth() + 1;
    let today = date.getDate();
    let year = date.getFullYear();

    function numDaysBetween(d1, d2) {
        let diff = Math.abs(d1.getTime() - d2.getTime());
        return diff / (1000 * 60 * 60 * 24);
    }
    if (input.value === '') {
        return 'no hay nada'
    }
    if ( numDaysBetween(new Date(input.value.split('-')),new Date(`${year}-${month}-${today}`.split('-'))) > 20 ) {
        return 'fecha menor'
    } else if (new Date(input.value.split('-')) > new Date(`${year}-${month}-${today}`.split('-'))) {
        return 'fecha mayor'
    } else {
        return input.value
    }
}


function validacionNumeroReferencia(id) {
    let input = document.getElementById(id);
   
    if (/^\d+$/gi.test(input.value)) {
        // if (input.value.length === 20) {

            return input.value
        // } else {
        //     return 'no tiene 20'    
        // }
        
    } else {
        return 'caracteres no validos'
    }
}


function validacionBancosDisponibles(id) {
    let bancos = [
        "Venezuela",
        "Banesco",
        "Provincial",
        "Mercantil",
        "Bod",
        "Bicentenario",
        "DelTesoro",
        "Bancaribe",
        "AgrícoladeVzla",
        "MiBanco",
        "BancoActivo",
        "BancoCaroní",
        "BancoExterior",
        "BancoPlaza",
        "BancoSofitasa",
        "BancoVenezolanodeCrédito",
        "Bancrecer",
        "BanFANB",
        "Bangente",
        "Banplus",
        "BFCBancoFondoComún",
        "DELSUR",
        "100%Banco",
        "MiBanco",
        "NacionaldeCrédito"
    ];
    let input = document.getElementById(id);
    if (bancos.includes(input.value)) {
        return input.value
    } else {
        return 'no estan en los bancos'
    }
}


function validacionCellphone(id) {
    let cellphone = document.getElementById(id);

    if (cellphone === null) return '';

    if (/(^(\+58\s?)?(\(\d{3}\)|\d{4})([\s\-]?\d{3})([\s\-]?\d{4})$)/g.test(cellphone.value)) {
        return cellphone.value.replace(/(\s)|([\(,\),-])|(\+)/g,'')
    } else {
        return 'no es un numero valido'
    }
}


function validacionCedula(id) {
    let input = document.getElementById(id);
        
    if (input === null) return '';
    if (/[a-zA-Z]/gi.test(input.value)) {
        return 'hay una letra'
    }
    if (input.value.length === 10 || input.value.length === 8 ) {
        // if (/(\d{1,2})|(\.\d{3})/gi.test(input.value)) {
        if (/^\d+$/gi.test(input.value)) {
            return input.value
        } 
    } else if (input.value.length === 0) {
        return 'no hay nada'
    } else {
        return 'cantidad no aceptada'
    } 
}


function validacionNumeroDeCuenta(id) {

    let input = document.getElementById(id);
    
    if (input === null) return '';

    if (/^\d+$/ig.test(input.value)) {
        // if (parseInt(input.value.length) > 20) {
        //     return 'mas numeros de lo debidos'
        // }
        // if (parseInt(input.value.length) < 20) {
        //     return 'menos numeros de los debidos'
        // }
        // if (parseInt(input.value.length) === 20) {
            return input.value
        // }
    } else {
        return 'hay caracteres invalidos'
    }
}

function validacionNombre(id) {
    let name = document.getElementById(id);
    
    if (name === null) return '';
    
    if (name.value === '') return 'no hay nada';
    
    if (name.value.length > 45) return 'maximo de caracteres permitido';
    
    if (/[0-9]/ig.test(name.value)) return 'hay un Numero';

    if (/[a-zA-Z]/gi.test(name.value)) return name.value
}


function validacionOtros(id) {
    let input = document.getElementById(id);

    if (input === null) return '';

    if (/(\w)|(\s)|([\.,\,,\(,\)])/ig.test(input.value)) return input.value
}


function validacionCorreo(id) {
    let correo = document.getElementById(id);

    if (correo.value === '') return 'no hay nada';

    if (/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/ig.test(correo.value)) {
        return correo.value
    } else {
        return 'no aceptado'
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

    if (validacionCorreo('billing_email') === 'no hay nada') {
        arrayDeErrores.push('Error No hay nada en el Correo, por favor ingrese su correo')
        
    } 

    if (validacionCorreo('billing_email') === 'no aceptado') {
        arrayDeErrores.push('Error No es aceptado el Correo, por favor ingrese su correo')
        
    } 

    if (arrayDeErrores.length === 0) {
        return true
    } else {
        showError(arrayDeErrores)
        return false
    }
}
