<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Cancel;

use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Cancel\CancelOrder;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}


class CancelMbwayOrder extends CancelOrder
{
    protected $paymentMethod = Gateway::MBWAY;
  
    public function cancelOrder(): void
    {
        if ($this->gatewaySettings['cancelMbwayOrder'] && $this->gatewaySettings['cancelMbwayOrder'] === 'on') {
            $this->setPendingOrders();
            if (!empty($this->pendingOrders)) {
                foreach ($this->pendingOrders as $order) {
                    $mbwayPayment = $this->paymentRepository->getPaymentByOrderId((string) $order['id']);
                    if (!empty($mbwayPayment)) {
                        $this->gatewayDataBuilder->setMbwayKey($this->gatewaySettings['mbwayKey']);
                        $this->gatewayDataBuilder->setIdPedido($mbwayPayment['id_transacao']);
                        if (!$this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                            $this->checkTimeChangeStatus($order, $mbwayPayment);
                        }
                        $this->logCancelOrder($mbwayPayment['id_transacao'], $order);
                    }
                    
                }
            }
        }
    }
}


