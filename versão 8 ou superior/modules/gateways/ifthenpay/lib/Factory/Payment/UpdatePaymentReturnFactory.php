<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Factory\Payment;

use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\UpdateMbwayPaymentReturn;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\UpdatePayshopPaymentReturn;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\PaymentReturnInterface;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\UpdateMultibancoPaymentReturn;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class UpdatePaymentReturnFactory extends StrategyFactory
{
    public function build(): PaymentReturnInterface {
        switch ($this->type) {
            case 'multibanco':
                return new UpdateMultibancoPaymentReturn(
                    $this->paymentDefaultData, 
                    $this->gatewayBuilder, 
                    $this->ifthenpayGateway,
                    $this->whmcsGatewaySettings,
                    $this->utility,
                    $this->repositoryFactory,
                    $this->ifthenpayLogger,
                    $this->smartyDefaultData
                );
            case 'mbway':
                return new UpdateMbwayPaymentReturn(
                    $this->paymentDefaultData, 
                    $this->gatewayBuilder, 
                    $this->ifthenpayGateway,
                    $this->whmcsGatewaySettings,
                    $this->utility,
                    $this->repositoryFactory,
                    $this->ifthenpayLogger,
                    $this->smartyDefaultData
                );
            case 'payshop':
                return new UpdatePayshopPaymentReturn(
                    $this->paymentDefaultData, 
                    $this->gatewayBuilder, 
                    $this->ifthenpayGateway,
                    $this->whmcsGatewaySettings,
                    $this->utility,
                    $this->repositoryFactory,
                    $this->ifthenpayLogger,
                    $this->smartyDefaultData
                );
            default:
                throw new \Exception('Unknown Update Payment Return Class');
        }
    }
}
