<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Forms;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\ifthenpay\Forms\ConfigForm;
use WHMCS\Module\Gateway\ifthenpay\Forms\Composite\Elements\Input;

class MultibancoConfigForm extends ConfigForm
{
    protected $paymentMethod = 'multibanco';

    protected function checkConfigValues(): array
    {
        $entidade = $this->gatewayVars['entidade'];
        $subEntidade = $this->gatewayVars['subEntidade'];
        return $entidade && $subEntidade ? ['entidade' => $entidade, 'subEntidade' => $subEntidade] : [];
    }

    protected function addPaymentInputsToForm(): void
    {
        if (!$this->configValues) {
            $this->addToOptions();
        } else {
            $this->options[$this->configValues['entidade']] = $this->configValues['entidade'];
        }
        $this->form->add($this->ioc->makeWith(Input::class, [
            'friendlyName' => \AdminLang::trans('multibancoEntity'),
            'type' => 'dropdown',
            'name' => 'entidade',
            'options' => $this->options,
            'description' => \AdminLang::trans('multibancoEntityDescription'),
        ]));
        $this->ifthenpayLogger->info('multibanco entidade input config added with success to form', ['options' => $this->options]);   
        if (!$this->configValues) {
            $this->options = [
                'default' => \AdminLang::trans('multibancoEntityDescription')
            ];
        } else {
            $this->options = [];
            $this->options[$this->configValues['subEntidade']] = $this->configValues['subEntidade'];
        }
        $this->form->add($this->ioc->makeWith(Input::class, [
            'friendlyName' => \AdminLang::trans('multibancoSubEntity'),
            'type' => 'dropdown',
            'name' => 'subEntidade',
            'options' => $this->options,
            'description' => \AdminLang::trans('multibancoSubEntityDescription'),
        ]));
        $this->ifthenpayLogger->info('multibanco subentidade input config added with success to form', ['options' => $this->options]);
    }   

    public function setGatewayBuilderData(): void
    {
        if ($this->configValues) {
            parent::setGatewayBuilderData();
            $this->gatewayDataBuilder->setEntidade($this->configValues['entidade']);
            $this->gatewayDataBuilder->setSubEntidade($this->configValues['subEntidade']);
        }
    }
}
