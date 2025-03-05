import Utils from './utils.js';
import IftpLang from './iftpLang.js';


class IfthenpayConfig {

	/** @type {NodeListOf<HTMLButtonElement>} */
	resetButtons;


	constructor() {
		this.init();
	}

	/**
	 * Initialize the form
	 * @returns {Promise<void>}
	 */
	async init() {

		this.addEventListener_resetBtnClick();
	}



	addEventListener_resetBtnClick() {
		this.resetButtons = document.querySelectorAll('.ifthenpay_reset_btn') || null;
		if (!this.resetButtons) return;

		this.resetButtons.forEach(button => {
			button.addEventListener('click', async () => {

				if (!button.dataset.method) {
					return
				}

				const method = button.dataset.method;
				const userConfirmed = window.confirm(IftpLang.trans('msg_are_sure_reset_config'));
				if (userConfirmed) {
					Utils.addSpinner(button);
					await this.resetConfig(method);
					Utils.removeSpinner(button);
				}
			});
		});

	}



	async resetConfig(method) {
		try {
			const response = await fetch("../modules/gateways/ifthenpaylib/controllers/resetconfig.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded",
				},
				body: new URLSearchParams({ 'paymentMethod': method }),
			});

			const result = await response.json();
			if (result.success) {
				location.reload();
			} else {
				console.error("Error: unable to reset config.");
			}
		} catch (error) {
			console.error("Error: unable to reset config.");
		}

		return [];
	}


}



export function ifthenpayConfig() {
	return new IfthenpayConfig();
}
