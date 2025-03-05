<?php

use WHMCS\Module\GatewaySetting;
use WHMCS\Exception\Module\InvalidConfiguration;

use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Module\Gateway\ifthenpaylib\Lang\IftpLang;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpayService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\PayshopService;

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
function ifthenpaypayshop_MetaData()
{
	return array(
		'DisplayName' => Config::PAYSHOP_NAME,
		'APIVersion' => '1.1', // Use API Version 1.1
	);
}


function ifthenpaypayshop_config_validate($params)
{
	if (isset($params[Config::CF_BACKOFFICE_KEY]) && !preg_match('/^\d{4}-\d{4}-\d{4}-\d{4}$/', $params[Config::CF_BACKOFFICE_KEY])) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_backoffice_key'));
	}

	if (isset($params[Config::CF_PAYSHOP_KEY]) && !(preg_match('/^[A-Z]{3}-\d{6}$/', $params[Config::CF_PAYSHOP_KEY]))) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_key'));
	}

	if ((isset($params[Config::CF_DEADLINE])) && $params[Config::CF_DEADLINE] !== '' && !preg_match('/^[1-9]\d{0,3}$/', $params[Config::CF_DEADLINE])) {
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


	$settings = GatewaySetting::getForGateway(Config::PAYSHOP_MODULE_CODE);

	// save accounts
	$accounts = PayshopService::getKeysByBackofficKey($params[Config::CF_BACKOFFICE_KEY]);
	if ($accounts !== false) {
		GatewaySetting::setValue(Config::PAYSHOP_MODULE_CODE, Config::CF_ACCOUNTS, json_encode($accounts));
	}


	// save callback
	if (
		$params[Config::CF_CAN_ACTIVATE_CALLBACK] === 'on' &&
		(
			!isset($settings[Config::CF_BACKOFFICE_KEY]) ||
			(isset($params[Config::CF_PAYSHOP_KEY]) && isset($settings[Config::CF_PAYSHOP_KEY]) && $params[Config::CF_PAYSHOP_KEY] != $settings[Config::CF_PAYSHOP_KEY]) ||
			(!isset($params[Config::CF_CALLBACK_STATUS]) || $params[Config::CF_CALLBACK_STATUS] !== 'on')
		)

	) {
		PayshopService::activateCallback($params[Config::CF_BACKOFFICE_KEY], $params[Config::CF_PAYSHOP_KEY]);
	} else if (isset($params[Config::CF_CAN_ACTIVATE_CALLBACK]) && $params[Config::CF_CAN_ACTIVATE_CALLBACK] !== 'on' && isset($params[Config::CF_CALLBACK_STATUS]) && $params[Config::CF_CALLBACK_STATUS] === 'on') {
		PayshopService::clearCallback();
	}

	try {
		PayshopService::handleDbCreateUpdate();
	} catch (\Throwable $th) {
		throw new InvalidConfiguration(IftpLang::trans('msg_error_updating_payshop_database'));
	}

	GatewaySetting::setValue(Config::PAYSHOP_MODULE_CODE, Config::CF_INSTALLED_MODULE_VERSION, Config::MODULE_VERSION);
}



function ifthenpaypayshop_config($params)
{

	try {

		$resetBtn = isset($params[Config::CF_BACKOFFICE_KEY]) ? '<button type="button" class="btn btn-danger ifthenpay_reset_btn" data-method="' . Config::PAYSHOP . '">' . IftpLang::trans('reset') . '</button>' : '';

		$backofficeKeyReadOnly = $resetBtn != '' ? true : false;


		$configForm = [
			'FriendlyName' => [
				'Type' => 'System',
				'Value' => Config::PAYSHOP_NAME
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


		$configForm[Config::CF_PAYSHOP_KEY] = [
			'FriendlyName' => IftpLang::trans('payshop_key'),
			'Type' => 'dropdown',
			'Options' => PayshopService::getKeyOptions($params)
		];

		$configForm[Config::CF_DEADLINE] = [
			'FriendlyName' => IftpLang::trans('deadline'),
			'Type' => 'text',
			'Description' => '<span>' . IftpLang::trans('payshop_deadline_desc') . '</span><span class="ifthenpay_validation"></span>'
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
			'Type' => 'yesno',
			'Description' => IftpLang::trans('show_payment_icon_desc'),
		];

		$configForm[Config::CF_CAN_CANCEL] = [
			'FriendlyName' => IftpLang::trans('cancel_payshop'),
			'Type' => 'yesno',
			'Description' => IftpLang::trans('cancel_payshop_desc'),
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
						<th><span style="margin-left: 8px">' . IftpLang::trans('anti_phishing_key') . ' </span></th>
						<td><span class="badge">' . $antiphishingKey . '</span></td>
					</tr>
					<tr>
						<th><span style="margin-left: 8px">' . IftpLang::trans('callback_url') . ' </span></th>
						<td><span class="badge" style="white-space: normal;">' . $callbackUrl . '</span></td>
					</tr>
				</table>',
		];



		return $configForm;
	} catch (\Throwable $th) {

		IfthenpayLog::error(Config::PAYSHOP, 'Error loading admin config form', $th->__toString());
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
function ifthenpaypayshop_link($params)
{
	try {
		if ($_GET['paymentfailed']) {
			return '';
		}

		if (PayshopService::shouldGeneratePayment($params)) {
			$paymentDetails = PayshopService::generatePayment($params);
			PayshopService::savePayment($paymentDetails);
		}

		if ($_GET['id']) {
			return PayshopService::getPaymentDetailsHtml($params);
		}
	} catch (\Throwable $th) {
		IfthenpayLog::error(Config::PAYSHOP, 'Error generating Payshop payment', $th->__toString());
		header("Location: viewinvoice.php?id={$params['invoiceid']}&paymentfailed=true");
		return '';
	}
}
