<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Base\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Base\PaymentBase;
use WHMCS\Database\Capsule;

class MultibancoBase extends PaymentBase
{
    protected $paymentMethod = 'multibanco';
    
    protected function setGatewayBuilderData(): void
    {
        $this->gatewayBuilder->setEntidade($this->whmcsGatewaySettings['entidade']);
        $this->gatewayBuilder->setSubEntidade($this->whmcsGatewaySettings['subEntidade']);
    }

    protected function saveToDatabase(): void
    {
        Capsule::table($this->paymentTable)->insert(
            [
                'entidade' => $this->paymentGatewayResultData->entidade,
                'referencia' => $this->paymentGatewayResultData->referencia, 
                'order_id' => $this->paymentDefaultData->orderId, 
                'status' => 'pending'
            ]
        );
    }
}
