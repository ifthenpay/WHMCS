<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpaylib\Services;

use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpayHttpClient;
use WHMCS\Module\Gateway\ifthenpaylib\Lang\IftpLang;
use WHMCS\Module\GatewaySetting;




class IfthenpayService
{


	public static function requestCallbackActivation(string $backofficeKey, string $entity, string $subEntity, string $antiPhishingKey, string $callbackUrl): bool
	{
		try {
			$payload = [
				'chave' => $backofficeKey,
				'entidade' => $entity,
				'subentidade' => $subEntity,
				'apKey' => $antiPhishingKey,
				'urlCb' => $callbackUrl,
			];

			$response = IfthenpayHttpClient::post(
				Config::API_URL_ACTIVATE_CALLBACK,
				$payload,
				['Content-Type' => 'application/json'],
				'text'
			);

			if (strpos($response, 'OK:') === 0) {
				IfthenpayLog::info('general_logs', 'Callback activated');
				return true;
			}
			IfthenpayLog::info('general_logs', 'requestCallbackActivation unsuccessful - response: ', ['payload' => $payload, 'response' => $response]);
		} catch (\Throwable $th) {
			IfthenpayLog::error('general_logs', 'Unexpected error requesting callback activation ', $th->__toString());
		}
		return false;
	}



	public static function getLatestModuleVersionJson(): array
	{
		try {
			$response = IfthenpayHttpClient::get(
				Config::API_URL_GET_LATEST_VERSION
			);

			return $response;
		} catch (\Throwable $th) {
			IfthenpayLog::error('general_logs', 'Unexpected error getting latest module version ', $th->__toString());
		}
		return [];
	}



	public static function generateModuleVersionBlock(): string
	{
		$currentVersion = Config::MODULE_VERSION;
		$versionData = self::getLatestModuleVersionJson();

		if (!isset($versionData['version'])) {
			return IftpLang::trans('your_current_version') . ' <b>' . $currentVersion . '<b>';
		}

		if ($currentVersion && version_compare($versionData['version'], $currentVersion, '>')) {

			return '<div class="infobox">' . IftpLang::trans('current_version_installed') . ' <b>' . $currentVersion . '.</b>
			<span>' . IftpLang::trans('a_new_version') . ' <b>' . $versionData['version'] . '</b> ' . IftpLang::trans('is_available') . ' </span><a class="btn btn-success" href="' . $versionData['download'] . '" target="_blank">' . IftpLang::trans('download') . '</a></div>
			';
		} else {
			return '<b>' . Config::MODULE_VERSION . '</b> <span>' . IftpLang::trans('module_up_to_date') . '</span>';
		}
	}


	private static function isAmountWithinMinMax(string $paymentMethodCode, string $amount)
	{
		$minAmount = GatewaySetting::getValue($paymentMethodCode, Config::CF_MIN_AMOUNT);
		$maxAmount = GatewaySetting::getValue($paymentMethodCode, Config::CF_MAX_AMOUNT);

		if (($minAmount != '' && $minAmount > $amount) ||
			($maxAmount != '' && $maxAmount < $amount)
		) {
			return false;
		}
		return true;
	}

	public static function filterPaymentMethodsByMinMax(array $gatewayArray, string $amount): array
	{
		try {
			foreach ($gatewayArray as $paymentMethodCode => $value) {
				if (
					in_array($paymentMethodCode, Config::PAYMENT_METHODS_ARRAY) &&
					!self::isAmountWithinMinMax($paymentMethodCode, $amount)
				) {
					IfthenpayLog::info('general_logs', 'filterPaymentMethodsByMinMax - hiding ' . $paymentMethodCode . ' payment method');
					unset($gatewayArray[$paymentMethodCode]);
				}
			}

			return $gatewayArray;
		} catch (\Throwable $th) {
			IfthenpayLog::error('general_logs', 'Error, unable to filter payment methods by min max', $th->__toString());
			return $gatewayArray;
		}
	}



