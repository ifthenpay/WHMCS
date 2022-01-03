<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Strategy\Cancel;

use WHMCS\Module\Gateway\Ifthenpay\Factory\Cancel\CancelIfthenpayOrderFactory;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class IfthenpayCancelOrder
{
    private $paymentMethod;

    public function __construct(
        CancelIfthenpayOrderFactory $factory
    )
    {
        $this->factory = $factory;
    }
    public function execute(): void
    {
        $this->factory
            ->setType($this->paymentMethod)
            ->build()
            ->cancelOrder();
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
