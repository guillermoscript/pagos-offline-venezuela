import {
	validationFinalBank,
	validationAvailebleBanks,
	validationIdn,
	validacionCellphone,
	validationEmail,
	validationName,
	validationReferenceNumberZelle,
	validationNumberOfTransfer,
} from "./validationsCheckout.js";

import { errors } from "./errors.js";
import { showError } from "./utils.js";

export function validationCheckout() {
	let arrayOfErrors = [];

	if (validationIdn("billing_cid") === "cantidad no aceptada") {
		arrayOfErrors.push(errors.validationIdn['cantidad no aceptada']);
	}
	if (validationIdn("billing_cid") === "no hay nada") {
		arrayOfErrors.push(errors.validationIdn['no hay nada']);
	}
	if (validationIdn("billing_cid") === "hay una letra") {
		arrayOfErrors.push(errors.validationIdn['hay una letra']);
	}
	if (validationName("billing_first_name") === "hay un Numero") {
		arrayOfErrors.push(errors.validationName['hay un Numero']);
	}
	if (validationName("billing_first_name") === "maximo de caracteres permitido") {
		arrayOfErrors.push(errors.validationName['maximo de caracteres permitido']);
	}
	if (validationName("billing_first_name") === "no hay nada") {
		arrayOfErrors.push(errors.validationName['no hay nada']);
	}
	if (validacionCellphone("billing_phone") === "no es un numero valido") {
		arrayOfErrors.push(errors.validacionCellphone['no es un numero valido']);
	}
	if (validationName("billing_last_name") === "hay un Numero") {
		arrayOfErrors.push(errors.validationName['hay un Numero'].replace("nombre", "Apellido"));
	}
	if (validationName("billing_last_name") === "maximo de caracteres permitido") {
		arrayOfErrors.push(errors.validationName['maximo de caracteres permitido'].replace("nombre", "Apellido"));
	}
	if (validationName("billing_last_name") === "no hay nada") {
		arrayOfErrors.push(errors.validationName['no hay nada'].replace("nombre", "Apellido"));
	}

	// if (validationEmail('billing_email') === 'no hay nada') {
	//     arrayOfErrors.push('Error No hay nada en el Correo, por favor ingrese su correo')

	// }

	// if (validationEmail('billing_email') === 'no aceptado') {
	//     arrayOfErrors.push('Error No es aceptado el Correo, por favor ingrese su correo')

	// }

	if (arrayOfErrors.length === 0) {
		return true;
	} else {
		showError(arrayOfErrors);
		return false;
	}
}

export 
function validationOfSpecialInputsInForm(claseToValidate) {
	let arrayOfErrors = [];
	let allowedExtensions = /(\.jpg|\.jpeg|\.pdf|\.png|\.gif)$/i;
	let clase = "";
	let num = "";

	if (claseToValidate === "pago_movil") {
		clase = "comprobante_pago_movil";
		num = "2";
	} else {
		clase = "comprobante_transferencia";
		num = "1";
	}

	let fileInput = document.getElementById(clase);
	let filePath = fileInput.value;

	// if (validationDateTrans('fecha_' + claseToValidate) === 'fecha menor') {
	//     arrayOfErrors.push('Error la fecha pago es 20 dias menor a la de hoy, por favor corrijalo, el limite es 20 dias antes de hoy')
	// }

	// if (validationDateTrans('fecha_' + claseToValidate) === 'fecha mayor') {
	//     arrayOfErrors.push('Error la fecha pago es mayor a la de hoy, por favor corrijalo');
	// }

	// if (validationDate('fecha_' + claseToValidate) === 'no hay nada') {
	//     arrayOfErrors.push('¡Error! Seleccione una fecha de pago, por favor.');
	// }

	// if (validationNumberOfTransfer('numero_recibo_' + claseToValidate) === 'caracteres no validos') {
	//     arrayOfErrors.push('¡Error! Agrege solo numeros en el recibo, por favor.');
	// }

	if (validationNumberOfTransfer("numero_recibo_" + claseToValidate) === "no hay nada") {
		arrayOfErrors.push(errors.validationNumberOfTransfer['caracteres no validos']);
	}

	if (validationAvailebleBanks("bancos_" + claseToValidate) === "no estan en los bancos") {
		arrayOfErrors.push(errors.validationAvailebleBanks['no estan en los bancos']);
	}

	if (fileInput.value === "") {
		arrayOfErrors.push(errors.file['no hay nada']);
		showError(arrayOfErrors);
		return false;
	}
	

	if (!allowedExtensions.test(filePath)) {
		arrayOfErrors.push(errors.file[false]);
	}

	if (jQuery("#" + clase)[0].files[0].size > 3000000) {
		arrayOfErrors.push(errors.file['3000000']);
	}

	if (arrayOfErrors.length === 0) {
		return true;
	} else {
		showError(arrayOfErrors);
		return false;
	}
}

export function validationZelle() {
	let arrayOfErrors = [];

	if (validationEmail("email-origen") === "no hay nada") {
		arrayOfErrors.push(errors.validationEmail['no hay nada']);
	}
	if (document.getElementById("zelle_email").value === "") {
		arrayOfErrors.push(errors.zelle['correo zelle']);
	}
	if (validationEmail("email-origen") === "no aceptado") {
		arrayOfErrors.push(errors.validationEmail['no aceptado']);
	}
	if (
		validationReferenceNumberZelle("reference_number") === "no hay nada" ||
		validationReferenceNumberZelle("reference_number") === ""
	) {
		arrayOfErrors.push(errors.zelle['referencia vacio']);
	}
	if (validationReferenceNumberZelle("reference_number") === "no aceptado") {
		arrayOfErrors.push(errors.zelle['no acepetado']);
	}
	if (validationName("zelle_sender_name") === "no hay nada") {
		arrayOfErrors.push(errors.zelle['no hay nada']);
	}
	if (validationName("zelle_sender_name") === "maximo de caracteres permitido") {
		arrayOfErrors.push(errors.zelle['maximo de caracteres permitido']);
	}
	if (validationName("zelle_sender_name") === "hay un Numero") {
		arrayOfErrors.push(errors.zelle['hay un Numero']);
	}

	if (arrayOfErrors.length === 0) {
		return true;
	} else {
		showError(arrayOfErrors);
		return false;
	}
}

export function validatePagoMovil() {
	let arrayOfErrors = [];

	if (validacionCellphone("telefono_pago_movil") === "no es un numero valido") {
		arrayOfErrors.push(errors.validacionCellphone['no es un numero valido']);
	}
	if (arrayOfErrors.length === 0) {
		return true;
	} else {
		showError(arrayOfErrors);
		return false;
	}
}
