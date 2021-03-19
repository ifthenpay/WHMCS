<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Builders\DataBuilder;

interface PaymentMethodInterface
{
    public function checkValue(): void;
    public function buy(): DataBuilder;
}
