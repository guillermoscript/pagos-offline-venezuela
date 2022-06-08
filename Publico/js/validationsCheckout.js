function validationDate(id) {

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
    }
}


function validationDateTrans(id) {

    let input = document.getElementById(id)
    let date = new Date();
    let month = date.getMonth() + 1;
    let today = date.getDate();
    let year = date.getFullYear();

    function numDaysBetween(d1, d2) {
        let diff = Math.abs(d1.getTime() - d2.getTime());
        return diff / (1000 * 60 * 60 * 24);
    }

    if (input.value === null) return 'no hay nada';

    if (input.value === '') return 'no hay nada';

    if ( numDaysBetween(new Date(input.value.split('-')),new Date(`${year}-${month}-${today}`.split('-'))) > 20 ) {
        return 'fecha menor'
    } else if (new Date(input.value.split('-')) > new Date(`${year}-${month}-${today}`.split('-'))) {
        return 'fecha mayor'
    }
}


function validationNumberOfTransfer(id) {
    let input = document.getElementById(id);
   
    if (!/^\d+$/gi.test(input.value)) {
        return 'caracteres no validos'
    }
}


function validationAvailebleBanks(id) {
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
    if (!bancos.includes(input.value)) {
        return 'no estan en los bancos'
    }
}


function validacionCellphone(id) {
    let cellphone = document.getElementById(id);

    if (cellphone === null) return '';

    if (!/(^(\+58\s?)?(\d{3}|\d{4})([\s\-]?\d{3})([\s\-]?\d{4})$)/g.test(cellphone.value)) {
        return 'no es un numero valido'
    }
}


function validationIdn(id) {
    let input = document.getElementById(id);
        
    if (input === null) return '';
    if (/[a-zA-Z]/gi.test(input.value)) {
        return 'hay una letra'
    }
    if (input.value.length < 7) {
        return 'cantidad no aceptada'
    } else if (input.value.length === 0) {
        return 'no hay nada'
    } 
}


function validacionNumeroDeCuenta(id) {

    let input = document.getElementById(id);
    
    if (input === null) return '';

    if (!/^\d+$/ig.test(input.value)) {
        return 'hay caracteres invalidos'
    }
}

function validationName(id) {
    let name = document.getElementById(id);
    
    if (name === null) return '';
    
    if (name.value === '') return 'no hay nada';
    
    if (name.value.length > 45) return 'maximo de caracteres permitido';
    
    if (/[0-9]/ig.test(name.value)) return 'hay un Numero';

    // if (/[a-zA-Z]/gi.test(name.value)) return name.value
}


function validationFinalBank(id) {
    let input = document.getElementById(id);

    if (input.value === '') return 'no hay nada';

    // if (input.value) return input.value
}


function validacionOtros(id) {
    let input = document.getElementById(id);

    if (input === null) return '';

    // if (/(\w)|(\s)|([\.,\,,\(,\)])/ig.test(input.value)) return input.value
}

function validationCapture(id) {

	const fileInput = document.getElementById(id);
	const filePath = fileInput.value;
	const allowedExtensions = /(\.jpg|\.jpeg|\.pdf|\.png|\.gif)$/i;
    
	if (fileInput.value === "") {
		return 'no hay nada'
	}
	

	if (!allowedExtensions.test(filePath)) {
        return 'no es una extension valida';
	}

	if (jQuery("#" + id)[0].files[0].size > 3000000) {
        return 'el archivo es muy grande';
	}
}

function validationEmail(id) {
    let correo = document.getElementById(id);

    if (correo.value === '') return 'no hay nada';

    if (!/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/ig.test(correo.value)) {
        return 'no aceptado'
    }

}

function validationReferenceNumberZelle(id) {

    let input = document.getElementById(id);

    if (input === null) return '';

    if (input.value === '') return 'no hay nada';

    // if (/^\w+$/ig.test(input.value)) {
    //     return 'no aceptado'
    // }
}


export {
    validationFinalBank,
    validationAvailebleBanks,
    validationIdn,
    validacionCellphone,
    // validationCheckout,
    validationEmail,
    validationDate,
    validationReferenceNumberZelle,
    // validationDateTrans,
    validationName,
    validacionNumeroDeCuenta,
    validationNumberOfTransfer,
    validationCapture,
    validacionOtros
}