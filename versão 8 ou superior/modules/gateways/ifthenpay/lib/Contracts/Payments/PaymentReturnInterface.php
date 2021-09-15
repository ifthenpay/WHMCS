<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface PaymentReturnInterface
{
    public function getPaymentReturn(): PaymentReturnInterface;
    public function persistToDatabase(): void;
}
