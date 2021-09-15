<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Data;

use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Payments\MbWayPaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\WhmcsHistoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\MbWayRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\InvoiceRepositoryInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}


class MbwayCancelOrder
{
    private $gatewayDataBuilder;
    private $mbwayPaymentStatus;
    private $gatewaySettings;
    private $invoiceRepository;
    private $mbwayRepository;
    private $whmcsInvoiceHistory;
    private $ifthenpayLogger;
    private $paymentMethod = 'mbway';

    public function __construct(
        GatewayDataBuilder $gatewayDataBuilder, 
        MbWayPaymentStatus $mbwayPaymentStatus,
        InvoiceRepositoryInterface $invoiceRepository,
        MbWayRepositoryInterface $mbwayRepository,
        WhmcsHistoryInterface $whmcsInvoiceHistory,
        IfthenpayLogger $ifthenpayLogger,
        array $gatewaySettings
    )
	{
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->mbwayPaymentStatus = $mbwayPaymentStatus;
        $this->invoiceRepository = $invoiceRepository;
        $this->mbwayRepository = $mbwayRepository;
        $this->whmcsInvoiceHistory = $whmcsInvoiceHistory;
        $this->ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_PAYMENTS)->getLogger();
        $this->gatewaySettings = $gatewaySettings;
	}
    
    
    public function cancelOrder(): void
    {
        if ($this->gatewaySettings['cancelMbwayOrder'] && $this->gatewaySettings['cancelMbwayOrder'] === 'on') {
            $mbwayPendingOrders = $this->invoiceRepository->getAllUnPaidInvoices($this->paymentMethod);
            $this->ifthenpayLogger->info('mbway pending orders retrieved with success', ['mbwayPendingOrders' => $mbwayPendingOrders, 'className' => get_class($this)]);
            if (!empty($mbwayPendingOrders)) {
                date_default_timezone_set('Europe/Lisbon');
                foreach ($mbwayPendingOrders as $mbwayOrder) {
                    $mbwayPayment = $this->mbwayRepository->getPaymentByOrderId((string) $mbwayOrder['id']);
                    $this->gatewayDataBuilder->setMbwayKey($this->gatewaySettings['mbwayKey']);
                    $this->gatewayDataBuilder->setIdPedido($mbwayPayment['id_transacao']);

                    if (!$this->mbwayPaymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                        $minutes_to_add = 30;
                        $time = new \DateTime($mbwayOrder['created_at']);
                        $time->add(new \DateInterval('PT' . $minutes_to_add . 'M'));
                        $today = new \DateTime(date("Y-m-d G:i"));
                        if ($time < $today) {
                            $this->whmcsInvoiceHistory
                                ->setTransactionId($mbwayOrder)
                                ->setInvoiceId($this->paymentMethod, $mbwayPayment['order_id'])
                                ->cancelInvoice($mbwayOrder);
                        }
                    }
                }
                $this->ifthenpayLogger->info('mbway cancel orders with success', ['className' => get_class($this)]);
            }
        }
    }
}


