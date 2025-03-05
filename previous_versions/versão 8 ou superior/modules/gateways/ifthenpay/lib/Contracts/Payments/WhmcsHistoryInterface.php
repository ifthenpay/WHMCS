<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface WhmcsHistoryInterface
{
    public function setInvoiceId(string $paymentMethod, string $orderId): WhmcsHistoryInterface;
    public function setTransactionId(array $paymentData): WhmcsHistoryInterface;
    public function processInvoice(string $ammount, array $invoice, string $transId = null): void;
    public function cancelInvoice(array $invoice): void;
    public function getInvoiceId(): int;
}