<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpaylib\Services;

use Smarty;
use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Lang\IftpLang;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpayHttpClient;
use WHMCS\Module\Gateway\ifthenpaylib\Repository\MbwayRepository;
use WHMCS\Module\GatewaySetting;
use WHMCS\Module\Gateway\ifthenpaylib\Config\Sql;
use WHMCS\Module\Gateway\ifthenpaylib\Repository\OrderRepository;

class MbwayService
{

	/**
	 * get mbway entities by backoffice key
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
				if ($item['Entidade'] === strtoupper(Config::MBWAY)) {
					$keys = $item['SubEntidade'];

					foreach ($keys as $key) {

						$keysData[$key] = $key;
					}
				}
			}
			return $keysData;
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::MBWAY, 'Error getting keys by backofficeKey', $th->__toString());
		}
	}



	public static function activateCallback(string $backofficeKey, string $key): void
	{
		$antiPhishingKey = md5((string) rand());

		$callbackUrl = self::generateCallbackUrl();

		// save in ifthenpay server
		$requestResult = IfthenpayService::requestCallbackActivation($backofficeKey, Config::MBWAY, $key, $antiPhishingKey, $callbackUrl);

		// save callback status
		GatewaySetting::setValue(Config::MBWAY_MODULE_CODE, Config::CF_CALLBACK_STATUS, $requestResult ? 'on' : 'off');

		// save antiphishingKey and callbackurl
		GatewaySetting::setValue(Config::MBWAY_MODULE_CODE, Config::CF_ANTIPHISHING_KEY, $requestResult ? $antiPhishingKey : '');
		GatewaySetting::setValue(Config::MBWAY_MODULE_CODE, Config::CF_CALLBACK_URL, $requestResult ? $callbackUrl : '');
	}



	public static function generateCallbackUrl(): string
	{

		$str = UtilsService::getSystemUrl() . 'modules/gateways/callback/ifthenpaymbway.php' . Config::MBWAY_CALLBACK_STRING;
		$str = str_replace('{ec}', 'wh_' . UtilsService::getWHMCSVersion(), $str);
		$str = str_replace('{mv}', Config::MODULE_VERSION, $str);

		return $str;
	}



	public static function clearCallback(): void
	{
		GatewaySetting::setValue(Config::MBWAY_MODULE_CODE, Config::CF_CALLBACK_STATUS, 'off');
		GatewaySetting::setValue(Config::MBWAY_MODULE_CODE, Config::CF_ANTIPHISHING_KEY, '');
		GatewaySetting::setValue(Config::MBWAY_MODULE_CODE, Config::CF_CALLBACK_URL,  '');
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
		$key = GatewaySetting::getValue(Config::MBWAY_MODULE_CODE, Config::CF_MBWAY_KEY) ?? '';

		if ($key == '') {
			throw new \Exception("Error generating payment, missing account key.", 1);
		}

		// Note: invoiceid is assigned as orderId
		$orderId = (string) $params['invoiceid'];

		$paymentDetails = [
			'order_id' => $orderId,
			'mobile_number' => $params['mobile_number'],
			'amount' => $params['amount'],
			'status' => Config::RECORD_STATUS_PENDING,
		];

		$result = self::getNewPayment($orderId, $params['amount'], $key, $params['mobile_number']);

		if (empty($result)) {
			throw new \Exception("Error Generating Payment", 1);
		}

		$paymentDetails['transaction_id'] = $result['request_id'];

		return $paymentDetails;
	}


	public static function savePlaceholderPaymentIfNotFound(array $params): void
	{
		if(!empty(MbwayRepository::getPaymentRecordByInvoiceId((string) $params['invoiceid']))){
			return;
		}

		$placeholerPaymentData = [
			'order_id' => $params['invoiceid'],
			'amount' => $params['amount'],
			'status' => Config::RECORD_STATUS_INITIALIZED,
		];

		self::savePayment($placeholerPaymentData);
	}



	private static function getNewPayment(string $orderId, string $amount, string $key, string $mobileNumber)
	{
		try {
			$payload = [
				'mbWayKey' => $key,
				'orderId' => $orderId,
				'amount' => $amount,
				'mobileNumber' => $mobileNumber,
				'description' => str_replace('{{invoice_id}}', $orderId, GatewaySetting::getValue(Config::MBWAY_MODULE_CODE, Config::CF_MBWAY_NOTIFICATION_DESCRIPTION))
			];

			$response = IfthenpayHttpClient::post(
				Config::API_URL_MBWAY_SET_REQUEST,
				$payload
			);

			if (isset($response['Status']) && $response['Status'] == Config::MBWAY_STATUS_CODE_PAID) {

				$paymentDetails['request_id'] = $response['RequestId'];

				IfthenpayLog::info(Config::MBWAY, 'Mbway payment generated with success.', ['payload' => $payload, 'response' => $response]);

				return $paymentDetails;
			} else {
				IfthenpayLog::error(Config::MBWAY, 'Error generating payment',  ['payload' => $payload, 'response' => $response]);
			}
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::MBWAY, 'Unexpected error generating payment', $th->__toString());
		}
		return [];
	}


	public static function checkMbwayStatusAndGetHtmlStatus(string $invoiceId, bool $countdownExpired): string
	{

		if ($countdownExpired) {
			$statusCode = Config::MBWAY_STATUS_CODE_EXPIRED;
		} else {
			$mbwayKey = GatewaySetting::getValue(Config::MBWAY_MODULE_CODE, Config::CF_MBWAY_KEY);
			$transactionId = MbwayRepository::getPaymentRecordTransactionIdByInvoiceId($invoiceId);
			$statusCode = self::getMbwayStatus($mbwayKey, $transactionId);
		}



		switch ($statusCode) {

			case Config::MBWAY_STATUS_CODE_PAID:
				return '
					<div class="ifthenpay_m_t_20">
						<img src="' . UtilsService::addCacheBuster(UtilsService::getUrl(UtilsService::pathToAssetImage('success.png'))) . '" alt="payment confirmed">
					</div>
					<div class="panel-body">
						<h5>' . IftpLang::trans('mbway_status_payment_confirmed') . '</h5>
					</div>';

			case Config::MBWAY_STATUS_CODE_REJECTED_BY_USER:
				return '
					<div class="ifthenpay_m_t_20">
						<img src="' . UtilsService::addCacheBuster(UtilsService::getUrl(UtilsService::pathToAssetImage('warning.png'))) . '" alt="payment rejected by user">
					</div>
					<div class="panel-body">
						<h5>' . IftpLang::trans('mbway_status_payment_rejected_by_user') . '</h5>
					</div>
					
					<div class="panel-body">
						<form method="post" action="viewinvoice.php?id=' . $invoiceId . '" class="ifthenpay_mbway_form">
							<div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
								<button type="submit" class="btn btn-success">' . IftpLang::trans('resend_mbway_notification') . '</button>
							</div>
						</form>
					</div>';

			case Config::MBWAY_STATUS_CODE_EXPIRED:
				return '
					<div class="ifthenpay_m_t_20">
						<img src="' . UtilsService::addCacheBuster(UtilsService::getUrl(UtilsService::pathToAssetImage('warning.png'))) . '" alt="payment expired">
					</div>
					<div class="panel-body">
						<h5>' . IftpLang::trans('mbway_status_payment_expired') . '</h5>
					</div>
					<div class="panel-body">
					<form method="post" action="viewinvoice.php?id=' . $invoiceId . '" class="ifthenpay_mbway_form">
						<div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
							<button type="submit" class="btn btn-success">' . IftpLang::trans('resend_mbway_notification') . '</button>
						</div>
					</form>
				</div>';

			case Config::MBWAY_STATUS_CODE_DECLINED:
				return '
					<div class="ifthenpay_m_t_20">
						<img src="' . UtilsService::addCacheBuster(UtilsService::getUrl(UtilsService::pathToAssetImage('fail.png'))) . '" alt="payment declined">
					</div>
					<div class="panel-body">
						<h5>' . IftpLang::trans('mbway_status_payment_declined') . '</h5>
					</div>
					<div class="panel-body">
						<form method="post" action="viewinvoice.php?id=' . $invoiceId . '" class="ifthenpay_mbway_form">
							<div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
								<button type="submit" class="btn btn-success">' . IftpLang::trans('resend_mbway_notification') . '</button>
							</div>
						</form>
					</div>';

			case Config::MBWAY_STATUS_CODE_FAIL:
				return '
					<div class="ifthenpay_m_t_20">
						<img src="' . UtilsService::addCacheBuster(UtilsService::getUrl(UtilsService::pathToAssetImage('fail.png'))) . '" alt="payment error">
					</div>
					<div class="panel-body">
						<h5>' . IftpLang::trans('mbway_status_payment_error') . '</h5>
					</div>
					<div class="panel-body">
						<form method="post" action="viewinvoice.php?id=' . $invoiceId . '" class="ifthenpay_mbway_form">
							<div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
								<button type="submit" class="btn btn-success">' . IftpLang::trans('resend_mbway_notification') . '</button>
							</div>
						</form>
					</div>';

			case Config::MBWAY_STATUS_CODE_PENDING:
				return '';

			default:
				return '
					<div class="ifthenpay_m_t_20">
						<img src="' . UtilsService::addCacheBuster(UtilsService::getUrl(UtilsService::pathToAssetImage('fail.png'))) . '" alt="payment error">
					</div>
					<div class="panel-body">
						<h5>' . IftpLang::trans('mbway_status_payment_error') . '</h5>
					</div>';
		}
	}


	private static function getMbwayStatus(string $mbwayKey, string $transactionId)
	{
		try {
			$payload = [
				'mbWayKey' => $mbwayKey,
				'requestId' => $transactionId
			];

			$response = IfthenpayHttpClient::get(
				Config::API_URL_GET_MBWAY_STATUS,
				$payload
			);

			if ($response['Status'] == Config::MBWAY_STATUS_CODE_PENDING) {
				IfthenpayLog::info(Config::MBWAY, 'Mbway check status resulted as Pending.', ['payload' => $payload, 'response' => $response]);
				return Config::MBWAY_STATUS_CODE_PENDING;
			}
			if ($response['Status'] == Config::MBWAY_STATUS_CODE_PAID) {
				IfthenpayLog::info(Config::MBWAY, 'Mbway check status resulted as Paid.', ['payload' => $payload, 'response' => $response]);
				return Config::MBWAY_STATUS_CODE_PAID;
			}
			if ($response['Status'] == Config::MBWAY_STATUS_CODE_REJECTED_BY_USER) {
				IfthenpayLog::info(Config::MBWAY, 'Mbway check status resulted as Rejected by user.', ['payload' => $payload, 'response' => $response]);
				return Config::MBWAY_STATUS_CODE_REJECTED_BY_USER;
			}
			if ($response['Status'] == Config::MBWAY_STATUS_CODE_EXPIRED) {
				IfthenpayLog::info(Config::MBWAY, 'Mbway check status resulted as expired.', ['payload' => $payload, 'response' => $response]);
				return Config::MBWAY_STATUS_CODE_EXPIRED;
			}
			if ($response['Status'] == Config::MBWAY_STATUS_CODE_DECLINED) {
				IfthenpayLog::info(Config::MBWAY, 'Mbway check status resulted as declined.', ['payload' => $payload, 'response' => $response]);
				return Config::MBWAY_STATUS_CODE_DECLINED;
			}
			IfthenpayLog::error(Config::MBWAY, 'Mbway check status resulted as unexpected code', ['payload' => $payload, 'response' => $response]);
			return Config::MBWAY_STATUS_CODE_FAIL;
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::MBWAY, 'An error occurred at Mbway check status: ' . $th->__toString());
			return Config::MBWAY_STATUS_CODE_FAIL;
		}
	}



	public static function savePayment(array $data): void
	{
		MbwayRepository::savePayment($data);
	}



	public static function resetConfig(): void
	{
		MbwayRepository::resetConfig();
	}



	public static function getPaymentRecordByRequest(array $request)
	{
		$paymentRecord = [];

		if (isset($request[Config::CB_TRANSACTION_ID]) && $request[Config::CB_TRANSACTION_ID] != '') {
			$paymentRecord = MbwayRepository::getPaymentRecordByTransactionId($request[Config::CB_TRANSACTION_ID]);
		}

		return $paymentRecord;
	}



	public static function getPaymentRecordByInvoiceId(string $invoiceId)
	{
		return MbwayRepository::getPaymentRecordByInvoiceId($invoiceId);
	}



	public static function getPaymentDetailsHtml($invoiceId)
	{

		$showCountdown = GatewaySetting::getValue(Config::MBWAY_MODULE_CODE, Config::CF_MBWAY_SHOW_COUNTDOWN);

		$templateVars = [
			'stylesPath' => UtilsService::addCacheBuster(UtilsService::pathToAssetCss('frontStyles.css')),
			'jsFilePath' => UtilsService::addCacheBuster(UtilsService::pathToAssetJs('frontMbwayInvoice.js')),

			'paymentMethod' => IftpLang::trans('mbway'),
			'payWith' => IftpLang::trans('pay_with'),
			'paymentLogo' => UtilsService::addCacheBuster(UtilsService::pathToAssetImage('mbway.png')),
			'amountLabel' => IftpLang::trans('amount_label'),
			'notificationSent' => IftpLang::trans('notification_sent'),
			'showCountdown' => $showCountdown,
			'invoiceId' => $invoiceId,
		];

		// load template
		$smarty = new Smarty;

		$smarty->assign($templateVars);

		return $smarty->fetch(ROOTDIR . '/modules/gateways/ifthenpaylib/lib/templates/paymentDetails/mbway.tpl');
	}



	public static function getPaymentFormHtml($invoiceId)
	{
		$templateVars = [
			'stylesPath' => UtilsService::addCacheBuster(UtilsService::pathToAssetCss('frontStyles.css')),
			'jsFilePath' => UtilsService::addCacheBuster(UtilsService::pathToAssetJs('frontMbwayInvoice.js')),

			'paymentMethod' => IftpLang::trans('mbway'),
			'payWith' => IftpLang::trans('pay_with'),
			'paymentLogo' => UtilsService::addCacheBuster(UtilsService::pathToAssetImage('mbway.png')),
			'mobileNumberPlaceholder' => IftpLang::trans('mobile_number'),
			'randHash' => md5((string) rand()),
			'mobileCodeSelectHtml' => self::generateMobileCodesSelectHtml(),
			'payBtn' => IftpLang::trans('pay'),
			'invoiceId' => $invoiceId,
			'msg_mbway_invalid_number' => IftpLang::trans('msg_mbway_invalid_number'),
		];

		// load template
		$smarty = new Smarty;

		$smarty->assign($templateVars);

		return $smarty->fetch(ROOTDIR . '/modules/gateways/ifthenpaylib/lib/templates/paymentForms/mbway.tpl');
	}



	public static function handleCallback(array $request): void
	{
		$settings = GatewaySetting::getForGateway(Config::MBWAY_MODULE_CODE);

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
			Config::MBWAY_MODULE_CODE
		);
		// End - WHMCS native payment logic 

		self::updateRecordStatus($storedRecord['order_id'], Config::RECORD_STATUS_PAID);
	}



	public static function updateRecordStatus(string $orderId, string $status): void
	{
		MbwayRepository::updateRecordStatus($orderId, $status);
	}



	public static function validateCallbackAntiphishingKey(array $request)
	{
		$antiPhishingKey = GatewaySetting::getValue(Config::MBWAY_MODULE_CODE, Config::CF_ANTIPHISHING_KEY) ?? '';
		// is valid antiphishingkey
		if (!(isset($request[Config::CB_ANTIPHISHING_KEY]) && $antiPhishingKey == $request[Config::CB_ANTIPHISHING_KEY])) {
			IfthenpayLog::info(Config::MBWAY, 'validateCallbackAntiphishingKey - Invalid anti-phishing key. ERROR code: ' . Config::CB_ERROR_INVALID_ANTIPHISHING_KEY, ['request' => $request, 'antiPhishingKey' => $antiPhishingKey]);
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
			IfthenpayLog::info(Config::MBWAY, 'validateCallback - Invalid request params. ERROR code: ' . Config::CB_ERROR_INVALID_PARAMS);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_PARAMS);
		}

		// is method configured
		if (!$settings['type']) {
			IfthenpayLog::info(Config::MBWAY, 'validateCallback - Unconfigured method. ERROR code: ' . Config::CB_ERROR_UNCONFIGURED_METHOD);
			throw new \Exception("Error", Config::CB_ERROR_UNCONFIGURED_METHOD);
		}

		// is payment method one of the list
		if (!(isset($request[Config::CB_PAYMENT_METHOD]) && strpos(implode(' ', Config::PAYMENT_METHODS_ARRAY), strtolower($request[Config::CB_PAYMENT_METHOD])) !== false)) {
			IfthenpayLog::info(Config::MBWAY, 'validateCallback - Invalid payment method. ERROR code: ' . Config::CB_ERROR_INVALID_PAYMENT_METHOD, ['request' => $request, 'paymentList' => Config::PAYMENT_METHODS_ARRAY]);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_PAYMENT_METHOD);
		}

		// is callback active
		if (!(isset($settings[Config::CF_CALLBACK_STATUS]) && $settings[Config::CF_CALLBACK_STATUS] == 'on')) {
			IfthenpayLog::info(Config::MBWAY, 'validateCallback - Callback is not active. ERROR code: ' . Config::CB_ERROR_CALLBACK_NOT_ACTIVE, ['request' => $request]);
			throw new \Exception("Error", Config::CB_ERROR_CALLBACK_NOT_ACTIVE);
		}


		// has ifthenpay record?
		$storedData = self::getPaymentRecordByRequest($request);
		if (empty($storedData)) {
			IfthenpayLog::info(Config::MBWAY, 'validateCallback - StoredPaymentData not found in local table. ERROR code: ' . Config::CB_ERROR_RECORD_NOT_FOUND, ['request' => $request]);
			throw new \Exception("Error", Config::CB_ERROR_RECORD_NOT_FOUND);
		}


		// already paid?
		if ($storedData['status'] == Config::RECORD_STATUS_PAID) {
			IfthenpayLog::info(Config::MBWAY, 'validateCallback - Order already paid. ERROR code: ' . Config::CB_ERROR_ALREADY_PAID, ['request' => $request, 'storedData' => $storedData]);
			throw new \Exception("Error", Config::CB_ERROR_ALREADY_PAID);
		}


		// has order record
		$order = OrderRepository::getOrderByInvoiceId($storedData['order_id']);

		if (empty($order)) {
			IfthenpayLog::info(Config::MBWAY, 'validateCallback - Order not found. ERROR code: ' . Config::CB_ERROR_ORDER_NOT_FOUND, ['request' => $request, 'storedData' => $storedData]);
			throw new \Exception("Error", Config::CB_ERROR_ORDER_NOT_FOUND);
		}


		// has valid amount
		$orderAmount = floatval($order['amount'] ? $order['amount'] : $order['total']);
		$requestAmount = floatval($request[Config::CB_AMOUNT] ?? 0); // defaults to zero if missing
		if (round($orderAmount, 2) !== round($requestAmount, 2)) {
			IfthenpayLog::info(Config::MBWAY, 'validateCallback - Invalid amount. ERROR code: ' . Config::CB_ERROR_INVALID_AMOUNT, ['request' => $request, 'orderAmount' => $orderAmount]);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_AMOUNT);
		}
	}



	public static function cancelExpiredPayments()
	{

		try {

			$pendingPayments = MbwayRepository::getPendingPayments();

			foreach ($pendingPayments as $pendingPayment) {

				if (self::isBeyondDeadline($pendingPayment)) {

					// cancel order
					OrderRepository::updateInvoiceStatusById($pendingPayment['order_id'], Config::INVOICE_STATUS_CANCELLED);

					// update record
					MbwayRepository::updateRecordStatus($pendingPayment['order_id'], 'canceled');

					IfthenpayLog::info('cron', 'Mbway payment expired, invoice status updated to Cancelled: ' . $pendingPayment['order_id']);
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



	private static function generateMobileCodesSelectHtml(): string
	{
		$codesArray = UtilsService::getCountryCodesAsValueNameArray(IftpLang::getLangCode());

		$html = '';
		foreach ($codesArray as $code) {
			$selected = $code['value'] == '351' ? 'selected' : '';

			$html .= '<option title="' . $code['desc'] . '" ' . $selected . ' value="' . $code['value'] . '">' . $code['name'] . '</option>';
		}

		if ($html != '') {
			$html = '<select name="mobile_code" class="form-control select-inline ifthenpay_w_6_rem">' . $html . '</select>';
		}

		return $html;
	}



	public static function handleDbCreateUpdate(): void
	{
		if (!Sql::hasTable(Config::MBWAY_TABLE)) {
			Sql::createMbwayTable();
			return;
		}

		$previousVersionInstalled = GatewaySetting::getValue(Config::MBWAY_MODULE_CODE, Config::CF_INSTALLED_MODULE_VERSION) ?? '0.0.0';
		$newVersionInstalled = Config::MODULE_VERSION;



		IfthenpayLog::info(Config::MBWAY, 'Mbway check status resulted as Pending.', ['previous' => $previousVersionInstalled]);



		if (version_compare($newVersionInstalled, $previousVersionInstalled) == 0) {
			return;
		}

		// If new install, upgrading from old module or just reactivating 
		if ($previousVersionInstalled == '0.0.0' && version_compare('8.0.0', $previousVersionInstalled, '>') == 1) {
			Sql::updateMbwayTableFromVersion_0_0_0();
		}
	}
}
