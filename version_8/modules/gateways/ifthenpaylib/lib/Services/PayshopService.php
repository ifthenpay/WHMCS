<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpaylib\Services;

use Smarty;
use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Config\Sql;
use WHMCS\Module\Gateway\ifthenpaylib\Lang\IftpLang;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpayHttpClient;
use WHMCS\Module\Gateway\ifthenpaylib\Repository\PayshopRepository;
use WHMCS\Module\GatewaySetting;
use WHMCS\Module\Gateway\ifthenpaylib\Repository\OrderRepository;

class PayshopService
{

	/**
	 * get payshop entities by backoffice key
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
				if ($item['Entidade'] === strtoupper(Config::PAYSHOP)) {
					$keys = $item['SubEntidade'];

					foreach ($keys as $key) {

						$keysData[$key] = $key;
					}
				}
			}
			return $keysData;
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::PAYSHOP, 'Error getting keys by backofficeKey', $th->__toString());
		}
	}



	public static function activateCallback(string $backofficeKey, string $key): void
	{
		$antiPhishingKey = md5((string) rand());

		$callbackUrl = self::generateCallbackUrl();

		// save in ifthenpay server
		$requestResult = IfthenpayService::requestCallbackActivation($backofficeKey, Config::PAYSHOP, $key, $antiPhishingKey, $callbackUrl);

		// save callback status
		GatewaySetting::setValue(Config::PAYSHOP_MODULE_CODE, Config::CF_CALLBACK_STATUS, $requestResult ? 'on' : 'off');

		// save antiphishingKey and callbackurl
		GatewaySetting::setValue(Config::PAYSHOP_MODULE_CODE, Config::CF_ANTIPHISHING_KEY, $requestResult ? $antiPhishingKey : '');
		GatewaySetting::setValue(Config::PAYSHOP_MODULE_CODE, Config::CF_CALLBACK_URL, $requestResult ? $callbackUrl : '');
	}



	public static function generateCallbackUrl(): string
	{

		$str = UtilsService::getSystemUrl() . 'modules/gateways/callback/ifthenpaypayshop.php' . Config::PAYSHOP_CALLBACK_STRING;
		$str = str_replace('{ec}', 'wh_' . UtilsService::getWHMCSVersion(), $str);
		$str = str_replace('{mv}', Config::MODULE_VERSION, $str);

		return $str;
	}



	public static function clearCallback(): void
	{
		GatewaySetting::setValue(Config::PAYSHOP_MODULE_CODE, Config::CF_CALLBACK_STATUS, 'off');
		GatewaySetting::setValue(Config::PAYSHOP_MODULE_CODE, Config::CF_ANTIPHISHING_KEY, '');
		GatewaySetting::setValue(Config::PAYSHOP_MODULE_CODE, Config::CF_CALLBACK_URL,  '');
	}



	public static function shouldGeneratePayment(array $params): bool
	{
		$paymentRecord = self::getPaymentRecordByInvoiceId((string) $params['invoiceid']);

		if (empty($paymentRecord)) {
			IfthenpayLog::info(Config::PAYSHOP, 'Should create new payment.');
			return true;
		}

		if ($paymentRecord['status'] === Config::RECORD_STATUS_PAID)
		{
			IfthenpayLog::info(Config::PAYSHOP, 'shouldGeneratePayment - invoice already paid.', ['paymentRecord' => $paymentRecord]);
			return false;
		}

		if ($paymentRecord['amount'] != $params['amount']) {
			IfthenpayLog::info(Config::PAYSHOP, 'Should update existing payment.', ['newAmount' => $params['amount'], 'paymentRecord' => $paymentRecord]);
			return true;
		}

		return false;
	}



	public static function generatePayment($params)
	{

		$key = GatewaySetting::getValue(Config::PAYSHOP_MODULE_CODE, Config::CF_PAYSHOP_KEY) ?? '';

		if ($key == '') {
			throw new \Exception("Error generating payment, missing account key.", 1);
		}

		// Note: invoiceid is assigned as orderId
		$orderId = (string) $params['invoiceid'];

		$paymentDetails = [
			'order_id' => $orderId,
			'status' => 'pending',
		];

		$deadline = GatewaySetting::getValue(Config::PAYSHOP_MODULE_CODE, Config::CF_DEADLINE) ?? '';
		$result = self::getPaymentReference($orderId, $params['amount'], $key, $deadline);

		if (empty($result)) {
			throw new \Exception("Error Generating Payment", 1);
		}

		$paymentDetails['amount'] = $params['amount'];
		$paymentDetails['reference'] = $result['reference'];
		$paymentDetails['transaction_id'] = $result['request_id'];
		$paymentDetails['deadline'] = $result['deadline'];


		return $paymentDetails;
	}



	private static function getPaymentReference(string $orderId, string $amount, string $key, string $deadline)
	{
		try {
			$payload = [
				'payshopkey' => $key,
				'valor' => $amount,
				'id' => $orderId,
			];


			if ($deadline != '') {
				$payload['validade'] = self::convertDaysToDate($deadline);
				$deadlineDate = date('d-m-Y', strtotime($payload['validade']));
			}

			$response = IfthenpayHttpClient::post(
				Config::API_URL_PAYSHOP_SET_REQUEST,
				$payload
			);

			if (isset($response['RequestId']) && $response['RequestId'] != '') {


				$paymentDetails['reference'] = $response['Reference'];
				$paymentDetails['request_id'] = $response['RequestId'];
				$paymentDetails['deadline'] = $deadlineDate;


				IfthenpayLog::info(Config::PAYSHOP, 'Payshop payment reference generated with success.', ['payload' => $payload, 'response' => $response]);

				return $paymentDetails;
			}
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::PAYSHOP, 'Unexpected error generating payment reference.', $th->__toString());
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
		PayshopRepository::savePayment($data);
	}



	public static function resetConfig(): void
	{
		PayshopRepository::resetConfig();
	}



	public static function getPaymentRecordByRequest(array $request)
	{
		$paymentRecord = [];

		if (isset($request[Config::CB_TRANSACTION_ID]) && $request[Config::CB_TRANSACTION_ID] != '') {
			$paymentRecord = PayshopRepository::getPaymentRecordByTransactionId($request[Config::CB_TRANSACTION_ID]);
		}

		return $paymentRecord;
	}



	public static function getPaymentRecordByInvoiceId(string $invoiceId)
	{
		return PayshopRepository::getPaymentRecordByInvoiceId($invoiceId);
	}



	public static function getPaymentDetailsHtml($vars)
	{
		$amount = isset($vars['amount']) ?
			formatCurrency($vars['amount'], $vars['currency'] ?
				$vars['currency'] : null)->toSuffixed() : $vars['total']->toSuffixed();

		$invoiceId = $vars['invoiceid'];
		$paymentData = self::getPaymentRecordByInvoiceId((string)$invoiceId);

		$templateVars = [
			'stylesPath' => UtilsService::addCacheBuster(UtilsService::pathToAssetCss('frontStyles.css')),

			'paymentMethod' => IftpLang::trans('payshop'),
			'payWith' => IftpLang::trans('pay_with'),
			'paymentLogo' => UtilsService::addCacheBuster(UtilsService::pathToAssetImage('payshop.png')),
			'referenceLabel' => IftpLang::trans('reference_label'),
			'reference' => $paymentData['reference'],
			'deadlineLabel' => IftpLang::trans('deadline_label'),
			Config::CF_DEADLINE => $paymentData['deadline'],
			'amountLabel' => IftpLang::trans('amount_label'),
			'amount' => $amount,

		];

		$smarty = new Smarty;
		$smarty->assign($templateVars);

		return $smarty->fetch(ROOTDIR . '/modules/gateways/ifthenpaylib/lib/templates/paymentDetails/payshop.tpl');
	}



	public static function handleCallback(array $request): void
	{
		$settings = GatewaySetting::getForGateway(Config::PAYSHOP_MODULE_CODE);

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
			Config::PAYSHOP_MODULE_CODE
		);
		// End - WHMCS native payment logic 
		self::updateRecordStatus($storedRecord['order_id'], Config::RECORD_STATUS_PAID);
	}



	public static function updateRecordStatus(string $orderId, string $status): void
	{
		PayshopRepository::updateRecordStatus($orderId, $status);
	}



	public static function validateCallbackAntiphishingKey(array $request)
	{
		$antiPhishingKey = GatewaySetting::getValue(Config::PAYSHOP_MODULE_CODE, Config::CF_ANTIPHISHING_KEY) ?? '';
		// is valid antiphishingkey
		if (!(isset($request[Config::CB_ANTIPHISHING_KEY]) && $antiPhishingKey == $request[Config::CB_ANTIPHISHING_KEY])) {
			IfthenpayLog::info(Config::PAYSHOP, 'validateCallbackAntiphishingKey - Invalid anti-phishing key. ERROR code: ' . Config::CB_ERROR_INVALID_ANTIPHISHING_KEY, ['request' => $request, 'antiPhishingKey' => $antiPhishingKey]);
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
			(!isset($request[Config::CB_REFERENCE]) || $request[Config::CB_REFERENCE] == '') &&
			!isset($request[Config::CB_TRANSACTION_ID])
		) {
			IfthenpayLog::info(Config::PAYSHOP, 'validateCallback - Invalid request params. ERROR code: ' . Config::CB_ERROR_INVALID_PARAMS);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_PARAMS);
		}

		// is method configured
		if (!$settings['type']) {
			IfthenpayLog::info(Config::PAYSHOP, 'validateCallback - Unconfigured method. ERROR code: ' . Config::CB_ERROR_UNCONFIGURED_METHOD);
			throw new \Exception("Error", Config::CB_ERROR_UNCONFIGURED_METHOD);
		}

		// is payment method one of the list
		if (!(isset($request[Config::CB_PAYMENT_METHOD]) && strpos(implode(' ', Config::PAYMENT_METHODS_ARRAY), strtolower($request[Config::CB_PAYMENT_METHOD])) !== false)) {
			IfthenpayLog::info(Config::PAYSHOP, 'validateCallback - Invalid payment method. ERROR code: ' . Config::CB_ERROR_INVALID_PAYMENT_METHOD, ['request' => $request, 'paymentList' => Config::PAYMENT_METHODS_ARRAY]);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_PAYMENT_METHOD);
		}

		// is callback active
		if (!(isset($settings[Config::CF_CALLBACK_STATUS]) && $settings[Config::CF_CALLBACK_STATUS] == 'on')) {
			IfthenpayLog::info(Config::PAYSHOP, 'validateCallback - Callback is not active. ERROR code: ' . Config::CB_ERROR_CALLBACK_NOT_ACTIVE, ['request' => $request]);
			throw new \Exception("Error", Config::CB_ERROR_CALLBACK_NOT_ACTIVE);
		}


		// has ifthenpay record?
		$storedData = self::getPaymentRecordByRequest($request);
		if (empty($storedData)) {
			IfthenpayLog::info(Config::PAYSHOP, 'validateCallback - StoredPaymentData not found in local table. ERROR code: ' . Config::CB_ERROR_RECORD_NOT_FOUND, ['request' => $request]);
			throw new \Exception("Error", Config::CB_ERROR_RECORD_NOT_FOUND);
		}


		// already paid?
		if ($storedData['status'] == Config::RECORD_STATUS_PAID) {
			IfthenpayLog::info(Config::PAYSHOP, 'validateCallback - Order already paid. ERROR code: ' . Config::CB_ERROR_ALREADY_PAID, ['request' => $request, 'storedData' => $storedData]);
			throw new \Exception("Error", Config::CB_ERROR_ALREADY_PAID);
		}

		// has order record
		$order = OrderRepository::getOrderByInvoiceId($storedData['order_id']);

		if (empty($order)) {
			IfthenpayLog::info(Config::PAYSHOP, 'validateCallback - Order not found. ERROR code: ' . Config::CB_ERROR_ORDER_NOT_FOUND, ['request' => $request, 'storedData' => $storedData]);
			throw new \Exception("Error", Config::CB_ERROR_ORDER_NOT_FOUND);
		}


		// has valid amount
		$orderAmount = floatval($order['amount'] ? $order['amount'] : $order['total']);
		$requestAmount = floatval($request[Config::CB_AMOUNT] ?? 0); // defaults to zero if missing
		if (round($orderAmount, 2) !== round($requestAmount, 2)) {
			IfthenpayLog::info(Config::PAYSHOP, 'validateCallback - Invalid amount. ERROR code: ' . Config::CB_ERROR_INVALID_AMOUNT, ['request' => $request, 'orderAmount' => $orderAmount]);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_AMOUNT);
		}
	}



	public static function cancelExpiredPayments()
	{

		try {

			$pendingPayments = PayshopRepository::getPendingPayments();

			foreach ($pendingPayments as $pendingPayment) {

				if (self::isBeyondDeadline($pendingPayment)) {

					// cancel order
					OrderRepository::updateInvoiceStatusById($pendingPayment['order_id'], Config::INVOICE_STATUS_CANCELLED);

					// update record
					PayshopRepository::updateRecordStatus($pendingPayment['order_id'], 'canceled');

					IfthenpayLog::info('cron', 'Payshop payment expired, invoice status updated to Cancelled: ' . $pendingPayment['order_id']);
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



	public static function getKeyOptions($settings)
	{
		if (!isset($settings[Config::CF_BACKOFFICE_KEY]) || !isset($settings[Config::CF_ACCOUNTS])) {
			return [];
		}

		$accounts = json_decode($settings[Config::CF_ACCOUNTS], true);

		return $accounts;
	}



	public static function handleDbCreateUpdate(): void
	{		
		if (!Sql::hasTable(Config::PAYSHOP_TABLE)) {
			Sql::createPayshopTable();
			return;
		}

		$previousVersionInstalled = GatewaySetting::getValue(Config::PAYSHOP_MODULE_CODE, Config::CF_INSTALLED_MODULE_VERSION) ?? '0.0.0';
		$newVersionInstalled = Config::MODULE_VERSION;

		if (version_compare($newVersionInstalled, $previousVersionInstalled) == 0) {
			return;
		}

		// If new install, upgrading from old module or just reactivating 
		if ($previousVersionInstalled == '0.0.0' && version_compare('8.0.0', $previousVersionInstalled, '>') == 1) {
			Sql::updatePayshopTableFromVersion_0_0_0();
		}
	}
}
