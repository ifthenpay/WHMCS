<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Strategy\Form;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Factory\Config\IfthenpayConfigFormFactory;

class IfthenpayConfigForms
{
    private $paymentMethod;

	public function __construct(string $paymentMethod, IfthenpayConfigFormFactory $ifthenpayConfigFormFactory)
	{
        $this->paymentMethod = $paymentMethod;
        $this->ifthenpayConfigFormFactory = $ifthenpayConfigFormFactory;
	}
    
    public function buildForm(): array
    {
        return $this->ifthenpayConfigFormFactory->setType($this->paymentMethod)->build()->getForm();
    }

    public function processForm(): void
    {
        $this->ifthenpayConfigFormFactory->setType($this->paymentMethod)->build()->processForm();
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
