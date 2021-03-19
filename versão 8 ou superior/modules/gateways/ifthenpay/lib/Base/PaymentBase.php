<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Base;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;
use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Builders\SmartyDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\PaymentDataBuilder;


abstract class PaymentBase
{
    protected $whmcsGatewaySettings;
    protected $gatewayBuilder;
    protected $paymentDefaultData;
    protected $smartyDefaultData;
    protected $paymentGatewayResultData;
    protected $ifthenpayGateway;
    protected $paymentDataFromDb;
    protected $paymentTable;
    protected $paymentMethod;
    protected $params;

    public function __construct(
        PaymentDataBuilder $paymentDefaultData,
        GatewayDataBuilder $gatewayBuilder,
        Gateway $ifthenpayGateway,
        array $whmcsGatewaySettings,
        Utility $utility,
        SmartyDataBuilder $smartyDefaultData = null
    ) {
        $this->gatewayBuilder = $gatewayBuilder;
        $this->paymentDefaultData = $paymentDefaultData->getData();
        $this->smartyDefaultData = $smartyDefaultData;
        $this->ifthenpayGateway = $ifthenpayGateway;
        $this->whmcsGatewaySettings = $whmcsGatewaySettings;
        $this->utility = $utility;
    }

    public function setPaymentTable(string $tableName): PaymentBase
    {
        $this->paymentTable = $tableName;
        return $this;
    }

    public function getFromDatabaseById(): void
    {
        $this->paymentDataFromDb = $this->utility->convertObjectToarray(
            Capsule::table($this->paymentTable)->where('order_id', $this->paymentDefaultData->orderId)->first()
        );    
    }

    public function getSmartyVariables(): SmartyDataBuilder
    {
        return $this->smartyDefaultData;
    }

    abstract protected function setGatewayBuilderData(): void;
    abstract protected function saveToDatabase(): void;

    /**
     * Get the value of paymentDataFromDb
     */
    public function getPaymentDataFromDb()
    {
        return $this->paymentDataFromDb;
    }

    /**
     * Get the value of paymentGatewayResultData
     */ 
    public function getPaymentGatewayResultData()
    {
        return $this->paymentGatewayResultData;
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
