import Utils from './utils.js';


class MbwayInvoice {

	/** @type {string} */
	timerDisplaySelector = '#ifthenpay_mbway_countdown';
	/** @type {string} */
	hiddenInvoiceIdInputSelector = '#ifthenpay_mbway_invoiceid';
	/** @type {string} */
	statusDivSelector = '#ifthenpay_mbway_status';
	/** @type {HTMLDivElement} */
	statusDivDom;
	/** @type {HTMLHeadingElement} */
	timerDisplayDom;
	/** @type {HTMLInputElement} */
	hiddenInvoiceIdInputDom
	/** @type {number} */
	countdownTimeInSeconds = 4 * 60;
	/** @type {number} */
	countdownInterval;
	/** @type {number} */
	checkStatusInterval;
	/** @type {boolean} */
	countdownExpired = false;


	static init() {
		const mbwayInvoice = new MbwayInvoice();

		mbwayInvoice.addEventListenerForMbwayValidation();
		mbwayInvoice.startTimer();

	}

	addEventListenerForMbwayValidation() {

		/** @type {HTMLInputElement} */
		const phoneNumberInputDom = document.querySelector('.ifthenpay_mbway_form input[name="mobile_number"]') || null;
		
		/** @type {HTMLSelectElement} */
		const phoneCodeSelectDom = document.querySelector('.ifthenpay_mbway_form select[name="mobile_code"]') || null;

		/** @type {HTMLButtonElement} */
		const phoneNumberButtonDom = document.querySelector('.ifthenpay_mbway_form button') || null;

		if (!phoneNumberInputDom || !phoneNumberButtonDom) {
			return;
		}

		phoneNumberButtonDom.addEventListener('click', function (event) {

			const phoneCode = phoneCodeSelectDom.value;
			const phoneNumber = phoneNumberInputDom.value;
			const phoneRegex = /^(\d{1,3}#)?\d{8,10}$/;
			const completePhoneNumber = phoneCode + '#' + phoneNumber;

			if (phoneNumber == '' || !phoneRegex.test(completePhoneNumber)) {
				event.preventDefault();
				alert(msg_mbway_invalid_number); // variable from tpl
			}
		})
	}


	async startTimer() {

		this.timerDisplayDom = document.querySelector(this.timerDisplaySelector) || null;
		if (!this.timerDisplayDom) return;

		this.hiddenInvoiceIdInputDom = document.querySelector(this.hiddenInvoiceIdInputSelector) || null;
		if (!this.hiddenInvoiceIdInputDom || this.hiddenInvoiceIdInputDom.value == '') return;

		this.statusDivDom = document.querySelector(this.statusDivSelector) || null;
		if (!this.statusDivDom) return;


		setTimeout(() => {
			this.checkStatus();
		}, 1);

		this.countdown();
	}

	countdown() {

		this.countdownInterval = setInterval(() => {

			this.countdownTimeInSeconds--;

			const minutes = Math.floor(this.countdownTimeInSeconds / 60);
			const seconds = this.countdownTimeInSeconds % 60;

			// Display the formatted time
			this.timerDisplayDom.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

			// If time reaches 0, stop the timer and status check
			if (this.countdownTimeInSeconds <= 0) {
				clearInterval(this.countdownInterval);
				clearInterval(this.checkStatusInterval);
				this.checkAndUpdateStatus(true);
				// this.countdownExpired = true;
			}

		}, 1000);
	}



	checkStatus() {
		this.checkStatusInterval = setInterval(() => {
			this.checkAndUpdateStatus();
		}, 10000);

	}



	async checkAndUpdateStatus(countdownExpired = false) {
		this.getMbwayStatus(countdownExpired).then(response => {
			if (response.success && response.html) {

				Utils.addSpinner(this.timerDisplayDom);

				setTimeout(() => {
					this.statusDivDom.innerHTML = response.html;

					clearInterval(this.countdownInterval);
					clearInterval(this.checkStatusInterval);
					Utils.removeSpinner(this.timerDisplayDom);
				}, 500);
			}
		});
	}



	async getMbwayStatus(countdownExpired) {

		try {
			const data = {
				'invoiceId': this.hiddenInvoiceIdInputDom.value,
				'countdownExpired': countdownExpired
			}

			const response = await fetch("../modules/gateways/ifthenpaylib/controllers/getmbwaypaymentstatus.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded",
				},
				body: new URLSearchParams(data),
			});

			const result = await response.json();

			if (result.success) {
				return result;
			}
			return {};
		} catch (error) {
			return {};
		}
	}

}

MbwayInvoice.init();
