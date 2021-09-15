<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments;

use WHMCS\Module\Gateway\Ifthenpay\Payments\PaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface PaymentStatusInterface
{
    public function getPaymentStatus(): bool;
    public function setData(GatewayDataBuilder $data): PaymentStatus;
}