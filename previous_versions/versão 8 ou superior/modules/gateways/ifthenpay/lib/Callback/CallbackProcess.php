<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Callback;

use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackData;
use WHMCS\Module\Gateway\Ifthenpay\Payments\WhmcsInvoiceHistory;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\TokenInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\StatusInterface;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Repository\RepositoryFactory;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\TokenExtraInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\ConvertEurosInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ClientRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\InvoiceRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\CurrencieRepositoryInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class CallbackProcess
{
    protected $paymentMethod;
    protected $callbackData;
    protected $paymentRepository;
    protected $callbackValidate;
    protected $gateway;
    protected $paymentData;
    protected $request;
    protected $token;
    protected $status;
    protected $tokenExtra;
    protected $whmcsInvoiceHistory;
    protected $invoiceRepository;
    protected $ifthenpayLogger;
    protected $currencieRepository;
    protected $clientRepository;
    protected $convertEuros;

	public function __construct(
        CallbackData $callbackData, 
        CallbackValidate $callbackValidate, 
        RepositoryFactory $repositoryFactory,
        InvoiceRepositoryInterface $invoiceRepository,
        WhmcsInvoiceHistory $whmcsInvoiceHistory,
        IfthenpayLogger $ifthenpayLogger,
        StatusInterface $status = null,
        TokenInterface $token = null,
        TokenExtraInterface $tokenExtra = null,
        CurrencieRepositoryInterface $currencieRepository = null,
        ClientRepositoryInterface $clientRepository = null,
        ConvertEurosInterface $convertEuros = null
    )
	{
        $this->callbackData = $callbackData;
        $this->paymentRepository = $repositoryFactory;
        $this->invoiceRepository = $invoiceRepository;
        $this->callbackValidate = $callbackValidate;
        $this->whmcsInvoiceHistory = $whmcsInvoiceHistory;
        $this->ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_CALLBACK)->getLogger();
        $this->status = $status;
        $this->token = $token;
        $this->tokenExtra = $tokenExtra;
        $this->currencieRepository = $currencieRepository;
        $this->clientRepository = $clientRepository;
        $this->convertEuros = $convertEuros;
	}
    
    /**
     * Set the value of paymentMethod
     *
     * @return  self
     */ 
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
        $this->paymentRepository = $this->paymentRepository->setType($this->paymentMethod)->build();
        $this->ifthenpayLogger->info('payment method and payment repository set with success', [
                'paymentMethod' => $this->paymentMethod,
                'className' => get_class($this)
            ]
        );
        return $this;
    }

    /**
     * Set the value of paymentData
     *
     * @return  self
     */ 
    public function setPaymentData(): void
    {
        $this->paymentData = $this->callbackData->setRequest($this->request)->execute();
        $this->ifthenpayLogger->info('callback payment data retrieved with success', [
                'paymentMethod' => $this->paymentMethod,
                'request' => $this->request,
                'paymentData' => $this->paymentData,
                'className' => get_class($this)
            ]
        );
    }

    /**
     * Set the value of request
     *
     * @return  self
     */ 
    public function setRequest(array $request)
    {
        $this->request = $request;

        return $this;
    }

    protected function logGatewayDataRetrieved(array $data): void
    {
        $this->ifthenpayLogger->info('callback gateway data retrieved with success', [
                'paymentMethod' => $this->paymentMethod,
                'gateway' => $data,
                'className' => get_class($this)
            ]
        );
    }

    protected function logCallbackDataNotFound(): void
    {
        $this->ifthenpayLogger->warning('callback payment data not found', [
                'paymentMethod' => $this->paymentMethod,
                'request' => $this->request,
                'className' => get_class($this)
            ]
        );
    }

    protected function logCallbackPaymentOrder(array $order): void
    {
        $this->ifthenpayLogger->info('callback payment order data retrieved with success', [
                'paymentMethod' => $this->paymentMethod,
                'order' => $order,
                'className' => get_class($this)
            ]
        );
    }

    protected function logCallbackProcess(array $order, $amount): void
    {
        $this->ifthenpayLogger->info('callback processed with success', [
                'paymentMethod' => $this->paymentMethod,
                'order' => $order,
                'ammount' => $amount,
                'paymentData' => $this->paymentData,
                'status' => 'paid',
                'className' => get_class($this)
            ]
        );
    }
}
