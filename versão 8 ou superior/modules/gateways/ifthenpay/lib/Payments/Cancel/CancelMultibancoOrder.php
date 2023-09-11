<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Cancel;

use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Cancel\CancelOrder;

if (!defined("WHMCS")) {
	die("This file cannot be accessed directly");
}


class CancelMultibancoOrder extends CancelOrder
{
	protected $paymentMethod = Gateway::MULTIBANCO;

	public function cancelOrder(): void
	{
		if ($this->gatewaySettings['cancelMultibancoOrder'] && $this->gatewaySettings['cancelMultibancoOrder'] === 'on') {
			$this->setPendingOrders();
			if (!empty($this->pendingOrders)) {
				foreach ($this->pendingOrders as $order) {
					$multibancoPayment = $this->paymentRepository->getPaymentByOrderId((string) $order['id']);
					if (!empty($multibancoPayment) && $multibancoPayment['requestId'] && $multibancoPayment['validade']) {
						$this->setGatewayDataBuilderBackofficeKey();
						$this->gatewayDataBuilder->setEntidade($this->gatewaySettings['entidade']);
						$this->gatewayDataBuilder->setSubEntidade($this->gatewaySettings['subEntidade']);
						$this->gatewayDataBuilder->setReferencia($multibancoPayment['referencia']);
						$this->gatewayDataBuilder->setTotalToPay($order['total']);
						if (!$this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
							$this->checkTimeChangeStatus($order, $multibancoPayment, $this->gatewaySettings['multibancoValidity']);
						}
						$this->logCancelOrder($multibancoPayment['referencia'], $order);
					} else {
						$this->ifthenpayLogger->info('Multibanco order was not canceled because validaty is not defined', [
							'paymentMethod' => $this->paymentMethod,
							'multibancoPayment' => $multibancoPayment,
							'className' => get_class($this)
						]);
					}
				}
			}
		}
	}
}
