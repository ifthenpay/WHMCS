<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Payments\Payment;
use WHMCS\Module\Gateway\Ifthenpay\Builders\DataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\PaymentMethodInterface;
use WHMCS\Module\Gateway\Ifthenpay\Request\WebService;

class MbWay extends Payment implements PaymentMethodInterface
{
    private $mbwayKey;
    private $telemovel;
    private $mbwayPedido;

    public function __construct(GatewayDataBuilder $data, string $orderId, string $valor, DataBuilder $dataBuilder, WebService $webService)
    {
        parent::__construct($orderId, $valor, $dataBuilder, $webService);
        $this->mbwayKey = $data->getData()->mbwayKey;
        $this->telemovel = $data->getData()->telemovel;
    }

    public function checkValue(): void
    {
        if (intval($this->valor) < 0.10) {
            throw new \Exception(\Lang::trans('mbwayUnderPayment'));
        }
    }

    private function checkEstado(): void
    {
        if ($this->mbwayPedido['Estado'] !== '000') {
            throw new \Exception($this->mbwayPedido['MsgDescricao']);
        }
    }

    private function setReferencia(): void
    {
        $this->mbwayPedido = $this->webService->postRequest(
            'https://mbway.ifthenpay.com/IfthenPayMBW.asmx/SetPedidoJSON',
            [
                    'MbWayKey' => $this->mbwayKey,
                    'canal' => '03',
                    'referencia' => $this->orderId,
                    'valor' => $this->valor,
                    'nrtlm' => $this->telemovel,
                    'email' => '',
                    'descricao' => '',
                ]
        )->getResponseJson();
    }

    private function getReferencia(): DataBuilder
    {
        $this->setReferencia();
        $this->checkEstado();
        $this->dataBuilder->setIdPedido($this->mbwayPedido['IdPedido']);
        $this->dataBuilder->setTelemovel($this->telemovel);
        $this->dataBuilder->setTotalToPay((string)$this->valor);
        return $this->dataBuilder;
    }

    public function buy(): DataBuilder
    {
        $this->checkValue();
        return $this->getReferencia();
    }
}
