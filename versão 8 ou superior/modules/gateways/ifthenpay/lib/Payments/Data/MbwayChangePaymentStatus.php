<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Data;

use WHMCS\Module\Gateway\Ifthenpay\Base\CheckPaymentStatusBase;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class MbwayChangePaymentStatus extends CheckPaymentStatusBase
{
    protected $paymentMethod = 'mbway';

    protected function setGatewayDataBuilder(): void
    {
        $this->gatewayDataBuilder->setMbwayKey($this->whmcsGatewaySettings['mbwayKey']);
        $this->logGatewayBuilderData();
    }

    protected function getPendingOrders(): void
    {
        $this->pendingInvoices = $this->invoiceRepository->getAllUnPaidInvoices($this->paymentMethod);
        $this->logGetPendingOrders($this->pendingInvoices);
    }
    
    public function changePaymentStatus(): void
    {
        $this->setGatewayDataBuilder();
        $this->getPendingOrders();
        if (!empty($this->pendingInvoices)) {
            foreach ($this->pendingInvoices as $pendingInvoice) {
                $mbwayPayment = $this->paymentRepository->getPaymentByOrderId((string) $pendingInvoice['id']);
                $this->gatewayDataBuilder->setIdPedido($mbwayPayment['id_transacao']);
                if ($this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                    $this->updatePaymentStatus((string) $mbwayPayment['id']);
                    $this->whmcsHistory
                        ->loadWhmcsFunctions()
                        ->setTransactionId($pendingInvoice)
                        ->setInvoiceId($this->paymentMethod, $mbwayPayment['order_id'])
                        ->processInvoice($pendingInvoice['total'], $mbwayPayment);
                }
            }
            $this->logChangePaymentStatus();
        }
    }
}