<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Builders;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Builders\DataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Builders\PaymentDataBuilderInterface;

class PaymentDataBuilder extends DataBuilder implements PaymentDataBuilderInterface
{
    public function setOrderId(string $value): PaymentDataBuilderInterface
    {
        $this->data->orderId = $value;
        return $this;
    }
}
