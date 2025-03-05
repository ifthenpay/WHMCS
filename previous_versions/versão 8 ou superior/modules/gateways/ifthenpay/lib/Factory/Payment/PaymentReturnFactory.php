<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Factory\Payment;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\CCardPaymentReturn;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\MbwayPaymentReturn;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\PayshopPaymentReturn;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\MultibancoPaymentReturn;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\PaymentReturnInterface;

class PaymentReturnFactory extends StrategyFactory
{
    public function build(): PaymentReturnInterface {
        switch ($this->type) {
            case Gateway::MULTIBANCO:
                return new MultibancoPaymentReturn(
                    $this->paymentDefaultData, 
                    $this->gatewayBuilder, 
                    $this->ifthenpayGateway,
                    $this->whmcsGatewaySettings,
                    $this->utility,
                    $this->repositoryFactory,
                    $this->ifthenpayLogger,
                    $this->smartyDefaultData
                );
            case Gateway::MBWAY:
                return new MbwayPaymentReturn(
                    $this->paymentDefaultData, 
                    $this->gatewayBuilder, 
                    $this->ifthenpayGateway,
                    $this->whmcsGatewaySettings,
                    $this->utility,
                    $this->repositoryFactory,
                    $this->ifthenpayLogger,
                    $this->token,
                    $this->smartyDefaultData
                );
            case Gateway::PAYSHOP:
                return new PayshopPaymentReturn(
                    $this->paymentDefaultData, 
                    $this->gatewayBuilder, 
                    $this->ifthenpayGateway,
                    $this->whmcsGatewaySettings,
                    $this->utility,
                    $this->repositoryFactory,
                    $this->ifthenpayLogger,
                    $this->smartyDefaultData
                );
            case Gateway::CCARD:
                return new CCardPaymentReturn(
                    $this->paymentDefaultData, 
                    $this->gatewayBuilder, 
                    $this->ifthenpayGateway,
                    $this->whmcsGatewaySettings,
                    $this->utility,
                    $this->repositoryFactory,
                    $this->ifthenpayLogger,
                    $this->token,
                    $this->status,
                    $this->convertEuros,
                    $this->smartyDefaultData
                );
            default:
                throw new \Exception('Unknown Payment Return Class');
        }
    }
}
