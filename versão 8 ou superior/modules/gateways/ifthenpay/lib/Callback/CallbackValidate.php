<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Callback;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class CallbackValidate
{
    private $httpRequest;
    private $order;
    private $configurationChaveAntiPhishing;
    private $paymentDataFromDb;

    private function validateOrder(): void
    {
        if (!$this->order) {
            throw new \Exception(\Lang::trans('paymentOrderNotFound'));
        }
    }

    private function validateOrderValue(): void
    {
        $orderTotal = floatval($this->order['amount'] ? $this->order['amount'] : $this->order['total']);
        $requestValor = floatval($this->httpRequest['valor']);
        if (round($orderTotal, 2) !== round($requestValor, 2)) {
            throw new \Exception(\Lang::trans('errorPaymentTotal'));
        }
    }

    private function validateOrderStatus(): void
    {
        if ($this->paymentDataFromDb['status'] === 'paid') {
                throw new \Exception(\Lang::trans('orderPaid'));
        }
    }

    private function validateChaveAntiPhishing()
    {
        if (!$this->httpRequest['chave']) {
            throw new \Exception(\Lang::trans('antiPhishingKeyNotSentCallback'));
        }

        if ($this->httpRequest['chave'] !== $this->configurationChaveAntiPhishing) {
            throw new \Exception(\Lang::trans('antiPhishingKeyNotValidCallback'));
        }
    }

    public function validate(): bool
    {
        $this->validateChaveAntiPhishing();
        $this->validateOrder();
        $this->validateOrderValue();
        $this->validateOrderStatus();
        return true;
    }

    /**
     * Set the value of httpRequest
     *
     * @return  self
     */ 
    public function setHttpRequest(array $httpRequest)
    {
        $this->httpRequest = $httpRequest;

        return $this;
    }

    /**
     * Set the value of order
     *
     * @return  self
     */ 
    public function setOrder(array $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Set the value of configurationChaveAntiPhishing
     *
     * @return  self
     */ 
    public function setConfigurationChaveAntiPhishing(string $configurationChaveAntiPhishing)
    {
        $this->configurationChaveAntiPhishing = $configurationChaveAntiPhishing;

        return $this;
    }

    /**
     * Set the value of paymentDataFromDb
     *
     * @return  self
     */ 
    public function setPaymentDataFromDb(array $paymentDataFromDb)
    {
        $this->paymentDataFromDb = $paymentDataFromDb;

        return $this;
    }
}
