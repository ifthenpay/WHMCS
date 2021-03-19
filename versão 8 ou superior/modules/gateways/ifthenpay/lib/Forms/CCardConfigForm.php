<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Forms;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\ifthenpay\Forms\ConfigForm;
use WHMCS\Module\Gateway\ifthenpay\Forms\Composite\Elements\Input;


class CCardConfigForm extends ConfigForm
{
    protected $paymentMethod = 'ccard';
    protected $paymentMethodNameAlias = 'Cartão de Crédito (Ifthenpay)';
    protected $hasCallback = false;

    public function checkConfigValues(): array
    {
        $ccardKey = $this->gatewayVars['ccardKey'];
        return $ccardKey ? ['ccardKey' => $ccardKey] : [];
    }

    protected function addPaymentInputsToForm(): void
    {
        if (!$this->configValues) {
            $this->addToOptions();
        } else {
            $this->options[$this->configValues['ccardKey']] = $this->configValues['ccardKey'];
        }

        $this->form->add($this->ioc->makeWith(Input::class, [
            'friendlyName' => 'CCard key',
            'type' => 'dropdown',
            'name' => 'ccardKey',
            'options' => $this->options,
            'description' => 'Choose CCard key',
        ])); 
    }

    public function setGatewayBuilderData(): void
    {
        if ($this->configValues) {
            parent::setGatewayBuilderData();
            $this->gatewayDataBuilder->setEntidade(strtoupper($this->paymentMethod));
            $this->gatewayDataBuilder->setSubEntidade($this->configValues['ccardKey']);
        }
        
    }

    public function processForm(): void
    {
        //void
    }
}
