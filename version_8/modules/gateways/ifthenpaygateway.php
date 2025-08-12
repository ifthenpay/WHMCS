<?php

use WHMCS\Module\GatewaySetting;
use WHMCS\Exception\Module\InvalidConfiguration;

use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Module\Gateway\ifthenpaylib\Lang\IftpLang;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpayService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpaygatewayService;

if (!defined("WHMCS")) {
	die("This file cannot be accessed directly");
}






/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related capabilities and
 * settings.
 *
 * @see https://developers.whmcs.com/payment-gateways/meta-data-params/
 *
 * @return array
 */
function ifthenpaygateway_MetaData()
{
	return array(
		'DisplayName' => Config::IFTHENPAYGATEWAY_NAME,
		'APIVersion' => '1.1', // Use API Version 1.1
	);
}


function ifthenpaygateway_config_validate($params)
{
	if (isset($params[Config::CF_BACKOFFICE_KEY]) && !preg_match('/^\d{4}-\d{4}-\d{4}-\d{4}$/', $params[Config::CF_BACKOFFICE_KEY])) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_backoffice_key'));
	}

	if (isset($params[Config::CF_IFTHENPAYGATEWAY_KEY]) && !(preg_match('/^[A-Z]{4}-\d{6}$/', $params[Config::CF_MBWAY_KEY]))) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_key'));
	}

	$paymentMethods = $_POST[Config::CF_IFTHENPAYGATEWAY_PAYMENT_METHODS] ?? [];
	$hasSelectedMethod = false;
	foreach ($paymentMethods as $key => $value) {

		if (isset($value['is_active']) && $value['is_active'] === '1') {
			$hasSelectedMethod = true;
			break;
		}
	}
	if (!$hasSelectedMethod) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_gateway_methods'));
	}

	if ((isset($params[Config::CF_DEADLINE])) && $params[Config::CF_DEADLINE] != '' && !preg_match('/^[1-9]\d{0,3}$/', $params[Config::CF_DEADLINE])) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_deadline'));
	}

	if (isset($params[Config::CF_MIN_AMOUNT]) && $params[Config::CF_MIN_AMOUNT] != '' && !preg_match('/^\d+(\.\d{1,2})?$/', $params[Config::CF_MIN_AMOUNT])) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_minimum_amount'));
	}

	if (isset($params[Config::CF_MAX_AMOUNT]) && $params[Config::CF_MAX_AMOUNT] != '' && !preg_match('/^\d+(\.\d{1,2})?$/', $params[Config::CF_MAX_AMOUNT])) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_maximum_amount'));
	}

	if (
		isset($params[Config::CF_MAX_AMOUNT]) && $params[Config::CF_MAX_AMOUNT] != '' &&
		isset($params[Config::CF_MIN_AMOUNT]) && $params[Config::CF_MIN_AMOUNT] != '' &&
		$params[Config::CF_MIN_AMOUNT] >= $params[Config::CF_MAX_AMOUNT]
	) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_min_max_amount'));
	}

	// description
	if (isset($params[Config::CF_IFTHENPAYGATEWAY_DESCRIPTION]) && $params[Config::CF_IFTHENPAYGATEWAY_DESCRIPTION] != '' && strlen($params[Config::CF_IFTHENPAYGATEWAY_DESCRIPTION]) > 200) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_ifthenpaygateway_description'));
	}


	// btn close
	if (isset($params[Config::CF_IFTHENPAYGATEWAY_CLOSE_BTN_LABEL]) && $params[Config::CF_IFTHENPAYGATEWAY_CLOSE_BTN_LABEL] != '' && strlen($params[Config::CF_IFTHENPAYGATEWAY_CLOSE_BTN_LABEL]) > 50) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_ifthenpaygateway_close_btn'));
	}

	$settings = GatewaySetting::getForGateway(Config::IFTHENPAYGATEWAY_MODULE_CODE);

	// save accounts
	$accounts = IfthenpaygatewayService::getKeysByBackofficKey($params[Config::CF_BACKOFFICE_KEY]);
	if ($accounts !== false) {
		GatewaySetting::setValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_ACCOUNTS, json_encode($accounts));
	}

	// save gateway methods
	if (isset($_POST[Config::CF_IFTHENPAYGATEWAY_PAYMENT_METHODS])) {
		GatewaySetting::setValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_IFTHENPAYGATEWAY_PAYMENT_METHODS, json_encode($_POST[Config::CF_IFTHENPAYGATEWAY_PAYMENT_METHODS]));
	}

	if (isset($params[Config::CF_SHOWICON]) && isset($_POST[Config::CF_IFTHENPAYGATEWAY_PAYMENT_METHODS])) {
		GatewaySetting::setValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_IFTHENPAYGATEWAY_FRONT_ICON, IfthenpaygatewayService::generateIconImageString($params[Config::CF_SHOWICON], $_POST[Config::CF_IFTHENPAYGATEWAY_PAYMENT_METHODS]));
	}

	// save callback in bulk
	if (
		$params[Config::CF_CAN_ACTIVATE_CALLBACK] === 'on'
	) {
		if (
			isset($params[Config::CF_IFTHENPAYGATEWAY_KEY]) && isset($settings[Config::CF_IFTHENPAYGATEWAY_KEY]) && $params[Config::CF_IFTHENPAYGATEWAY_KEY] != $settings[Config::CF_IFTHENPAYGATEWAY_KEY] ||
			isset($params[Config::CF_CAN_ACTIVATE_CALLBACK]) && $params[Config::CF_CAN_ACTIVATE_CALLBACK] == 'on' && (!isset($settings[Config::CF_CAN_ACTIVATE_CALLBACK]) || $settings[Config::CF_CAN_ACTIVATE_CALLBACK] != 'on')
		) {
			$forceActivation = true;
		} else {
			$forceActivation = false;
		}


		$forceActivation = (isset($params[Config::CF_CAN_ACTIVATE_CALLBACK]) && $params[Config::CF_CAN_ACTIVATE_CALLBACK] == 'on' && (!isset($settings[Config::CF_CAN_ACTIVATE_CALLBACK]) || $settings[Config::CF_CAN_ACTIVATE_CALLBACK] != 'on')) ? true : false;

		IfthenpaygatewayService::bulkActivateCallback($params[Config::CF_BACKOFFICE_KEY], $params[Config::CF_IFTHENPAYGATEWAY_PAYMENT_METHODS] ?? '', $_POST[Config::CF_IFTHENPAYGATEWAY_PAYMENT_METHODS], $forceActivation);
	} else if (isset($params[Config::CF_CAN_ACTIVATE_CALLBACK]) && $params[Config::CF_CAN_ACTIVATE_CALLBACK] !== 'on' && isset($params[Config::CF_CALLBACK_STATUS]) && $params[Config::CF_CALLBACK_STATUS] === 'on') {
		IfthenpaygatewayService::clearCallback();
	}

	try {
		IfthenpaygatewayService::handleDbCreateUpdate();
	} catch (\Throwable $th) {
		throw new InvalidConfiguration(IftpLang::trans('msg_error_updating_ifthenpaygateway_database'));
	}

	GatewaySetting::setValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_INSTALLED_MODULE_VERSION, Config::MODULE_VERSION);
}



