<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Factory\Payment;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\CCardOrderDetail;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\MbwayOrderDetail;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Payment\StrategyFactory;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\PayshopOrderDetail;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\MultibancoOrderDetail;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Order\OrderDetailInterface;

class OrderDetailFactory extends StrategyFactory
{    
    public function build(): OrderDetailInterface {
        switch (strtolower($this->type)) {
            case 'multibanco':
                return new MultibancoOrderDetail(
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
                return new MbwayOrderDetail(
                    $this->paymentDefaultData, 
                    $this->gatewayBuilder, 
                    $this->ifthenpayGateway,
                    $this->whmcsGatewaySettings,
                    $this->utility,
                    $this->repositoryFactory,
                    $this->ifthenpayLogger,
                    $this->smartyDefaultData,
                    $this->tokenExtra
                );
            case 'payshop':
                return new PayshopOrderDetail(
                    $this->paymentDefaultData, 
                    $this->gatewayBuilder, 
                    $this->ifthenpayGateway,
                    $this->whmcsGatewaySettings,
                    $this->utility,
                    $this->repositoryFactory,
                    $this->ifthenpayLogger,
                    $this->smartyDefaultData
                );
            case 'ccard' || 'credit card (ifthenpay)':
                return new CCardOrderDetail(
                    $this->paymentDefaultData, 
                    $this->gatewayBuilder, 
                    $this->ifthenpayGateway,
                    $this->whmcsGatewaySettings,
                    $this->utility,
                    $this->repositoryFactory,
                    $this->ifthenpayLogger,
                    $this->token,
                    $this->status,
                    $this->smartyDefaultData
                );
            default:
                throw new \Exception('Unknown Order Detail Class');
        }
    }
}
