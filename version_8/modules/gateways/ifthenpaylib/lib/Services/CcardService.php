<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpaylib\Services;

use Smarty;
use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Config\Sql;
use WHMCS\Module\Gateway\ifthenpaylib\Lang\IftpLang;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpayHttpClient;
use WHMCS\Module\Gateway\ifthenpaylib\Repository\CcardRepository;
use WHMCS\Module\GatewaySetting;
use WHMCS\Module\Gateway\ifthenpaylib\Repository\OrderRepository;

class CcardService
{

	public static function getKeysByBackofficKey(string $backofficeKey): mixed
	{
		try {
			$accounts = IfthenpayService::getAccountsByBackofficKey($backofficeKey);

			if (empty($accounts)) {
				return false;
			}

			$keysData = [];


			foreach ($accounts as $item) {
				if ($item['Entidade'] === strtoupper(Config::CCARD)) {
					$keys = $item['SubEntidade'];

					foreach ($keys as $key) {

						$keysData[$key] = $key;
					}
				}
			}
			return $keysData;
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::CCARD, 'Error getting keys by backofficeKey', $th->__toString);
		}
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
		$key = GatewaySetting::getValue(Config::CCARD_MODULE_CODE, Config::CF_CCARD_KEY) ?? '';

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

		$result = self::getNewPayment($orderId, $params['amount'], $key);

		if (empty($result)) {
			throw new \Exception("Error Generating Payment", 1);
		}

		$paymentDetails['transaction_id'] = $result['request_id'];
		$paymentDetails['paymentUrl'] = $result['paymentUrl'];

