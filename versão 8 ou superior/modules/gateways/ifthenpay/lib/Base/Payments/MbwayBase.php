<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Base\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Base\PaymentBase;
use WHMCS\Database\Capsule;

class MbwayBase extends PaymentBase
{
    protected $paymentMethod = 'mbway';

    protected function setGatewayBuilderData(): void
    {
        $this->gatewayBuilder->setMbwayKey($this->whmcsGatewaySettings['mbwayKey']);
        $this->gatewayBuilder->setTelemovel($_POST['mbwayPhoneNumber'] ? $_POST['mbwayPhoneNumber'] : $_COOKIE['mbwayPhoneNumber']);
    }

    protected function saveToDatabase(): void
    {
        Capsule::table($this->paymentTable)->insert(
            [
                'id_transacao' => $this->paymentGatewayResultData->idPedido,
                'telemovel' => $this->paymentGatewayResultData->telemovel, 
                'order_id' => $this->paymentDefaultData->orderId, 
                'status' => 'pending'
            ]
        );
    }
}
