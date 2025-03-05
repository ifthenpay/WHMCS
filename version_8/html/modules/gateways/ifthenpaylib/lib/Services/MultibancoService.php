<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpaylib\Services;

use Smarty;
use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Config\Sql;
use WHMCS\Module\Gateway\ifthenpaylib\Lang\IftpLang;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpayHttpClient;
use WHMCS\Module\Gateway\ifthenpaylib\Repository\MultibancoRepository;
use WHMCS\Module\GatewaySetting;
use WHMCS\Module\Gateway\ifthenpaylib\Repository\OrderRepository;

class MultibancoService
{

	/**
	 * get multibanco entities by backoffice key
	 * @return array
	 */
	public static function getEntitiesByBackofficKey(string $backofficeKey): mixed
	{
		try {
			$accounts = IfthenpayService::getAccountsByBackofficKey($backofficeKey);

			if (empty($accounts)) {
				return false;
			}

			$entities = [];

			foreach ($accounts as $item) {
				if (is_numeric($item['Entidade']) || strcasecmp($item['Entidade'], Config::MULTIBANCO_DYNAMIC) === 0) {
					$entities[$item['Entidade']] = $item['SubEntidade'];
				}
			}

			return $entities;
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::MULTIBANCO, 'Error getting keys by backofficeKey', $th->__toString());
			return false;
		}
	}



	public static function activateCallback($backofficeKey, $entity, $subEntity): void
	{
		$antiPhishingKey = md5((string) rand());

		$callbackUrl = self::generateCallbackUrl();

		// save in ifthenpay server
		$requestResult = IfthenpayService::requestCallbackActivation($backofficeKey, $entity, $subEntity, $antiPhishingKey, $callbackUrl);

		// save callback status
		GatewaySetting::setValue(Config::MULTIBANCO_MODULE_CODE, Config::CF_CALLBACK_STATUS, $requestResult ? 'on' : 'off');

		// save antiphishingKey and callbackurl
		GatewaySetting::setValue(Config::MULTIBANCO_MODULE_CODE, Config::CF_ANTIPHISHING_KEY, $requestResult ? $antiPhishingKey : '');
		GatewaySetting::setValue(Config::MULTIBANCO_MODULE_CODE, Config::CF_CALLBACK_URL, $requestResult ? $callbackUrl : '');
	}



	public static function generateCallbackUrl(): string
	{

		$str = UtilsService::getSystemUrl() . 'modules/gateways/callback/ifthenpaymultibanco.php' . Config::MULTIBANCO_CALLBACK_STRING;
		$str = str_replace('{ec}', 'wh_' . UtilsService::getWHMCSVersion(), $str);
		$str = str_replace('{mv}', Config::MODULE_VERSION, $str);

		return $str;
	}



	public static function clearCallback(): void
	{
		GatewaySetting::setValue(Config::MULTIBANCO_MODULE_CODE, Config::CF_CALLBACK_STATUS, 'off');
		GatewaySetting::setValue(Config::MULTIBANCO_MODULE_CODE, Config::CF_ANTIPHISHING_KEY, '');
		GatewaySetting::setValue(Config::MULTIBANCO_MODULE_CODE, Config::CF_CALLBACK_URL,  '');
	}



	public static function ifPaymentExists(string $invoiceId)
	{
		$record = self::getPaymentRecordByInvoiceId($invoiceId);

		if ($record) {
			return true;
		}
	}



	public static function shouldGeneratePayment(array $params): bool
	{
		$paymentRecord = self::getPaymentRecordByInvoiceId((string) $params['invoiceid']);

		if (empty($paymentRecord)) {
			IfthenpayLog::info(Config::MULTIBANCO, 'Should create new payment.');
			return true;
		}

		if ($paymentRecord['status'] === Config::RECORD_STATUS_PAID) {
			IfthenpayLog::info(Config::MULTIBANCO, 'shouldGeneratePayment - invoice already paid.', ['paymentRecord' => $paymentRecord]);
			return false;
		}

		if ($paymentRecord['amount'] != $params['amount']) {
			IfthenpayLog::info(Config::MULTIBANCO, 'Should update existing payment.', ['newAmount' => $params['amount'], 'paymentRecord' => $paymentRecord]);
			return true;
		}

		return false;
	}



	public static function generatePayment($params)
	{

		$entity = GatewaySetting::getValue(Config::MULTIBANCO_MODULE_CODE, Config::CF_MULTIBANCO_ENTITY) ?? '';
		$subEntity = GatewaySetting::getValue(Config::MULTIBANCO_MODULE_CODE, Config::CF_MULTIBANCO_SUBENTITY) ?? '';

		if ($entity == '' || $subEntity == '') {
			throw new \Exception("Error generating payment, missing entity or subentity.", 1);
		}

		// Note: invoiceid is assigned as orderId
		$orderId = (string) $params['invoiceid'];

		$paymentDetails = [
			'order_id' => $orderId,
			'amount' => $params['amount'],
			'status' => 'pending',
		];

		if ($entity == 'MB') {
			// use dynamic
			$deadline = GatewaySetting::getValue(Config::MULTIBANCO_MODULE_CODE, Config::CF_DEADLINE) ?? '';
			$result = self::getDynamicReference($orderId, $params['amount'], $subEntity, $deadline);

			if (empty($result)) {
				throw new \Exception("Error Generating Payment dynamic reference", 1);
			}

			$paymentDetails['reference'] = $result['reference'];
			$paymentDetails['entity'] = $result['entity'];
			$paymentDetails['transaction_id'] = $result['request_id'];
			$paymentDetails['deadline'] = $result['deadline'];
		} else {
			$reference = self::generateStaticReference($orderId, $params['amount'], $entity, $subEntity);

			if ($reference == '') {
				throw new \Exception("Error Generating Payment static reference", 1);
			}
			$paymentDetails['reference'] = $reference;
			$paymentDetails['entity'] = $entity;
			$paymentDetails['transaction_id'] = UtilsService::generateTransactionId($orderId);
		}

		return $paymentDetails;
	}



	private static function getDynamicReference(string $orderId, string $amount, string $subEntity, string $deadline)
	{
		try {
			$payload = [
				'mbKey' => $subEntity,
				'orderId' => $orderId,
				'amount' => $amount,
				'description' => 'order from whmcs',
			];

			if ($deadline != '') {
				$payload['expiryDays'] = $deadline;
			}

			$response = IfthenpayHttpClient::post(
				Config::API_URL_MULTIBANCO_DYNAMIC_SET_REQUEST,
				$payload
			);

			if (isset($response['RequestId']) && $response['RequestId'] != '') {

				$paymentDetails['reference'] = $response['Reference'];
				$paymentDetails['entity'] = $response['Entity'];
				$paymentDetails['request_id'] = $response['RequestId'];
				$paymentDetails['deadline'] = $response['ExpiryDate'];

				IfthenpayLog::info(Config::MULTIBANCO, 'Dynamic Multibanco reference generated with success.', ['payload' => $payload, 'response' => $response]);

				return $paymentDetails;
			}
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::MULTIBANCO, 'Unexpected error generating payment (dynamic reference)', $th->__toString());
		}
		return [];
	}



	private static function generateStaticReference(string $orderId, string $amount, string $entity, string $subEntity): string
	{
		$orderId = "0000" . $orderId;

		if (strlen($subEntity) === 2) {
			//Apenas sao considerados os 5 caracteres mais a direita do order_id
			$seed = substr($orderId, (strlen($orderId) - 5), strlen($orderId));
			$chk_str = sprintf('%05u%02u%05u%08u', $entity, $subEntity, $seed, round($amount * 100));
		} else {
			//Apenas sao considerados os 4 caracteres mais a direita do order_id
			$seed = substr($orderId, (strlen($orderId) - 4), strlen($orderId));
			$chk_str = sprintf('%05u%03u%04u%08u', $entity, $subEntity, $seed, round($amount * 100));
		}
		$chk_array = array(3, 30, 9, 90, 27, 76, 81, 34, 49, 5, 50, 15, 53, 45, 62, 38, 89, 17, 73, 51);
		$chk_val = 0;
		for ($i = 0; $i < 20; $i++) {
			$chk_int = substr($chk_str, 19 - $i, 1);
			$chk_val += ($chk_int % 10) * $chk_array[$i];
		}
		$chk_val %= 97;
		$chk_digits = sprintf('%02u', 98 - $chk_val);
		//referencia
		return $subEntity . $seed . $chk_digits;
	}



	public static function savePayment(array $data): void
	{
		MultibancoRepository::savePayment($data);
	}



	public static function resetConfig(): void
	{
		MultibancoRepository::resetConfig();
	}



	public static function getPaymentRecordByRequest(array $request)
	{
		$paymentRecord = [];

		if (isset($request[Config::CB_REFERENCE]) && $request[Config::CB_REFERENCE] != '') {
			$paymentRecord = MultibancoRepository::getPaymentRecordByReference($request[Config::CB_REFERENCE]);
		}

		return $paymentRecord;
	}



	public static function getPaymentRecordByInvoiceId(string $invoiceId)
	{
		return MultibancoRepository::getPaymentRecordByInvoiceId($invoiceId);
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

			'payWith' => IftpLang::trans('pay_with'),
			'paymentMethod' => IftpLang::trans('multibanco'),
			'paymentLogo' => UtilsService::addCacheBuster(UtilsService::pathToAssetImage('multibanco.png')),
			'entityLabel' => IftpLang::trans('entity_label'),
			'entity' => $paymentData[Config::CF_MULTIBANCO_ENTITY],
			'referenceLabel' => IftpLang::trans('reference_label'),
			'reference' => $paymentData['reference'],
			'deadlineLabel' => IftpLang::trans('deadline_label'),
			'deadline' => $paymentData['deadline'],
			'amountLabel' => IftpLang::trans('amount_label'),
			'amount' => $amount,

		];

		// load template
		$smarty = new Smarty;

		$smarty->assign($templateVars);

		return $smarty->fetch(ROOTDIR . '/modules/gateways/ifthenpaylib/lib/templates/paymentDetails/multibanco.tpl');
	}



	public static function handleCallback(array $request): void
	{
		$settings = GatewaySetting::getForGateway(Config::MULTIBANCO_MODULE_CODE);

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
			Config::MULTIBANCO_MODULE_CODE
		);
		// End - WHMCS native payment logic 

		self::updateRecordStatus($storedRecord['order_id'], Config::RECORD_STATUS_PAID);
	}



	public static function updateRecordStatus(string $orderId, string $status): void
	{
		MultibancoRepository::updateRecordStatus($orderId, $status);
	}



	public static function validateCallbackAntiphishingKey(array $request)
	{
		$antiPhishingKey = GatewaySetting::getValue(Config::MULTIBANCO_MODULE_CODE, Config::CF_ANTIPHISHING_KEY) ?? '';
		// is valid antiphishingkey
		if (!(isset($request[Config::CB_ANTIPHISHING_KEY]) && $antiPhishingKey == $request[Config::CB_ANTIPHISHING_KEY])) {
			IfthenpayLog::info(Config::MULTIBANCO, 'validateCallbackAntiphishingKey - Invalid anti-phishing key. ERROR code: ' . Config::CB_ERROR_INVALID_ANTIPHISHING_KEY, ['request' => $request, 'antiPhishingKey' => $antiPhishingKey]);
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
			(!isset($request[Config::CB_ENTITY]) || $request[Config::CB_ENTITY] == '') &&
			(!isset($request[Config::CB_REFERENCE]) || $request[Config::CB_REFERENCE] == '') &&
			!isset($request[Config::CB_TRANSACTION_ID])
		) {
			IfthenpayLog::info(Config::MULTIBANCO, 'validateCallback - Invalid request params. ERROR code: ' . Config::CB_ERROR_INVALID_PARAMS);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_PARAMS);
		}

		// is method configured
		if (!$settings['type']) {
			IfthenpayLog::info(Config::MULTIBANCO, 'validateCallback - Unconfigured method. ERROR code: ' . Config::CB_ERROR_UNCONFIGURED_METHOD);
			throw new \Exception("Error", Config::CB_ERROR_UNCONFIGURED_METHOD);
		}

		// is payment method one of the list
		if (!(isset($request[Config::CB_PAYMENT_METHOD]) && strpos(implode(' ', Config::PAYMENT_METHODS_ARRAY), strtolower($request[Config::CB_PAYMENT_METHOD])) !== false)) {
			IfthenpayLog::info(Config::MULTIBANCO, 'validateCallback - Invalid payment method. ERROR code: ' . Config::CB_ERROR_INVALID_PAYMENT_METHOD, ['request' => $request, 'paymentList' => Config::PAYMENT_METHODS_ARRAY]);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_PAYMENT_METHOD);
		}

		// is callback active
		if (!(isset($settings[Config::CF_CALLBACK_STATUS]) && $settings[Config::CF_CALLBACK_STATUS] == 'on')) {
			IfthenpayLog::info(Config::MULTIBANCO, 'validateCallback - Callback is not active. ERROR code: ' . Config::CB_ERROR_CALLBACK_NOT_ACTIVE, ['request' => $request]);
			throw new \Exception("Error", Config::CB_ERROR_CALLBACK_NOT_ACTIVE);
		}


		// has ifthenpay record?
		$storedData = self::getPaymentRecordByRequest($request);
		if (empty($storedData)) {
			IfthenpayLog::info(Config::MULTIBANCO, 'validateCallback - StoredPaymentData not found in local table. ERROR code: ' . Config::CB_ERROR_RECORD_NOT_FOUND, ['request' => $request]);
			throw new \Exception("Error", Config::CB_ERROR_RECORD_NOT_FOUND);
		}


		// already paid?
		if ($storedData['status'] == Config::RECORD_STATUS_PAID) {
			IfthenpayLog::info(Config::MULTIBANCO, 'validateCallback - Order already paid. ERROR code: ' . Config::CB_ERROR_ALREADY_PAID, ['request' => $request, 'storedData' => $storedData]);
			throw new \Exception("Error", Config::CB_ERROR_ALREADY_PAID);
		}


		// has order record
		$order = OrderRepository::getOrderByInvoiceId($storedData['order_id']);

		if (empty($order)) {
			IfthenpayLog::info(Config::MULTIBANCO, 'validateCallback - Order not found. ERROR code: ' . Config::CB_ERROR_ORDER_NOT_FOUND, ['request' => $request, 'storedData' => $storedData]);
			throw new \Exception("Error", Config::CB_ERROR_ORDER_NOT_FOUND);
		}


		// has valid amount
		$orderAmount = floatval($order['amount'] ? $order['amount'] : $order['total']);
		$requestAmount = floatval($request[Config::CB_AMOUNT] ?? 0); // defaults to zero if missing
		if (round($orderAmount, 2) !== round($requestAmount, 2)) {
			IfthenpayLog::info(Config::MULTIBANCO, 'validateCallback - Invalid amount. ERROR code: ' . Config::CB_ERROR_INVALID_AMOUNT, ['request' => $request, 'orderAmount' => $orderAmount]);
			throw new \Exception("Error", Config::CB_ERROR_INVALID_AMOUNT);
		}
	}



	public static function cancelExpiredPayments()
	{

		try {

			$pendingPayments = MultibancoRepository::getPendingPayments();

			foreach ($pendingPayments as $pendingPayment) {

				if (self::isBeyondDeadline($pendingPayment)) {

					// cancel order
					OrderRepository::updateInvoiceStatusById($pendingPayment['order_id'], Config::INVOICE_STATUS_CANCELLED);

					// update record
					MultibancoRepository::updateRecordStatus($pendingPayment['order_id'], 'canceled');

					IfthenpayLog::info('cron', 'Multibanco payment expired, invoice status updated to Cancelled: ' . $pendingPayment['order_id']);
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
			$deadline->setTime(23, 59); //set time to 23h:59m
			$deadlineStr = $deadline->format('Y-m-d H:i:s');

			$currentDateTime = new \DateTime('now', $timezone);
			$currentDateTimeStr = $currentDateTime->format('Y-m-d H:i:s');

			return strtotime($deadlineStr) < strtotime($currentDateTimeStr);
		}

		return false;
	}



	public static function getEntityOptions($settings)
	{
		if (!isset($settings[Config::CF_BACKOFFICE_KEY]) || !isset($settings[Config::CF_ACCOUNTS])) {
			return [];
		}

		$accounts = json_decode($settings[Config::CF_ACCOUNTS]);

		$entityOptionArray = [];
		foreach ($accounts as $entity => $subEntity) {
			if (strcasecmp($entity, Config::MULTIBANCO_DYNAMIC) === 0) {
				$entityOptionArray[$entity] = IftpLang::trans('multibanco_dynamic_reference');
				continue;
			}

			$entityOptionArray[$entity] = $entity;
		}
		return $entityOptionArray;
	}



	public static function getSubEntityOptions($settings)
	{
		if (!isset($settings[Config::CF_BACKOFFICE_KEY]) || !isset($settings[Config::CF_ACCOUNTS]) || !isset($settings[Config::CF_MULTIBANCO_ENTITY])) {
			return [];
		}

		$accounts = json_decode($settings[Config::CF_ACCOUNTS], true);

		if (!isset($accounts[$settings['entity']])) {
			return [];
		}

		$subEntityArray = $accounts[$settings['entity']];
		$subEntityOptionArray = [];
		foreach ($subEntityArray as $subEntity) {
			$subEntityOptionArray[$subEntity] = $subEntity;
		}

		return $subEntityOptionArray;
	}



	public static function getDeadlineOptions()
	{
		return [
			'' => IftpLang::trans('no_deadline'),
			'0' => '0',
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
			'7' => '7',
			'8' => '8',
			'9' => '9',
			'10' => '10',
			'11' => '11',
			'12' => '12',
			'13' => '13',
			'14' => '14',
			'15' => '15',
			'16' => '16',
			'17' => '17',
			'18' => '18',
			'19' => '19',
			'20' => '20',
			'21' => '21',
			'22' => '22',
			'23' => '23',
			'24' => '24',
			'25' => '25',
			'26' => '26',
			'27' => '27',
			'28' => '28',
			'29' => '29',
			'30' => '30',
			'31' => '31',
			'32' => '32',
			'45' => '45',
			'60' => '60',
			'90' => '90',
			'120' => '120'
		];
	}



	public static function handleDbCreateUpdate(): void
	{
		if (!Sql::hasTable(Config::MULTIBANCO_TABLE)) {
			Sql::createMultibancoTable();
			return;
		}

		$previousVersionInstalled = GatewaySetting::getValue(Config::MULTIBANCO_MODULE_CODE, Config::CF_INSTALLED_MODULE_VERSION) ?? '0.0.0';
		$newVersionInstalled = Config::MODULE_VERSION;

		if (version_compare($newVersionInstalled, $previousVersionInstalled) == 0) {
			return;
		}

		// If new install, upgrading from old module or just reactivating 
		if ($previousVersionInstalled == '0.0.0' && version_compare('8.0.0', $previousVersionInstalled, '>') == 1) {
			Sql::updateMultibancoTableFromVersion_0_0_0();
		}
	}
}
