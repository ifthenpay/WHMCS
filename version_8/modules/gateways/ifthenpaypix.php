<?php

use WHMCS\Module\GatewaySetting;
use WHMCS\Exception\Module\InvalidConfiguration;

use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Config\Sql;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Module\Gateway\ifthenpaylib\Lang\IftpLang;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpayService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\PixService;

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
function ifthenpaypix_MetaData()
{
	return array(
		'DisplayName' => Config::PIX_NAME,
		'APIVersion' => '1.1', // Use API Version 1.1
	);
}


function ifthenpaypix_config_validate($params)
{
	if (isset($params[Config::CF_BACKOFFICE_KEY]) && !preg_match('/^\d{4}-\d{4}-\d{4}-\d{4}$/', $params[Config::CF_BACKOFFICE_KEY])) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_backoffice_key'));
	}

	if (isset($params[Config::CF_PIX_KEY]) && !(preg_match('/^[A-Z]{3}-\d{6}$/', $params[Config::CF_PIX_KEY]))) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_key'));
	}

	if (isset($params['minAmount']) && $params['minAmount'] != '' && !preg_match('/^\d+(\.\d{1,2})?$/', $params['minAmount'])) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_minimum_amount'));
	}

	if (isset($params['maxAmount']) && $params['maxAmount'] != '' && !preg_match('/^\d+(\.\d{1,2})?$/', $params['maxAmount'])) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_maximum_amount'));
	}

	if (
		isset($params['maxAmount']) && $params['maxAmount'] != '' &&
		isset($params['minAmount']) && $params['minAmount'] != '' &&
		$params['minAmount'] > $params['maxAmount']
	) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_min_max_amount'));
	}


	$settings = GatewaySetting::getForGateway(Config::PIX_MODULE_CODE);

	// save accounts
	$accounts = PixService::getKeysByBackofficKey($params[Config::CF_BACKOFFICE_KEY]);
	if ($accounts !== false) {
		GatewaySetting::setValue(Config::PIX_MODULE_CODE, 'accounts', json_encode($accounts));
	}


	// save callback
	if (
		$params['canActivateCallback'] === 'on' &&
		(
			!isset($settings[Config::CF_BACKOFFICE_KEY]) ||
			(isset($params[Config::CF_PIX_KEY]) && isset($settings[Config::CF_PIX_KEY]) && $params[Config::CF_PIX_KEY] != $settings[Config::CF_PIX_KEY]) ||
			(!isset($params['callbackStatus']) || $params['callbackStatus'] !== 'on')
		)

	) {
		PixService::activateCallback($params[Config::CF_BACKOFFICE_KEY], $params[Config::CF_PIX_KEY]);
	} else if (isset($params['canActivateCallback']) && $params['canActivateCallback'] !== 'on' && isset($params['callbackStatus']) && $params['callbackStatus'] === 'on') {
		PixService::clearCallback();
	}
	
	try {
		PixService::handleDbCreateUpdate();
	} catch (\Throwable $th) {
		throw new InvalidConfiguration(IftpLang::trans('msg_error_updating_pix_database'));
	}

	GatewaySetting::setValue(Config::PIX_MODULE_CODE, Config::CF_INSTALLED_MODULE_VERSION, Config::MODULE_VERSION);
}



function ifthenpaypix_config($params)
{

	try {

		$resetBtn = isset($params[Config::CF_BACKOFFICE_KEY]) ? '<button type="button" class="btn btn-danger ifthenpay_reset_btn" data-method="' . Config::PIX . '">' . IftpLang::trans('reset') . '</button>' : '';

		$backofficeKeyReadOnly = $resetBtn != '' ? true : false;


		$configForm = [
			'FriendlyName' => [
				'Type' => 'System',
				'Value' => Config::PIX_NAME
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


		$configForm[Config::CF_PIX_KEY] = [
			'FriendlyName' => IftpLang::trans('pix_key'),
			'Type' => 'dropdown',
			'Options' => PixService::getKeyOptions($params)
		];

		$configForm['minAmount'] = [
			'FriendlyName' => IftpLang::trans('min_amount'),
			'Type' => 'text',
			'Description' => IftpLang::trans('min_amount_desc'),
		];

		$configForm['maxAmount'] = [
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
			'FriendlyName' => IftpLang::trans('cancel_pix'),
			'Type' => 'yesno',
			'Description' => IftpLang::trans('cancel_pix_desc'),
		];

		$configForm['canActivateCallback'] = [
			'FriendlyName' => IftpLang::trans('callback'),
			'Type' => 'yesno',
			'Description' => IftpLang::trans('callback_desc'),
		];


		$callbackStatusColor = $params['callbackStatus'] === 'on' ? 'status-badge-green' : 'status-badge-orange';
		$callbackStatusText = $params['callbackStatus'] === 'on' ? IftpLang::trans('callback_active') : IftpLang::trans('callback_inactive');
		$antiphishingKey = $params['antiphishingKey'] ? $params['antiphishingKey'] : '----';
		$callbackUrl = $params['callbackUrl'] ? $params['callbackUrl'] : '----';

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

		IfthenpayLog::error(Config::PIX, 'Error loading admin config form', $th->__toString());
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
function ifthenpaypix_link($params)
{
	try {
		if ($_POST['ifthenpaypix_cpf']) { // create new payment and redirect

			$params['ifthenpaypix_name'] = $_POST['ifthenpaypix_name'];
			$params['ifthenpaypix_cpf'] = $_POST['ifthenpaypix_cpf'];
			$params['ifthenpaypix_email'] = $_POST['ifthenpaypix_email'];

			$paymentDetails = PixService::generatePayment($params);
			PixService::savePayment($paymentDetails);
			header('Location: ' . $paymentDetails['payment_url']);
			return '';
		}

		if ($_GET['id'] && $_GET['ifthenpaysuccess']) {
			return PixService::getPaymentDetailsHtml();
		}

		if ($_GET['id']) { // show the pix payment form
			PixService::savePlaceholderPaymentIfNotFound($params);

			return PixService::getPaymentFormHtml($params['invoiceid']);
		}
	} catch (\Throwable $th) {

		IfthenpayLog::error(Config::PIX, 'Error generating Pix payment', $th->__toString());
		header("Location: viewinvoice.php?id={$params['invoiceid']}&paymentfailed=true");
		return '';
	}
}
