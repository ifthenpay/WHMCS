<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Payments\Payment;
use WHMCS\Module\Gateway\Ifthenpay\Request\Webservice;
use WHMCS\Module\Gateway\Ifthenpay\Builders\DataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Traits\Payments\FormatReference;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\PaymentMethodInterface;

class Payshop extends Payment implements PaymentMethodInterface
{
    use FormatReference;
    
    private $payshopKey;
    protected $validade;
    private $payshopPedido;

    public function __construct(GatewayDataBuilder $data, string $orderId, string $valor, DataBuilder $dataBuilder, Webservice $webservice)
    {
        parent::__construct($orderId, $valor, $dataBuilder, $webservice);
        $this->payshopKey = $data->getData()->payshopKey;
        $this->validade = $this->makeValidade($data->getData()->validade);
    }

    private function makeValidade(string $validade): string
    {

        if ($validade === '0' || $validade === '') {
            return '';
        }
        return (new \DateTime(date("Ymd")))->modify('+' . $validade . 'day')
            ->format('Ymd');
    }

    public function checkValue(): void
    {
        if (intval($this->valor) < 0) {
            throw new \Exception(\Lang::trans('invalidPayshopValue'));
        }
    }

    private function checkEstado(): void
    {
        if ($this->payshopPedido['Code'] !== '0') {
            throw new \Exception($this->payshopPedido['Message']);
        }
    }

    private function setReferencia(): void
    {
        $this->payshopPedido = $this->webservice->postRequest(
            'https://ifthenpay.com/api/payshop/reference/',
            [
                    'payshopkey' => $this->payshopKey,
                    'id' => $this->orderId,
                    'valor' => $this->valor,
                    'validade' => $this->validade,
                ],
            true
        )->getResponseJson();
    }

    private function getReferencia(): DataBuilder
    {
        $this->setReferencia();
        $this->checkEstado();

        $this->dataBuilder->setIdPedido($this->payshopPedido['RequestId']);
        $this->dataBuilder->setReferencia($this->payshopPedido['Reference']);
        $this->dataBuilder->setTotalToPay((string)$this->valor);
        $this->dataBuilder->setValidade($this->validade);
        return $this->dataBuilder;
    }

    public function buy(): DataBuilder
    {
        $this->checkValue();
        return $this->getReferencia();
    }
}
