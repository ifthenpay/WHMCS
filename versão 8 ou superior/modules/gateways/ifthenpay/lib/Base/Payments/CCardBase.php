<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Base\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;
use WHMCS\Module\Gateway\ifthenpay\Utility\Token;
use WHMCS\Module\Gateway\ifthenpay\Utility\Status;
use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;
use WHMCS\Module\Gateway\Ifthenpay\Base\PaymentBase;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Builders\SmartyDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\PaymentDataBuilder;

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
        Token $token = null,
        Status $status = null,
        SmartyDataBuilder $smartyDefaultData = null
    ) {
        parent::__construct($paymentDefaultData, $gatewayBuilder, $ifthenpayGateway, $whmcsGatewaySettings, $utility, $smartyDefaultData);
        $this->token = $token;
        $this->status = $status;
    }

    protected function setGatewayBuilderData(): void
    {
        $this->gatewayBuilder->setCCardKey($this->whmcsGatewaySettings['ccardKey']);
        $this->gatewayBuilder->setSuccessUrl($this->utility->getCallbackControllerUrl($this->paymentMethod) . '?payment=ccard&qn=' . $this->token->encrypt($this->status->getStatusSucess()));
        $this->gatewayBuilder->setErrorUrl($this->utility->getCallbackControllerUrl($this->paymentMethod) . '?payment=ccard&qn=' . $this->token->encrypt($this->status->getStatusError()));
        $this->gatewayBuilder->setCancelUrl($this->utility->getCallbackControllerUrl($this->paymentMethod) . '?payment=ccard&qn=' . $this->token->encrypt($this->status->getStatusCancel()));
    }

    protected function saveToDatabase(): void
    {
        Capsule::table($this->paymentTable)->insert(
            [
                'requestId' => $this->paymentGatewayResultData->idPedido,
                'paymentUrl' => $this->paymentGatewayResultData->paymentUrl,
                'order_id' => $this->paymentDefaultData->orderId, 
                'status' => 'pending' 
            ]
        );
    }
}
