<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Factory\Payment;

use Illuminate\Contracts\Container\Container;
use WHMCS\Module\Gateway\ifthenpay\Utility\Token;
use WHMCS\Module\Gateway\ifthenpay\Utility\Status;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Factory;
use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Builders\SmartyDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

abstract class StrategyFactory extends Factory
{
    protected $paymentDefaultData;
    protected $smartyDefaultData;
    protected $gatewayBuilder;
    protected $ifthenpayGateway;
    protected $whmcsGatewaySettings;
    protected $utility;
    
    public function __construct(
        Container $ioc,
        GatewayDataBuilder $gatewayBuilder,
        Gateway $ifthenpayGateway,
        array $whmcsGatewaySettings,
        Utility $utility,
        Token $token = null,
        Status $status = null
    )
	{
        parent::__construct($ioc);
        $this->gatewayBuilder = $gatewayBuilder;
        $this->ifthenpayGateway = $ifthenpayGateway;
        $this->whmcsGatewaySettings = $whmcsGatewaySettings;
        $this->utility = $utility;
        $this->token = $token;
        $this->status = $status;
    }

    abstract public function build();

    /**
     * Set the value of paymentDefaultData
     *
     * @return  self
     */ 
    public function setPaymentDefaultData($paymentDefaultData)
    {
        $this->paymentDefaultData = $paymentDefaultData;

        return $this;
    }

    /**
     * Set the value of smartyDefaultData
     *
     * @return  self
     */ 
    public function setSmartyDefaultData(SmartyDataBuilder $smartyDefaultData)
    {
        $this->smartyDefaultData = $smartyDefaultData;

        return $this;
    }
}
