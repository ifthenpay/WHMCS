<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Base\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Base\PaymentBase;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;

class MultibancoBase extends PaymentBase
{
    protected $paymentMethod = Gateway::MULTIBANCO;
    
    protected function setGatewayBuilderData(): void
    {
        $this->gatewayBuilder->setEntidade($this->whmcsGatewaySettings['entidade']);
        $this->gatewayBuilder->setSubEntidade($this->whmcsGatewaySettings['subEntidade']);
        $this->gatewayBuilder->setValidade($this->whmcsGatewaySettings['multibancoValidity'] ? $this->whmcsGatewaySettings['multibancoValidity'] : '999999');
        
        $this->logGatewayBuilderData();
    }

    private function addIdPedidoAndValidadeToDatabaseData(array $paymentData): array
    {
        if ($this->paymentGatewayResultData->idPedido) {
            $paymentData['requestId'] = $this->paymentGatewayResultData->idPedido;
            $paymentData['validade'] = $this->paymentGatewayResultData->validade;
        }
        return $paymentData;
    }

    protected function saveToDatabase(): void
    {
        $paymentData = [
            'entidade' => $this->paymentGatewayResultData->entidade,
            'referencia' => $this->paymentGatewayResultData->referencia, 
            'order_id' => $this->paymentDefaultData->orderId, 
            'status' => 'pending'
        ];
        $paymentData = $this->addIdPedidoAndValidadeToDatabaseData($paymentData);
        $this->paymentRepository->createOrUpdate(['order_id' => $this->paymentDefaultData->orderId], $paymentData);
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
        $paymentData = $this->addIdPedidoAndValidadeToDatabaseData($paymentData);
        $this->paymentRepository->updatePaymentByOrderId($paymentData, $this->paymentDefaultData->orderId);
        $this->logSavePaymentDataInDatabase($paymentData, 'update');
    }
}
