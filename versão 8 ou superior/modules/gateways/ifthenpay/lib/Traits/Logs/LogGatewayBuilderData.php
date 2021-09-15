<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Traits\Logs;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

trait LogGatewayBuilderData
{
    protected function logGatewayBuilderData(): void
    {
        $this->ifthenpayLogger->info('gateway builder data set with success', [
                'paymentMethod' => $this->paymentMethod,
                'gatewayBuilder' => $this->gatewayBuilder,
                'className' => get_class($this)
            ]
        );
    }
}
