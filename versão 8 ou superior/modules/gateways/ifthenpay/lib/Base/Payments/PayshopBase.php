<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Base\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Base\PaymentBase;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;

class PayshopBase extends PaymentBase
{
    protected $paymentMethod = Gateway::PAYSHOP;

    protected function setGatewayBuilderData(): void
    {
        $this->gatewayBuilder->setPayshopKey($this->whmcsGatewaySettings['payshopKey']);
        $this->gatewayBuilder->setValidade($this->whmcsGatewaySettings['payshopValidity']);
        $this->logGatewayBuilderData();
    }

    protected function saveToDatabase(): void
    {
        $paymentData = [
            'id_transacao' => $this->paymentGatewayResultData->idPedido,
            'referencia' => $this->paymentGatewayResultData->referencia, 
            'order_id' => $this->paymentDefaultData->orderId,
            'validade' => $this->paymentGatewayResultData->validade,
            'status' => 'pending'
        ];
        $this->paymentRepository->createOrUpdate(['order_id' => $this->paymentDefaultData->orderId], $paymentData);
        //$this->paymentRepository->create($paymentData);
        $this->logSavePaymentDataInDatabase($paymentData);
    }

    protected function updateToDatabase(): void
    {
        $paymentData = [
            'id_transacao' => $this->paymentGatewayResultData->idPedido,
            'referencia' => $this->paymentGatewayResultData->referencia, 
            'order_id' => $this->paymentDefaultData->orderId,
            'validade' => $this->paymentGatewayResultData->validade,
            'status' => 'pending'
        ];
        $this->paymentRepository->createOrUpdate(['order_id' => $this->paymentDefaultData->orderId], $paymentData);
        //$this->paymentRepository->updatePaymentByOrderId($paymentData, $this->paymentDefaultData->orderId);
        $this->logSavePaymentDataInDatabase($paymentData, 'update');
    }
}
