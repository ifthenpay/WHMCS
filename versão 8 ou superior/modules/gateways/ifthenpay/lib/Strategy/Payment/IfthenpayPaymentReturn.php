<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment\IfthenpayStrategy;
use WHMCS\Module\Gateway\ifthenpay\Contracts\Payments\PaymentReturnInterface;


class IfthenpayPaymentReturn extends IfthenpayStrategy
{
    
    public function execute(): PaymentReturnInterface
    {
        $this->setDefaultData();

        return $this->factory
            ->setType(strtolower($this->params['paymentmethod']))
            ->setPaymentDefaultData($this->paymentDefaultData)
            ->build()
            ->getPaymentReturn();
    }
}
