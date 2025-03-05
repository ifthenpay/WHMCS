<?php

use WHMCS\Module\GatewaySetting;
use WHMCS\Exception\Module\InvalidConfiguration;

use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Module\Gateway\ifthenpaylib\Lang\IftpLang;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpayService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\MultibancoService;

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
function ifthenpaymultibanco_MetaData()
{
	return array(
		'DisplayName' => Config::MULTIBANCO_NAME,
		'APIVersion' => '1.1', // Use API Version 1.1
	);
}


function ifthenpaymultibanco_config_validate($params)
{
	if (isset($params[Config::CF_BACKOFFICE_KEY]) && !preg_match('/^\d{4}-\d{4}-\d{4}-\d{4}$/', $params[Config::CF_BACKOFFICE_KEY])) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_backoffice_key'));
	}

	if (isset($params[Config::CF_MULTIBANCO_ENTITY]) && !(preg_match('/^\d{5}$/', $params[Config::CF_MULTIBANCO_ENTITY]) || $params[Config::CF_MULTIBANCO_ENTITY] === 'MB')) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_entity'));
	}

	if (isset($params[Config::CF_MULTIBANCO_SUBENTITY]) && !((preg_match('/^\d{5}$/', $params[Config::CF_MULTIBANCO_ENTITY]) && preg_match('/^\d{2,3}$/', $params[Config::CF_MULTIBANCO_SUBENTITY])) || ($params[Config::CF_MULTIBANCO_ENTITY] === 'MB') && preg_match('/^[A-Z]{3}-\d{6}$/', $params[Config::CF_MULTIBANCO_SUBENTITY]))) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_subentity'));
	}

	if (($params[Config::CF_DEADLINE]) && isset($params[Config::CF_MULTIBANCO_ENTITY]) &&
		$params[Config::CF_MULTIBANCO_ENTITY] === 'MB' && $params[Config::CF_DEADLINE] != '' && !preg_match('/^\d{1,4}$/', $params[Config::CF_DEADLINE])
	) {
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


	$settings = GatewaySetting::getForGateway(Config::MULTIBANCO_MODULE_CODE);

	// save accounts
	$accounts = MultibancoService::getEntitiesByBackofficKey($params[Config::CF_BACKOFFICE_KEY]);
	if ($accounts !== false) {
		GatewaySetting::setValue(Config::MULTIBANCO_MODULE_CODE, Config::CF_ACCOUNTS, json_encode($accounts));
	}

	// save callback
	if (
		$params[Config::CF_CAN_ACTIVATE_CALLBACK] === 'on' &&
		(
			!isset($settings[Config::CF_BACKOFFICE_KEY]) ||
			(isset($params[Config::CF_MULTIBANCO_ENTITY]) && isset($settings[Config::CF_MULTIBANCO_ENTITY]) && $params[Config::CF_MULTIBANCO_ENTITY] != $settings[Config::CF_MULTIBANCO_ENTITY]) ||
			(isset($params[Config::CF_MULTIBANCO_SUBENTITY]) && isset($settings[Config::CF_MULTIBANCO_SUBENTITY]) && $params[Config::CF_MULTIBANCO_SUBENTITY] != $settings[Config::CF_MULTIBANCO_SUBENTITY]) ||
			(!isset($params[Config::CF_CALLBACK_STATUS]) || $params[Config::CF_CALLBACK_STATUS] !== 'on')
		)
	) {
		MultibancoService::activateCallback($params[Config::CF_BACKOFFICE_KEY], $params[Config::CF_MULTIBANCO_ENTITY], $params[Config::CF_MULTIBANCO_SUBENTITY]);
	} else if (isset($params[Config::CF_CAN_ACTIVATE_CALLBACK]) && $params[Config::CF_CAN_ACTIVATE_CALLBACK] !== 'on' && isset($params[Config::CF_CALLBACK_STATUS]) && $params[Config::CF_CALLBACK_STATUS] === 'on') {
		MultibancoService::clearCallback();
	}

	try {
		MultibancoService::handleDbCreateUpdate();
	} catch (\Throwable $th) {
		throw new InvalidConfiguration(IftpLang::trans('msg_error_updating_multibanco_database'));
	}

	GatewaySetting::setValue(Config::MULTIBANCO_MODULE_CODE, Config::CF_INSTALLED_MODULE_VERSION, Config::MODULE_VERSION);
}



function ifthenpaymultibanco_config($params)
{
	try {

		$resetBtn = isset($params[Config::CF_BACKOFFICE_KEY]) ? '<button type="button" class="btn btn-danger ifthenpay_reset_btn" data-method="' . Config::MULTIBANCO . '">' . IftpLang::trans('reset') . '</button>' : '';

		$backofficeKeyReadOnly = $resetBtn != '' ? true : false;


		$configForm = [
			'FriendlyName' => [
				'Type' => 'System',
				'Value' => Config::MULTIBANCO_NAME
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

		$configForm[Config::CF_MULTIBANCO_ENTITY] = [
			'FriendlyName' => IftpLang::trans('entity'),
			'Type' => 'dropdown',
			'Options' => MultibancoService::getEntityOptions($params)
		];

		$configForm[Config::CF_MULTIBANCO_SUBENTITY] = [
			'FriendlyName' => IftpLang::trans(Config::CF_MULTIBANCO_SUBENTITY),
			'Type' => 'dropdown',
			'Options' => MultibancoService::getSubEntityOptions($params)
		];

		$configForm[Config::CF_DEADLINE] = [
			'FriendlyName' => IftpLang::trans(Config::CF_DEADLINE),
			'Type' => 'dropdown',
			'Options' => MultibancoService::getDeadlineOptions(),
			'Description' => '<span>' . IftpLang::trans('multibanco_deadline_desc') . '</span>'
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
			'FriendlyName' => IftpLang::trans('cancel_multibanco'),
			'Type' => 'yesno',
			'Description' => IftpLang::trans('cancel_multibanco_desc'),
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

		IfthenpayLog::error(Config::MULTIBANCO, 'Error loading admin config form', $th->__toString());
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
function ifthenpaymultibanco_link($params)
{
	try {

		if ($_GET['paymentfailed']) {
			return '';
		}

		if (MultibancoService::shouldGeneratePayment($params)) {
			$paymentDetails = MultibancoService::generatePayment($params);
			MultibancoService::savePayment($paymentDetails);
		}

		if ($_GET['id']) {

			return MultibancoService::getPaymentDetailsHtml($params);
		}
	} catch (\Throwable $th) {

		IfthenpayLog::error(Config::MULTIBANCO, 'Error generating Multibanco payment', $th->__toString());
		header("Location: viewinvoice.php?id={$params['invoiceid']}&paymentfailed=true");
		return '';
	}
}