	public static function filterPaymentMethodsByAvailability(array $gatewayArray): array
	{
		// PM_BOILERPLATE

		try {
			$availablePaymentMethods = IfthenpayHttpClient::get(Config::API_URL_GET_IFTHENPAY_AVAILABLE_METHODS);

			if (empty($availablePaymentMethods)) {
				return $gatewayArray;
			}

			foreach ($availablePaymentMethods as $method) {
				if ($method['IsVisible'] === false || $method['IsVisible'] === 'false') {

					$methodName = strtolower($method['Entity']);

					if (strcasecmp($methodName, Config::MULTIBANCO_DYNAMIC) === 0) { // handle "mb" as multibanco entity
						$methodName = Config::MULTIBANCO;
					}

					switch ($methodName) {
						case Config::MULTIBANCO:
							unset($gatewayArray['ifthenpay' . Config::MULTIBANCO]);
							break;
						case Config::PAYSHOP:
							unset($gatewayArray['ifthenpay' . Config::PAYSHOP]);
							break;
						case Config::MBWAY:
							unset($gatewayArray['ifthenpay' . Config::MBWAY]);
							break;
						case Config::CCARD:
							unset($gatewayArray['ifthenpay' . Config::CCARD]);
							break;
						case Config::COFIDIS:
							unset($gatewayArray['ifthenpay' . Config::COFIDIS]);
							break;
						case Config::PIX:
							unset($gatewayArray['ifthenpay' . Config::PIX]);
							break;
						default:
							break;
					}
				}
			}

			return $gatewayArray;
		} catch (\Throwable $th) {
			IfthenpayLog::error('general_logs', 'Error, unable to filter payment methods by availability', $th->__toString());
			return $gatewayArray;
		}
	}



	public static function injectPaymentMethodLogos(array $gatewayArray): array
	{
		// PM_BOILERPLATE
		try {
			foreach ($gatewayArray as $key => $value) {

				if (
					$key == Config::MULTIBANCO_MODULE_CODE &&
					GatewaySetting::getValue(Config::MULTIBANCO_MODULE_CODE, Config::CF_SHOWICON) == 'on'
				) {
					$gatewayArray[$key]['name'] = '<img src="' . UtilsService::addCacheBuster(UtilsService::getUrl(UtilsService::pathToAssetImage('multibanco_option.png'))) . '" alt="' . Config::MULTIBANCO_NAME . '" height="30" title="' . Config::MULTIBANCO_NAME . '">';
				} else if (
					$key == Config::PAYSHOP_MODULE_CODE &&
					GatewaySetting::getValue(Config::PAYSHOP_MODULE_CODE, Config::CF_SHOWICON) == 'on'
				) {
					$gatewayArray[$key]['name'] = '<img src="' . UtilsService::addCacheBuster(UtilsService::getUrl(UtilsService::pathToAssetImage('payshop_option.png'))) . '" height="30" title="' . Config::PAYSHOP_NAME . '">';
				} else if (
					$key == Config::MBWAY_MODULE_CODE &&
					GatewaySetting::getValue(Config::MBWAY_MODULE_CODE, Config::CF_SHOWICON) == 'on'
				) {
					$gatewayArray[$key]['name'] = '<img src="' . UtilsService::addCacheBuster(UtilsService::getUrl(UtilsService::pathToAssetImage('mbway_option.png'))) . '" height="30" title="' . Config::MBWAY_NAME . '">';
				} else if (
					$key == Config::CCARD_MODULE_CODE &&
					GatewaySetting::getValue(Config::CCARD_MODULE_CODE, Config::CF_SHOWICON) == 'on'
				) {
					$gatewayArray[$key]['name'] = '<img src="' . UtilsService::addCacheBuster(UtilsService::getUrl(UtilsService::pathToAssetImage('ccard_option.png'))) . '"height="30" title="' . Config::CCARD_NAME . '">';
				} else if (
					$key == Config::COFIDIS_MODULE_CODE &&
					GatewaySetting::getValue(Config::COFIDIS_MODULE_CODE, Config::CF_SHOWICON) == 'on'
				) {
					$gatewayArray[$key]['name'] = '<img src="' . UtilsService::addCacheBuster(UtilsService::getUrl(UtilsService::pathToAssetImage('cofidis_option.png'))) . '"height="30" title="' . Config::COFIDIS_NAME . '">';
				} else if (
					$key == Config::PIX_MODULE_CODE &&
					GatewaySetting::getValue(Config::PIX_MODULE_CODE, Config::CF_SHOWICON) == 'on'
				) {
					$gatewayArray[$key]['name'] = '<img src="' . UtilsService::addCacheBuster(UtilsService::getUrl(UtilsService::pathToAssetImage('pix_option.png'))) . '" height="30" title="' . Config::PIX_NAME . '">';
				} else if (
					$key == Config::IFTHENPAYGATEWAY_MODULE_CODE &&
					GatewaySetting::getValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_SHOWICON) != 'off'
				) {
					$gatewayArray[$key]['name'] = GatewaySetting::getValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_IFTHENPAYGATEWAY_FRONT_ICON);
				}
			}

