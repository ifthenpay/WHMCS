<?php

use WHMCS\Module\GatewaySetting;
use WHMCS\Exception\Module\InvalidConfiguration;

use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Module\Gateway\ifthenpaylib\Lang\IftpLang;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpayService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\CcardService;

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
function ifthenpayccard_MetaData()
{
	return array(
		'DisplayName' => Config::CCARD_NAME,
		'APIVersion' => '1.1', // Use API Version 1.1
	);
}


function ifthenpayccard_config_validate($params)
{
	if (isset($params[Config::CF_BACKOFFICE_KEY]) && !preg_match('/^\d{4}-\d{4}-\d{4}-\d{4}$/', $params[Config::CF_BACKOFFICE_KEY])) {
		throw new InvalidConfiguration(IftpLang::trans('msg_invalid_backoffice_key'));
	}

	if (isset($params[Config::CF_CCARD_KEY]) && !(preg_match('/^[A-Z]{3}-\d{6}$/', $params[Config::CF_CCARD_KEY]))) {
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

	// save accounts
	$accounts = CcardService::getKeysByBackofficKey($params[Config::CF_BACKOFFICE_KEY]);
	if ($accounts !== false) {
		GatewaySetting::setValue(Config::CCARD_MODULE_CODE, Config::CF_ACCOUNTS, json_encode($accounts));
	}

	try {
		CcardService::handleDbCreateUpdate();
	} catch (\Throwable $th) {
		throw new InvalidConfiguration(IftpLang::trans('msg_error_updating_ccard_database'));
	}

	GatewaySetting::setValue(Config::CCARD_MODULE_CODE, Config::CF_INSTALLED_MODULE_VERSION, Config::MODULE_VERSION);
}



function ifthenpayccard_config($params)
{

	try {

		$resetBtn = isset($params[Config::CF_BACKOFFICE_KEY]) ? '<button type="button" class="btn btn-danger ifthenpay_reset_btn" data-method="' . Config::CCARD . '">' . IftpLang::trans('reset') . '</button>' : '';

		$backofficeKeyReadOnly = $resetBtn != '' ? true : false;


		$configForm = [
			'FriendlyName' => [
				'Type' => 'System',
				'Value' => Config::CCARD_NAME
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

		$configForm[Config::CF_CCARD_KEY] = [
			'FriendlyName' => IftpLang::trans('ccard_key'),
			'Type' => 'dropdown',
			'Options' => CcardService::getKeyOptions($params)
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
			'FriendlyName' => IftpLang::trans('cancel_ccard'),
			'Type' => 'yesno',
			'Description' => IftpLang::trans('cancel_ccard_desc'),
		];


		return $configForm;
	} catch (\Throwable $th) {

		IfthenpayLog::error(Config::CCARD, 'Error loading admin config form', $th->__toString());
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
function ifthenpayccard_link($params)
{
	try {
		if ($_POST['ccard']) { // create new payment

			$paymentDetails = CcardService::generatePayment($params);

			CcardService::savePayment($paymentDetails);

			// redirect to gateway
			header('Location: ' . $paymentDetails['paymentUrl']);

			return '';
		}


		if ($_GET['id']) { // show the ccard payment form
			CcardService::savePlaceholderPaymentIfNotFound($params);

			return CcardService::getPaymentFormHtml($params['invoiceid']);
		}
	} catch (\Throwable $th) {

		IfthenpayLog::error(Config::CCARD, 'Error generating Ccard payment', $th->__toString());
		header("Location: viewinvoice.php?id={$params['invoiceid']}&paymentfailed=true");
		return '';
	}
}
