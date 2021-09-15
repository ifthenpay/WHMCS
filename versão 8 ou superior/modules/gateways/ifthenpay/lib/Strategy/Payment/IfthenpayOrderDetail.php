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
        $this->smartyDefaultData->setSpinnerUrl($this->utility->getSvgUrl() . '/' .  'oval.svg');
        $this->smartyDefaultData->setMbwayOrderConfirmUrl($this->utility->getSvgUrl() . '/' .  'mbwayOrderConfirm.svg');
        $this->smartyDefaultData->setErrorUrl($this->utility->getSvgUrl() . '/' .  'error.svg');
        $this->smartyDefaultData->setErrorPaymentProcessingLang(\Lang::trans('errorPaymentProcessing'));
        $this->smartyDefaultData->setErrorPaymentProcessingDescriptionLang(\Lang::trans('errorPaymentProcessingDescription'));
        $this->smartyDefaultData->setIfthenpayTotalToPayLang(\Lang::trans('ifthenpayTotalToPay'));
        $this->smartyDefaultData->setIfthenpayPayBy(\Lang::trans('ifthenpayPayBy') . $this->params['paymentmethod']); 
        $this->ifthenpayLogger->info('payment default smarty data set with success', [
                'params' => $this->params,
                'className' => get_class($this)
            ]
        );
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
