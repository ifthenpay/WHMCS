<?php

use WHMCS\Config\Setting;
use WHMCS\Module\GatewaySetting;
use WHMCS\Exception\Module\InvalidConfiguration;

use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Module\Gateway\ifthenpaylib\Lang\IftpLang;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpayService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\MbwayService;

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
function ifthenpaymbway_MetaData()
{
	return array(
		'DisplayName' => Config::MBWAY_NAME,
		'APIVersion' => '1.1', // Use API Version 1.1
	);
}


function ifthenpaymbway_config_validate($params)
{
	if (isset($params[Config::CF_BACKOFFICE_KEY]) && !preg_match('/^\d{4}-\d{4}-\d{4}-\d{4}$/', $params[Config::CF_BACKOFFICE_KEY])) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_backoffice_key'));
	}

	if (isset($params[Config::CF_MBWAY_KEY]) && !(preg_match('/^[A-Z]{3}-\d{6}$/', $params[Config::CF_MBWAY_KEY]))) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_key'));
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

	// notificationDescription
	if (isset($params[Config::CF_MBWAY_NOTIFICATION_DESCRIPTION]) && (!(preg_match('/^[A-Za-z ]*(\{\{invoice_id\}\}[A-Za-z ]*)?$/', $params[Config::CF_MBWAY_NOTIFICATION_DESCRIPTION])) || strlen($params[Config::CF_MBWAY_NOTIFICATION_DESCRIPTION]) > 100)) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_notification_description'));
	}


	$settings = GatewaySetting::getForGateway(Config::MBWAY_MODULE_CODE);

	// save accounts
	$accounts = MbwayService::getKeysByBackofficKey($params[Config::CF_BACKOFFICE_KEY]);
	GatewaySetting::setValue(Config::MBWAY_MODULE_CODE, Config::CF_ACCOUNTS, json_encode($accounts));


	// save callback
	if (
		$params[Config::CF_CAN_ACTIVATE_CALLBACK] === 'on' &&
		(
			!isset($settings[Config::CF_BACKOFFICE_KEY]) ||
			(isset($params[Config::CF_MBWAY_KEY]) && isset($settings[Config::CF_MBWAY_KEY]) && $params[Config::CF_MBWAY_KEY] != $settings[Config::CF_MBWAY_KEY]) ||
			(!isset($params[Config::CF_CALLBACK_STATUS]) || $params[Config::CF_CALLBACK_STATUS] !== 'on')
		)

	) {
		MbwayService::activateCallback($params[Config::CF_BACKOFFICE_KEY], $params[Config::CF_MBWAY_KEY]);
	} else if (isset($params[Config::CF_CAN_ACTIVATE_CALLBACK]) && $params[Config::CF_CAN_ACTIVATE_CALLBACK] !== 'on' && isset($params[Config::CF_CALLBACK_STATUS]) && $params[Config::CF_CALLBACK_STATUS] === 'on') {
		MbwayService::clearCallback();
	}

	try {
		MbwayService::handleDbCreateUpdate();
	} catch (\Throwable $th) {
		throw new InvalidConfiguration(IftpLang::trans('msg_error_updating_mbway_database'));
	}

	GatewaySetting::setValue(Config::MBWAY_MODULE_CODE, Config::CF_INSTALLED_MODULE_VERSION, Config::MODULE_VERSION);
}



function ifthenpaymbway_config($params)
{
	try {

		$resetBtn = isset($params[Config::CF_BACKOFFICE_KEY]) ? '<button type="button" class="btn btn-danger ifthenpay_reset_btn" data-method="' . Config::MBWAY . '">' . IftpLang::trans('reset') . '</button>' : '';

		$backofficeKeyReadOnly = $resetBtn != '' ? true : false;


		$configForm = [
			'FriendlyName' => [
				'Type' => 'System',
				'Value' => Config::MBWAY_NAME
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


		$configForm[Config::CF_MBWAY_KEY] = [
			'FriendlyName' => IftpLang::trans('mbway_key'),
			'Type' => 'dropdown',
			'Options' => MbwayService::getKeyOptions($params)
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

		$configForm[Config::CF_MBWAY_NOTIFICATION_DESCRIPTION] = [
			'FriendlyName' => IftpLang::trans('notification_description'),
			'Type' => 'text',
			'Description' => IftpLang::trans('notification_description_desc'),
			'Default' => IftpLang::trans('mbway_payment_invoice') . ' ' . Setting::getValue("CompanyName") ?? ''
		];

		$configForm[Config::CF_SHOWICON] = [
			'FriendlyName' => IftpLang::trans('show_payment_icon'),
			'Type' => 'yesno',
			'Description' => IftpLang::trans('show_payment_icon_desc'),
		];

		$configForm[Config::CF_MBWAY_SHOW_COUNTDOWN] = [
			'FriendlyName' => IftpLang::trans('show_mbway_countdown'),
			'Type' => 'yesno',
			'Description' => IftpLang::trans('show_mbway_countdown_desc'),
		];

		$configForm[Config::CF_CAN_CANCEL] = [
			'FriendlyName' => IftpLang::trans('cancel_mbway'),
			'Type' => 'yesno',
			'Description' => IftpLang::trans('cancel_mbway_desc'),
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

		IfthenpayLog::error(Config::MBWAY, 'Error loading admin config form', $th->__toString());
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
function ifthenpaymbway_link($params)
{
	try {
		if ($_POST['mobile_number'] && $_SESSION['ifthenpay_payment_id'] != $_POST['payment_id']) { // create new payment

			$params['mobile_number'] = $_POST['mobile_number'];

			if (isset($_POST['mobile_code']) && $_POST['mobile_code'] != '') {
				$params['mobile_number'] = $_POST['mobile_code'] . '#' . $params['mobile_number'];
			}

			$paymentDetails = MbwayService::generatePayment($params);
			MbwayService::savePayment($paymentDetails);

			$_SESSION['ifthenpay_payment_id'] = $_POST['payment_id']; // serves as gatekeeper to prevent a new payment from being generated on page refresh 

			return MbwayService::getPaymentDetailsHtml($params['invoiceid']);
		}

		if ($_GET['id']) { // show the mbway payment form
			MbwayService::savePlaceholderPaymentIfNotFound($params);

			return MbwayService::getPaymentFormHtml( $params['invoiceid']);
		}
	} catch (\Throwable $th) {

		IfthenpayLog::error(Config::MBWAY, 'Error generating Mbway payment', $th->__toString());
		header("Location: viewinvoice.php?id={$params['invoiceid']}&paymentfailed=true");
		return '';
	}
}
