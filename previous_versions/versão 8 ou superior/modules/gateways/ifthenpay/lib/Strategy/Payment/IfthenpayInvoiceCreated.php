<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Contracts\Order\OrderDetailInterface;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment\IfthenpayOrderDetail;

class IfthenpayInvoiceCreated extends IfthenpayOrderDetail
{

    private function setDefaultSmartyData(): void
    {
        $this->smartyDefaultData->setTotalToPay($this->paymentValueFormated);
        $this->smartyDefaultData->setPaymentMethod($this->params['invoice_payment_method']);
        $this->smartyDefaultData->setPaymentLogo($this->utility->getImgUrl() . '/' . strtolower($this->params['invoice_payment_method']) . '.png');
        $this->smartyDefaultData->setIfthenpayTotalToPayLang(\Lang::trans('ifthenpayTotalToPay'));
        $this->ifthenpayLogger->info('invoice create smarty default data set with success', [
                'params' => $this->params,
                'className' => get_class($this)
            ]
        );
    }

    protected function setDefaultData(): void
    {
        $this->paymentDefaultData->setOrderId(strval($this->params['invoice_id']));
        $this->paymentDefaultData->setPaymentMethod($this->params['invoice_payment_method']);
        $this->paymentDefaultData->setTotalToPay(strval($this->params['invoice_total']->toNumeric()));
        $this->ifthenpayLogger->info('invoice create default data set with success', [
                'params' => $this->params,
                'className' => get_class($this)
            ]
        );
    }

    /**
     * Set the value of paymentValueFormated
     *
     * @return  self
     */ 
    private function setPaymentValueFormated()
    {
        $this->paymentValueFormated = $this->params['invoice_total']->toSuffixed();
        $this->ifthenpayLogger->info('payment value formated with success', [
                'paymentValueFormated' => $this->paymentValueFormated,
                'className' => get_class($this)
            ]
        );
    }

    public function setParams($params)
    {
        $this->params = $params;
        $this->setPaymentValueFormated();
        $this->ifthenpayLogger->info('payment params data set with success', [
                'params' => $params,
                'className' => get_class($this)
            ]
        );
        return $this;
    }

    public function execute(): OrderDetailInterface
    {
        $this->setDefaultData();
        $this->setDefaultSmartyData();

        return $this->factory
            ->setType(strtolower($this->params['invoice_payment_method']))
            ->setPaymentDefaultData($this->paymentDefaultData)
            ->setSmartyDefaultData($this->smartyDefaultData)
            ->build()->setParams($this->params)->getOrderDetail();
    }
}
