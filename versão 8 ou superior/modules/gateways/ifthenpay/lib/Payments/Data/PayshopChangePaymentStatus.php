<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Data;

use WHMCS\Module\Gateway\Ifthenpay\Base\CheckPaymentStatusBase;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class PayshopChangePaymentStatus extends CheckPaymentStatusBase
{
    protected $paymentMethod = 'payshop';

    protected function setGatewayDataBuilder(): void
    {
        $this->gatewayDataBuilder->setBackofficeKey($this->whmcsGatewaySettings['backofficeKey']);
        $this->gatewayDataBuilder->setPayshopKey($this->whmcsGatewaySettings['payshopKey']);
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
                $payshopPayment = $this->paymentRepository->getPaymentByOrderId((string) $pendingInvoice['id']);
                $this->gatewayDataBuilder->setReferencia($payshopPayment['referencia']);
                if ($this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                    $this->updatePaymentStatus((string) $payshopPayment['id']);
                    $this->whmcsHistory
                        ->setTransactionId($payshopPayment)
                        ->setInvoiceId($this->paymentMethod, $payshopPayment['order_id'])
                        ->processInvoice($pendingInvoice['total'], $payshopPayment);
                }
            }
            $this->logChangePaymentStatus();
        }
    }
}