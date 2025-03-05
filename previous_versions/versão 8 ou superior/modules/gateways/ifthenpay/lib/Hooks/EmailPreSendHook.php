<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Hooks;

use Smarty;
use WHMCS\Module\Gateway\ifthenpay\Hooks\CheckoutHook;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\MixInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\UtilityInterface;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment\IfthenpayInvoiceCreated;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class EmailPreSendHook extends CheckoutHook
{
    private $ifthenpayInvoiceCreated;

    public function __construct(UtilityInterface $utility, IfthenpayInvoiceCreated $ifthenpayInvoiceCreated, MixInterface $mix)
	{
        parent::__construct($utility, $mix);
        $this->ifthenpayInvoiceCreated = $ifthenpayInvoiceCreated;
    }
    
    public function validateTemplate(): bool
    {
        return true;
    }

    public function executeStyles(): string
    {
        return $this->validateTemplate() ? '<link rel="stylesheet" href="'. $this->utility->getCssUrl() . '/ifthenpayConfirmPage.css">' : '';
    }

    public function execute()
    {
        $ifthenpayOrderDetail = $this->ifthenpayInvoiceCreated->setParams($this->vars)->execute();
        $smarty = new Smarty;
        $smarty->assign($ifthenpayOrderDetail->getSmartyVariables()->setStatus('ok')->toArray());

        return $this->executeStyles() . $smarty->fetch('file:' . ROOTDIR . '\modules\gateways\ifthenpay\templates\emailIfthenpayPaymentReturn.tpl');
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