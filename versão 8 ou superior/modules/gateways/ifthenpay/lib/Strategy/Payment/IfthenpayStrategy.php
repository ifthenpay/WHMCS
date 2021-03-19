<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;
use WHMCS\Module\Gateway\ifthenpay\Builders\SmartyDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\PaymentDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Payment\StrategyFactory;

require_once($_SERVER['CONTEXT_DOCUMENT_ROOT'] . explode(
    strpos($_SERVER['SCRIPT_NAME'], 'viewinvoice')  ? 'viewinvoice.php' : 'cart.php', $_SERVER['SCRIPT_NAME'], 2)[0] . 'init.php');


class IfthenpayStrategy
{
    protected $paymentDefaultData;
    protected $smartyDefaultData;
    protected $params;
    protected $paymentValueFormated;
    protected $utility;
    protected $factory;


    public function __construct(
        PaymentDataBuilder $paymentDataBuilder, 
        SmartyDataBuilder $smartyDataBuilder, 
        Utility $utility, 
        StrategyFactory $factory
    )
    {
        $this->paymentDefaultData = $paymentDataBuilder;
        $this->smartyDefaultData = $smartyDataBuilder;
        $this->utility = $utility;
        $this->factory = $factory;
    }

    protected function setDefaultData(): void
    {
        $this->paymentDefaultData->setOrderId(strval($this->params['invoiceid']));
        $this->paymentDefaultData->setPaymentMethod($this->params['paymentmethod']);
        $this->paymentDefaultData->setTotalToPay(strval(isset($this->params['amount']) ? $this->params['amount'] : $this->params['total']->toNumeric()));
    }

    /**
     * Set the value of params
     *
     * @return  self
     */ 
    public function setParams($params)
    {
        $this->params = $params;
        $this->setPaymentValueFormated();
        return $this;
    }

    /**
     * Set the value of paymentValueFormated
     *
     * @return  self
     */ 
    private function setPaymentValueFormated()
    {
        $this->paymentValueFormated = isset($this->params['amount']) ? 
            formatCurrency($this->params['amount'], $this->params['currency'] ? 
                $this->params['currency'] : null)->toSuffixed() : $this->params['total']->toSuffixed();
    }
}
