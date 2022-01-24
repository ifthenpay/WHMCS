<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments;

use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\WhmcsHistoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\InvoiceRepositoryInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class WhmcsInvoiceHistory implements WhmcsHistoryInterface
{
    private $transId;
    private $invoiceRepository;
    private $invoiceId;
    private $paymentMethod;
    private $orderId;
    private $ifthenpayLogger;

    const STATUS_CANCELED = 'Cancelled';
    const STATUS_PAID = 'Paid';
    const STATUS_PENDING = 'Unpaid';

    public function __construct(InvoiceRepositoryInterface $invoiceRepository, IfthenpayLogger $ifthenpayLogger)
	{
        $this->invoiceRepository = $invoiceRepository;
        $this->ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_PAYMENTS)->getLogger();
	}
    

    public function setTransactionId(array $paymentData): WhmcsHistoryInterface
    {
        if ($paymentData['entidade']) {
            $this->transId = $paymentData['entidade'] . $paymentData['referencia'] . $paymentData['order_id'];
        } else {
            $this->transId = $paymentData['id_transacao'] . $paymentData['order_id'];
        }
        $this->ifthenpayLogger->info('transaction id set with success', [
                'paymentMethod' => $this->paymentMethod, 
                'transactionId' => $this->transId, 
                'className' => get_class($this)
            ]
        );
        return $this;
    }

    public function setInvoiceId(string $paymentMethod, string $orderId): WhmcsHistoryInterface
    {
        $this->paymentMethod = $paymentMethod;
        $this->orderId = $orderId;
        $this->invoiceId = checkCbInvoiceID($this->orderId, $this->paymentMethod);
        $this->ifthenpayLogger->info('invoice id set with success', [
                'paymentMethod' => $paymentMethod,
                'orderId' => $orderId,
                'invoiceId' => $this->invoiceId,
                'className' => get_class($this)
            ]
        );
        return $this;
    }

    public function processInvoice(string $ammount, array $invoice, string $transId = null): void
    {
        try {
            $this->transId = !is_null($transId) ? $transId : $this->transId;
            checkCbTransID($this->transId);
            addInvoicePayment($this->invoiceId, $this->transId, $ammount, '', $this->paymentMethod);
            logTransaction($this->paymentMethod, $invoice, \Lang::trans('paymentSuccessful'));
            $this->ifthenpayLogger->info('process invoice with success', [
                    'paymentMethod' => $this->paymentMethod,
                    'transactionId' => $this->transId,
                    'invoiceId' => $this->invoiceId,
                    'ammount' => $ammount,
                    'className' => get_class($this)
                ]
            );
        } catch (\Throwable $th) {
            $this->ifthenpayLogger->error('error processing invoice', [
                    'paymentMethod' => $this->paymentMethod,
                    'transactionId' => $this->transId,
                    'invoiceId' => $this->invoiceId,
                    'ammount' => $ammount,
                    'className' => get_class($this)
                ]
            );
            throw $th;
        }
    }

    public function cancelInvoice(array $invoice): void
    {
        $this->invoiceRepository->update(['status' => self::STATUS_CANCELED], (string) $this->invoiceId);
        logTransaction($this->paymentMethod, $invoice, \Lang::trans('mbwayExpiredCanceled'));
        $this->ifthenpayLogger->info('cancel invoice with success', [
                'paymentMethod' => $this->paymentMethod,
                'invoice' => $this->invoiceId,
                'className' => get_class($this)
            ]
        );
    }

    /**
     * Get the value of invoiceId
     */ 
    public function getInvoiceId(): int
    {
        return $this->invoiceId;
    }
}