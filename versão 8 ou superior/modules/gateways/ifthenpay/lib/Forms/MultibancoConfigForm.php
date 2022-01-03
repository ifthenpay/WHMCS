<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Forms;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use Smarty;
use WHMCS\Module\Gateway\ifthenpay\Forms\ConfigForm;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\ifthenpay\Forms\Composite\Elements\Input;

class MultibancoConfigForm extends ConfigForm
{
    protected $paymentMethod = Gateway::MULTIBANCO;

    protected function checkConfigValues(): array
    {
        $entidade = $this->gatewayVars['entidade'];
        $subEntidade = $this->gatewayVars['subEntidade'];
        return $entidade && $subEntidade ? ['entidade' => $entidade, 'subEntidade' => $subEntidade] : [];
    }

    private function addValidatyCancelMultibancoOrderInput():void
    {
        $options = [];
            for ($i=0; $i < 32; $i++) {
                $options[$i] = $i;
            }
            foreach ([45, 60, 90, 120] as $value) {
                $options[$value] = $value;
            }
            $this->form->add($this->ioc->makeWith(Input::class, [
                'friendlyName' => \AdminLang::trans('multibancoDeadline'),
                'type' => 'dropdown',
                'name' => 'multibancoValidity',
                'options' => $options,
                'description' => \AdminLang::trans('multibancoDeadlineDescription'),
            ]));
    }

    protected function addDynamicMbInputs(): void
    {
        if (!$this->ifthenpayGateway->checkDynamicMb($this->ifthenpayUserAccount)) {
            $this->form->add($this->ioc->makeWith(Input::class, [
                'type' => 'System', 
                'name' => 'UsageNotes', 
                'value' =>  '<button type="button" class="btn btn-danger">' . \AdminLang::trans('notMultibancoDeadline') . 
                    '</button>' . '<br><br>' . \AdminLang::trans('requestMultibancoDeadline') . 
                    ':<br><a id="requestMultibancoDynamicAccount" class="btn btn-success" href="">' . \AdminLang::trans('sendEmailNewAccount') . '</a><br><br>'
            ]));
            $this->ifthenpayLogger->info('user with no multibanco deadline field notification added to form with success');
        } else {
            $this->ifthenpaySql->addRequestIdValidadeToMultibancoTable();
            $this->addValidatyCancelMultibancoOrderInput();
        }

    }

    protected function addPaymentInputsToForm(): void
    {
        if (!$this->configValues) {
            $this->addToOptions();
        } else {
            $this->options[$this->configValues['entidade']] = $this->configValues['entidade'];
            $this->addToOptions(true);
        }
        if ($this->ifthenpayGateway->checkDynamicMb($this->ifthenpayUserAccount)) {
            $this->form->add($this->ioc->makeWith(Input::class, [
                'friendlyName' => \AdminLang::trans('cancelMultibancoOrder'),
                'type' => 'yesno',
                'name' => 'cancelMultibancoOrder',
                'description' => \AdminLang::trans('cancelMultibancoOrderDescription'),
            ]));
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
            foreach ($this->ifthenpayGateway->getSubEntidadeInEntidade($this->configValues['entidade']) as $subEntidade) {
                $this->options[$subEntidade] = $subEntidade;
            }
        }
        $this->form->add($this->ioc->makeWith(Input::class, [
            'friendlyName' => \AdminLang::trans('multibancoSubEntity'),
            'type' => 'dropdown',
            'name' => 'subEntidade',
            'options' => $this->options,
            'description' => \AdminLang::trans('multibancoSubEntityDescription'),
        ]));
        $this->addDynamicMbInputs();
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
