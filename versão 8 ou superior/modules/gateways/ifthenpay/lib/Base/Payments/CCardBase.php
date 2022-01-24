<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Base\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Base\PaymentBase;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Builders\SmartyDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\PaymentDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\TokenInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\StatusInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\UtilityInterface;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Repository\RepositoryFactory;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\ConvertEurosInterface;

class CCardBase extends PaymentBase
{
    
    protected $paymentMethod = Gateway::CCARD;
    protected $convertEuros;
    protected $status;
    protected $currencieRepository;
    protected $clientRepository;
    
    

    public function __construct(
        PaymentDataBuilder $paymentDefaultData,
        GatewayDataBuilder $gatewayBuilder,
        Gateway $ifthenpayGateway,
        array $whmcsGatewaySettings,
        UtilityInterface $utility,
        RepositoryFactory $repositoryFactory,
        IfthenpayLogger $ifthenpayLogger,
        TokenInterface $token,
        StatusInterface $status,
        ConvertEurosInterface $convertEuros,
        SmartyDataBuilder $smartyDefaultData = null
    ) {
        parent::__construct(
            $paymentDefaultData, 
            $gatewayBuilder, 
            $ifthenpayGateway, 
            $whmcsGatewaySettings, 
            $utility, 
            $repositoryFactory, 
            $ifthenpayLogger, 
            $smartyDefaultData
        );
        $this->token = $token;
        $this->status = $status;
        $this->convertEuros = $convertEuros;
    }

    protected function setGatewayBuilderData(): void
    {
        $this->gatewayBuilder->setCCardKey($this->whmcsGatewaySettings['ccardKey']);
        $this->gatewayBuilder->setSuccessUrl($this->utility->getCallbackControllerUrl($this->paymentMethod) . '?qn=' . $this->token->encrypt($this->status->getStatusSucess()));
        $this->gatewayBuilder->setErrorUrl($this->utility->getCallbackControllerUrl($this->paymentMethod) . '?qn=' . $this->token->encrypt($this->status->getStatusError()));
        $this->gatewayBuilder->setCancelUrl($this->utility->getCallbackControllerUrl($this->paymentMethod) . '?qn=' . $this->token->encrypt($this->status->getStatusCancel()));
        $this->logGatewayBuilderData();
    }

    protected function saveToDatabase(): void
    {
        $paymentData = [
            'requestId' => $this->paymentGatewayResultData->idPedido,
            'order_id' => $this->paymentDefaultData->orderId, 
            'status' => 'pending' 
        ];
        //$this->paymentRepository->create($paymentData);
        $this->paymentRepository->createOrUpdate(['order_id' => $this->paymentDefaultData->orderId], $paymentData);
        $this->logSavePaymentDataInDatabase($paymentData);
    }

    protected function updateToDatabase(): void
    {        
        // void
    }
}
