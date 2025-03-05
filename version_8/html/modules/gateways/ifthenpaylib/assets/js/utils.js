class Utils {


	/**
	 * add spinner element next to the target element
	 * @param {*} targetElement 
	 */
	static addSpinner(targetElement) {

		const spinner = document.createElement("span");
		spinner.classList.add('ifthenpay_spinner');


		targetElement.insertAdjacentElement("afterend", spinner);
	}



	/**
	 * remove spinner element next to the target element
	 * @param {*} targetElement 
	 * @return {void}
	 */
	static removeSpinner(targetElement) {
		const spinner = targetElement.nextElementSibling;
		if (spinner) {
			spinner.remove();
		}
	}

	

	/**
	 * 
	 * @param {HTMLDivElement} container 
	 */
	static reloadConfigPageIfSaveSuccessfull(container) {

		const buttonDom = container.querySelector("button[type='submit'].btn.btn-primary");

		if (!(buttonDom instanceof HTMLButtonElement)) {
			return;
		}

		buttonDom.addEventListener('click', function () {
			let count = 0;

			setTimeout(function () { // needs to be used this way in order to work with safari and chrome
				buttonDom.disabled = true;
			}, 0);

			let test = setInterval(() => {

				if (document.querySelector('#growls .growl-notice') instanceof HTMLDivElement) {
					clearInterval(test);
					location.reload();
				}
				else {
					count++;
					if (count >= 15) {

						clearInterval(test); // Stop the interval after specified iterations
						buttonDom.disabled = false;
					}
				}

			}, 400);
		});

	}
}


export default Utils;
