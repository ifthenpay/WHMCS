<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Factory\Payment;

use Illuminate\Container\Container;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Factory;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Request\WebService;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Base\CheckPaymentStatusBase;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Payments\CCardPaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Payments\MbWayPaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Payments\PayshopPaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Payments\MultibancoPaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Repository\RepositoryFactory;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\CCardChangePaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\MbwayChangePaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\ConvertEurosInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\WhmcsHistoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\PayshopChangePaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\MultibancoChangePaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ClientRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\InvoiceRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\CurrencieRepositoryInterface;



if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}


class PaymentChangeStatusFactory extends Factory
{
    private $gatewayDataBuilder;
    private $webService;
    private $invoiceRepository;
    private $repositoryFactory;
    private $whmcsHistory;
    private $ifthenpayLogger;
    private $currencieRepository;
    private $clientRepository;
    private $convertEuros;
    private $gatewaySettings;

    public function __construct(
        Container $ioc, 
        GatewayDataBuilder $gatewayDataBuilder,
        WebService $webService,
        InvoiceRepositoryInterface $invoiceRepository,
        RepositoryFactory $repositoryFactory,
        WhmcsHistoryInterface $whmcsHistory,
        IfthenpayLogger $ifthenpayLogger,
        CurrencieRepositoryInterface $currencieRepository,
        ClientRepositoryInterface $clientRepository,
        ConvertEurosInterface $convertEuros,
        array $gatewaySettings
    )
	{
        parent::__construct($ioc);
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->webService = $webService;
        $this->invoiceRepository = $invoiceRepository;
        $this->repositoryFactory = $repositoryFactory;
        $this->whmcsHistory = $whmcsHistory;
        $this->ifthenpayLogger = $ifthenpayLogger;
        $this->currencieRepository = $currencieRepository;
        $this->clientRepository = $clientRepository;
        $this->convertEuros = $convertEuros;
        $this->gatewaySettings = $gatewaySettings;
    }

    public function build(): CheckPaymentStatusBase {
        switch ($this->type) {
            case Gateway::MULTIBANCO:
                return new MultibancoChangePaymentStatus(
                    $this->gatewayDataBuilder,
                    $this->ioc->make(MultibancoPaymentStatus::class),
                    $this->webService,
                    $this->invoiceRepository,
                    $this->repositoryFactory,
                    $this->whmcsHistory,
                    $this->ifthenpayLogger,
                    $this->gatewaySettings
                );
            case Gateway::MBWAY:
                return new MbwayChangePaymentStatus(
                    $this->gatewayDataBuilder,
                    $this->ioc->make(MbWayPaymentStatus::class),
                    $this->webService,
                    $this->invoiceRepository,
                    $this->repositoryFactory,
                    $this->whmcsHistory,
                    $this->ifthenpayLogger,
                    $this->gatewaySettings
                );
            case Gateway::PAYSHOP:
                return new PayshopChangePaymentStatus(
                    $this->gatewayDataBuilder,
                    $this->ioc->make(PayshopPaymentStatus::class),
                    $this->webService,
                    $this->invoiceRepository,
                    $this->repositoryFactory,
                    $this->whmcsHistory,
                    $this->ifthenpayLogger,
                    $this->gatewaySettings
                );
            case Gateway::CCARD:
                return new CCardChangePaymentStatus(
                    $this->gatewayDataBuilder,
                    $this->ioc->make(CCardPaymentStatus::class),
                    $this->webService,
                    $this->invoiceRepository,
                    $this->repositoryFactory,
                    $this->whmcsHistory,
                    $this->ifthenpayLogger,
                    $this->currencieRepository,
                    $this->clientRepository,
                    $this->convertEuros,
                    $this->gatewaySettings
                );
            default:
                throw new \Exception('Unknown Payment Change Status Class');
        }
    }
}
