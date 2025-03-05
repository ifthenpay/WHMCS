<?php

if (!defined("WHMCS")) {
	die("This file cannot be accessed directly");
}

use WHMCS\Session;
use WHMCS\Exception\Module\InvalidConfiguration;

use WHMCS\Module\Gateway\Ifthenpay\Config\Ifthenpay;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Facades\PaymentFacade;
use WHMCS\Module\Gateway\Ifthenpay\Exceptions\BackOfficeException;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Form\IfthenpayConfigForms;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ConfigGatewaysRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;

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
function ccard_MetaData()
{
	return array(
		'DisplayName' => 'Cartão de Crédito (Ifthenpay)',
		'APIVersion' => '1.1', // Use API Version 1.1
	);
}

/**
 * Define gateway configuration options.
 *
 * The fields you define here determine the configuration options that are
 * presented to administrator users when activating and configuring your
 * payment gateway module for use.
 *
 * Supported field types include:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each field type and their possible configuration parameters are
 * provided in the sample function below.
 *
 * @return array
 */

function ccard_config()
{
	try {
		$ioc = new Ifthenpay(Gateway::CCARD);
		$ifthenpayLogger = $ioc->getIoc()->make(IfthenpayLogger::class);
		$ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_BACKOFFICE_CONFIG_CCARD)->getLogger();
		return $ioc->getIoc()->make(IfthenpayConfigForms::class)->buildForm();
	} catch (\Throwable $th) {
		if ($th instanceof BackOfficeException) {
			$ioc->getIoc()->make(ConfigGatewaysRepositoryInterface::class)->deleteWhere(['gateway' => Gateway::CCARD, 'setting' => 'backofficeKey']);
			$ifthenpayLogger->error($th->getMessage(), ['exception' => $th]);
			Session::setAndRelease("ConfigurationError", $th->getMessage());
			$redirect = "error=ccard#m_ccard";
			redir($redirect);
		}
		$ifthenpayLogger->alert($th->getMessage(), ['exception' => $th]);
	}
}



function ccard_config_validate($params)
{
	try {

		// has backofficeKey?
		if (!isset($params['backofficeKey']) || (isset($params['backofficeKey']) && empty($params['backofficeKey']))) {
			throw new \Exception(\AdminLang::trans('configMsgBackofficeKeyRequired'));
		}

		// is backofficeKey format valid?
		if (!preg_match('/^\d{4}-\d{4}-\d{4}-\d{4}$/', $params['backofficeKey'])) {
			throw new \Exception(\AdminLang::trans('configMsgBackofficeKeyFormatInvalid'));
		}

		// is backofficeKey valid?
		$ifthenpayModuleApp = new Ifthenpay();
		$gateway = $ifthenpayModuleApp->getIoc()->make(Gateway::class);
		$gateway->authenticate($params['backofficeKey']); //throws exception if invalid

		// is ccardKey valid?
		if (isset($params['ccardKey']) && $params['ccardKey'] == 'empty') {
			throw new \Exception(\AdminLang::trans('configMsgCcardKeyRequired'));
		}

	} catch (\Exception $error) {
		throw new InvalidConfiguration($error->getMessage());
	}
}


function ccard_link($params)
{
	try {
		$ifthenpayContainer = (new Ifthenpay(Gateway::CCARD))->getIoc();
		$ifthenpayLogger = $ifthenpayContainer->make(IfthenpayLogger::class);
		$ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_PAYMENTS)->getLogger();
		$paymentData = $ifthenpayContainer->make(PaymentFacade::class)->setPaymentMethod(Gateway::CCARD)->setParams($params)->execute();

		if (is_array($paymentData) && $paymentData['status'] === 'pending' && !strpos($_SERVER['REQUEST_URI'], 'admin/invoices')) {
			$ifthenpayLogger->info(
				'redirect user to provider ccard page',
				[
					'paymentMethod' => Gateway::CCARD,
					'paymentData' => $paymentData
				]
			);
			$paymentUrl = $_SESSION['paymentUrl'];
			header('Location: ' . $paymentUrl);

		} else if (!is_array($paymentData) && $paymentData->getPaymentGatewayResultData()->status === '0') {
			$ifthenpayLogger->info(
				'redirect user to provider ccard page',
				[
					'paymentMethod' => Gateway::CCARD,
					'paymentGatewayResulData' => $paymentData->getPaymentGatewayResultData()
				]
			);
			$_SESSION['paymentUrl'] = $paymentData->getPaymentGatewayResultData()->paymentUrl;
			header('Location: ' . $paymentData->getPaymentGatewayResultData()->paymentUrl);
		} /*else {
							   $ifthenpayLogger->info('payment data retrieved with success', [
									   'paymentMethod' => Gateway::CCARD,
									   'paymentData' => $paymentData
								   ]
							   );
							   return $paymentData;
						   }*/
	} catch (\Throwable $th) {
		$ifthenpayLogger->error(
			'error processing payment - ' . $th->getMessage(),
			[
				'paymentMethod' => Gateway::CCARD,
				'params' => $params,
				'exception' => $th
			]
		);
		return '<div class=\"alert alert-danger\">' . $th->getMessage() . '</div>';
	}
}
