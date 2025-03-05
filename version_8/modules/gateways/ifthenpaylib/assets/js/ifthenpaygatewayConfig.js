import IftpLang from './iftpLang.js';
import Utils from './utils.js';



class IfthenpaygatewayConfig {

	/** @type {string} */
	containerSelector;
	/** @type {HTMLDivElement} */
	ifthenpaygatewayContainerDom;
	/** @type {HTMLInputElement} */
	backofficeKeyInputDom;
	/** @type {HTMLSelectElement} */
	keySelectDom;
	/** @type {HTMLInputElement} */
	deadlineInputDom
	/** @type {HTMLDivElement} */
	gatewayPaymentMethodsDom
	/** @type {HTMLDivElement} */
	gatewayDefaultPaymentMethodDom
	keyArray = [];



	/**
	 * @param {string} containerSelector 
	 */
	constructor(containerSelector = '#configifthenpaygateway') {
		this.containerSelector = containerSelector;
		this.init();
	}


	/**
	 * Initialize the form
	 * @returns {Promise<void>}
	 */
	async init() {

		this.ifthenpaygatewayContainerDom = document.querySelector(this.containerSelector) || null;
		if (!this.ifthenpaygatewayContainerDom) return;

		this.backofficeKeyInputDom = this.ifthenpaygatewayContainerDom.querySelector('input[name*="backofficeKey"]');
		if (!this.backofficeKeyInputDom) return;

		this.keySelectDom = this.ifthenpaygatewayContainerDom.querySelector('select[name*="key"]') || null;
		if (!this.keySelectDom) return;

		this.deadlineInputDom = this.ifthenpaygatewayContainerDom.querySelector('input[name*="deadline"]') || null;
		if (!this.deadlineInputDom) return;

		this.gatewayPaymentMethodsDom = this.ifthenpaygatewayContainerDom.querySelector('#ifthenpay_gateway_payment_methods') || null;
		if (!this.gatewayPaymentMethodsDom) return;

		this.gatewayDefaultPaymentMethodDom = this.ifthenpaygatewayContainerDom.querySelector('#ifthenpay_gateway_default_payment_method') || null;
		if (!this.gatewayDefaultPaymentMethodDom) return;

		this.backofficeKeyInputDom.addEventListener("input", async () => {
			const value = this.backofficeKeyInputDom.value;
			const validationMessageDom = this.backofficeKeyInputDom.parentElement?.querySelector('.ifthenpay_validation');
			const regex = /^\d{4}-\d{4}-\d{4}-\d{4}$/;

			this.gatewayPaymentMethodsDom.innerHTML = '';
			this.keySelectDom.innerHTML = '';
			this.gatewayDefaultPaymentMethodDom.innerHTML = '';

			if (value.length < 19) {
				if (validationMessageDom) validationMessageDom.textContent = '';  // Clear message if valid
				return;
			}

			if (regex.test(value)) {
				if (validationMessageDom) validationMessageDom.textContent = '';  // Clear message if valid
			} else {
				if (validationMessageDom) {
					validationMessageDom.textContent = IftpLang.trans('msg_invalid_backoffice_key_example');

					return;
				}
				return;
			}

			const keysData = await this.getKeys();

			// if invalid backoffice key
			if (keysData === false) {
				if (validationMessageDom) {
					validationMessageDom.textContent = IftpLang.trans('msg_invalid_backoffice_key');
					return;
				}
			}

			if (keysData.length == 0 || !Object.hasOwn(keysData[0], 'gatewayKey')) {
				this.showRequestAccountButton();
			}

			this.keyArray = keysData;
			this.updateKeysDom(keysData);
			this.updatePaymentMethodsDom();
		});

		this.keySelectDom.addEventListener('change', async () => {
			this.updatePaymentMethodsDom();
		});

		this.addEventListenerDelegatedOnIsActiveCheckbox();
		this.addEventListenerDelegatedOnIsClickedRequestGatewayMethod();

		Utils.reloadConfigPageIfSaveSuccessfull(this.ifthenpaygatewayContainerDom);
	}



	addEventListenerDelegatedOnIsActiveCheckbox() {
		const that = this;

		this.gatewayPaymentMethodsDom.addEventListener('change', function (event) {

			if (event.target instanceof HTMLInputElement && event.target.classList.contains('method_checkbox_input')) {

				const methodName = event.target.dataset.method;
				const isChecked = event.target.checked;
				const optionToUpdate = that.gatewayDefaultPaymentMethodDom.querySelector(`[data-method="${methodName}"]`);

				if (optionToUpdate instanceof HTMLOptionElement) {
					optionToUpdate.disabled = !isChecked;
					const isSelected = optionToUpdate.selected;


					if (isSelected) {
						optionToUpdate.selected = !isChecked;

						const optionNone = that.gatewayDefaultPaymentMethodDom.querySelector(`option[value="0"]`);

						if (optionNone instanceof HTMLOptionElement) {
							optionNone.selected = true;
						}
					}

				}

			}
		})
	}



