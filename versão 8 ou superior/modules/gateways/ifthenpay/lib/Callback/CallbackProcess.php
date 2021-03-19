<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Callback;

use WHMCS\Module\Gateway\ifthenpay\Utility\Token;
use WHMCS\Module\Gateway\ifthenpay\Utility\Status;
use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackData;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class CallbackProcess
{
    protected $paymentMethod;
    protected $callbackData;
    protected $utility;
    protected $callbackValidate;
    protected $whmcs;
    protected $gateway;
    protected $paymentData;
    protected $request;
    protected $token;
    protected $status;

	public function __construct(
        CallbackData $callbackData, 
        CallbackValidate $callbackValidate, 
        Utility $utility,
        Status $status = null,
        Token $token = null
    )
	{
        $this->callbackData = $callbackData;
        $this->utility = $utility;
        $this->callbackValidate = $callbackValidate;
        $this->status = $status;
        $this->token = $token;
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
     * Set the value of paymentData
     *
     * @return  self
     */ 
    public function setPaymentData(): void
    {
        $this->paymentData = $this->callbackData->setRequest($this->request)->execute();
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

    /**
     * Set the value of whmcs
     *
     * @return  self
     */ 
    public function setWhmcs($whmcs)
    {
        $this->whmcs = $whmcs;

        return $this;
    }
}
