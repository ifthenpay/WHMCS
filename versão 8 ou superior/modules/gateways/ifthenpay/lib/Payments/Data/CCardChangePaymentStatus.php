<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Data;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Request\WebService;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Base\CheckPaymentStatusBase;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Repository\RepositoryFactory;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\ConvertEurosInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\WhmcsHistoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\PaymentStatusInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ClientRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\InvoiceRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\CurrencieRepositoryInterface;

class CCardChangePaymentStatus extends CheckPaymentStatusBase
{
	private $convertEuros;
    private $currencieRepository;
    private $clientRepository;
    protected $paymentMethod = Gateway::CCARD;

    public function __construct(
        GatewayDataBuilder $gatewayDataBuilder,
        PaymentStatusInterface $paymentStatus,
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
        parent::__construct(
            $gatewayDataBuilder,
            $paymentStatus,
            $webService,
            $invoiceRepository,
            $repositoryFactory,
            $whmcsHistory,
            $ifthenpayLogger,
            $gatewaySettings
        );
        $this->currencieRepository = $currencieRepository;
        $this->clientRepository = $clientRepository;
        $this->convertEuros = $convertEuros;
	}

    protected function setGatewayDataBuilder(): void
    {
        $this->setGatewayDataBuilderBackofficeKey();
        $this->gatewayDataBuilder->setCCardKey($this->gatewaySettings['ccardKey']);
        $this->logGatewayBuilderData();
    }

    public function changePaymentStatus(): void
    {
        $this->getPendingOrders();
        if (!empty($this->pendingInvoices)) {
            $this->setGatewayDataBuilder();
            foreach ($this->pendingInvoices as $pendingInvoice) {
                $ccardPayment = $this->paymentRepository->getPaymentByOrderId((string) $pendingInvoice['id']);
                if(!empty($ccardPayment)) {
                    $this->gatewayDataBuilder->setReferencia((string) $pendingInvoice['id']);
                    $clientCurrency = $this->currencieRepository->findById((string) $this->clientRepository->findById((string) $pendingInvoice['userid'])['currency'])['code'];
                    $this->gatewayDataBuilder->setTotalToPay($this->convertEuros->execute(
                            $clientCurrency,
                            $pendingInvoice['total']
                        )
                    );
                    if ($this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                        $this->updatePaymentStatus((string) $ccardPayment['id']);
                        $this->whmcsHistory
                            ->setTransactionId($ccardPayment)
                            ->setInvoiceId($this->paymentMethod, $ccardPayment['order_id'])
                            ->processInvoice($pendingInvoice['total'], $ccardPayment);
                    }
                    $this->logChangePaymentStatus($ccardPayment);
                }
            }
        }
    }
}
