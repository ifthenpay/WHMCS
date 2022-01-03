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
        $this->setGatewayDataBuilderBackofficeKey();
        $this->gatewayDataBuilder->setEntidade($this->gatewaySettings['entidade']);
        $this->gatewayDataBuilder->setSubEntidade($this->gatewaySettings['subEntidade']);
        $this->logGatewayBuilderData();
    }
    
    public function changePaymentStatus(): void
    {
        $this->getPendingOrders();
        if (!empty($this->pendingInvoices)) {
            $this->setGatewayDataBuilder();
            foreach ($this->pendingInvoices as $pendingInvoice) {
                $multibancoPayment = $this->paymentRepository->getPaymentByOrderId((string) $pendingInvoice['id']);
                if (!empty($multibancoPayment)) {
                    $this->gatewayDataBuilder->setReferencia($multibancoPayment['referencia']);
                    if ($this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                        $this->updatePaymentStatus((string) $multibancoPayment['id']);
                        $this->whmcsHistory
                            ->setTransactionId($multibancoPayment)
                            ->setInvoiceId($this->paymentMethod, $multibancoPayment['order_id'])
                            ->processInvoice($pendingInvoice['total'], $multibancoPayment);
                    }
                    $this->logChangePaymentStatus($multibancoPayment);
                }
            }
        }
    }
}