<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpaylib\Repository;

use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Database\Capsule;
use WHMCS\Module\Gateway\ifthenpaylib\Services\UtilsService;

class PixRepository
{

	/**
	 * clear config
	 * Expected NOT to remove Config::CF_INSTALLED_MODULE_VERSION
	 */
	public static function resetConfig(): void
	{
		Capsule::table('tblpaymentgateways')
			->where('gateway', Config::PIX_MODULE_CODE)
			->whereIn('setting', [
				Config::CF_BACKOFFICE_KEY,
				Config::CF_PIX_KEY,
				Config::CF_MIN_AMOUNT,
				Config::CF_MAX_AMOUNT,
				Config::CF_SHOWICON,
				Config::CF_CAN_CANCEL,
				Config::CF_CAN_ACTIVATE_CALLBACK,
				Config::CF_ANTIPHISHING_KEY,
				Config::CF_CALLBACK_URL,
				Config::CF_CALLBACK_STATUS,
				Config::CF_CALLBACK_INFO,
				Config::CF_ACCOUNTS,
				Config::CF_UPGRADE
			])->delete();
	}



	public static function savePayment(array $data): void
	{
		try {
			$dateTime = UtilsService::dateTime();

			$exists = Capsule::table(Config::PIX_TABLE)
				->where('order_id', $data['order_id'])
				->exists();

			if (!$exists) {

				$data['created_at'] = $dateTime;
				$data['updated_at'] = $dateTime;
				Capsule::table(Config::PIX_TABLE)
					->insert($data);
			} else {

				$data['updated_at'] = $dateTime;
				Capsule::table(Config::PIX_TABLE)
					->where('order_id', $data['order_id'])
					->update($data);
			}

			IfthenpayLog::info(Config::PIX, 'Payment details saved successfully.', ['record_data' => $data]);
		} catch (\Throwable $th) {
			IfthenpayLog::error(Config::PIX, 'Unexpected error saving payment record', $th->__toString());
		}
	}



	public static function getPaymentRecordByInvoiceId(string $invoiceId): array
	{
		$paymentData = Capsule::table(Config::PIX_TABLE)
			->where('order_id', $invoiceId)
			->first();

		return $paymentData ? (array) $paymentData : [];
	}



	public static function getPaymentRecordByTransactionId(string $transactionId): array
	{
		$paymentData = Capsule::table(Config::PIX_TABLE)
			->where('transaction_id', $transactionId)
			->first();

		return $paymentData ? (array) $paymentData : [];
	}



	public static function updateRecordStatus(string $orderId, string $status): void
	{
		Capsule::table(Config::PIX_TABLE)
			->where('order_id', $orderId)
			->update(['status' => $status]);
	}



	public static function getPendingPayments(): array
	{
		$payments = Capsule::table(Config::PIX_TABLE)
			->where('status', Config::RECORD_STATUS_PENDING)
			->orWhere('status', Config::RECORD_STATUS_INITIALIZED)
			->get()
			->toArray();

		return json_decode(json_encode($payments), true);
	}
}
