<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Base;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\GatewaySetting;
use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\ifthenpay\Utility\TokenExtra;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Builders\SmartyDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\PaymentDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Traits\Logs\LogGatewayBuilderData;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Repository\RepositoryFactory;


abstract class PaymentBase
{
    use LogGatewayBuilderData;
    
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
    protected $paymentRepository;
    protected $tokenExtra;
    protected $ifthenpayLogger;

    public function __construct(
        PaymentDataBuilder $paymentDefaultData,
        GatewayDataBuilder $gatewayBuilder,
        Gateway $ifthenpayGateway,
        array $whmcsGatewaySettings,
        Utility $utility,
        RepositoryFactory $repositoryFactory,
        IfthenpayLogger $ifthenpayLogger,
        SmartyDataBuilder $smartyDefaultData = null,
        TokenExtra $tokenExtra = null
    ) {
        $this->gatewayBuilder = $gatewayBuilder;
        $this->paymentDefaultData = $paymentDefaultData->getData();
        $this->smartyDefaultData = $smartyDefaultData;
        $this->ifthenpayGateway = $ifthenpayGateway;
        $this->whmcsGatewaySettings = $whmcsGatewaySettings;
        $this->utility = $utility;
        $this->paymentRepository = $repositoryFactory->setType($this->paymentMethod)->build();
        $this->ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_PAYMENTS)->getLogger();
        $this->tokenExtra = $tokenExtra;
    }

    public function setPaymentTable(string $tableName): PaymentBase
    {
        $this->paymentTable = $tableName;
        $this->ifthenpayLogger->info('payment table set with success', ['paymentTable' => $this->paymentTable, 'className' => get_class($this)]);
        return $this;
    }

    public function getFromDatabaseById(): void
    {
        $this->paymentDataFromDb = $this->paymentRepository->getPaymentByOrderId($this->paymentDefaultData->orderId);
        $this->ifthenpayLogger->info('payment by orderId retrieved with success', [
                'paymentMethod' => $this->paymentMethod,
                'paymentDataFromDb' => $this->paymentDataFromDb,
                'orderId' => $this->paymentDefaultData->orderId,
                'className' => get_class($this)
            ]
        );   
    }

    public function getSmartyVariables(): SmartyDataBuilder
    {
        return $this->smartyDefaultData;
    }

    abstract protected function setGatewayBuilderData(): void;
    abstract protected function saveToDatabase(): void;
    abstract protected function updateToDatabase(): void;

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

    public function persistToDatabase(): void
    {
        $this->saveToDatabase();
    }

    /**
     * Set the value of whmcsGatewaySettings
     *
     * @return  self
     */ 
    public function setWhmcsGatewaySettings()
    {
        $this->whmcsGatewaySettings = GatewaySetting::getForGateway($this->paymentMethod);

        return $this;
    }

    protected function logSavePaymentDataInDatabase(array $data, string $type = 'save'): void
    {
        $logData = array_merge([
            'paymentMethod' => $this->paymentMethod,
            'className' => get_class($this)
        ], $data);
        $this->ifthenpayLogger->info($type === 'type' ? 'payment data saved in database with success' : 'payment data updated in database with success', $logData); 
    }

    protected function logSmartyBuilderData(): void
    {
        $this->ifthenpayLogger->info('smarty builder data set with success', [
                'paymentMethod' => $this->paymentMethod,
                'smartyData' => $this->smartyDefaultData,
                'className' => get_class($this)
            ]
        );
    }

    protected function logPaymentGatewayResultData(): void
    {
        $this->ifthenpayLogger->info('payment gateway result data made with success', [
                'paymentMethod' => $this->paymentMethod,
                'gatewayResultData' => $this->paymentGatewayResultData,
                'className' => get_class($this)
            ]
        );
    }

}
