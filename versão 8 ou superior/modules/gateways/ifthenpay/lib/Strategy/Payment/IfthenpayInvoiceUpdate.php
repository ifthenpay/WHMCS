<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment;

use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Builders\SmartyDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\PaymentDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Payment\StrategyFactory;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\UtilityInterface;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment\IfthenpayPaymentReturn;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\InvoiceUpdateInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\PaymentReturnInterface;
use WHMCS\Module\Gateway\Ifthenpay\Exceptions\IfthenpayInvoiceUpdateException;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\InvoiceRepositoryInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class IfthenpayInvoiceUpdate extends IfthenpayPaymentReturn implements InvoiceUpdateInterface
{
    private $invoiceRepository;
    private $allowedPaymentMethods = [Gateway::MULTIBANCO, Gateway::MBWAY, Gateway::PAYSHOP];
    private $gateway;

    public function __construct(
        PaymentDataBuilder $paymentDataBuilder, 
        SmartyDataBuilder $smartyDataBuilder, 
        UtilityInterface $utility, 
        StrategyFactory $factory,
        InvoiceRepositoryInterface $invoiceRepository,
        Gateway $gateway,
        IfthenpayLogger $ifthenpayLogger
    )
    {
        parent::__construct($paymentDataBuilder, $smartyDataBuilder, $utility, $factory, $ifthenpayLogger);
        $this->invoiceRepository = $invoiceRepository;
        $this->gateway = $gateway;
    }

    public function checkPaymentMethod(): bool 
    {
        if ($this->gateway->checkIfthenpayPaymentMethod($this->params['paymentmethod']) && in_array($this->params['paymentmethod'], $this->allowedPaymentMethods)) {
            return true;
        }
        return false;
    }

    protected function setDefaultData(): void
    {
        $this->paymentDefaultData->setOrderId(strval($this->params['id']));
        $this->paymentDefaultData->setPaymentMethod($this->params['paymentmethod']);
        $this->paymentDefaultData->setTotalToPay(strval($this->params['total']));
        $this->ifthenpayLogger->info('invoice update payment default data set with success', [
                'params' => $this->params
            ]
        );
    }

    public function setParams($params)
    {
        $this->params = $this->invoiceRepository->getOrderById((int) $params['invoiceid']);
        if (!$this->checkPaymentMethod()) {
            throw new IfthenpayInvoiceUpdateException('Payment by credit card not allowed');
        }
        $this->ifthenpayLogger->info('invoice update payment params set with success', [
                'params' => $params,
                'className' => get_class($this)
            ]
        );
        return $this;
    }

    public function execute(): PaymentReturnInterface
    {
        $this->setDefaultData();

        return $this->factory
            ->setType(strtolower($this->params['paymentmethod']))
            ->setPaymentDefaultData($this->paymentDefaultData)
            ->build()->setParams($this->params)->setWhmcsGatewaySettings()->getPaymentReturn();
    }
}
