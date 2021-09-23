<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Facades;

use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Repository\RepositoryFactory;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment\IfthenpayPaymentReturn;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class PaymentFacade
{
    private $repository;
    private $paymentData;
    private $params;
    private $ifthenpayPaymentReturn;
    private $ifthenpayLogger;

	public function __construct(RepositoryFactory $repositoryFactory, IfthenpayPaymentReturn $ifthenpayPaymentReturn, IfthenpayLogger $ifthenpayLogger)
	{
        $this->repository = $repositoryFactory;
        $this->ifthenpayPaymentReturn = $ifthenpayPaymentReturn;
        $this->ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_PAYMENTS)->getLogger();
	}

    private function getPaymentData(): void
    {
        $this->repository = $this->repository->setType($this->paymentMethod)->build();
        $this->paymentData = $this->repository->getPaymentByOrderId((string) $this->params['invoiceid']);
        $this->ifthenpayLogger->info('payment data retrieved with success', [
                'paymentMethod' => $this->paymentMethod,
                'params' => $this->params,
                'paymentData' => $this->paymentData
            ]
        );
    }
    

    public function execute()
    {
        $this->getPaymentData();
        
        if (empty($this->paymentData)) {
            return $this->ifthenpayPaymentReturn->setParams($this->params)->execute();
        } else if ((isset($_POST['mbwayPhoneNumber']) && $_POST['mbwayPhoneNumber']  !== $this->paymentData['telemovel'])) {
            return $this->ifthenpayPaymentReturn->setParams($this->params)->execute();
        } 
        else {
            return $this->paymentData;
        }
    }

    /**
     * Set the value of paymentMethod
     *
     * @return  self
     */ 
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Set the value of params
     *
     * @return  self
     */ 
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }
}