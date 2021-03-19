<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Base\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Base\PaymentBase;
use WHMCS\Database\Capsule;

class PayshopBase extends PaymentBase
{
    protected $paymentMethod = 'payshop';

    protected function setGatewayBuilderData(): void
    {
        $this->gatewayBuilder->setPayshopKey($this->whmcsGatewaySettings['payshopKey']);
        $this->gatewayBuilder->setValidade($this->whmcsGatewaySettings['payshopValidity']);
    }

    protected function saveToDatabase(): void
    {
        Capsule::table($this->paymentTable)->insert(
            [
                'id_transacao' => $this->paymentGatewayResultData->idPedido,
                'referencia' => $this->paymentGatewayResultData->referencia, 
                'order_id' => $this->paymentDefaultData->orderId,
                'validade' => $this->paymentGatewayResultData->validade,
                'status' => 'pending'
            ]
        );
    }
}
