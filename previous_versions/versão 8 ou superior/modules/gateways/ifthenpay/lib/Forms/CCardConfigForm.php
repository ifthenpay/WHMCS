<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Forms;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\ifthenpay\Forms\ConfigForm;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\ifthenpay\Forms\Composite\Elements\Input;


class CCardConfigForm extends ConfigForm
{
    protected $paymentMethod = Gateway::CCARD;
    protected $paymentMethodNameAlias;
    protected $hasCallback = true;

    public function checkConfigValues(): array
    {
        $ccardKey = $this->gatewayVars['ccardKey'];
        return $ccardKey ? ['ccardKey' => $ccardKey] : [];
    }

    protected function addPaymentInputsToForm(): void
    {
        try {
            $this->ifthenpaySql->removePaymentUrlFromCCardTable();
            $this->ifthenpayLogger->info('remove paymentUrl from ccard table with success');
        } catch (\Throwable $th) {
            $this->ifthenpayLogger->error('error removing paymentUrl from ccard table', ['exception' => $th]);
            throw $th;
        } 
        if (!$this->configValues) {
            $this->addToOptions();
        } else {
            $this->options[$this->configValues['ccardKey']] = $this->configValues['ccardKey'];
            $this->addToOptions(true);
        }

        $this->form->add($this->ioc->makeWith(Input::class, [
            'friendlyName' => \AdminLang::trans('cancelCCardOrder'),
            'type' => 'yesno',
            'name' => 'cancelCCardOrder',
            'description' => \AdminLang::trans('cancelCCardOrderDescription'),
        ]));

        $this->form->add($this->ioc->makeWith(Input::class, [
            'friendlyName' => 'CCard key',
            'type' => 'dropdown',
            'name' => 'ccardKey',
            'options' => $this->options,
            'description' => \AdminLang::trans('chooseCcardKey'),
        ]));
        $this->ifthenpayLogger->info('ccardKey input config added with success to form', ['options' => $this->options]);
    }

    public function setGatewayBuilderData(): void
    {
        if ($this->configValues) {
            parent::setGatewayBuilderData();
            $this->gatewayDataBuilder->setEntidade(strtoupper($this->paymentMethod));
            $this->gatewayDataBuilder->setSubEntidade($this->configValues['ccardKey']);
        }
        
    }
    /**
     * Get the value of paymentMethodNameAlias
     */ 
    public function getPaymentMethodNameAlias()
    {
        return \AdminLang::trans('creditCardAlias');
    }
}
