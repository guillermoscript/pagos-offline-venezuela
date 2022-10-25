import { stopIt, removeAllHtmlWithThisClass, showError } from "./utils.js";

import { validatePagoMovil, validationBinance, validationOfSpecialInputsInForm, validationReserve, validationZelle } from "./guardianFunctions.js";

/**
 * this function let the client upload the capture of the payment
 * adds a lodaing bar so the client can see that the image or pdf can be uploaded
 * and then after the image its uplodaded then do the normal checkout
 *
 * @param {string} nonce string nonce so that the ajax can be done.
 */
function sendImage(nonce) {
	let btnCheckOut = document.getElementById("place_order");
	let pagoMovilCheckBox = document.getElementById("payment_method_pago_movil") ? document.getElementById("payment_method_pago_movil") : null;
	let transferenciaCheckBox = document.getElementById(
		"payment_method_transferencia"
	) ? document.getElementById("payment_method_transferencia") : null;
	let reserveCheckBox = document.getElementById("payment_method_reserve") ? document.getElementById("payment_method_reserve") : null;
	let binanceCheckBox = document.getElementById("payment_method_binance") ? document.getElementById("payment_method_binance") : null;
	let clase = "";
	let num = "";

	if (pagoMovilCheckBox !== null && pagoMovilCheckBox.checked === true) {
		clase = "comprobante_pago_movil";
		num = "2";
	} else if (transferenciaCheckBox !== null && transferenciaCheckBox.checked === true) {
		clase = "comprobante_transferencia";
		num = "1";
	} else if (reserveCheckBox !== null && reserveCheckBox.checked === true) {
		clase = "comprobante_reserve";
		num = "3";
	} else if (binanceCheckBox !== null && binanceCheckBox.checked === true) {
		clase = "comprobante_binance";
		num = "4";
	}

	let fdata = new FormData();
	fdata.append("file", jQuery("#" + clase)[0].files[0]);
	fdata.append("action", nonce.action);
	fdata.append("nonce", nonce.nonce);

	document.body.insertAdjacentHTML(
		"afterbegin",
		`<div class="blockUI blockOverlay" style="z-index: 1000; border: medium none; margin: 0px; padding: 0px; width: 100%; height: 100%; top: 0px; left: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; opacity: 0.6; cursor: default; position: fixed;"></div>`
	);

	jQuery
		.ajax({
			method: "POST",
			xhr: function () {
				let xhr = new window.XMLHttpRequest();
				xhr.upload.addEventListener(
					"progress",
					function (evt) {
						if (evt.lengthComputable) {
							jQuery("#bararea" + num).removeClass("non2");
							let percentComplete = evt.loaded / evt.total;
							percentComplete = parseInt(percentComplete * 100);
							jQuery("#bar" + num).width(percentComplete + "%");
						}
					},
					false
				);
				return xhr;
			},
			url: nonce.url,
			processData: false,
			contentType: false,
			data: fdata,
		})
		.done(function (data) {
			let response = JSON.parse(data);

			if (response.error) {
				if (response.type) {
					console.log(response.type);
				}
				showError(response.error);
				return false;
			}
			btnCheckOut.removeEventListener("click", stopIt);
			// btnCheckOut.removeEventListener('click',sendImage )
			document.getElementById("capture-" + clase).value = response.id;
			btnCheckOut.click();
		})
		.fail(function (data) {
			// let err = JSON.parse(data);
			console.log(data);
		});
}

/**
 * Its a guardian class so it will call other function to validate.
 *
 * @param {string} nonce string pass to the sendImage function so ajax can be done.
 */
function validationContainer(nonce) {
	let btnCheckOut = document.getElementById("place_order");
	let pagoMovilCheckBox = document.getElementById("payment_method_pago_movil") ? document.getElementById("payment_method_pago_movil") : null;
	let reserveCheckBox = document.getElementById("payment_method_reserve") ? document.getElementById("payment_method_reserve") : null;
	let binanceCheckBox = document.getElementById("payment_method_binance") ? document.getElementById("payment_method_binance") : null;
	let transferenciaCheckBox = document.getElementById(
		"payment_method_transferencia"
	) ? document.getElementById("payment_method_transferencia") : null;
	let zelleCheckBox = document.getElementById("payment_method_zelle") ? document.getElementById("payment_method_zelle") : null;
	let claseToValidate = "";

	removeAllHtmlWithThisClass("woocommerce-NoticeGroup");

	if (reserveCheckBox !== null && reserveCheckBox.checked === true) {
		console.log('reserveCheckBox');
		if (validationReserve()) {
			sendImage(nonce);
			return true;
		} else {
			return false;
		}
	}

	if (binanceCheckBox !== null && binanceCheckBox.checked === true) {
		console.log('binanceCheckBox');
		if (validationBinance()) {
			sendImage(nonce);
			return true;
		} else {
			return false;
		}
	}

	if (pagoMovilCheckBox !== null && pagoMovilCheckBox.checked === true) {
		claseToValidate = "pago_movil";
	} else if (transferenciaCheckBox !== null && transferenciaCheckBox.checked === true) {
		claseToValidate = "transferencia";
	} else if (zelleCheckBox !== null && zelleCheckBox.checked === true) {
		if (validationZelle() === true) {
			btnCheckOut.removeEventListener("click", stopIt);
			btnCheckOut.click();
			return true;
		}
	}

	if (claseToValidate !== "" && validationOfSpecialInputsInForm(claseToValidate) === true) {
		if (claseToValidate === "pago_movil") {
			if (validatePagoMovil() === false) {
				return false;
			}
		}
		// btnCheckOut.addEventListener('click', enviarImagen );
		sendImage(nonce);
		return true;
	} else {
		return false;
	}
}

export {
	validationContainer,
};