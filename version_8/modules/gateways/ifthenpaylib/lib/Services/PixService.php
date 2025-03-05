<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpaylib\Services;

use Smarty;
use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Config\Sql;
use WHMCS\Module\Gateway\ifthenpaylib\Lang\IftpLang;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpayHttpClient;
use WHMCS\Module\Gateway\ifthenpaylib\Repository\PixRepository;
use WHMCS\Module\GatewaySetting;
use WHMCS\Module\Gateway\ifthenpaylib\Repository\OrderRepository;

class PixService
{

	/**
	 * get pix entities by backoffice key
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
				if ($item['Entidade'] === strtoupper(Config::PIX)) {
					$keys = $item['SubEntidade'];

					foreach ($keys as $key) {

						$keysData[$key] = $key;
					}
				}
			}
			return $keysData;
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::PIX, 'Error getting keys by backofficeKey', $th->__toString());
		}
	}



	public static function activateCallback(string $backofficeKey, string $key): void
	{
		$antiPhishingKey = md5((string) rand());

		$callbackUrl = self::generateCallbackUrl();

		// save in ifthenpay server
		$requestResult = IfthenpayService::requestCallbackActivation($backofficeKey, Config::PIX, $key, $antiPhishingKey, $callbackUrl);

		// save callback status
		GatewaySetting::setValue(Config::PIX_MODULE_CODE, Config::CF_CALLBACK_STATUS, $requestResult ? 'on' : 'off');

		// save antiphishingKey and callbackurl
		GatewaySetting::setValue(Config::PIX_MODULE_CODE, Config::CF_ANTIPHISHING_KEY, $requestResult ? $antiPhishingKey : '');
		GatewaySetting::setValue(Config::PIX_MODULE_CODE, Config::CF_CALLBACK_URL, $requestResult ? $callbackUrl : '');
	}



	public static function generateCallbackUrl(): string
	{

		$str = UtilsService::getSystemUrl() . 'modules/gateways/callback/ifthenpaypix.php' . Config::PIX_CALLBACK_STRING;
		$str = str_replace('{ec}', 'wh_' . UtilsService::getWHMCSVersion(), $str);
		$str = str_replace('{mv}', Config::MODULE_VERSION, $str);

		return $str;
	}



	public static function clearCallback(): void
	{
		GatewaySetting::setValue(Config::PIX_MODULE_CODE, Config::CF_CALLBACK_STATUS, 'off');
		GatewaySetting::setValue(Config::PIX_MODULE_CODE, Config::CF_ANTIPHISHING_KEY, '');
		GatewaySetting::setValue(Config::PIX_MODULE_CODE, Config::CF_CALLBACK_URL,  '');
	}



	public static function generatePayment(array $params): array
	{
		$key = GatewaySetting::getValue(Config::PIX_MODULE_CODE, Config::CF_PIX_KEY) ?? '';

		if ($key == '') {
			throw new \Exception("Error generating payment, missing account key.");
		}

		if (!isset($params['ifthenpaypix_name']) || $params['ifthenpaypix_name'] == '' || strlen($params['ifthenpaypix_name']) > 150) {
			throw new \Exception(IftpLang::trans('msg_pix_invalid_name'));
		}

		if (!isset($params['ifthenpaypix_cpf']) || $params['ifthenpaypix_cpf'] == '' || !preg_match("/^(\d{3}\.\d{3}\.\d{3}-\d{2}|\d{11})$/", $params['ifthenpaypix_cpf'])) {
			throw new \Exception(IftpLang::trans('msg_pix_invalid_cpf'));
		}

		if (!isset($params['ifthenpaypix_email']) || $params['ifthenpaypix_email'] == '' || !filter_var($params['ifthenpaypix_email'], FILTER_VALIDATE_EMAIL)) {
			throw new \Exception(IftpLang::trans('msg_pix_invalid_email'));
		}

		// Note: invoiceid is assigned as orderId
		$orderId = (string) $params['invoiceid'];

		$paymentDetails = [
			'order_id' => $orderId,
			'status' => Config::RECORD_STATUS_PENDING,
		];

		$result = self::getNewPayment($orderId, $params['amount'], $key, $params['ifthenpaypix_name'], $params['ifthenpaypix_cpf'], $params['ifthenpaypix_email']);

		if (empty($result)) {
			throw new \Exception("Error Generating Payment", 1);
		}

		$paymentDetails['amount'] = $params['amount'];
		$paymentDetails['payment_url'] = $result['payment_url'];
		$paymentDetails['transaction_id'] = $result['request_id'];


		return $paymentDetails;
	}

			
		
	public static function savePlaceholderPaymentIfNotFound(array $params): void
	{
		if(!empty(pixRepository::getPaymentRecordByInvoiceId((string) $params['invoiceid']))){
			return;
		}

		$placeholerPaymentData = [
			'order_id' => $params['invoiceid'],
			'amount' => $params['amount'],
			'status' => Config::RECORD_STATUS_INITIALIZED,
		];

		self::savePayment($placeholerPaymentData);
	}




	private static function getNewPayment(string $orderId, string $amount, string $key, string $name, string $cpf, string $email): array
	{
		try {
			$returnUrl = UtilsService::getSystemUrl() . str_replace('[ORDER_ID]', $orderId, Config::PIX_RETURN_URL_STRING);

			$payload = [
				'customerName' => $name,
				'customerCpf' => $cpf,
				'customerEmail' => $email,
				'orderId' => $orderId,
				'amount' => $amount,
				'redirectUrl' => $returnUrl,
				'description' => 'request whmcs'
			];

			$response = IfthenpayHttpClient::post(
				Config::API_URL_PIX_SET_REQUEST . $key,
				$payload
			);

			if (isset($response['status']) && $response['status'] == '0' && isset($response['paymentUrl'])) {

				$paymentDetails['request_id'] = $response['requestId'];
				$paymentDetails['payment_url'] = $response['paymentUrl'];

				IfthenpayLog::info(Config::PIX, 'Pix payment generated with success.', ['pixKey' => $key, 'payload' => $payload, 'response' => $response]);

				return $paymentDetails;
			} else {
				IfthenpayLog::error(Config::PIX, 'Error generating Pix payment', ['pixKey' => $key, 'payload' => $payload, 'response' => $response]);
			}
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::PIX, 'Unexpected error generating payment', $th->__toString());
		}
		return [];
	}



	public static function savePayment(array $data): void
	{
		PixRepository::savePayment($data);
	}



	public static function resetConfig(): void
	{
		PixRepository::resetConfig();
	}



	public static function getPaymentRecordByRequest(array $request)
	{
		$paymentRecord = [];

		if (isset($request[Config::CB_TRANSACTION_ID]) && $request[Config::CB_TRANSACTION_ID] != '') {
			$paymentRecord = PixRepository::getPaymentRecordByTransactionId($request[Config::CB_TRANSACTION_ID]);
		}

		return $paymentRecord;
	}



	public static function getPaymentRecordByInvoiceId(string $invoiceId)
	{
		return PixRepository::getPaymentRecordByInvoiceId($invoiceId);
	}



	public static function getPaymentDetailsHtml()
	{

		$templateVars = [
			'stylesPath' => UtilsService::addCacheBuster(UtilsService::pathToAssetCss('frontStyles.css')),

			'paymentMethod' => IftpLang::trans('pix'),
			'payWith' => IftpLang::trans('pay_with'),
			'paymentLogo' => UtilsService::addCacheBuster(UtilsService::pathToAssetImage('pix.png')),
			'paymentProcessCompleted' => IftpLang::trans('payment_process_completed'),
			'waitForVerification' => IftpLang::trans('wait_for_payment_verification')
		];

		$smarty = new Smarty;
		$smarty->assign($templateVars);

		return $smarty->fetch(ROOTDIR . '/modules/gateways/ifthenpaylib/lib/templates/paymentDetails/pix.tpl');
	}



	public static function getPaymentFormHtml($invoiceId): string
	{

		$templateVars = [
			'stylesPath' => UtilsService::addCacheBuster(UtilsService::pathToAssetCss('frontStyles.css')),
			'jsFilePath' => UtilsService::addCacheBuster(UtilsService::pathToAssetJs('frontPixInvoice.js')),

			'paymentMethod' => IftpLang::trans('pix'),
			'payBtn' => IftpLang::trans('pay_btn'),
			'payWith' => IftpLang::trans('pay_with'),
			'paymentLogo' => UtilsService::addCacheBuster(UtilsService::pathToAssetImage('pix.png')),
			'invoiceId' => $invoiceId,
			'nameLabel' => IftpLang::trans('pix_name_label'),
			'cpfLabel' => IftpLang::trans('pix_cpf_label'),
			'emailLabel' => IftpLang::trans('pix_email_label'),
			'msgNameInvalid' => IftpLang::trans('msg_pix_invalid_name'),
			'msgCpfInvalid' => IftpLang::trans('msg_pix_invalid_cpf'),
			'msgEmailInvalid' => IftpLang::trans('msg_pix_invalid_email'),

		];

		// load template
		$smarty = new Smarty;

		$smarty->assign($templateVars);

		return $smarty->fetch(ROOTDIR . '/modules/gateways/ifthenpaylib/lib/templates/paymentForms/pix.tpl');
	}



	public static function handleCallback(array $request): void
	{
		$settings = GatewaySetting::getForGateway(Config::PIX_MODULE_CODE);

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
			Config::PIX_MODULE_CODE
		);
		// End - WHMCS native payment logic 

		self::updateRecordStatus($storedRecord['order_id'], Config::RECORD_STATUS_PAID);
	}



	public static function updateRecordStatus(string $orderId, string $status): void
	{
		PixRepository::updateRecordStatus($orderId, $status);
	}



	public static function validateCallbackAntiphishingKey(array $request)
	{
		$antiPhishingKey = GatewaySetting::getValue(Config::PIX_MODULE_CODE, Config::CF_ANTIPHISHING_KEY) ?? '';
		// is valid antiphishingkey
		if (!(isset($request[Config::CB_ANTIPHISHING_KEY]) && $antiPhishingKey == $request[Config::CB_ANTIPHISHING_KEY])) {
			IfthenpayLog::info(Config::PIX, 'validateCallbackAntiphishingKey - Callback invalid anti-phishing key. ERROR code: ' . Config::CB_ERROR_INVALID_ANTIPHISHING_KEY, ['request' => $request, 'antiPhishingKey' => $antiPhishingKey]);
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
			IfthenpayLog::info(Config::PIX, 'validateCallback - Invalid request params. ERROR code: ' . Config::CB_ERROR_INVALID_PARAMS);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_PARAMS);
		}

		// is method configured
		if (!$settings['type']) {
			IfthenpayLog::info(Config::PIX, 'validateCallback - Unconfigured method. ERROR code: ' . Config::CB_ERROR_UNCONFIGURED_METHOD);
			throw new \Exception("Error", Config::CB_ERROR_UNCONFIGURED_METHOD);
		}

		// is payment method one of the list
		if (!(isset($request[Config::CB_PAYMENT_METHOD]) && strpos(implode(' ', Config::PAYMENT_METHODS_ARRAY), strtolower($request[Config::CB_PAYMENT_METHOD])) !== false)) {
			IfthenpayLog::info(Config::PIX, 'validateCallback - Invalid payment method. ERROR code: ' . Config::CB_ERROR_INVALID_PAYMENT_METHOD, ['request' => $request, 'paymentList' => Config::PAYMENT_METHODS_ARRAY]);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_PAYMENT_METHOD);
		}

		// is callback active
		if (!(isset($settings[Config::CF_CALLBACK_STATUS]) && $settings[Config::CF_CALLBACK_STATUS] == 'on')) {
			IfthenpayLog::info(Config::PIX, 'validateCallback - Callback is not active. ERROR code: ' . Config::CB_ERROR_CALLBACK_NOT_ACTIVE, ['request' => $request]);
			throw new \Exception("Error", Config::CB_ERROR_CALLBACK_NOT_ACTIVE);
		}


		// has ifthenpay record?
		$storedData = self::getPaymentRecordByRequest($request);
		if (empty($storedData)) {
			IfthenpayLog::info(Config::PIX, 'validateCallback - StoredPaymentData not found in local table. ERROR code: ' . Config::CB_ERROR_RECORD_NOT_FOUND, ['request' => $request]);
			throw new \Exception("Error", Config::CB_ERROR_RECORD_NOT_FOUND);
		}


		// already paid?
		if ($storedData['status'] == Config::RECORD_STATUS_PAID) {
			IfthenpayLog::info(Config::PIX, 'validateCallback - Order already paid. ERROR code: ' . Config::CB_ERROR_ALREADY_PAID, ['request' => $request, 'storedData' => $storedData]);
			throw new \Exception("Error", Config::CB_ERROR_ALREADY_PAID);
		}


		// has order record
		$order = OrderRepository::getOrderByInvoiceId($storedData['order_id']);

		if (empty($order)) {
			IfthenpayLog::info(Config::PIX, 'validateCallback - Order not found. ERROR code: ' . Config::CB_ERROR_ORDER_NOT_FOUND, ['request' => $request, 'storedData' => $storedData]);
			throw new \Exception("Error", Config::CB_ERROR_ORDER_NOT_FOUND);
		}


		// has valid amount
		$orderAmount = floatval($order['amount'] ? $order['amount'] : $order['total']);
		$requestAmount = floatval($request[Config::CB_AMOUNT] ?? 0); // defaults to zero if missing
		if (round($orderAmount, 2) !== round($requestAmount, 2)) {
			IfthenpayLog::info(Config::PIX, 'validateCallback - Invalid amount. ERROR code: ' . Config::CB_ERROR_INVALID_AMOUNT, ['request' => $request, 'orderAmount' => $orderAmount]);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_AMOUNT);
		}
	}



	public static function cancelExpiredPayments()
	{

		try {

			$pendingPayments = PixRepository::getPendingPayments();

			foreach ($pendingPayments as $pendingPayment) {

				if (self::isBeyondDeadline($pendingPayment)) {

					// cancel order
					OrderRepository::updateInvoiceStatusById($pendingPayment['order_id'], Config::INVOICE_STATUS_CANCELLED);

					// update record
					PixRepository::updateRecordStatus($pendingPayment['order_id'], 'canceled');

					IfthenpayLog::info('cron', 'Pix payment expired, invoice status updated to Cancelled: ' . $pendingPayment['order_id']);
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
			$deadline->add(new \DateInterval('PT' . 30 . 'M'));
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
		if (!Sql::hasTable(Config::PIX_TABLE)) {
			Sql::createPixTable();
			return;
		}
	}
}
