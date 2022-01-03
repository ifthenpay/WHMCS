<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Data;

use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Base\CheckPaymentStatusBase;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class PayshopChangePaymentStatus extends CheckPaymentStatusBase
{
    protected $paymentMethod = Gateway::PAYSHOP;

    protected function setGatewayDataBuilder(): void
    {
        $this->setGatewayDataBuilderBackofficeKey();
        $this->gatewayDataBuilder->setPayshopKey($this->gatewaySettings['payshopKey']);
        $this->logGatewayBuilderData();
    }
    
    public function changePaymentStatus(): void
    {
        $this->getPendingOrders();
        if (!empty($this->pendingInvoices)) {
            foreach ($this->pendingInvoices as $pendingInvoice) {
                $this->setGatewayDataBuilder();
                $payshopPayment = $this->paymentRepository->getPaymentByOrderId((string) $pendingInvoice['id']);
                if (!empty($payshopPayment)) {
                    $this->gatewayDataBuilder->setReferencia($payshopPayment['referencia']);
                    if ($this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                        $this->updatePaymentStatus((string) $payshopPayment['id']);
                        $this->whmcsHistory
                            ->setTransactionId($payshopPayment)
                            ->setInvoiceId($this->paymentMethod, $payshopPayment['order_id'])
                            ->processInvoice($pendingInvoice['total'], $payshopPayment);
                    }
                }
                $this->logChangePaymentStatus($payshopPayment);
            }    
        }
    }
}