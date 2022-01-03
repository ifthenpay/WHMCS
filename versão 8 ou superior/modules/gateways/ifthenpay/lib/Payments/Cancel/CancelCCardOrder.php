<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Cancel;

use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Cancel\CancelOrder;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Payment\PaymentStatusFactory;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Repository\RepositoryFactory;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\ConvertEurosInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\WhmcsHistoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ClientRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\InvoiceRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\CurrencieRepositoryInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}


class CancelCCardOrder extends CancelOrder
{
    protected $paymentMethod = Gateway::CCARD;
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
        parent::__construct($gatewayDataBuilder, $paymentStatusFactory, $invoiceRepository, $repositoryFactory, $whmcsInvoiceHistory, $ifthenpayLogger, $gatewaySettings);
        $this->clientRepository = $clientRepository;
        $this->currencieRepository = $currencieRepository;
        $this->convertEuros = $convertEuros;
	}
  
    public function cancelOrder(): void
    {
        if ($this->gatewaySettings['cancelCCardOrder'] && $this->gatewaySettings['cancelCCardOrder'] === 'on') {
            $this->setPendingOrders();
            if (!empty($this->pendingOrders)) {
                foreach ($this->pendingOrders as $order) {
                    $ccardPayment = $this->paymentRepository->getPaymentByOrderId((string) $order['id']);
                    if (!empty($ccardPayment)) {
                        $this->setGatewayDataBuilderBackofficeKey();
                        $this->gatewayDataBuilder->setCCardKey($this->gatewaySettings['ccardKey']);
                        $this->gatewayDataBuilder->setReferencia((string) $order['id']);
                        $clientCurrency = $this->currencieRepository->findById(
                            (string) $this->clientRepository->findById((string)$order['userid'])['currency']
                        )['code'];
                        $this->gatewayDataBuilder->setTotalToPay($this->convertEuros->execute(
                                $clientCurrency,
                                $order['total']
                            )
                        );
                        if (!$this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                            $this->checkTimeChangeStatus($order, $ccardPayment);
                        }
                        $this->logCancelOrder($ccardPayment['requestId'], $order);
                    }
                }
            }
        }
    }
}


