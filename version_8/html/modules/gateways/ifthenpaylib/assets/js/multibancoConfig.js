import IftpLang from './iftpLang.js';
import Utils from './utils.js';


class MultibancoConfig {

	/** @type {string} */
	containerSelector;
	/** @type {HTMLDivElement} */
	multibancoContainerDom;
	/** @type {HTMLInputElement} */
	backofficeKeyInputDom;
	/** @type {HTMLSelectElement} */
	entitySelectDom;
	/** @type {HTMLSelectElement} */
	subEntitySelectDom;
	/** @type {HTMLInputElement} */
	deadlineInputDom
	entitySubEntityArray = [];



	/**
	 * @param {string} containerSelector 
	 */
	constructor(containerSelector = '#configifthenpaymultibanco') {
		this.containerSelector = containerSelector;
		this.init();
	}

	/**
	 * Initialize the form
	 * @returns {Promise<void>}
	 */
	async init() {

		this.multibancoContainerDom = document.querySelector(this.containerSelector) || null;
		if (!this.multibancoContainerDom) return;

		this.backofficeKeyInputDom = this.multibancoContainerDom.querySelector('input[name*="backofficeKey"]');
		if (!this.backofficeKeyInputDom) return;

		this.entitySelectDom = this.multibancoContainerDom.querySelector('select[name*="field[entity]"]') || null;
		if (!this.entitySelectDom) return;

		this.subEntitySelectDom = this.multibancoContainerDom.querySelector('select[name*="field[subentity]"]') || null;
		if (!this.subEntitySelectDom) return;

		this.deadlineInputDom = this.multibancoContainerDom.querySelector('select[name*="deadline"]') || null;
		if (!this.deadlineInputDom) return;




		this.updateDeadlineDom();


		this.backofficeKeyInputDom.addEventListener("input", async () => {
			const value = this.backofficeKeyInputDom.value;
			const validationMessageDom = this.backofficeKeyInputDom.parentElement?.querySelector('.ifthenpay_validation');
			const regex = /^\d{4}-\d{4}-\d{4}-\d{4}$/;


			this.updateSubentityDom([]);
			this.updateEntitiesDom([]);

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

			const entitiesData = await this.getEntitiesSubEntities();

			// if invalid backoffice key
			if (entitiesData === false) {
				if (validationMessageDom) {
					validationMessageDom.textContent = IftpLang.trans('msg_invalid_backoffice_key');
					return;
				}
			}

			if (entitiesData.length == 0) {
				this.showRequestAccountButton();
			}


			this.entitySubEntityArray = entitiesData;
			this.updateEntitiesDom(entitiesData);


			let subEntities = [];
			if (entitiesData && Object.keys(entitiesData).length > 0) {

				const firstKey = Object.keys(entitiesData)[0];
				subEntities = entitiesData[firstKey];
			}

			this.updateSubentityDom(subEntities);
			this.updateDeadlineDom();
		});


		this.multibancoContainerDom.addEventListener('change', async (event) => {
			if (event.target && event.target.matches('select[name*="field[entity]"]')) {
				let subentitiesData = [];

				// if has locally use it, this is in the use case of a user selecting before saving the backoffice key
				const entity = event.target.value;
				if (this.entitySubEntityArray?.[entity]) {
					subentitiesData = this.entitySubEntityArray[entity];
				} else {
					subentitiesData = await this.getSubEntities();
				}

				this.updateSubentityDom(subentitiesData);
				this.updateDeadlineDom();
			}
		});

		Utils.reloadConfigPageIfSaveSuccessfull(this.multibancoContainerDom);
	}



	/**
	 * get entities/subentities by requesting a controller in the module
	 * @returns Promise<array>
	 */
	async getEntitiesSubEntities() {
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
				body: new URLSearchParams({ 'backofficeKey': backofficeKey, 'paymentMethod': 'multibanco' }),
			});

			const result = await response.json();
			if (result.success) {
				return result.data;
			} else {
				console.error("Error: unable to fetch entities.");
			}
		} catch (error) {
			console.error("Error: unable to fetch entities.");
		} finally {
			Utils.removeSpinner(this.backofficeKeyInputDom);
		}

		return null;
	}



	/**
	 * get subentities belonging to currently selected Entity
	 * @returns Promise<array>
	 */
	async getSubEntities() {

		const entity = this.entitySelectDom?.value;
		if (!entity) {
			console.error("Entity is missing");
			return [];
		}

		Utils.addSpinner(this.subEntitySelectDom);

		try {
			const response = await fetch("../modules/gateways/ifthenpaylib/controllers/getmultibancosubentities.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded",
				},
				body: new URLSearchParams({ entity }),
			});

			const result = await response.json();
			if (result.success) {
				return result.data;
			} else {
				console.error("Error: unable to fetch entities.");
			}
		} catch (error) {
			console.error("Error: unable to fetch entities.");
		} finally {
			Utils.removeSpinner(this.subEntitySelectDom);
		}

		return [];
	}



	updateDeadlineDom() {
		const entityValue = this.entitySelectDom.value ?? '';

		if (entityValue === 'MB') {
			this.deadlineInputDom.disabled = false;
		}
		else {
			this.deadlineInputDom.disabled = true;
			this.deadlineInputDom.value = '';
		}
	}



	/**
	 * updates the entities dropdown with the Entities in data
	 * @param {array} data 
	 * @returns {void}
	 */
	updateEntitiesDom(data) {
		let html = '';
		for (const key in data) {
			if (key === 'MB') {
				html += `<option value="${key}">${IftpLang.trans('multibanco_dynamic_reference')}</option>`
			}
			else {
				html += `<option value="${key}">${key}</option>`
			}
		}

		this.entitySelectDom.innerHTML = html;
	}



	/**
	 * updates the subentity dropdown based on the data
	 * @param {array} data 
	 * @returns {void}
	 */
	updateSubentityDom(data) {
		let html = '';

		if (data.length === 0) {
			this.subEntitySelectDom.innerHTML = html;
			return;
		}

		data.forEach(subEntity => {
			html += `<option value="${subEntity}">${subEntity}</option>`
		});

		this.subEntitySelectDom.innerHTML = html;
	}



	async showRequestAccountButton() {
		if (window.confirm(IftpLang.trans('msg_no_multibanco_accounts_found') + '\n' + IftpLang.trans('msg_request_new_account'))) {

			try {
				const payload = {
					'paymentMethod': "Multibanco",
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




export function multibancoConfig() {
	return new MultibancoConfig();
}
