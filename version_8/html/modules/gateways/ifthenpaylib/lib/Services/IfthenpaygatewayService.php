<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpaylib\Services;

use Smarty;
use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Config\Sql;
use WHMCS\Module\Gateway\ifthenpaylib\Lang\IftpLang;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpayHttpClient;
use WHMCS\Module\Gateway\ifthenpaylib\Repository\IfthenpaygatewayRepository;
use WHMCS\Module\GatewaySetting;
use WHMCS\Module\Gateway\ifthenpaylib\Repository\OrderRepository;

class IfthenpaygatewayService
{

	/**
	 * get ifthenpaygateway keys by backoffice key
	 * @return array
	 */
	public static function getKeysByBackofficKey(string $backofficeKey): mixed
	{
		try {

			$accounts = IfthenpayService::getAccountsByBackofficKey($backofficeKey);
			$gatewayKeys = IfthenpayService::getGatewayKeysByBackofficKey($backofficeKey);

			if (empty($accounts) || (!$accounts[0]['Entidade'] && empty($accounts[0]['SubEntidade']))) {
				return false; // invalid key
			}

			if (!empty($gatewayKeys)) {

				$gatewayData = [];
				foreach ($gatewayKeys as $gateway) {
					$gatewayData[] = [
						'alias' => $gateway['Alias'],
						'gatewayKey' => $gateway['GatewayKey'],
						'type' => $gateway['Tipo'] == 'Dinâmicas' ? 'dynamic' : 'static'
					];
				}

				return $gatewayData;
			}

			return $accounts;
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::IFTHENPAYGATEWAY, 'Error getting keys by backofficeKey', $th->__toString());
		}
	}



	public static function bulkActivateCallback(string $backofficeKey, string $storedPaymentMethods, array $paymentMethods, bool $forceActivation = false): void
	{
		try {
			if ($forceActivation) {
				$storedPaymentMethods = [];
			} else {
				$storedPaymentMethods = $storedPaymentMethods != '' ? json_decode($storedPaymentMethods, true) : [];
			}

			$paymentMethodsToActivate = [];

			if (
				empty($storedPaymentMethods)
			) {
				$paymentMethodsToActivate = array_filter($paymentMethods, function ($item) {
					return $item['is_active'] === '1';
				});
			} else {
				foreach ($paymentMethods as $key => $paymentMethod) {

					if (
						(!isset($storedPaymentMethods[$key]['is_active']) && $paymentMethod['is_active'] === '1') ||
						(isset($storedPaymentMethods[$key]) && $storedPaymentMethods[$key]['is_active'] === '0' && $paymentMethod['is_active'] === '1') ||
						(!isset($storedPaymentMethods[$key]) && $paymentMethod['is_active'] === '1')
					) {
						$paymentMethodsToActivate[$key] = $paymentMethod;
					}
				}
			}

			if (!empty($paymentMethodsToActivate)) {

				$callbackUrl = self::generateCallbackUrl();
				$antiPhishingKey = GatewaySetting::getValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_ANTIPHISHING_KEY) ?? '';
				$antiPhishingKey = ($antiPhishingKey != '' || !$forceActivation) ? $antiPhishingKey : md5((string) rand());

				$requestResult = true;
				foreach ($paymentMethodsToActivate as $key => $paymentMethod) {

					$paymentMethodEntitySubentity = explode('|', $paymentMethod['account']);
					$paymentMethodEntity = trim($paymentMethodEntitySubentity[0]);
					$paymentMethodSubEntity = trim($paymentMethodEntitySubentity[1]);

					$requestResult = $requestResult && IfthenpayService::requestCallbackActivation($backofficeKey, $paymentMethodEntity, $paymentMethodSubEntity, $antiPhishingKey, $callbackUrl);

					if ($requestResult) {
						IfthenpayLog::info(Config::IFTHENPAYGATEWAY, 'bulkActivateCallback - activated ' . $key . ' callback for ', $paymentMethod);
					}
				}

				if ($requestResult) {
					GatewaySetting::setValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_CALLBACK_STATUS, $requestResult ? 'on' : 'off');
					GatewaySetting::setValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_ANTIPHISHING_KEY, $requestResult ? $antiPhishingKey : '');
					GatewaySetting::setValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_CALLBACK_URL, $requestResult ? $callbackUrl : '');
				}
			}
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::IFTHENPAYGATEWAY, 'bulkActivateCallback - Error occurred', $th->__toString());
		}
	}



	public static function generateCallbackUrl(): string
	{
		$str = UtilsService::getSystemUrl() . 'modules/gateways/callback/ifthenpaygateway.php' . Config::IFTHENPAYGATEWAY_CALLBACK_STRING;
		$str = str_replace('{ec}', 'wh_' . UtilsService::getWHMCSVersion(), $str);
		$str = str_replace('{mv}', Config::MODULE_VERSION, $str);

		return $str;
	}



	public static function clearCallback(): void
	{
		GatewaySetting::setValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_CALLBACK_STATUS, 'off');
		GatewaySetting::setValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_ANTIPHISHING_KEY, '');
		GatewaySetting::setValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_CALLBACK_URL,  '');
	}



	public static function generatePayment(array $params): array
	{
		$gatewayKey = GatewaySetting::getValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_IFTHENPAYGATEWAY_KEY) ?? '';

		if ($gatewayKey == '') {
			throw new \Exception("Error generating payment, missing account key.", 1);
		}

		// Note: invoiceid is assigned as orderId
		$orderId = (string) $params['invoiceid'];

		$paymentDetails = [
			'order_id' => $orderId,
			'status' => Config::RECORD_STATUS_PENDING,
		];

		$deadline = GatewaySetting::getValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_DEADLINE) ?? '';

		$methodsStr = '';


		$methodArr = GatewaySetting::getValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_IFTHENPAYGATEWAY_PAYMENT_METHODS) ?? '';
		$methodArr = $methodArr != '' ? json_decode($methodArr, true) : [];

		$methodsStr = '';
		foreach ($methodArr as $key => $value) {
			if ($value != null && $value['is_active'] === '1') {

				$methodsStr .= str_replace(' ', '', $value['account']) . ';';
			}
		}

		$defaultMethod = GatewaySetting::getValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_IFTHENPAYGATEWAY_DEFAULT_PAYMENT) ?? '';
		$btnCloseLabel = GatewaySetting::getValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_IFTHENPAYGATEWAY_CLOSE_BTN_LABEL) ?? '';
		$description = GatewaySetting::getValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_IFTHENPAYGATEWAY_DESCRIPTION) ?? '';


		$result = self::getNewPayment($orderId, $params['amount'], $gatewayKey, $deadline, $methodsStr, $defaultMethod, $btnCloseLabel, $description);

		if (empty($result)) {
			throw new \Exception("Error Generating Payment", 1);
		}

		$paymentDetails['amount'] = $params['amount'];
		$paymentDetails['payment_url'] = $result['paymentUrl'];
		$paymentDetails['deadline'] = $result['deadline'];
		$paymentDetails['transaction_id'] = $result['transaction_id'];

		return $paymentDetails;
	}



	public static function generatePlaceholderPayment(array $params): array
	{
		$deadline = UtilsService::dateAfterDays(GatewaySetting::getValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_DEADLINE));

		return [
			'order_id' => $params['invoiceid'],
			'amount' => $params['amount'],
			'deadline' => $deadline,
			'status' => Config::RECORD_STATUS_INITIALIZED,
		];
	}

	
	public static function savePlaceholderPaymentIfNotFound(array $params): void
	{
		if(!empty(IfthenpaygatewayRepository::getPaymentRecordByInvoiceId((string) $params['invoiceid']))){
			return;
		}

		$deadline = UtilsService::dateAfterDays(GatewaySetting::getValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_DEADLINE), 'd-m-Y');

		$placeholerPaymentData = [
			'order_id' => $params['invoiceid'],
			'amount' => $params['amount'],
			'deadline' => $deadline,
			'status' => Config::RECORD_STATUS_INITIALIZED,
		];

		self::savePayment($placeholerPaymentData);
	}



	private static function getNewPayment(string $orderId, string $amount, string $key, string $deadline, string $methodsStr, string $defaultMethod, string $btnCloseLabel, string $description)
	{
		try {
			$payload = [
				'description' => $description,
				'lang' => IftpLang::getLangCode(),
				'amount' => $amount,
				'id' => $orderId,
				'accounts' => $methodsStr,
				'selected_method' => $defaultMethod,
				'btnCloseUrl' => self::generateReturnUrl($orderId, 'success'),
				'success_url' => self::generateReturnUrl($orderId, 'success'),
				'cancel_url' => self::generateReturnUrl($orderId, 'cancel'),
				'error_url' => self::generateReturnUrl($orderId, 'error'),
			];

			if ($btnCloseLabel) {
				$payload['btnCloseLabel'] = $btnCloseLabel;
			}

			if ($deadline != '') {
				$payload['expiredate'] = self::convertDaysToDate($deadline);
				$deadlineDate = date('d-m-Y', strtotime($payload['expiredate']));
			}

			$response = IfthenpayHttpClient::post(
				Config::API_URL_IFTHENPAYGATEWAY_SET_REQUEST . $key,
				$payload
			);

			if (isset($response['PinCode']) && $response['PinCode'] != '') {

				$paymentDetails['paymentUrl'] = $response['RedirectUrl'];
				$paymentDetails['deadline'] = $deadlineDate;
				$paymentDetails['transaction_id'] = UtilsService::generateTransactionId($orderId);

				IfthenpayLog::info(Config::IFTHENPAYGATEWAY, 'Ifthenpaygateway payment generated with success.', ['payload' => $payload, 'response' => $response]);

				return $paymentDetails;
			}
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::IFTHENPAYGATEWAY, 'Unexpected error generating payment ', $th->__toString());
		}
		return [];
	}



	private static function convertDaysToDate(string $deadline): string
	{
		if ($deadline === '0' || $deadline === '') {
			return '';
		}
		return (new \DateTime(date("Ymd")))->modify('+' . $deadline . 'day')->format('Ymd');
	}



	public static function savePayment(array $data): void
	{
		IfthenpaygatewayRepository::savePayment($data);
	}



	public static function resetConfig(): void
	{
		IfthenpaygatewayRepository::resetConfig();
	}



	public static function getPaymentRecordByRequest(array $request)
	{
		$paymentRecord = [];

		if (empty($paymentRecord) && isset($request[Config::CB_ORDER_ID]) && $request[Config::CB_ORDER_ID] != '') {
			$paymentRecord = IfthenpaygatewayRepository::getPaymentRecordByInvoiceId($request[Config::CB_ORDER_ID]);
		}

		return $paymentRecord;
	}



	public static function hasPaymentRecordByInvoiceId(string $invoiceId): bool
	{
		return !empty(IfthenpaygatewayRepository::getPaymentRecordByInvoiceId($invoiceId));
	}



	public static function getPaymentRecordByInvoiceId(string $invoiceId): array
	{
		return IfthenpaygatewayRepository::getPaymentRecordByInvoiceId($invoiceId);
	}



	public static function getPaymentDetailsHtml(): string
	{
		$templateVars = [
			'stylesPath' => UtilsService::addCacheBuster(UtilsService::pathToAssetCss('frontStyles.css')),

			'paymentMethod' => IftpLang::trans('ifthenpaygateway'),
			'payBy' => IftpLang::trans('pay_by'),
			'paymentLogo' => GatewaySetting::getValue(Config::IFTHENPAYGATEWAY, Config::CF_IFTHENPAYGATEWAY_FRONT_ICON),
			'paymentProcessCompleted' => IftpLang::trans('payment_process_completed'),
			'waitForVerification' => IftpLang::trans('wait_for_payment_verification')
		];

		$smarty = new Smarty;
		$smarty->assign($templateVars);

		return $smarty->fetch(ROOTDIR . '/modules/gateways/ifthenpaylib/lib/templates/paymentDetails/ifthenpaygateway.tpl');
	}



	public static function getPaymentFormHtml($invoiceId): string
	{
		$templateVars = [
			'stylesPath' => UtilsService::addCacheBuster(UtilsService::pathToAssetCss('frontStyles.css')),
			'jsFilePath' => UtilsService::addCacheBuster(UtilsService::pathToAssetJs('ifthenpaygatewayInvoice.js')),

			'paymentMethod' => IftpLang::trans(Config::IFTHENPAYGATEWAY_MODULE_CODE),
			'payBtn' => IftpLang::trans('pay_btn'),
			'payBy' => IftpLang::trans('pay_by'),
			'paymentLogoStr' => GatewaySetting::getValue(Config::IFTHENPAYGATEWAY, Config::CF_IFTHENPAYGATEWAY_FRONT_ICON),
			'payBtn' => IftpLang::trans('pay'),
			'invoiceId' => $invoiceId,
		];

		// load template
		$smarty = new Smarty;

		$smarty->assign($templateVars);

		return $smarty->fetch(ROOTDIR . '/modules/gateways/ifthenpaylib/lib/templates/paymentForms/ifthenpaygateway.tpl');
	}



	public static function handleCallback(array $request): void
	{
		$settings = GatewaySetting::getForGateway(Config::IFTHENPAYGATEWAY_MODULE_CODE);

		self::validateCallback($request, $settings);

		$storedRecord = self::getPaymentRecordByRequest($request);
		$invoiceId = $storedRecord['order_id'];
		$paymentAmount = $request[Config::CB_AMOUNT];
		$paymentFee = Config::USE_FEE ? $request[Config::CB_FEE] ?? '' : '';

		if (!(isset($request[Config::CB_TRANSACTION_ID]) && $request[Config::CB_TRANSACTION_ID] != '' && $request[Config::CB_TRANSACTION_ID] != '[REQUEST_ID]')) {
			$transactionId = $storedRecord['transaction_id'];
		} else {
			$transactionId = $request[Config::CB_TRANSACTION_ID];
		}
		// Start - WHMCS native payment logic 
		// Validate Callback Invoice ID.
		$invoiceId = checkCbInvoiceID($invoiceId, $settings['name']);
		// Check Callback Transaction ID.
		checkCbTransID($transactionId);
		// Log Transaction.
		logTransaction($settings['name'], $request, 'success');
		// Add Invoice Payment
		addInvoicePayment(
			$invoiceId,
			$transactionId,
			$paymentAmount,
			$paymentFee,
			Config::IFTHENPAYGATEWAY_MODULE_CODE
		);

		// DEV_NOTE: could replace the gateway name, but currently not enough information on its side-effects.
		// replace
		// Config::IFTHENPAYGATEWAY_MODULE_CODE
		// with
		// Config::IFTHENPAYGATEWAY_MODULE_CODE . '(' .$request[Config::CB_PAYMENT_METHOD]. ')'

		// End - WHMCS native payment logic 

		self::updateRecordStatus($storedRecord['order_id'], Config::RECORD_STATUS_PAID);
	}



	public static function updateRecordStatus(string $orderId, string $status): void
	{
		IfthenpaygatewayRepository::updateRecordStatus($orderId, $status);
	}



	public static function validateCallbackAntiphishingKey(array $request)
	{
		$antiPhishingKey = GatewaySetting::getValue(Config::IFTHENPAYGATEWAY_MODULE_CODE, Config::CF_ANTIPHISHING_KEY) ?? '';
		// is valid antiphishingkey
		if (!(isset($request[Config::CB_ANTIPHISHING_KEY]) && $antiPhishingKey == $request[Config::CB_ANTIPHISHING_KEY])) {
			IfthenpayLog::info(Config::IFTHENPAYGATEWAY, 'Invalid anti-phishing key. ERROR code: ' . Config::CB_ERROR_INVALID_ANTIPHISHING_KEY, ['request' => $request, 'antiPhishingKey' => $antiPhishingKey]);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_ANTIPHISHING_KEY);
		}
	}



	public static function validateCallback(array $request, array $settings)
	{
		// has required params
		if (
			(!isset($request[Config::CB_ANTIPHISHING_KEY]) || $request[Config::CB_ANTIPHISHING_KEY] == '') &&
			(!isset($request[Config::CB_PAYMENT_METHOD]) || $request[Config::CB_PAYMENT_METHOD] == '') &&
			(!isset($request[Config::CB_AMOUNT]) || $request[Config::CB_AMOUNT] == '') &&
			(!isset($request[Config::CB_ORDER_ID]) || $request[Config::CB_ORDER_ID] == '')
		) {
			IfthenpayLog::info(Config::IFTHENPAYGATEWAY, 'validateCallback - Invalid request params. ERROR code: ' . Config::CB_ERROR_INVALID_PARAMS);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_PARAMS);
		}

		// is method configured
		if (!$settings['type']) {
			IfthenpayLog::info(Config::IFTHENPAYGATEWAY, 'validateCallback - Unconfigured method. ERROR code: ' . Config::CB_ERROR_UNCONFIGURED_METHOD);
			throw new \Exception("Error", Config::CB_ERROR_UNCONFIGURED_METHOD);
		}

		// has ifthenpay record?
		$storedData = self::getPaymentRecordByRequest($request);
		if (empty($storedData)) {
			IfthenpayLog::info(Config::IFTHENPAYGATEWAY, 'validateCallback - StoredPaymentData not found in local table. ERROR code: ' . Config::CB_ERROR_RECORD_NOT_FOUND, ['request' => $request]);
			throw new \Exception("Error", Config::CB_ERROR_RECORD_NOT_FOUND);
		}

		// is payment method one of the list
		if (!(isset($request[Config::CB_PAYMENT_METHOD]) && strpos(implode(' ', Config::PAYMENT_METHODS_ARRAY), strtolower($request[Config::CB_PAYMENT_METHOD])) !== false)) {
			IfthenpayLog::info(Config::IFTHENPAYGATEWAY, 'validateCallback - Invalid payment method. ERROR code: ' . Config::CB_ERROR_INVALID_PAYMENT_METHOD, ['request' => $request, 'paymentList' => Config::PAYMENT_METHODS_ARRAY]);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_PAYMENT_METHOD);
		}

		// is callback active
		if (!(isset($settings[Config::CF_CALLBACK_STATUS]) && $settings[Config::CF_CALLBACK_STATUS] == 'on')) {
			IfthenpayLog::info(Config::IFTHENPAYGATEWAY, 'validateCallback - Callback is not active. ERROR code: ' . Config::CB_ERROR_CALLBACK_NOT_ACTIVE, ['request' => $request]);
			throw new \Exception("Error", Config::CB_ERROR_CALLBACK_NOT_ACTIVE);
		}

		// already paid?
		if ($storedData['status'] == Config::RECORD_STATUS_PAID) {
			IfthenpayLog::info(Config::IFTHENPAYGATEWAY, 'validateCallback - Order already paid. ERROR code: ' . Config::CB_ERROR_ALREADY_PAID, ['request' => $request, 'storedData' => $storedData]);
			throw new \Exception("Error", Config::CB_ERROR_ALREADY_PAID);
		}


		// has order record
		$order = OrderRepository::getOrderByInvoiceId($storedData['order_id']);

		if (empty($order)) {
			IfthenpayLog::info(Config::IFTHENPAYGATEWAY, 'validateCallback - Order not found. ERROR code: ' . Config::CB_ERROR_ORDER_NOT_FOUND, ['request' => $request, 'storedData' => $storedData]);
			throw new \Exception("Error", Config::CB_ERROR_ORDER_NOT_FOUND);
		}


		// has valid amount
		$orderAmount = floatval($order['amount'] ? $order['amount'] : $order['total']);
		$requestAmount = floatval($request[Config::CB_AMOUNT] ?? 0); // defaults to zero if missing
		if (round($orderAmount, 2) !== round($requestAmount, 2)) {
			IfthenpayLog::info(Config::IFTHENPAYGATEWAY, 'validateCallback - Invalid amount. ERROR code: ' . Config::CB_ERROR_INVALID_AMOUNT, ['request' => $request, 'orderAmount' => $orderAmount]);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_AMOUNT);
		}
	}



	public static function cancelExpiredPayments()
	{

		try {

			$pendingPayments = IfthenpaygatewayRepository::getPendingPayments();

			foreach ($pendingPayments as $pendingPayment) {

				if (self::isBeyondDeadline($pendingPayment)) {

					// cancel order
					OrderRepository::updateInvoiceStatusById($pendingPayment['order_id'], Config::INVOICE_STATUS_CANCELLED);

					// update record
					IfthenpaygatewayRepository::updateRecordStatus($pendingPayment['order_id'], 'canceled');

					IfthenpayLog::info('cron', 'Ifthenpaygateway payment expired, invoice status updated to Cancelled: ' . $pendingPayment['order_id']);
				}
			}
		} catch (\Throwable $th) {
			IfthenpayLog::error('cron', 'Error executing Cancel cronjob: ' . $th->__toString());
		}
	}



	private static function isBeyondDeadline(array $pendingPayment): bool
	{
		$deadline = $pendingPayment['deadline'] ?? '';

		if ($deadline != '') {

			$timezone = new \DateTimeZone('Europe/Lisbon');

			$deadline = \DateTime::createFromFormat('d-m-Y', $deadline, $timezone);
			$deadline->setTime(0, 0);
			$deadlineStr = $deadline->format('Y-m-d H:i:s');

			$currentDateTime = new \DateTime('now', $timezone);
			$currentDateTimeStr = $currentDateTime->format('Y-m-d H:i:s');

			return strtotime($deadlineStr) < strtotime($currentDateTimeStr);
		}

		return false;
	}



	public static function getKeySelectHtml($settings)
	{
		if (!isset($settings[Config::CF_BACKOFFICE_KEY])) {
			return '<select name="field[key]" class="form-control select-inline ifthenpay_w_400"></select>';
		}

		$options = '';

		if (isset($settings[Config::CF_ACCOUNTS]) && $settings[Config::CF_ACCOUNTS] != '') {
			$accountsArray = json_decode($settings[Config::CF_ACCOUNTS], true);

			foreach ($accountsArray as $account) {
				$selectedStr = $account['gatewayKey'] == $settings[Config::CF_IFTHENPAYGATEWAY_KEY] ? 'selected="selected"' : '';
				$options .= '<option value="' . $account['gatewayKey'] . '" data="' . $account['type'] . '" ' . $selectedStr . '>' . $account['alias'] . '</option>';
			}
		}

		$select = '<select name="field[key]" class="form-control select-inline ifthenpay_w_400">' . $options . '</select>';

		return $select;
	}



	private static function isGatewayKeyDynamic(array $paymentMethodGroupArray, string $gatewayKey): bool
	{
		foreach ($paymentMethodGroupArray as $paymentMethodGroup) {
			if (
				isset($paymentMethodGroup['type']) &&
				isset($paymentMethodGroup['gatewayKey']) &&
				$paymentMethodGroup['gatewayKey'] === $gatewayKey &&
				$paymentMethodGroup['type'] === 'dynamic'
			) {
				return true;
			}
		}
		return false;
	}




	public static function getDefaultPaymentMethodSelectHtml($settings): string
	{
		if (!isset($settings[Config::CF_IFTHENPAYGATEWAY_KEY]) || !isset($settings[Config::CF_BACKOFFICE_KEY])) {
			return '<p>' . IftpLang::trans('ifthenpaygateway_select_a_gateway_key') . '</p>';
		}

		$paymentMethodGroupArray = self::getPaymentMethodsByBackofficeKeyAndGatewayKey($settings[Config::CF_BACKOFFICE_KEY], $settings[Config::CF_IFTHENPAYGATEWAY_KEY]);

		$storedDefaultPaymentMethod = GatewaySetting::getValue(Config::IFTHENPAYGATEWAY, Config::CF_IFTHENPAYGATEWAY_DEFAULT_PAYMENT) ?? '';

		$storedMethods = GatewaySetting::getValue(Config::IFTHENPAYGATEWAY, Config::CF_IFTHENPAYGATEWAY_PAYMENT_METHODS) ?? '';
		$storedMethods = $storedMethods != '' ? json_decode($storedMethods, true) : [];


		$html = '';

		$index = 0;
		$accountOptions = '<option value="' . $index . '">' . IftpLang::trans('none') . '</option>';

		foreach ($paymentMethodGroupArray as $paymentMethodGroup) {
			$index++;

			$isDisabled = '';

			if (
				empty($paymentMethodGroup['accounts']) ||
				(!empty($storedMethods) && (!isset($storedMethods[$paymentMethodGroup['Entity']]['is_active']) || $storedMethods[$paymentMethodGroup['Entity']]['is_active'] != '1'))
			) {
				$isDisabled = 'disabled';
			}

			$selectedStr = $index == $storedDefaultPaymentMethod ? 'selected' : '';

			$accountOptions .= '<option value="' . $index . '" data-method="' . $paymentMethodGroup['Entity'] . '" ' . $selectedStr . ' ' . $isDisabled . '>' . $paymentMethodGroup['Method'] . '</option>';
		}


		$html = '<select name="field[' . Config::CF_IFTHENPAYGATEWAY_DEFAULT_PAYMENT . ']" class="form-control select-inline ifthenpay_w_400">
			' . $accountOptions . '
		</select>';

		return $html;
	}


	public static function getPaymentMethodsSelectHtml(array $settings): string
	{
		if (!isset($settings[Config::CF_IFTHENPAYGATEWAY_KEY]) || !isset($settings[Config::CF_BACKOFFICE_KEY])) {
			return '<p>' . IftpLang::trans('ifthenpaygateway_select_a_gateway_key') . '</p>';
		}

		self::getKeysByBackofficKey($settings[Config::CF_BACKOFFICE_KEY]);
		$paymentMethodGroupArray = self::getPaymentMethodsByBackofficeKeyAndGatewayKey($settings[Config::CF_BACKOFFICE_KEY], $settings[Config::CF_IFTHENPAYGATEWAY_KEY]);

		$storedGatewayKey = GatewaySetting::getValue(Config::IFTHENPAYGATEWAY, Config::CF_IFTHENPAYGATEWAY_KEY) ?? '';

		$ifthenpaygatewayKeys = GatewaySetting::getValue(Config::IFTHENPAYGATEWAY, Config::CF_ACCOUNTS) ?? '';
		$ifthenpaygatewayKeys = $ifthenpaygatewayKeys != '' ? json_decode($ifthenpaygatewayKeys, true) : [];

		if (empty($ifthenpaygatewayKeys)) {
			$ifthenpaygatewayKeys = self::getKeysByBackofficKey($settings[Config::CF_BACKOFFICE_KEY]);
		}

		$isStaticGatewayKey = !self::isGatewayKeyDynamic($ifthenpaygatewayKeys, $settings[Config::CF_IFTHENPAYGATEWAY_KEY]);

		$storedMethods = GatewaySetting::getValue(Config::IFTHENPAYGATEWAY, Config::CF_IFTHENPAYGATEWAY_PAYMENT_METHODS) ?? '';
		$storedMethods = $storedMethods != '' ? json_decode($storedMethods, true) : [];


		$html = '';

		$placeHolderAccounts = [];

		foreach ($paymentMethodGroupArray as $paymentMethodGroup) {

			if (! $paymentMethodGroup['IsVisible']) {
				continue;
			}

			$hiddenSelectReplacementStr = '';

			$accountOptions = '';
			$account = [];

			$entity = $paymentMethodGroup['Entity']; // unique identifier code like 'MB' or 'MULTIBANCO'
			$imgUrl = $paymentMethodGroup['SmallImageUrl'];

			$index = 0;
			foreach ($paymentMethodGroup['accounts'] as $account) {

				if ($index === 0 && empty($storedMethods)) {
					if (isset($account['SubEntidade'])) {
						$placeHolderAccounts[$entity] = [
							'account' => $account['Conta'],
							'is_active' => '1',
							'image_url' => $imgUrl,
						];
					}
				}
				$index++;

				// set selected payment method key
				$selectedStr = '';
				if ($storedGatewayKey == $settings[Config::CF_IFTHENPAYGATEWAY_KEY] && isset($storedMethods[$entity]['account'])) {
					$selectedStr = $account['Conta'] == $storedMethods[$entity]['account'] ? 'selected' : '';
					$hiddenSelectReplacementStr = $isStaticGatewayKey ? '<input type="hidden" name="paymentMethods[' . $paymentMethodGroup['Entity'] . '][account]" value="' . $account['Conta'] . '" />' : '';
				}

				$accountOptions .= '<option value="' . $account['Conta'] . '" ' . $selectedStr . '>' . $account['Alias'] . '</option>';
			}


			$checkDisabledStr = $accountOptions === '' ? 'disabled' : '';
			$selectDisabledStr = ($accountOptions === '' || $isStaticGatewayKey) ? 'disabled' : '';
			$checkedStr = '';


			if ($accountOptions !== '') {
				// show method account select

				$selectOrActivate = '<select ' . $selectDisabledStr . ' name="paymentMethods[' . $paymentMethodGroup['Entity'] . '][account]" id="' . $paymentMethodGroup['Entity'] . '" class="form-control" data-img_url="' . $imgUrl . '">
					' . $accountOptions . '
				</select>';

				// if the isActive is saved use it
				$checkedStr = (isset($storedMethods[$entity]['is_active']) && $storedMethods[$entity]['is_active'] == '1') || !$storedMethods || $storedGatewayKey != $settings[Config::CF_IFTHENPAYGATEWAY_KEY] ? 'checked' : '';
			} else {

				// show request button
				$selectOrActivate = '<button class="ifthenpay_new_method_btn" type="button" title="request payment method" data-method="' . $paymentMethodGroup['Entity'] . '">
                        ' . IftpLang::trans('btn_request_gateway_method') . '
                    </button>';
			}

			$html .= '<div class="method_line" data-method="' . $paymentMethodGroup['Entity'] . '" >
			<div class="method_checkbox">
				<label>
					<input type="checkbox" name="paymentMethods[' . $paymentMethodGroup['Entity'] . '][is_active]" value="1" ' . $checkedStr . ' ' . $checkDisabledStr . ' data-method="' . $paymentMethodGroup['Entity'] . '" class="method_checkbox_input"/>
					<img src="' . UtilsService::addCacheBuster($paymentMethodGroup['ImageUrl']) . '" alt="' . $paymentMethodGroup['Method'] . '" title="' . $paymentMethodGroup['Method'] . '"/>
				</label>
			</div>
			<div class="method_select">
				' . $selectOrActivate . '
			</div>
			<input type="hidden" name="paymentMethods[' . $paymentMethodGroup['Entity'] . '][logo_url]" value="' . $paymentMethodGroup['ImageUrl'] . '" />
			<input type="hidden" name="paymentMethods[' . $paymentMethodGroup['Entity'] . '][small_logo_url]" value="' . $paymentMethodGroup['SmallImageUrl'] . '" />
			' . $hiddenSelectReplacementStr . '
		</div>';
		}
		return $html;
	}


	public static function getPaymentMethodsByBackofficeKeyAndGatewayKey(string $backofficeKey, string $gatewayKey): array
	{
		try {
			$availablePaymentMethods = IfthenpayHttpClient::get(Config::API_URL_GET_IFTHENPAY_AVAILABLE_METHODS);
			if (empty($availablePaymentMethods)) {
				return [];
			}

			$gatewayAccounts = IfthenpayHttpClient::post(
				Config::API_URL_ACCOUNTS_OF_GATEWAY_KEY,
				[
					'backofficekey' => $backofficeKey,
					'gatewayKey' => $gatewayKey
				],
				['Content-Type' => ' application/x-www-form-urlencoded']
			);


			if (empty($gatewayAccounts)) {
				return [];
			}

			foreach ($availablePaymentMethods as &$method) {

				$methodCode = $method['Entity'];
				$filteredAccounts = array_filter($gatewayAccounts, function ($item) use ($methodCode) {
					return $item['Entidade'] === $methodCode || ($methodCode === 'MB' && is_numeric($item['Entidade']));
				});

				$method['accounts'] = $filteredAccounts;
			}
			unset($method);

			return $availablePaymentMethods;
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::IFTHENPAYGATEWAY, 'Error requesting ifthenpay available methods', $th->__toString());
			return [];
		}
	}



	public static function generateReturnUrl(string $orderId, string $type): string
	{

		switch ($type) {
			case 'success':
				return UtilsService::getUrl("viewinvoice.php?id=" . $orderId . "&ifthenpaysuccess=true");

			case 'cancel':
			case 'error':
				return UtilsService::getUrl("viewinvoice.php?id=" . $orderId . "&paymentfailed=true");

			default:
				return UtilsService::getUrl("viewinvoice.php?id=" . $orderId . "&paymentfailed=true");
		}
	}



	public static function generateIconImageString(string $showIcon, array $paymentMethods): string
	{
		$imgStr = '';
		if ($showIcon == 'composite') { // use set of images, one of each payment method
			foreach ($paymentMethods as $key => $settings) {
				if (isset($settings['is_active']) && $settings['is_active'] == '1') {
					$imgStr .= '<img src="' .  UtilsService::addCacheBuster($settings['small_logo_url']) . '" alt="' . $key . '"  height="30" {{STYLE}} title="' . $key . '">';
				}
			}
			$lastPos = strrpos($imgStr, '{{STYLE}}');
			$imgStr = substr_replace($imgStr, '', $lastPos, strlen('{{STYLE}}'));
			$imgStr = str_replace('{{STYLE}}', 'style="padding-right:8px;"', $imgStr);
		} else if ($showIcon == 'on') { // use default image
			$imgStr = '<img src="' .  UtilsService::getUrl(UtilsService::pathToAssetImage('ifthenpaygateway_option.png')) . '" alt="' . Config::IFTHENPAYGATEWAY_NAME . '" height="30" title="' . Config::IFTHENPAYGATEWAY_NAME . '">';
		} else {
			$imgStr = '<span>' . GatewaySetting::getValue(Config::IFTHENPAYGATEWAY, 'name') . '</span>';
		}
		return $imgStr;
	}



	public static function handleDbCreateUpdate(): void
	{
		if (!Sql::hasTable(Config::IFTHENPAYGATEWAY_TABLE)) {
			Sql::createIfthenpaygatewayTable();
			return;
		}
	}
}
