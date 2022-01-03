<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Factory\Cancel;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Factory\Factory;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Cancel\CancelOrder;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Cancel\CancelCCardOrder;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Cancel\CancelMbwayOrder;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Cancel\CancelPayshopOrder;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Payment\PaymentStatusFactory;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Repository\RepositoryFactory;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Cancel\CancelMultibancoOrder;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\ConvertEurosInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\WhmcsHistoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ClientRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\InvoiceRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\CurrencieRepositoryInterface;


class CancelIfthenpayOrderFactory extends Factory
{
    private $gatewayDataBuilder; 
    private $paymentStatusFactory;
    private $invoiceRepository;
    private $repositoryFactory;
    private $whmcsInvoiceHistory;
    private $ifthenpayLogger;
    private $gatewaySettings;
    private $clientRepository;
    private $currencieRepository;
    private $convertEuros;

    public function __construct(
        GatewayDataBuilder $gatewayDataBuilder, 
        PaymentStatusFactory $paymentStatusFactory,
        InvoiceRepositoryInterface $invoiceRepository,
        RepositoryFactory $repositoryFactory,
        WhmcsHistoryInterface $whmcsInvoiceHistory,
        IfthenpayLogger $ifthenpayLogger,
        array $gatewaySettings,
        ClientRepositoryInterface $clientRepository,
        CurrencieRepositoryInterface $currencieRepository,
        ConvertEurosInterface $convertEuros
    )
	{
        $this->gatewayDataBuilder = $gatewayDataBuilder; 
        $this->paymentStatusFactory = $paymentStatusFactory;
        $this->invoiceRepository = $invoiceRepository;
        $this->repositoryFactory = $repositoryFactory;
        $this->whmcsInvoiceHistory = $whmcsInvoiceHistory;
        $this->ifthenpayLogger = $ifthenpayLogger;
        $this->gatewaySettings = $gatewaySettings;
        $this->clientRepository = $clientRepository;
        $this->currencieRepository = $currencieRepository;
        $this->convertEuros = $convertEuros;
	}

    public function build(): CancelOrder {
            switch ($this->type) {
                case Gateway::MULTIBANCO:
                    return new CancelMultibancoOrder(
                        $this->gatewayDataBuilder, 
                        $this->paymentStatusFactory,
                        $this->invoiceRepository,
                        $this->repositoryFactory,
                        $this->whmcsInvoiceHistory,
                        $this->ifthenpayLogger,
                        $this->gatewaySettings
                );
                case Gateway::MBWAY:
                    return new CancelMbwayOrder(
                        $this->gatewayDataBuilder, 
                        $this->paymentStatusFactory,
                        $this->invoiceRepository,
                        $this->repositoryFactory,
                        $this->whmcsInvoiceHistory,
                        $this->ifthenpayLogger,
                        $this->gatewaySettings
                    );
                case Gateway::PAYSHOP:
                    return new CancelPayshopOrder(
                        $this->gatewayDataBuilder, 
                        $this->paymentStatusFactory,
                        $this->invoiceRepository,
                        $this->repositoryFactory,
                        $this->whmcsInvoiceHistory,
                        $this->ifthenpayLogger,
                        $this->gatewaySettings
                    );
                case Gateway::CCARD:
                    return new CancelCCardOrder(
                        $this->gatewayDataBuilder, 
                        $this->paymentStatusFactory,
                        $this->invoiceRepository,
                        $this->repositoryFactory,
                        $this->whmcsInvoiceHistory,
                        $this->ifthenpayLogger,
                        $this->gatewaySettings,
                        $this->clientRepository,
                        $this->currencieRepository,
                        $this->convertEuros
                    );
                default:
                    throw new \Exception('Unknown Cancel Order Class');
            }
        }

}
