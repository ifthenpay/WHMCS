<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Forms;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\ifthenpay\Forms\ConfigForm;
use WHMCS\Module\Gateway\ifthenpay\Forms\Composite\Elements\Input;

class PayshopConfigForm extends ConfigForm
{
    protected $paymentMethod = 'payshop';

    public function checkConfigValues(): array
    {
        $payshopKey = $this->gatewayVars['payshopKey'];
        $payshopValidity = $this->gatewayVars['payshopValidity'];
        return $payshopKey ? ['payshopKey' => $payshopKey, 'payshopValidity' => $payshopValidity] : [];
    }

    protected function addPaymentInputsToForm(): void
    {
        if (!$this->configValues) {
            $this->addToOptions();
        } else {
            $this->options[$this->configValues['payshopKey']] = $this->configValues['payshopKey'];
        }

        $this->form->add($this->ioc->makeWith(Input::class, [
            'friendlyName' => 'Payshop key',
            'type' => 'dropdown',
            'name' => 'payshopKey',
            'options' => $this->options,
            'description' => \AdminLang::trans('choosePayshopKey'),
        ]));
        $this->ifthenpayLogger->info('payshopKey input config added with success to form', ['options' => $this->options]);
        $this->form->add($this->ioc->makeWith(Input::class, [
            'friendlyName' => \AdminLang::trans('payshopValidaty'),
            'type' => 'text',
            'name' => 'payshopValidity',
            'description' => \AdminLang::trans('payshopValidatyDescription'),
        ]));
        $this->ifthenpayLogger->info('payshop validade input config added with success to form');    
    }

    public function setGatewayBuilderData(): void
    {
        if ($this->configValues) {
            parent::setGatewayBuilderData();
            $this->gatewayDataBuilder->setEntidade(strtoupper($this->paymentMethod));
            $this->gatewayDataBuilder->setSubEntidade($this->configValues['payshopKey']);
        }
    }
}