		return $paymentDetails;
	}



	public static function savePlaceholderPaymentIfNotFound(array $params): void
	{
		if(!empty(CcardRepository::getPaymentRecordByInvoiceId((string) $params['invoiceid']))){
			return;
		}

		$placeholerPaymentData = [
			'order_id' => $params['invoiceid'],
			'amount' => $params['amount'],
			'status' => Config::RECORD_STATUS_INITIALIZED,
		];

		self::savePayment($placeholerPaymentData);
	}



	private static function getNewPayment(string $orderId, string $amount, string $key)
	{
		try {
			$successUrl = self::generateReturnUrl() . '?status=success';
			$errorUrl = self::generateReturnUrl() . '?status=error';
			$cancelUrl = self::generateReturnUrl() . '?status=cancel';
			$language = $_SESSION['language'] ?? UtilsService::getLanguage();

			$payload = [
				'orderId' => $orderId,
				'amount' => $amount,
				'successUrl' => $successUrl,
				'errorUrl' => $errorUrl,
				'cancelUrl' => $cancelUrl,
				'language' => $language,
			];

			$response = IfthenpayHttpClient::post(
				Config::API_URL_CCARD_SET_REQUEST . $key,
				$payload
			);

			if (isset($response['Status']) && $response['Status'] == '0') {

				$paymentDetails['request_id'] = $response['RequestId'];
				$paymentDetails['paymentUrl'] = $response['PaymentUrl'];

				IfthenpayLog::info(Config::CCARD, 'Ccard generated with success.', ['payload' => $payload, 'response' => $response]);

				return $paymentDetails;
			} else {
				IfthenpayLog::error(Config::CCARD, 'Error generating Ccard payment', ['payload' => $payload, 'response' => $response]);
			}
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::CCARD, 'Unexpected Error generating Ccard payment', $th);
		}
		return [];
	}



	public static function savePayment(array $data): void
	{
		CcardRepository::savePayment($data);
	}



	public static function resetConfig(): void
	{
		CcardRepository::resetConfig();
	}



	public static function getPaymentRecordByRequest(array $request)
	{
		$paymentRecord = [];

		if (isset($request['requestId']) && $request['requestId'] != '') {
			$paymentRecord = CcardRepository::getPaymentRecordByTransactionId($request['requestId']);
		}

		return $paymentRecord;
	}



	public static function getPaymentRecordByInvoiceId(string $invoiceId)
	{
		return CcardRepository::getPaymentRecordByInvoiceId($invoiceId);
	}



	public static function getPaymentFormHtml($invoiceId)
	{
		$templateVars = [
			'stylesPath' => UtilsService::addCacheBuster(UtilsService::pathToAssetCss('frontStyles.css')),
			'jsFilePath' => UtilsService::addCacheBuster(UtilsService::pathToAssetJs('ccardInvoice.js')),

			'paymentMethod' => IftpLang::trans('ccard'),
			'payBtn' => IftpLang::trans('pay_btn'),
			'payWith' => IftpLang::trans('pay_with'),
			'paymentLogo' => UtilsService::addCacheBuster(UtilsService::pathToAssetImage('ccard.png')),
			'payBtn' => IftpLang::trans('pay'),
			'invoiceId' => $invoiceId,
		];

		// load template
		$smarty = new Smarty;

		$smarty->assign($templateVars);

		return $smarty->fetch(ROOTDIR . '/modules/gateways/ifthenpaylib/lib/templates/paymentForms/ccard.tpl');
	}



	public static function handleCallback(array $request): void
	{
		$settings = GatewaySetting::getForGateway(Config::CCARD_MODULE_CODE);

		self::validateCallback($request, $settings);

		$storedRecord = self::getPaymentRecordByRequest($request);
		$invoiceId = $storedRecord['order_id'];

		if ($request['status'] == 'success') {
			$paymentAmount = $request['amount'];
			$paymentFee = '';
			$transactionId = $storedRecord['transaction_id'];

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
				Config::CCARD_MODULE_CODE
			);
			// End - WHMCS native payment logic 

			self::updateRecordStatus($storedRecord['order_id'], Config::RECORD_STATUS_PAID);
			IfthenpayLog::info(Config::CCARD, 'Payment operation successful.', ['invoiceId' => $request['id']]);
		} else if ($request['status'] == 'cancel') {

			CcardRepository::updateRecordStatus($invoiceId, Config::RECORD_STATUS_CANCELLED);
			OrderRepository::updateInvoiceStatusById($invoiceId, Config::INVOICE_STATUS_CANCELLED);

			logTransaction($settings['name'], $request, 'cancel');
			IfthenpayLog::error(Config::CCARD, 'Payment operation canceled.', ['invoiceId' => $request['id']]);
		} else {
			CcardRepository::updateRecordStatus($invoiceId, Config::RECORD_STATUS_ERROR);
			logTransaction($settings['name'], $request, 'error');
			IfthenpayLog::error(Config::CCARD, 'Payment operation resulted in error.', ['request' => $request['id']]);
		}
	}



	public static function updateRecordStatus(string $orderId, string $status): void
	{
		CcardRepository::updateRecordStatus($orderId, $status);
	}



	public static function validateCallback(array $request, array $settings)
	{
		// has required params
		if (
			(!isset($request['sk']) || $request['sk'] == '') &&
			(!isset($request['id']) || $request['id'] == '') &&
			(!isset($request['amount']) || $request['amount'] == '') &&
			(!isset($request['status']) || $request['status'] == '') &&
			!isset($request['requestId'])
		) {
			IfthenpayLog::info(Config::CCARD, 'validateCallback - Invalid request params. ERROR code: ' . Config::CB_ERROR_INVALID_PARAMS);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_PARAMS);
		}

		// has valid secret sk
		$expectedSk = hash_hmac('sha256', $request['id'] . $request['amount'] . $request['requestId'], $settings[Config::CF_CCARD_KEY]);
		if ($request['sk'] != $expectedSk) {
			IfthenpayLog::info(Config::CCARD, 'validateCallback - Invalid token. ERROR code: ' . Config::CB_ERROR_INVALID_SECRET);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_SECRET);
		}

		// is method configured
		if (!$settings['type']) {
			IfthenpayLog::info(Config::CCARD, 'validateCallback - Unconfigured method. ERROR code: ' . Config::CB_ERROR_UNCONFIGURED_METHOD);
			throw new \Exception("Error", Config::CB_ERROR_UNCONFIGURED_METHOD);
		}

		// has ifthenpay record?
		$storedData = self::getPaymentRecordByRequest($request);
		if (empty($storedData)) {
			IfthenpayLog::info(Config::CCARD, 'validateCallback - StoredPaymentData not found in local table. ERROR code: ' . Config::CB_ERROR_RECORD_NOT_FOUND, ['request' => $request]);
			throw new \Exception("Error", Config::CB_ERROR_RECORD_NOT_FOUND);
		}

		// has order record
		$order = OrderRepository::getOrderByInvoiceId($storedData['order_id']);

		if (empty($order)) {
			IfthenpayLog::info(Config::CCARD, 'validateCallback - Order not found. ERROR code: ' . Config::CB_ERROR_ORDER_NOT_FOUND, ['request' => $request, 'storedData' => $storedData]);
			throw new \Exception("Error", Config::CB_ERROR_ORDER_NOT_FOUND);
		}

		// has valid amount
		$orderAmount = floatval($order['amount'] ? $order['amount'] : $order['total']);
		$requestAmount = floatval($request['amount'] ?? 0); // defaults to zero if missing
		if (round($orderAmount, 2) !== round($requestAmount, 2)) {
			IfthenpayLog::info(Config::CCARD, 'validateCallback - Invalid amount. ERROR code: ' . Config::CB_ERROR_INVALID_AMOUNT, ['request' => $request, 'orderAmount' => $orderAmount]);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_AMOUNT);
		}
	}



	public static function cancelExpiredPayments()
	{
		try {
			$pendingPayments = CcardRepository::getPendingPayments();

			foreach ($pendingPayments as $pendingPayment) {

				if (self::isBeyondDeadline($pendingPayment)) {

					// cancel order
					OrderRepository::updateInvoiceStatusById($pendingPayment['order_id'], Config::INVOICE_STATUS_CANCELLED);

					// update record
					CcardRepository::updateRecordStatus($pendingPayment['order_id'], 'canceled');

					IfthenpayLog::info('cron', 'Ccard payment expired, invoice status updated to Cancelled: ' . $pendingPayment['order_id']);
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
		if (!isset($settings[Config::CF_ACCOUNTS]) || !isset($settings[Config::CF_BACKOFFICE_KEY])) {
			return [];
		}

		$accounts = json_decode($settings[Config::CF_ACCOUNTS], true);

		return $accounts;
	}



	public static function generateReturnUrl(): string
	{
		$str = UtilsService::getSystemUrl() . 'modules/gateways/callback/ifthenpayccard.php';

		return $str;
	}



	public static function handleDbCreateUpdate(): void
	{
		if (!Sql::hasTable(Config::CCARD_TABLE)) {
			Sql::createCcardTable();
			return;
		}

		$previousVersionInstalled = GatewaySetting::getValue(Config::CCARD_MODULE_CODE, Config::CF_INSTALLED_MODULE_VERSION) ?? '0.0.0';
		$newVersionInstalled = Config::MODULE_VERSION;

		if (version_compare($newVersionInstalled, $previousVersionInstalled) == 0) {
			return;
		}

		// If new install, upgrading from old module or just reactivating 
		if ($previousVersionInstalled == '0.0.0' && version_compare('8.0.0', $previousVersionInstalled, '>') == 1) {
			Sql::updateCcardTableFromVersion_0_0_0();
		}
	}
}
