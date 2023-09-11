<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Cancel;

use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Cancel\CancelOrder;

if (!defined("WHMCS")) {
	die("This file cannot be accessed directly");
}


class CancelPayshopOrder extends CancelOrder
{
	protected $paymentMethod = Gateway::PAYSHOP;

	public function cancelOrder(): void
	{
		if ($this->gatewaySettings['cancelPayshopOrder'] && $this->gatewaySettings['cancelPayshopOrder'] === 'on') {
			$this->setPendingOrders();
			if (!empty($this->pendingOrders)) {
				foreach ($this->pendingOrders as $order) {
					$payshopPayment = $this->paymentRepository->getPaymentByOrderId((string) $order['id']);

					if (!empty($payshopPayment) && $payshopPayment['validade']) {
						$this->setGatewayDataBuilderBackofficeKey();
						$this->gatewayDataBuilder->setPayshopKey($this->gatewaySettings['payshopKey']);
						$this->gatewayDataBuilder->setReferencia($payshopPayment['referencia']);
						$this->gatewayDataBuilder->setTotalToPay($order['total']);
						if (!$this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
							$this->checkTimeChangeStatus($order, $payshopPayment, $this->gatewaySettings['payshopValidity']);
						}
						$this->logCancelOrder($payshopPayment['referencia'], $order);
					}
				}
			}
		}
	}
}
