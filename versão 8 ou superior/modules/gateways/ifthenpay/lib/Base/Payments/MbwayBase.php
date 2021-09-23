<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Base\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Base\PaymentBase;

class MbwayBase extends PaymentBase
{
    protected $paymentMethod = 'mbway';

    protected function setGatewayBuilderData(): void
    {
        $this->gatewayBuilder->setMbwayKey($this->whmcsGatewaySettings['mbwayKey']);
        $this->gatewayBuilder->setTelemovel($_POST['mbwayPhoneNumber']);
        $this->logGatewayBuilderData();
    }

    protected function saveToDatabase(): void
    {
        $paymentData = [
            'id_transacao' => $this->paymentGatewayResultData->idPedido,
            'telemovel' => $this->paymentGatewayResultData->telemovel, 
            'order_id' => $this->paymentDefaultData->orderId, 
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
            'telemovel' => $this->paymentGatewayResultData->telemovel, 
            'order_id' => $this->paymentDefaultData->orderId, 
            'status' => 'pending'
        ];
        //$this->paymentRepository->updatePaymentByOrderId($paymentData, $this->paymentDefaultData->orderId);
        $this->paymentRepository->createOrUpdate(['order_id' => $this->paymentDefaultData->orderId], $paymentData);
        $this->logSavePaymentDataInDatabase($paymentData, 'update');
    }
}
