<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Cancel;

use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Payment\PaymentStatusFactory;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Repository\RepositoryFactory;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\WhmcsHistoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Traits\Payments\GatewayDataBuilderBackofficeKey;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\InvoiceRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Utility\Time as IftpTime;

if (!defined("WHMCS")) {
	die("This file cannot be accessed directly");
}


abstract class CancelOrder
{
	use GatewayDataBuilderBackofficeKey;

	protected $gatewayDataBuilder;
	protected $paymentStatus;
	protected $gatewaySettings;
	protected $invoiceRepository;
	protected $paymentRepository;
	protected $whmcsInvoiceHistory;
	protected $ifthenpayLogger;
	protected $pendingOrders;
	protected $paymentMethod;

	public function __construct(
		GatewayDataBuilder $gatewayDataBuilder,
		PaymentStatusFactory $paymentStatusFactory,
		InvoiceRepositoryInterface $invoiceRepository,
		RepositoryFactory $repositoryFactory,
		WhmcsHistoryInterface $whmcsInvoiceHistory,
		IfthenpayLogger $ifthenpayLogger,
		array $gatewaySettings
	) {
		$this->gatewayDataBuilder = $gatewayDataBuilder;
		$this->paymentStatus = $paymentStatusFactory->setType($this->paymentMethod)->build();
		$this->invoiceRepository = $invoiceRepository;
		$this->paymentRepository = $repositoryFactory->setType($this->paymentMethod)->build();
		$this->whmcsInvoiceHistory = $whmcsInvoiceHistory;
		$this->ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_PAYMENTS)->getLogger();
		$this->gatewaySettings = $gatewaySettings;
	}

	protected function setPendingOrders(): void
	{
		$this->pendingOrders = $this->invoiceRepository->getAllUnPaidInvoices($this->paymentMethod);
		$this->ifthenpayLogger->info('pending orders retrieved with success', [
			'paymentMethod' => $this->paymentMethod,
			'pendingOrders' => $this->pendingOrders,
			'className' => get_class($this)
		]);
	}


	protected function checkTimeChangeStatus(array $order, array $ifthenpayPayment, string $days = null)
	{

		$deadlineStr = $this->getDeadlineForPaymentMethod($this->paymentMethod, $ifthenpayPayment);

		if ($deadlineStr == '') {
			return;
		}


		$currentTimeStr = IftpTime::getCurrentDateTimeStringForLisbon();



		if (strtotime($deadlineStr) < strtotime($currentTimeStr)) {

			$this->whmcsInvoiceHistory
				->setTransactionId($order)
				->setInvoiceId($this->paymentMethod, $ifthenpayPayment['order_id'])
				->cancelInvoice($order);
		}
	}

	protected function logCancelOrder(string $idPedido, array $orderData): void
	{
		$this->ifthenpayLogger->info($this->paymentMethod . ' cancel orders with success', [
			'paymentMethod' => $this->paymentMethod,
			'idPedido' => $idPedido,
			'order' => $orderData,
			'className' => get_class($this)
		]);
	}

	private function getDeadlineForPaymentMethod(string $paymentMethod, array $paymentData): string
	{

		switch ($paymentMethod) {
			case 'multibanco':

				$deadline = IftpTime::createDateTimeForLisbonFrom($paymentData['validade'], 'd-m-Y');
				$deadline->setTime(23, 59);
				break;

			case 'payshop':
				$deadline = IftpTime::createDateTimeForLisbonFrom($paymentData['validade'], 'Ymd');
				$deadline->setTime(0, 0);
				break;

			case 'mbway':
				$deadline = IftpTime::createDateTimeForLisbonFrom($paymentData['created_at'], 'Y-m-d H:i:s');
				$deadline->add(new \DateInterval('PT' . 30 . 'M'));
				break;

			case 'ccard':
				$deadline = IftpTime::createDateTimeForLisbonFrom($paymentData['created_at'], 'Y-m-d H:i:s');
				$deadline->add(new \DateInterval('PT' . 30 . 'M'));
				break;

			default:
				return '';
		}

		return $deadline->format('Y-m-d H:i:s');
	}

	abstract function cancelOrder(): void;
}
