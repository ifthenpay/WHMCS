<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments;

use WHMCS\Module\Gateway\Ifthenpay\Payments\PaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\PaymentStatusInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class MbWayPaymentStatus extends PaymentStatus implements PaymentStatusInterface
{
    private $mbwayPedido;

    private function checkEstado(): bool
    {
        if ($this->mbwayPedido['EstadoPedidos'][0]['Estado'] === '000') {
            return true;
        }
        return false;
    }

    private function getMbwayEstado(): void
    {
        $this->mbwayPedido = $this->webservice->postRequest(
            'https://mbway.ifthenpay.com/IfthenPayMBW.asmx/EstadoPedidosJSON',
                [
                    'MbWayKey' => $this->data->getData()->mbwayKey,
                    'canal' => '03',
                    'idspagamento' => $this->data->getData()->idPedido
                ]
        )->getResponseJson();
    }

    public function getPaymentStatus(): bool
    {
        $this->getMbwayEstado();
        $this->ifthenpayLogger->info('mbway payment status request executed with success', [
                'data' => $this->data,
                'mbwayPedido' => $this->mbwayPedido,
                'className' => get_class($this)
            ]
        );
        return $this->checkEstado();
    }
}