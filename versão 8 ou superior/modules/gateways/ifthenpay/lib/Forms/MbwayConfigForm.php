<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Forms;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;
use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;
use WHMCS\Module\Gateway\ifthenpay\Forms\ConfigForm;
use WHMCS\Module\Gateway\ifthenpay\Forms\Composite\Elements\Input;


class MbwayConfigForm extends ConfigForm
{
    protected $paymentMethod = 'mbway';

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
        }

        $this->form->add($this->ioc->makeWith(Input::class, [
            'friendlyName' => 'Mbway key',
            'type' => 'dropdown',
            'name' => 'mbwayKey',
            'options' => $this->options,
            'description' => 'Choose Mbway key',
        ]));   
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
