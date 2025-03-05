<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Base;

use WHMCS\Module\Gateway\Ifthenpay\Request\WebService;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Traits\Logs\LogGatewayBuilderData;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Repository\RepositoryFactory;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\WhmcsHistoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\PaymentStatusInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\InvoiceRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Traits\Payments\GatewayDataBuilderBackofficeKey;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}


abstract class CheckPaymentStatusBase
{
    use LogGatewayBuilderData, GatewayDataBuilderBackofficeKey;
    
    protected $paymentMethod;
    protected $gatewayDataBuilder;
    protected $gatewaySettings;
    protected $paymentStatus;
    protected $pendingInvoices;
    protected $webService;
    protected $orderInvoiceRepository;
    protected $paymentRepository;
    protected $whmcsHistory;
    protected $ifthenpayLogger;

    public function __construct(
        GatewayDataBuilder $gatewayDataBuilder,
        PaymentStatusInterface $paymentStatus,
        WebService $webService,
        InvoiceRepositoryInterface $invoiceRepository,
        RepositoryFactory $repositoryFactory,
        WhmcsHistoryInterface $whmcsHistory,
        IfthenpayLogger $ifthenpayLogger,
        array $gatewaySettings
    ) {
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->paymentStatus = $paymentStatus;
        $this->gatewaySettings = $gatewaySettings;
        $this->webService = $webService;
        $this->invoiceRepository = $invoiceRepository;
        $this->paymentRepository = $repositoryFactory->setType($this->paymentMethod)->build();
        $this->ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_PAYMENTS)->getLogger();
        $this->whmcsHistory = $whmcsHistory;
    }

    protected function updatePaymentStatus(string $id): void
    {
        $this->paymentRepository->update(['status' => 'paid'], $id);
        $this->ifthenpayLogger->info('payment status updated with success in database', [
                'paymentMethod' => $this->paymentMethod,
                'status' => 'paid',
                'id' => $id,
                'className' => get_class($this)
            ]
        );
    }

    protected function logGetPendingOrders(array $data): void
    {
        $this->ifthenpayLogger->info('pending orders retrieved with success from database', [
                'paymentMethod' => $this->paymentMethod,
                'pendingOrders' => $data,
                'className' => get_class($this)
            ]
        );
    }

    protected function logChangePaymentStatus(array $payment): void
    {
        $this->ifthenpayLogger->info('payment status changed with success', [
                'payment' => $payment,
                'paymentMethod' => $this->paymentMethod,
                'className' => get_class($this)
            ]
        );
    }

    protected function getPendingOrders(): void
    {
        $this->pendingInvoices = $this->invoiceRepository->getAllUnPaidInvoices($this->paymentMethod);
        $this->logGetPendingOrders($this->pendingInvoices);
    }

    abstract protected function setGatewayDataBuilder(): void;
    abstract public function changePaymentStatus(): void;
}