	addEventListenerDelegatedOnIsClickedRequestGatewayMethod() {
		const that = this;

		this.gatewayPaymentMethodsDom.addEventListener('click', function (event) {

			if (event.target instanceof HTMLButtonElement && event.target.classList.contains('ifthenpay_new_method_btn')) {

				const methodName = event.target.dataset.method || '';

				that.showRequestGatewayMethodDialog(methodName);
			}
		})
	}



	async updatePaymentMethodsDom() {
		Utils.addSpinner(this.keySelectDom);
		try {

			const key = this.keySelectDom.value;
			const backofficeKey = this.backofficeKeyInputDom?.value;

			const response = await fetch("../modules/gateways/ifthenpaylib/controllers/getifthenpaygatewaymethods.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded",
				},
				body: new URLSearchParams({ key, backofficeKey }),
			});

			const result = await response.json();
			if (result.success) {

				this.gatewayPaymentMethodsDom.innerHTML = result.data.paymentMethodsSelectHtml;
				this.gatewayDefaultPaymentMethodDom.innerHTML = result.data.defaultPaymentMethodSelectHtml;

			} else {
				console.error("Error: unable to fetch gateway payment methods.");
			}
		} catch (error) {
			console.error("Error: unable to fetch gateway payment methods.");
		} finally {
			Utils.removeSpinner(this.keySelectDom);
		}

		return [];
	}



	/**
	 * get keys by requesting a controller in the module
	 * @returns Promise<array>
	 */
	async getKeys() {
		const backofficeKey = this.backofficeKeyInputDom?.value;

		if (!backofficeKey) {
			console.error("Backoffice key is missing");
			return [];
		}

		Utils.addSpinner(this.backofficeKeyInputDom);

		try {
			const response = await fetch("../modules/gateways/ifthenpaylib/controllers/getkeys.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded",
				},
				body: new URLSearchParams({ 'backofficeKey': backofficeKey, 'paymentMethod': 'ifthenpaygateway' }),
			});

			const result = await response.json();
			if (result.success) {
				return result.data;
			} else {
				console.error("Error: unable to fetch keys.");
			}
		} catch (error) {
			console.error("Error: unable to fetch keys.");
		} finally {
			Utils.removeSpinner(this.backofficeKeyInputDom);
		}

		return [];
	}



	/**
	 * updates the keys dropdown with the Keys in data
	 * @param {array} data 
	 * @returns {void}
	 */
	updateKeysDom(data) {
		let html = '';
		if (Object.hasOwn(data[0], 'gatewayKey')) {
			for (const item of data) {
				html += `<option value="${item.gatewayKey}" data="${item.type}">${item.alias}</option>`;
			}
		}

		this.keySelectDom.innerHTML = html;
	}



	async showRequestAccountButton() {
		if (window.confirm(IftpLang.trans('msg_no_ifthenpaygateway_accounts_found') + '\n' + IftpLang.trans('msg_request_new_account'))) {

			try {
				const payload = {
					'paymentMethod': "Ifthenpaygateway",
					'backofficeKey': this.backofficeKeyInputDom.value
				};

				const response = await fetch("../modules/gateways/ifthenpaylib/controllers/requestnewaccount.php", {
					method: "POST",
					headers: {
						"Content-Type": "application/x-www-form-urlencoded",
					},
					body: new URLSearchParams(payload),
				});

				const result = await response.json();
				if (result.success) {
					alert(result.message);
				} else {
					alert(result.message ?? 'Error: unable to send account request.');
				}
			} catch (error) {
				console.error('Error: unable to send account request.');
			}


		}
	}


	/**
	 * @param {string} paymentMethod - The payment method identifier.
	 * @returns {Promise<void>}
	 */
	async showRequestGatewayMethodDialog(paymentMethod) {

		const dialogMessage = IftpLang.trans('msg_request_new_gateway_method').replace('{%method%}', paymentMethod);

		if (window.confirm(dialogMessage)) {

			try {
				const payload = {
					'gatewayKey': this.keySelectDom.value,
					'paymentMethod': paymentMethod,
					'backofficeKey': this.backofficeKeyInputDom.value
				};

				const response = await fetch("../modules/gateways/ifthenpaylib/controllers/requestnewgatewaymethod.php", {
					method: "POST",
					headers: {
						"Content-Type": "application/x-www-form-urlencoded",
					},
					body: new URLSearchParams(payload),
				});

				const result = await response.json();
				if (result.success) {
					alert(result.message);
				} else {
					alert(result.message ?? 'Error: unable to send account request.');
				}
			} catch (error) {
				console.error('Error: unable to send account request.');
			}


		}
	}

}



export function ifthenpaygatewayConfig() {
	return new IfthenpaygatewayConfig();
}
