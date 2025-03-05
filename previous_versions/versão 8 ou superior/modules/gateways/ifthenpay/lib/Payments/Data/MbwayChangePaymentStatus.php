<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Data;

use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Base\CheckPaymentStatusBase;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class MbwayChangePaymentStatus extends CheckPaymentStatusBase
{
    protected $paymentMethod = Gateway::MBWAY;

    protected function setGatewayDataBuilder(): void
    {
        $this->gatewayDataBuilder->setMbwayKey($this->gatewaySettings['mbwayKey']);
        $this->logGatewayBuilderData();
    }
    
    public function changePaymentStatus(): void
    {
        $this->getPendingOrders();
        if (!empty($this->pendingInvoices)) {
            $this->setGatewayDataBuilder();
            foreach ($this->pendingInvoices as $pendingInvoice) {
                $mbwayPayment = $this->paymentRepository->getPaymentByOrderId((string) $pendingInvoice['id']);
                if(!empty($mbwayPayment)) {
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
                $this->logChangePaymentStatus($mbwayPayment);
            }    
        }
    }
}