function ifthenpaygateway_config($params)
{
	try {

		$resetBtn = isset($params[Config::CF_BACKOFFICE_KEY]) ? '<button type="button" class="btn btn-danger ifthenpay_reset_btn" data-method="' . Config::IFTHENPAYGATEWAY . '">' . IftpLang::trans('reset') . '</button>' : '';

		$backofficeKeyReadOnly = $resetBtn != '' ? true : false;

		$configForm = [
			'FriendlyName' => [
				'Type' => 'System',
				'Value' => Config::IFTHENPAYGATEWAY_NAME
			]
		];

		$configForm[Config::CF_UPGRADE] = [
			'FriendlyName' => IftpLang::trans('version'),
			'Description' => IfthenpayService::generateModuleVersionBlock(),
		];

		$configForm[Config::CF_BACKOFFICE_KEY] = [
			'FriendlyName' => IftpLang::trans('backoffice_key'),
			'Type' => 'password',
			'Description' => $resetBtn . '<span class="ifthenpay_validation"></span>',
			'ReadOnly' => $backofficeKeyReadOnly
		];

		$configForm[Config::CF_IFTHENPAYGATEWAY_KEY] = [
			'FriendlyName' => IftpLang::trans('ifthenpaygateway_key'),
			'Description' => IfthenpaygatewayService::getKeySelectHtml($params),
		];


		// NOTE: does not use the "gatewayPaymentMethods" config but instead uses "paymentMethods",
		// the reason is because the values to be saved are in a nested array format in the validation function ifthenpaygateway_config_validate
		$configForm[Config::CF_IFTHENPAYGATEWAY_GATEWAY_PAYMENT_METHODS] = [
			'FriendlyName' => IftpLang::trans('ifthenpaygateway_payment_methods'),
			'Description' => '<div id="ifthenpay_gateway_payment_methods">' . IfthenpaygatewayService::getPaymentMethodsSelectHtml($params) . '</div>',
		];

		$configForm[Config::CF_IFTHENPAYGATEWAY_DEFAULT_PAYMENT] = [
			'FriendlyName' => IftpLang::trans('ifthenpaygateway_default_payment_method'),
			'Description' => '<div id="ifthenpay_gateway_default_payment_method">' . IfthenpaygatewayService::getDefaultPaymentMethodSelectHtml($params) . '</div>',
		];


		$configForm[Config::CF_DEADLINE] = [
			'FriendlyName' => IftpLang::trans('deadline'),
			'Type' => 'text',
			'Description' => '<span>' . IftpLang::trans('ifthenpaygateway_deadline_desc') . '</span><span class="ifthenpay_validation"></span>'
		];

		$configForm[Config::CF_IFTHENPAYGATEWAY_CLOSE_BTN_LABEL] = [
			'FriendlyName' => IftpLang::trans('close_btn_label'),
			'Type' => 'text',
			'Description' => IftpLang::trans('close_btn_label_desc'),
		];

		$configForm[Config::CF_IFTHENPAYGATEWAY_DESCRIPTION] = [
			'FriendlyName' => IftpLang::trans('ifthenpaygateway_description'),
			'Type' => 'text',
			'Description' => IftpLang::trans('ifthenpaygateway_description_desc'),
		];

		$configForm[Config::CF_MIN_AMOUNT] = [
			'FriendlyName' => IftpLang::trans('min_amount'),
			'Type' => 'text',
			'Description' => IftpLang::trans('min_amount_desc'),
		];

		$configForm[Config::CF_MAX_AMOUNT] = [
			'FriendlyName' => IftpLang::trans('max_amount'),
			'Type' => 'text',
			'Description' => IftpLang::trans('max_amount_desc'),
		];

		$configForm[Config::CF_SHOWICON] = [
			'FriendlyName' => IftpLang::trans('show_payment_icon'),
			'Type' => 'dropdown',
			'Options' => [
				'off' => IftpLang::trans('show_icon_off_method_name'),
				'on' => IftpLang::trans('show_icon_on_default'),
				'composite' => IftpLang::trans('show_icon_on_composite_image'),
			]
		];

		$configForm[Config::CF_CAN_CANCEL] = [
			'FriendlyName' => IftpLang::trans('cancel_ifthenpaygateway'),
			'Type' => 'yesno',
			'Description' => IftpLang::trans('cancel_ifthenpaygateway_desc'),
		];

		$configForm[Config::CF_CAN_ACTIVATE_CALLBACK] = [
			'FriendlyName' => IftpLang::trans('callback'),
			'Type' => 'yesno',
			'Description' => IftpLang::trans('callback_desc'),
		];


		$callbackStatusColor = $params[Config::CF_CALLBACK_STATUS] === 'on' ? 'status-badge-green' : 'status-badge-orange';
		$callbackStatusText = $params[Config::CF_CALLBACK_STATUS] === 'on' ? IftpLang::trans('callback_active') : IftpLang::trans('callback_inactive');
		$antiphishingKey = $params[Config::CF_ANTIPHISHING_KEY] ? $params[Config::CF_ANTIPHISHING_KEY] : '----';
		$callbackUrl = $params[Config::CF_CALLBACK_URL] ? $params[Config::CF_CALLBACK_URL] : '----';

		$configForm[Config::CF_CALLBACK_INFO] = [
			'FriendlyName' => '',
			'Description' => '
				<table class="info-table ifthenpay_callback_info">
					<tr>
						<td colspan="2"><span class="badge ' . $callbackStatusColor . '">' . $callbackStatusText . '</span></td>
					</tr>
					<tr>
						<th><span>' . IftpLang::trans('anti_phishing_key') . ' </span></th>
						<td><span class="badge">' . $antiphishingKey . '</span></td>
					</tr>
					<tr>
						<th><span>' . IftpLang::trans('callback_url') . ' </span></th>
						<td><span class="badge" style="white-space: normal;">' . $callbackUrl . '</span></td>
					</tr>
				</table>',
		];



		return $configForm;
	} catch (\Throwable $th) {

		IfthenpayLog::error(Config::IFTHENPAYGATEWAY, 'Error loading admin config form', $th->__toString());
		throw new InvalidConfiguration('Error: unable to load configuration form.');
	}
}

