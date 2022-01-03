<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Forms;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\ifthenpay\Forms\ConfigForm;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\ifthenpay\Forms\Composite\Elements\Input;


class MbwayConfigForm extends ConfigForm
{
    protected $paymentMethod = Gateway::MBWAY;

    public function checkConfigValues(): array
    {
        $mbwayKey = $this->gatewayVars['mbwayKey'];
        return $mbwayKey ? ['mbwayKey' => $mbwayKey] : [];
    }

    protected function addPaymentInputsToForm(): void
    {
        if (!$this->configValues) {
            $this->addToOptions();
        } else {
            $this->options[$this->configValues['mbwayKey']] = $this->configValues['mbwayKey'];
            $this->addToOptions(true);
        }

        $this->form->add($this->ioc->makeWith(Input::class, [
            'friendlyName' => \AdminLang::trans('cancelMbwayOrder'),
            'type' => 'yesno',
            'name' => 'cancelMbwayOrder',
            'description' => \AdminLang::trans('cancelMbwayOrderDescription'),
        ]));

        $this->form->add($this->ioc->makeWith(Input::class, [
            'friendlyName' => 'Mbway key',
            'type' => 'dropdown',
            'name' => 'mbwayKey',
            'options' => $this->options,
            'description' => \AdminLang::trans('chooseMbwayKey'),
        ]));
        $this->ifthenpayLogger->info('mbwayKey input config added with success to form', ['options' => $this->options]);  
    }

    public function setGatewayBuilderData(): void
    {
        if ($this->configValues) {
            parent::setGatewayBuilderData();
            $this->gatewayDataBuilder->setEntidade(strtoupper($this->paymentMethod));
            $this->gatewayDataBuilder->setSubEntidade($this->configValues['mbwayKey']);
        }
        
    }
}
