<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface PaymentRepositoryInterface 
{
    public function getPaymentByOrderId(string $orderId): array;
}