/**
 * Payment link.
 *
 * Required by third party payment gateway modules only.
 *
 * Defines the HTML output displayed on an invoice. Typically consists of an
 * HTML form that will take the user to the payment gateway endpoint.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/third-party-gateway/
 *
 * @return string
 */
function ifthenpaygateway_link($params)
{
	try {
		if ($_POST[Config::IFTHENPAYGATEWAY_MODULE_CODE]) { // create new payment

			$paymentDetails = IfthenpaygatewayService::generatePayment($params);
			IfthenpaygatewayService::savePayment($paymentDetails);
			header('Location: ' . $paymentDetails['payment_url']);
			return '';
		}

		if ($_GET['id'] && $_GET['ifthenpaysuccess']) { // show payment details
			return IfthenpaygatewayService::getPaymentDetailsHtml();
		}

		if ($_GET['id']) { // show the ifthenpaygateway payment form
			IfthenpaygatewayService::savePlaceholderPaymentIfNotFound($params);

			return IfthenpaygatewayService::getPaymentFormHtml($params['invoiceid']);
		}
	} catch (\Throwable $th) {

		IfthenpayLog::error(Config::IFTHENPAYGATEWAY, 'Error generating Ifthenpaygateway payment', $th->__toString());
		header("Location: viewinvoice.php?id={$params['invoiceid']}&paymentfailed=true");
		return '';
	}
}