			return ["gateways" => $gatewayArray];
		} catch (\Throwable $th) {
			IfthenpayLog::error('general_logs', 'Unable to inject payment logo image on checkout', $th->__toString());
			return ["gateways" => $gatewayArray];
		}
	}



	public static function cancelExpiredPayments()
	{
		// PM_BOILERPLATE

		if (GatewaySetting::getValue(Config::MULTIBANCO_MODULE_CODE, 'type') && GatewaySetting::getValue(Config::MULTIBANCO_MODULE_CODE, Config::CF_CAN_CANCEL) == 'on') {
			MultibancoService::cancelExpiredPayments();
		}

		if (GatewaySetting::getValue(Config::PAYSHOP_MODULE_CODE, 'type') && GatewaySetting::getValue(Config::PAYSHOP_MODULE_CODE, Config::CF_CAN_CANCEL) == 'on') {
			PayshopService::cancelExpiredPayments();
		}

		if (GatewaySetting::getValue(Config::MBWAY_MODULE_CODE, 'type') && GatewaySetting::getValue(Config::MBWAY_MODULE_CODE, Config::CF_CAN_CANCEL) == 'on') {
			MbwayService::cancelExpiredPayments();
		}

		if (GatewaySetting::getValue(Config::CCARD_MODULE_CODE, 'type') && GatewaySetting::getValue(Config::CCARD_MODULE_CODE, Config::CF_CAN_CANCEL) == 'on') {
			CcardService::cancelExpiredPayments();
		}

		if (GatewaySetting::getValue(Config::COFIDIS_MODULE_CODE, 'type') && GatewaySetting::getValue(Config::COFIDIS_MODULE_CODE, Config::CF_CAN_CANCEL) == 'on') {
			CofidisService::cancelExpiredPayments();
		}

		if (GatewaySetting::getValue(Config::PIX_MODULE_CODE, 'type') && GatewaySetting::getValue(Config::PIX_MODULE_CODE, Config::CF_CAN_CANCEL) == 'on') {
			PixService::cancelExpiredPayments();
		}

		if (GatewaySetting::getValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, 'type') && GatewaySetting::getValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_CAN_CANCEL) == 'on') {
			IfthenpaygatewayService::cancelExpiredPayments();
		}
	}



	public static function getAccountsByBackofficKey(string $backofficeKey): array
	{
		$response = IfthenpayHttpClient::post(
			Config::API_URL_GET_ACCOUNTS_BY_BACKOFFICE,
			[
				'chavebackoffice' => $backofficeKey,
			],
			[]
		);

		if ($response[0]['Entidade'] == '' && empty($response[0]['SubEntidade'])) {
			return [];
		}

		return $response;
	}



	public static function getGatewayKeysByBackofficKey(string $backofficeKey): array
	{
		try {

			$response = IfthenpayHttpClient::post(
				Config::API_URL_GET_GATEWAY_KEYS,
				[
					'backofficekey' => $backofficeKey,
				],
				[]
			);

			if (!isset($response[0]['Alias']) || !isset($response[0]['GatewayKey']) || !isset($response[0]['Tipo'])) {
				return [];
			}
		} catch (\Throwable $th) {
			IfthenpayLog::error('general_logs', 'Error getting gateway keys', $th->__toString());
		}

		return $response;
	}



	public static function handlePaymentMethodCallback(array $params): void
	{
		// PM_BOILERPLATE

		if (!isset($params['pm'])) {
			IfthenpayLog::info('general_logs', 'handlePaymentMethodCallback - Invalid request params "pm". ERROR code: ' . Config::CB_ERROR_INVALID_PARAMS);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_PARAMS);
		}

		if (strcasecmp($params['pm'], Config::MULTIBANCO_DYNAMIC) === 0 || is_numeric($params['pm'])) {
			$params['pm'] = Config::MULTIBANCO;
		}

		if (isset($params[Config::CB_ORDER_ID]) && IfthenpaygatewayService::hasPaymentRecordByInvoiceId($params[Config::CB_ORDER_ID])) {
			IfthenpaygatewayService::handleCallback($params);
			return;
		}

		switch (strtolower($params['pm'])) {
			case Config::MULTIBANCO:
				MultibancoService::handleCallback($params);
				break;
			case Config::PAYSHOP:
				PayshopService::handleCallback($params);
				break;
			case Config::MBWAY:
				MbwayService::handleCallback($params);
				break;
			case Config::CCARD:
				CcardService::handleCallback($params);
				break;
			case Config::COFIDIS:
				CofidisService::handleCallback($params);
				break;
			case Config::PIX:
				PixService::handleCallback($params);
				break;



			default:
				throw new \Exception("Error", Config::CB_ERROR_INVALID_PAYMENT_METHOD);
				break;
		}
	}
}
