<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface InvoiceUpdateInterface
{
    public function checkPaymentMethod(): bool;
}
