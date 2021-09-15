<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Base\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\ifthenpay\Utility\Token;
use WHMCS\Module\Gateway\ifthenpay\Utility\Status;
use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;
use WHMCS\Module\Gateway\Ifthenpay\Base\PaymentBase;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Builders\SmartyDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\PaymentDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Repository\RepositoryFactory;

class CCardBase extends PaymentBase
{
    
    protected $paymentMethod = 'ccard';
    private $token;
    private $status;

    public function __construct(
        PaymentDataBuilder $paymentDefaultData,
        GatewayDataBuilder $gatewayBuilder,
        Gateway $ifthenpayGateway,
        array $whmcsGatewaySettings,
        Utility $utility,
        RepositoryFactory $repositoryFactory,
        IfthenpayLogger $ifthenpayLogger,
        Token $token = null,
        Status $status = null,
        SmartyDataBuilder $smartyDefaultData = null
    ) {
        parent::__construct($paymentDefaultData, $gatewayBuilder, $ifthenpayGateway, $whmcsGatewaySettings, $utility, $repositoryFactory, $ifthenpayLogger, $smartyDefaultData);
        $this->token = $token;
        $this->status = $status;
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
            'paymentUrl' => $this->paymentGatewayResultData->paymentUrl,
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
