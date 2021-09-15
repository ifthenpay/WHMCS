<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Base\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Base\PaymentBase;

class MultibancoBase extends PaymentBase
{
    protected $paymentMethod = 'multibanco';
    
    protected function setGatewayBuilderData(): void
    {
        $this->gatewayBuilder->setEntidade($this->whmcsGatewaySettings['entidade']);
        $this->gatewayBuilder->setSubEntidade($this->whmcsGatewaySettings['subEntidade']);
        $this->logGatewayBuilderData();
    }

    protected function saveToDatabase(): void
    {
        $paymentData = [
            'entidade' => $this->paymentGatewayResultData->entidade,
            'referencia' => $this->paymentGatewayResultData->referencia, 
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
            'entidade' => $this->paymentGatewayResultData->entidade,
            'referencia' => $this->paymentGatewayResultData->referencia, 
            'order_id' => $this->paymentDefaultData->orderId, 
            'status' => 'pending'
        ];
        $this->paymentRepository->updatePaymentByOrderId($paymentData, $this->paymentDefaultData->orderId);
        $this->logSavePaymentDataInDatabase($paymentData, 'update');
    }
}
