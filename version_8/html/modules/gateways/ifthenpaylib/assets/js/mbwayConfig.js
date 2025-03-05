import IftpLang from './iftpLang.js';
import Utils from './utils.js';


class MbwayConfig {

	/** @type {string} */
	containerSelector;
	/** @type {HTMLDivElement} */
	mbwayContainerDom;
	/** @type {HTMLInputElement} */
	backofficeKeyInputDom;
	/** @type {HTMLSelectElement} */
	keySelectDom;
	/** @type {HTMLInputElement} */
	deadlineInputDom
	keyArray = [];



	/**
	 * @param {string} containerSelector 
	 */
	constructor(containerSelector = '#configifthenpaymbway') {
		this.containerSelector = containerSelector;
		this.init();
	}


	/**
	 * Initialize the form
	 * @returns {Promise<void>}
	 */
	async init() {		

		this.mbwayContainerDom = document.querySelector(this.containerSelector) || null;
		if (!this.mbwayContainerDom) return;

		this.backofficeKeyInputDom = this.mbwayContainerDom.querySelector('input[name*="backofficeKey"]');
		if (!this.backofficeKeyInputDom) return;

		this.keySelectDom = this.mbwayContainerDom.querySelector('select[name*="key"]') || null;
		if (!this.keySelectDom) return;


		this.backofficeKeyInputDom.addEventListener("input", async () => {
			const value = this.backofficeKeyInputDom.value;
			const validationMessageDom = this.backofficeKeyInputDom.parentElement?.querySelector('.ifthenpay_validation');
			const regex = /^\d{4}-\d{4}-\d{4}-\d{4}$/;


			this.updateKeysDom([]);

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

			if (keysData.length == 0) {
				this.showRequestAccountButton();
			}

			this.keyArray = keysData;
			this.updateKeysDom(keysData);
		});
		Utils.reloadConfigPageIfSaveSuccessfull(this.mbwayContainerDom);
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
				body: new URLSearchParams({ 'backofficeKey': backofficeKey, 'paymentMethod': 'mbway'}),
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
		for (const key in data) {
			html += `<option value="${key}">${key}</option>`
		}

		this.keySelectDom.innerHTML = html;
	}


	
	async showRequestAccountButton() {
		if (window.confirm(IftpLang.trans('msg_no_mbway_accounts_found') + '\n' + IftpLang.trans('msg_request_new_account'))) {

			try {
				const payload = {
					'paymentMethod': "Mbway",
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

}



export function mbwayConfig() {
	return new MbwayConfig();
}
