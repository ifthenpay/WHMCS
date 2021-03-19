<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Models;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface PaymentModelInterface
{
    public static function getByOrderId(string $orderId): array;
}
