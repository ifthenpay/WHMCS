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

class CCard extends Payment implements PaymentMethodInterface
{
    private $ccardKey;
    private $ccardPedido;
    private $successUrl;
    private $errorUrl;
    private $cancelUrl;

    public function __construct(GatewayDataBuilder $data, string $orderId, string $valor, DataBuilder $dataBuilder, WebService $webService)
    {
        parent::__construct($orderId, $valor, $dataBuilder, $webService);
        $this->ccardKey = $data->getData()->ccardKey;
        $this->successUrl = $data->getData()->successUrl;
        $this->errorUrl = $data->getData()->errorUrl;
        $this->cancelUrl = $data->getData()->cancelUrl;
    }

    public function checkValue(): void
    {
        //void
    }

    private function checkEstado(): void
    {
        if ($this->ccardPedido['Status'] !== '0') {
            throw new \Exception($this->ccardPedido['Message']);
        }
    }

    private function setReferencia(): void
    {
        $this->ccardPedido = $this->webService->postRequest(
            'https://ifthenpay.com/api/creditcard/init/' . $this->ccardKey,
            [
                "orderId" => $this->orderId,
                "amount" => $this->valor,
                "successUrl" => $this->successUrl,
                "errorUrl" => $this->errorUrl,
                "cancelUrl" => $this->cancelUrl,
                "language" => "pt"
            ],
            true
        )->getResponseJson();
    }

    private function getReferencia(): DataBuilder
    {
        $this->setReferencia();
        $this->checkEstado();
        
        $this->dataBuilder->setPaymentMessage($this->ccardPedido['Message']);
        $this->dataBuilder->setPaymentUrl($this->ccardPedido['PaymentUrl']);
        $this->dataBuilder->setIdPedido($this->ccardPedido['RequestId']);
        $this->dataBuilder->setPaymentStatus($this->ccardPedido['Status']);

        return $this->dataBuilder;
    }

    public function buy(): DataBuilder
    {
        return $this->getReferencia();
    }
}
