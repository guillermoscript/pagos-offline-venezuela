
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


function validacionBancoFinal(id) {
    let input = document.getElementById(id);

    if (input.value === '') return 'no hay nada';

    if (input.value) return input.value
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
    validacionBancoFinal,
    validacionBancosDisponibles,
    validacionCedula,
    validacionCellphone,
    validacionCheckOut,
    validacionCorreo,
    validacionFecha,
    validacionFechaTrans,
    validacionNombre,
    validacionNumeroDeCuenta,
    validacionNumeroReferencia,
    validacionOtros
}