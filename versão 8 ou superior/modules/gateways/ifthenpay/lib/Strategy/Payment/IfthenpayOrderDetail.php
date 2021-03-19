<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment\IfthenpayStrategy;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Order\OrderDetailInterface;

class IfthenpayOrderDetail extends IfthenpayStrategy
{

    private function setDefaultSmartyData(): void
    {
        $this->smartyDefaultData->setTotalToPay($this->paymentValueFormated);
        $this->smartyDefaultData->setPaymentMethod($this->params['paymentmethod']);
        $this->smartyDefaultData->setPaymentLogo($this->utility->getImgUrl() . '/' . strtolower($this->params['paymentmethod']) . '.png');
    }

    public function execute(): OrderDetailInterface
    {
        $this->setDefaultData();
        $this->setDefaultSmartyData();

        return $this->factory
            ->setType(strtolower($this->params['paymentmethod']))
            ->setPaymentDefaultData($this->paymentDefaultData)
            ->setSmartyDefaultData($this->smartyDefaultData)
            ->build()->setParams($this->params)->getOrderDetail();
    }
}
