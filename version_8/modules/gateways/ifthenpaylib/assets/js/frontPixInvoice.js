
class PixInvoice {

	/** @type {HTMLInputElement} */
	nameInputDom;
	/** @type {HTMLInputElement} */
	cpfInputDom;
	/** @type {HTMLInputElement} */
	emailInputDom;
	/** @type {HTMLButtonElement} */
	pixPayButtonDom

	static init() {
		const pixInvoice = new PixInvoice();

		pixInvoice.addEventListenerForPixValidation();
	}



	addEventListenerForPixValidation() {
		
		this.nameInputDom = document.querySelector('.ifthenpay_pix_form input[name="ifthenpaypix_name"]') || null;
		this.cpfInputDom = document.querySelector('.ifthenpay_pix_form input[name="ifthenpaypix_cpf"]') || null;
		this.emailInputDom = document.querySelector('.ifthenpay_pix_form input[name="ifthenpaypix_email"]') || null;
		this.pixPayButtonDom = document.querySelector('.ifthenpay_pix_form button') || null;


		if (!this.nameInputDom || !this.cpfInputDom || !this.emailInputDom || !this.pixPayButtonDom) {
			return;
		}

		if (typeof msg_pix_invalid_name === 'undefined' || typeof msg_pix_invalid_cpf === 'undefined' || typeof msg_pix_invalid_email === 'undefined') {
			return;
		}

		const that = this;

		this.pixPayButtonDom.addEventListener('click', function (event) {

			const nameValue = that.nameInputDom.value;

			if (nameValue == '') {
				event.preventDefault();
				alert(msg_pix_invalid_name); // variable from tpl
				return;
			}

			const cpfValue = that.cpfInputDom.value;
			const cpfRegex = /^(\d{3}\.\d{3}\.\d{3}-\d{2}|\d{11})$/;


			if (cpfValue == '' || !cpfRegex.test(cpfValue)) {
				event.preventDefault();
				alert(msg_pix_invalid_cpf); // variable from tpl
				return;
			}

			const emailValue = that.emailInputDom.value;
			const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

			if (emailValue == '' || !emailRegex.test(emailValue)) {
				event.preventDefault();
				alert(msg_pix_invalid_email); // variable from tpl
				return;
			}

		});
	}

}

PixInvoice.init();
