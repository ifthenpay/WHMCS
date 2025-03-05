<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpaylib\Services;

use Smarty;
use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Config\Sql;
use WHMCS\Module\Gateway\ifthenpaylib\Lang\IftpLang;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpayHttpClient;
use WHMCS\Module\Gateway\ifthenpaylib\Repository\CofidisRepository;
use WHMCS\Module\GatewaySetting;
use WHMCS\Module\Gateway\ifthenpaylib\Repository\OrderRepository;

class CofidisService
{

	/**
	 * get cofidis entities by backoffice key
	 * @return array
	 */
	public static function getKeysByBackofficKey(string $backofficeKey): mixed
	{
		try {
			$accounts = IfthenpayService::getAccountsByBackofficKey($backofficeKey);

			if (empty($accounts)) {
				return false;
			}

			$keysData = [];


			foreach ($accounts as $item) {
				if ($item['Entidade'] === strtoupper(Config::COFIDIS)) {
					$keys = $item['SubEntidade'];

					foreach ($keys as $key) {

						$keysData[$key] = $key;
					}
				}
			}
			return $keysData;
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::COFIDIS, 'Error getting keys by backofficeKey', $th->__toString());
		}
	}



	public static function activateCallback(string $backofficeKey, string $key): void
	{
		$antiPhishingKey = md5((string) rand());

		$callbackUrl = self::generateCallbackUrl();

		// save in ifthenpay server
		$requestResult = IfthenpayService::requestCallbackActivation($backofficeKey, Config::COFIDIS, $key, $antiPhishingKey, $callbackUrl);

		// save callback status
		GatewaySetting::setValue(Config::COFIDIS_MODULE_CODE, Config::CF_CALLBACK_STATUS, $requestResult ? 'on' : 'off');

		// save antiphishingKey and callbackurl
		GatewaySetting::setValue(Config::COFIDIS_MODULE_CODE, Config::CF_ANTIPHISHING_KEY, $requestResult ? $antiPhishingKey : '');
		GatewaySetting::setValue(Config::COFIDIS_MODULE_CODE, Config::CF_CALLBACK_URL, $requestResult ? $callbackUrl : '');
	}



	public static function generateCallbackUrl(): string
	{

		$str = UtilsService::getSystemUrl() . 'modules/gateways/callback/ifthenpaycofidis.php' . Config::COFIDIS_CALLBACK_STRING;
		$str = str_replace('{ec}', 'wh_' . UtilsService::getWHMCSVersion(), $str);
		$str = str_replace('{mv}', Config::MODULE_VERSION, $str);

		return $str;
	}



	public static function clearCallback(): void
	{
		GatewaySetting::setValue(Config::COFIDIS_MODULE_CODE, Config::CF_CALLBACK_STATUS, 'off');
		GatewaySetting::setValue(Config::COFIDIS_MODULE_CODE, Config::CF_ANTIPHISHING_KEY, '');
		GatewaySetting::setValue(Config::COFIDIS_MODULE_CODE, Config::CF_CALLBACK_URL,  '');
	}



	public static function ifPaymentExists(string $invoiceId)
	{
		$record = self::getPaymentRecordByInvoiceId($invoiceId);

		if ($record) {
			return true;
		}
	}



	public static function generatePayment(array $params): array
	{
		$key = GatewaySetting::getValue(Config::COFIDIS_MODULE_CODE, Config::CF_COFIDIS_KEY) ?? '';

		if ($key == '') {
			throw new \Exception("Error generating payment, missing account key.", 1);
		}

		// Note: invoiceid is assigned as orderId
		$orderId = (string) $params['invoiceid'];

		$paymentDetails = [
			'order_id' => $orderId,
			'amount' => $params['amount'],
			'status' => Config::RECORD_STATUS_PENDING,
		];

		$result = self::getNewPayment($orderId, $params['amount'], $key, self::getCustomerData($params));

		if (empty($result)) {
			throw new \Exception("Error Generating Payment", 1);
		}

		$paymentDetails['transaction_id'] = $result['request_id'];
		$paymentDetails['paymentUrl'] = $result['paymentUrl'];

		return $paymentDetails;
	}



	public static function savePlaceholderPaymentIfNotFound(array $params): void
	{
		if(!empty(CofidisRepository::getPaymentRecordByInvoiceId((string) $params['invoiceid']))){
			return;
		}

		$placeholerPaymentData = [
			'order_id' => $params['invoiceid'],
			'amount' => $params['amount'],
			'status' => Config::RECORD_STATUS_INITIALIZED,
		];

		self::savePayment($placeholerPaymentData);
	}



	private static function getCustomerData(array $params): array
	{
		$customerData = [];
		if (!isset($params['clientdetails'])) {
			return $customerData;
		}

		$clientDetails = $params['clientdetails'];

		$firstName = $clientDetails['firstname'] ?? '';
		$lastName = $clientDetails['lastname'] ?? '';
		$customerData['customerName'] = preg_replace('/\s+/', ' ', $firstName . ' ' . $lastName);

		$customerData['customerEmail'] = $clientDetails['email'] ?? '';

		$customerData['customerPhone'] = $clientDetails['phonenumber'] ?? '';

		$addressLine1 = $clientDetails['address1'] ?? '';
		$addressLine2 = $clientDetails['address2'] ?? '';
		$customerData['billingAddress'] = preg_replace('/\s+/', ' ', $addressLine1 . ' ' . $addressLine2);

		$customerData['billingZipCode'] = $clientDetails['postcode'] ?? '';

		$customerData['billingCity'] = $clientDetails['city'] ?? '';

		return $customerData;
	}



	private static function getNewPayment(string $orderId, string $amount, string $key, array $customerData)
	{
		try {

			$returnUrl = UtilsService::getSystemUrl() . str_replace('[ORDER_ID]', $orderId, Config::COFIDIS_RETURN_URL_STRING);

			$payload = $customerData;

			$payload['orderId'] = $orderId;
			$payload['amount'] = $amount;
			$payload['returnUrl'] = $returnUrl;
			$payload['description'] = 'WHMCS request';


			$response = IfthenpayHttpClient::post(
				Config::API_URL_COFIDIS_SET_REQUEST . $key,
				$payload
			);

			if (isset($response['status']) && $response['status'] == '0') {

				$paymentDetails['request_id'] = $response['requestId'];
				$paymentDetails['paymentUrl'] = $response['paymentUrl'];

				IfthenpayLog::info(Config::COFIDIS, 'Cofidis payment request resulted in success', ['payload' => $payload, 'response' => $response]);

				return $paymentDetails;
			} else {
				IfthenpayLog::error(Config::COFIDIS, 'Cofidis payment request resulted in not success status', ['payload' => $payload, 'response' => $response]);
			}
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::COFIDIS, 'Unexpected error generating Cofidis payment', $th->__toString());
		}
		return [];
	}



	public static function savePayment(array $data): void
	{
		CofidisRepository::savePayment($data);
	}



	public static function resetConfig(): void
	{
		CofidisRepository::resetConfig();
	}



	public static function getPaymentRecordByRequest(array $request)
	{
		$paymentRecord = [];

		if (isset($request[Config::CB_TRANSACTION_ID]) && $request[Config::CB_TRANSACTION_ID] != '') {
			$paymentRecord = CofidisRepository::getPaymentRecordByTransactionId($request[Config::CB_TRANSACTION_ID]);
		}

		return $paymentRecord;
	}


	public static function getPaymentRecordByInvoiceId(string $invoiceId)
	{
		return CofidisRepository::getPaymentRecordByInvoiceId($invoiceId);
	}



	public static function getPaymentDetailsHtml(): string
	{
		$templateVars = [
			'stylesPath' => UtilsService::addCacheBuster(UtilsService::pathToAssetCss('frontStyles.css')),

			'paymentMethod' => IftpLang::trans('cofidis'),
			'payWith' => IftpLang::trans('pay_with'),
			'paymentLogo' => UtilsService::addCacheBuster(UtilsService::pathToAssetImage('cofidis.png')),
			'paymentProcessCompleted' => IftpLang::trans('payment_process_completed'),
			'waitForVerification' => IftpLang::trans('wait_for_payment_verification')
		];

		$smarty = new Smarty;
		$smarty->assign($templateVars);

		return $smarty->fetch(ROOTDIR . '/modules/gateways/ifthenpaylib/lib/templates/paymentDetails/cofidis.tpl');
	}



	public static function getPaymentFormHtml($invoiceId)
	{
		$templateVars = [
			'stylesPath' => UtilsService::addCacheBuster(UtilsService::pathToAssetCss('frontStyles.css')),
			'jsFilePath' => UtilsService::addCacheBuster(UtilsService::pathToAssetJs('cofidisInvoice.js')),

			'paymentMethod' => IftpLang::trans('cofidis'),
			'payBtn' => IftpLang::trans('pay_btn'),
			'payWith' => IftpLang::trans('pay_with'),
			'paymentLogo' => UtilsService::addCacheBuster(UtilsService::pathToAssetImage('cofidis.png')),
			'cofidisDescLine1' => IftpLang::trans('cofidis_desc_line_1'),
			'cofidisDescLine2' => IftpLang::trans('cofidis_desc_line_2'),
			'cofidisDescLine3' => IftpLang::trans('cofidis_desc_line_3'),
			'cofidisDescLine4' => IftpLang::trans('cofidis_desc_line_4'),
			'cofidisDescLine5' => IftpLang::trans('cofidis_desc_line_5'),
			'payBtn' => IftpLang::trans('pay'),
			'invoiceId' => $invoiceId,
		];

		// load template
		$smarty = new Smarty;

		$smarty->assign($templateVars);

		return $smarty->fetch(ROOTDIR . '/modules/gateways/ifthenpaylib/lib/templates/paymentForms/cofidis.tpl');
	}



	public static function handleCallback(array $request): void
	{
		$settings = GatewaySetting::getForGateway(Config::COFIDIS_MODULE_CODE);

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
			Config::COFIDIS_MODULE_CODE
		);
		// End - WHMCS native payment logic 

		self::updateRecordStatus($storedRecord['order_id'], Config::RECORD_STATUS_PAID);
	}



	public static function updateRecordStatus(string $orderId, string $status): void
	{
		CofidisRepository::updateRecordStatus($orderId, $status);
	}



	public static function validateCallbackAntiphishingKey(array $request)
	{
		$antiPhishingKey = GatewaySetting::getValue(Config::COFIDIS_MODULE_CODE, Config::CF_ANTIPHISHING_KEY) ?? '';
		// is valid antiphishingkey
		if (!(isset($request[Config::CB_ANTIPHISHING_KEY]) && $antiPhishingKey == $request[Config::CB_ANTIPHISHING_KEY])) {
			IfthenpayLog::info(Config::COFIDIS, 'validateCallbackAntiphishingKey() Invalid anti-phishing key. ERROR code: ' . Config::CB_ERROR_INVALID_ANTIPHISHING_KEY,  ['request' => $request, 'antiPhishingKey' => $antiPhishingKey]);
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
			!isset($request[Config::CB_TRANSACTION_ID])
		) {
			IfthenpayLog::info(Config::COFIDIS, 'validateCallback - Invalid request params. ERROR code: ' . Config::CB_ERROR_INVALID_PARAMS);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_PARAMS);
		}

		// is method configured
		if (!$settings['type']) {
			IfthenpayLog::info(Config::COFIDIS, 'validateCallback - Unconfigured method. ERROR code: ' . Config::CB_ERROR_UNCONFIGURED_METHOD);
			throw new \Exception("Error", Config::CB_ERROR_UNCONFIGURED_METHOD);
		}

		// is payment method one of the list
		if (!(isset($request[Config::CB_PAYMENT_METHOD]) && strpos(implode(' ', Config::PAYMENT_METHODS_ARRAY), strtolower($request[Config::CB_PAYMENT_METHOD])) !== false)) {
			IfthenpayLog::info(Config::COFIDIS, 'validateCallback - Invalid payment method. ERROR code: ' . Config::CB_ERROR_INVALID_PAYMENT_METHOD, ['request' => $request, 'paymentList' => Config::PAYMENT_METHODS_ARRAY]);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_PAYMENT_METHOD);
		}

		// is callback active
		if (!(isset($settings[Config::CF_CALLBACK_STATUS]) && $settings[Config::CF_CALLBACK_STATUS] == 'on')) {
			IfthenpayLog::info(Config::COFIDIS, 'validateCallback - Callback is not active. ERROR code: ' . Config::CB_ERROR_CALLBACK_NOT_ACTIVE, ['request' => $request]);
			throw new \Exception("Error", Config::CB_ERROR_CALLBACK_NOT_ACTIVE);
		}


		// has ifthenpay record?
		$storedData = self::getPaymentRecordByRequest($request);
		if (empty($storedData)) {
			IfthenpayLog::info(Config::COFIDIS, 'validateCallback - StoredPaymentData not found in local table. ERROR code: ' . Config::CB_ERROR_RECORD_NOT_FOUND, ['request' => $request]);
			throw new \Exception("Error", Config::CB_ERROR_RECORD_NOT_FOUND);
		}


		// already paid?
		if ($storedData['status'] == Config::RECORD_STATUS_PAID) {
			IfthenpayLog::info(Config::COFIDIS, 'validateCallback - Order already paid. ERROR code: ' . Config::CB_ERROR_ALREADY_PAID, ['request' => $request, 'storedData' => $storedData]);
			throw new \Exception("Error", Config::CB_ERROR_ALREADY_PAID);
		}


		// has order record
		$order = OrderRepository::getOrderByInvoiceId($storedData['order_id']);

		if (empty($order)) {
			IfthenpayLog::info(Config::COFIDIS, 'validateCallback - Order not found. ERROR code: ' . Config::CB_ERROR_ORDER_NOT_FOUND, ['request' => $request, 'storedData' => $storedData]);
			throw new \Exception("Error", Config::CB_ERROR_ORDER_NOT_FOUND);
		}


		// has valid amount
		$orderAmount = floatval($order['amount'] ? $order['amount'] : $order['total']);
		$requestAmount = floatval($request[Config::CB_AMOUNT] ?? 0); // defaults to zero if missing
		if (round($orderAmount, 2) !== round($requestAmount, 2)) {
			IfthenpayLog::info(Config::COFIDIS, 'validateCallback - Invalid amount. ERROR code: ' . Config::CB_ERROR_INVALID_AMOUNT, ['request' => $request, 'orderAmount' => $orderAmount]);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_AMOUNT);
		}
	}



	public static function cancelExpiredPayments()
	{

		try {

			$pendingPayments = CofidisRepository::getPendingPayments();

			foreach ($pendingPayments as $pendingPayment) {

				if (self::isBeyondDeadline($pendingPayment)) {

					// cancel order
					OrderRepository::updateInvoiceStatusById($pendingPayment['order_id'], Config::INVOICE_STATUS_CANCELLED);

					// update record
					CofidisRepository::updateRecordStatus($pendingPayment['order_id'], 'canceled');

					IfthenpayLog::info('cron', 'Cofidis payment expired, invoice status updated to Cancelled: ' . $pendingPayment['order_id']);
				}
			}
		} catch (\Throwable $th) {
			IfthenpayLog::error('cron', 'Error executing Cancel cronjob: ' . $th->__toString());
		}
	}



	private static function isBeyondDeadline(array $pendingPayment): bool
	{

		$createdAt = $pendingPayment['updated_at'] ?? '';

		if ($createdAt != '') {

			$timezone = new \DateTimeZone('Europe/Lisbon');

			$deadline = \DateTime::createFromFormat('Y-m-d H:i:s', $createdAt);
			$deadline->add(new \DateInterval('PT' . 60 . 'M'));
			$deadlineStr = $deadline->format('Y-m-d H:i:s');

			$currentDateTime = new \DateTime('now', $timezone);
			$currentDateTimeStr = $currentDateTime->format('Y-m-d H:i:s');

			return strtotime($deadlineStr) < strtotime($currentDateTimeStr);
		}

		return false;
	}



	public static function getKeyOptions($settings)
	{
		if (!isset($settings[Config::CF_BACKOFFICE_KEY]) || !isset($settings[Config::CF_ACCOUNTS])) {
			return [];
		}

		$accounts = json_decode($settings[Config::CF_ACCOUNTS], true);

		return $accounts;
	}



	public static function getMinMaxFromIfthenpay(string $key)
	{
		try {
			$response = IfthenpayHttpClient::get(Config::API_URL_COFIDIS_GET_MAX_MIN_AMOUNT . $key);

			if (isset($response['message']) && $response['message'] == 'success') {

				IfthenpayLog::info(Config::COFIDIS, 'Request Cofidis min max stored at ifthenpay with success result', $response);
				return $response['limits'];
			} else {
				IfthenpayLog::error(Config::COFIDIS, 'Request Cofidis min max stored at ifthenpay with error result', $response);
			}
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::COFIDIS, 'Unexpected error requesting cofidis min max stored at ifthenpay', $th->__toString());
		}
		return [];
	}



	public static function handleDbCreateUpdate(): void
	{
		if (!Sql::hasTable(Config::COFIDIS_TABLE)) {
			Sql::createCofidisTable();
			return;
		}
	}



	public static function handleReturnFromCofidis(array $request): void
	{
		$cofidisKey = GatewaySetting::getValue(Config::COFIDIS_MODULE_CODE, Config::CF_COFIDIS_KEY);
		$storedPaymentRecord = self::getPaymentRecordByInvoiceId($request['order_id']);
		$transactionId = $storedPaymentRecord['transaction_id'];
		$orderId = $storedPaymentRecord['order_id'];

		$cofidisStatus = self::getCofidisPaymentStatus($cofidisKey, $transactionId);

		if ($cofidisStatus === Config::COFIDIS_STATUS_INITIATED || $cofidisStatus === Config::COFIDIS_STATUS_PENDING_INVOICE) {
			// if SUCCESS
			$gatewayName = GatewaySetting::getValue(Config::COFIDIS_MODULE_CODE, Config::COFIDIS_NAME);
			$paymentfee = '';

			// Start - WHMCS native payment logic 
			// Validate Callback Invoice ID.
			$invoiceId = checkCbInvoiceID($orderId, $gatewayName);
			// Check Callback Transaction ID.
			checkCbTransID($transactionId);
			// Log Transaction.
			logTransaction($gatewayName, $request, 'success');
			// Add Invoice Payment
			addInvoicePayment(
				$invoiceId,
				$transactionId,
				$paymentfee,
				$storedPaymentRecord['ammount'],
				Config::COFIDIS_MODULE_CODE
			);
			// End - WHMCS native payment logic 

			IfthenpayLog::info(Config::COFIDIS, 'Return from cofidis handled with success status code', ['statusCode' => $cofidisStatus]);

			self::updateRecordStatus($orderId, Config::RECORD_STATUS_CANCELLED);
		} else {
			if ($cofidisStatus === Config::COFIDIS_STATUS_CANCELED) {
				self::updateRecordStatus($orderId, Config::RECORD_STATUS_CANCELLED);
				IfthenpayLog::error(Config::COFIDIS, 'Error in returnFromCofidis: status check resulted in CANCELLED');
				throw new \Exception("Error, Cancelled");
			}
			if ($cofidisStatus === Config::COFIDIS_STATUS_NOT_APPROVED) {
				self::updateRecordStatus($orderId, Config::RECORD_STATUS_CANCELLED);
				IfthenpayLog::error(Config::COFIDIS, 'Error in returnFromCofidis: status check resulted in NOT APPROVED');
				throw new \Exception("Error, Not Approved");
			}
			if ($cofidisStatus === Config::COFIDIS_STATUS_TECHNICAL_ERROR) {
				self::updateRecordStatus($orderId, Config::RECORD_STATUS_ERROR);
				IfthenpayLog::error(Config::COFIDIS, 'Error in returnFromCofidis: status check resulted in TECHNICAL ERROR');
				throw new \Exception("Error, Technical Error");
			}
			if ($cofidisStatus === Config::COFIDIS_STATUS_TECHNICAL_ERROR) {
				self::updateRecordStatus($orderId, Config::RECORD_STATUS_ERROR);
				IfthenpayLog::error(Config::COFIDIS, 'Error in returnFromCofidis: status check resulted in TECHNICAL ERROR');
				throw new \Exception("Error, Technical Error");
			} else {
				self::updateRecordStatus($orderId, Config::RECORD_STATUS_ERROR);
				IfthenpayLog::error(Config::COFIDIS, 'Error in returnFromCofidis: status check resulted in unexpected status');
				throw new \Exception("Error, Unexpected Error");
			}
		}
	}


	public static function getCofidisPaymentStatus(string $cofidisKey, string $transactionId): string
	{
		$payload = [
			'cofidisKey' => $cofidisKey,
			'requestId' => $transactionId,
		];
		$responseArray = [];

		// sleep 5 seconds because error, cancel, not approved may not be present right after returning with error from cofidis
		for ($i = 0; $i < 2; $i++) {

			sleep(5);
			$response = IfthenpayHttpClient::post(
				Config::API_URL_COFIDIS_GET_PAYMENT_STATUS,
				$payload
			);


			if (isset($response[0]) && isset($response[0]['statusCode'])) {
				$responseArray = $response;
			}

			if (count($response) > 1) {
				break;
			}
		}

		if (count($responseArray) < 1) {
			return 'ERROR';
		}
		if ($responseArray[0]['statusCode'] == Config::COFIDIS_STATUS_INITIATED) {
			return Config::COFIDIS_STATUS_INITIATED;
		}
		if ($responseArray[0]['statusCode'] == Config::COFIDIS_STATUS_PENDING_INVOICE) {
			return Config::COFIDIS_STATUS_PENDING_INVOICE;
		}
		if ($responseArray[0]['statusCode'] == Config::COFIDIS_STATUS_NOT_APPROVED) {
			return Config::COFIDIS_STATUS_NOT_APPROVED;
		}
		if ($responseArray[0]['statusCode'] == Config::COFIDIS_STATUS_TECHNICAL_ERROR) {
			return Config::COFIDIS_STATUS_TECHNICAL_ERROR;
		}
		if ($responseArray[0]['statusCode'] == Config::COFIDIS_STATUS_CANCELED) {
			foreach ($responseArray as $status) {
				if ($status['statusCode'] == Config::COFIDIS_STATUS_TECHNICAL_ERROR) {
					return Config::COFIDIS_STATUS_TECHNICAL_ERROR;
				}
			}
			return Config::COFIDIS_STATUS_CANCELED;
		}

		return 'ERROR';
	}
}
