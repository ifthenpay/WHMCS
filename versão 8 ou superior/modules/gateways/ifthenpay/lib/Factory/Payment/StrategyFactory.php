<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Factory\Payment;

use Illuminate\Contracts\Container\Container;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Factory;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Builders\SmartyDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\TokenInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\StatusInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\UtilityInterface;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Repository\RepositoryFactory;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\TokenExtraInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\ConvertEurosInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ClientRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\CurrencieRepositoryInterface;

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
    protected $repositoryFactory;
    protected $token;
    protected $status;
    protected $ifthenpayLogger;
    protected $convertEuros;
    
    public function __construct(
        Container $ioc,
        GatewayDataBuilder $gatewayBuilder,
        Gateway $ifthenpayGateway,
        array $whmcsGatewaySettings,
        UtilityInterface $utility,
        RepositoryFactory $repositoryFactory,
        IfthenpayLogger $ifthenpayLogger,
        TokenInterface $token,
        StatusInterface $status,
        TokenExtraInterface $tokenExtra,
        ConvertEurosInterface $convertEuros
    )
	{
        parent::__construct($ioc);
        $this->gatewayBuilder = $gatewayBuilder;
        $this->ifthenpayGateway = $ifthenpayGateway;
        $this->whmcsGatewaySettings = $whmcsGatewaySettings;
        $this->utility = $utility;
        $this->repositoryFactory = $repositoryFactory;
        $this->ifthenpayLogger = $ifthenpayLogger;
        $this->token = $token;
        $this->status = $status;
        $this->tokenExtra = $tokenExtra;
        $this->convertEuros = $convertEuros;
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
