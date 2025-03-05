<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpaylib\Repository;

use WHMCS\Database\Capsule;
use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;

class OrderRepository
{


	public static function getOrderByInvoiceId(string $invoiceId): array
	{
		$order = Capsule::table('tblorders')->where('invoiceid', $invoiceId)->first();

		return $order ? (array) $order : [];
	}


	public static function getPendingInvoicesByPaymentMethod(string $paymentMethod): array
	{
		$order = Capsule::table('tblinvoices')
		->where('paymentmethod', $paymentMethod)
		->where('status', Config::INVOICE_STATUS_PENDING)
		->get()
		->toArray();
		return $order ? (array) $order : [];
	}




	public static function getInvoiceById(string $invoiceId): array
	{
		$order = Capsule::table('tblinvoices')->where('id', $invoiceId)->first();

		return $order ? (array) $order : [];
	}



	public static function updateInvoiceStatusById(string $invoiceId, string $status): void
	{
		Capsule::table('tblinvoices')->where('id', $invoiceId)
		->update(['status' => $status]);
	}


	public static function updateInvoiceStatusToCancelledByIdIfStatusPending(string $invoiceId): void
	{
		Capsule::table('tblinvoices')->where('id', $invoiceId)
		->where('status', Config::INVOICE_STATUS_PENDING)
		->update(['status' => Config::INVOICE_STATUS_CANCELLED]);
	}

}
