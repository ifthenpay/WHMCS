<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment;

use WHMCS\Module\Gateway\Ifthenpay\Factory\Payment\PaymentStatusFactory;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class IfthenpayPaymentStatus
{
    private $paymentMethod;

    public function __construct(
        PaymentStatusFactory $factory
    )
    {
        $this->factory = $factory;
    }
    public function execute(): void
    {
        $this->factory
            ->setType($this->paymentMethod)
            ->build()
            ->changePaymentStatus();
    }

    /**
     * Set the value of paymentMethod
     *
     * @return  self
     */ 
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }
}
