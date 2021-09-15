<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Factory\Payment;

use Illuminate\Container\Container;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Factory;
use WHMCS\Module\Gateway\Ifthenpay\Request\WebService;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Base\CheckPaymentStatusBase;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Payments\MbWayPaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Payments\PayshopPaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Payments\MultibancoPaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Repository\RepositoryFactory;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\MbwayChangePaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\WhmcsHistoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\PayshopChangePaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\MultibancoChangePaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\InvoiceRepositoryInterface;


if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}


class PaymentStatusFactory extends Factory
{
    private $gatewayDataBuilder;
    private $invoiceRepository;
    private $repositoryFactory;
    private $whmcsHistory;
    private $ifthenpayLogger;
    private $whmcsGatewaySettings;

    public function __construct(
        Container $ioc, 
        GatewayDataBuilder $gatewayDataBuilder, 
        InvoiceRepositoryInterface $invoiceRepository,
        RepositoryFactory $repositoryFactory,
        WhmcsHistoryInterface $whmcsHistory,
        IfthenpayLogger $ifthenpayLogger,
        array $whmcsGatewaySettings
    )
	{
        parent::__construct($ioc);
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->invoiceRepository = $invoiceRepository;
        $this->repositoryFactory = $repositoryFactory;
        $this->whmcsHistory = $whmcsHistory;
        $this->ifthenpayLogger = $ifthenpayLogger;
        $this->whmcsGatewaySettings = $whmcsGatewaySettings;
    }

    public function build(): CheckPaymentStatusBase {
        switch ($this->type) {
            case 'multibanco':
                return new MultibancoChangePaymentStatus(
                    $this->gatewayDataBuilder, 
                    $this->ioc->make(MultibancoPaymentStatus::class), 
                    $this->ioc->make(WebService::class),
                    $this->invoiceRepository,
                    $this->repositoryFactory,
                    $this->whmcsHistory,
                    $this->ifthenpayLogger,
                    $this->whmcsGatewaySettings
                );
            case 'mbway':
                return new MbwayChangePaymentStatus(
                    $this->gatewayDataBuilder, 
                    $this->ioc->make(MbWayPaymentStatus::class), 
                    $this->ioc->make(WebService::class),
                    $this->invoiceRepository,
                    $this->repositoryFactory,
                    $this->whmcsHistory,
                    $this->ifthenpayLogger,
                    $this->whmcsGatewaySettings
                );
            case 'payshop':
                return new PayshopChangePaymentStatus(
                    $this->gatewayDataBuilder, 
                    $this->ioc->make(PayshopPaymentStatus::class), 
                    $this->ioc->make(WebService::class),
                    $this->invoiceRepository,
                    $this->repositoryFactory,
                    $this->whmcsHistory,
                    $this->ifthenpayLogger,
                    $this->whmcsGatewaySettings
                );
            default:
                throw new \Exception('Unknown Payment Change Status Class');
        }
    }
}
