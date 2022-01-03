<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Traits\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

trait GatewayDataBuilderBackofficeKey
{
    protected function setGatewayDataBuilderBackofficeKey(): void
    {
        $this->gatewayDataBuilder->setBackofficeKey($this->gatewaySettings['backofficeKey']);
    }
}
