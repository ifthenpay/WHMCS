<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Data;

use WHMCS\Module\Gateway\Ifthenpay\Base\CheckPaymentStatusBase;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class MultibancoChangePaymentStatus extends CheckPaymentStatusBase
{
    protected $paymentMethod = 'multibanco';

    protected function setGatewayDataBuilder(): void
    {
        $this->gatewayDataBuilder->setBackofficeKey($this->whmcsGatewaySettings['backofficeKey']);
        $this->gatewayDataBuilder->setEntidade($this->whmcsGatewaySettings['entidade']);
        $this->gatewayDataBuilder->setSubEntidade($this->whmcsGatewaySettings['subEntidade']);
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
                $multibancoPayment = $this->paymentRepository->getPaymentByOrderId((string) $pendingInvoice['id']);
                $this->gatewayDataBuilder->setReferencia($multibancoPayment['referencia']);
                if ($this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                    $this->updatePaymentStatus((string) $multibancoPayment['id']);
                    $this->whmcsHistory
                        ->setTransactionId($multibancoPayment)
                        ->setInvoiceId($this->paymentMethod, $multibancoPayment['order_id'])
                        ->processInvoice($pendingInvoice['total'], $multibancoPayment);
                }
            }
            $this->logChangePaymentStatus();
        }
    }
}