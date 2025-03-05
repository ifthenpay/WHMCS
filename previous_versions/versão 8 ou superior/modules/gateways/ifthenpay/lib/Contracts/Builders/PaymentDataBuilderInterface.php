<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Builders;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface PaymentDataBuilderInterface
{
    public function setOrderId(string $value): PaymentDataBuilderInterface;
}
