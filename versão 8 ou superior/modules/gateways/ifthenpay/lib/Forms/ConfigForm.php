<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Forms;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use Smarty;
use WHMCS\Database\Capsule;
use Illuminate\Container\Container;
use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Callback\Callback;
use WHMCS\Module\Gateway\Ifthenpay\Config\IfthenpaySql;
use WHMCS\Module\Gateway\Ifthenpay\Config\IfthenpayUpgrade;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\ifthenpay\Forms\Composite\Elements\Form;
use WHMCS\Module\Gateway\ifthenpay\Forms\Composite\Elements\Input;

abstract class ConfigForm
{
    protected $ioc;
    protected $backofficeKey;
    protected $callbackData;
    protected $ifthenpayUserAccount;
    protected $activatedCallback;
    protected $paymentMethodNameAlias;
    protected $utility;
    protected $callback;
    protected $ifthenpaySql;


    protected $paymentMethod;
    protected $form;
    protected $gatewayDataBuilder;
    private $ifthenpayGateway;
    protected $options;
    protected $configValues;
    protected $gatewayVars;
    protected $hasCallback = true;
    private $ifthenpayUpgrade;
    private $smarty;

    public function __construct(
        Container $ioc,
        array $gatewayVars,
        Gateway $gateway, 
        GatewayDataBuilder $gatewayDataBuilder, 
        Utility $utility, 
        Callback $callback,
        IfthenpaySql $ifthenpaySql,
        IfthenpayUpgrade $ifthenpayUpgrade,
        Smarty $smarty
    )
    {
        $this->ioc = $ioc;
        $this->gatewayVars = $gatewayVars;
        $this->backofficeKey = $this->gatewayVars['backofficeKey'];
        $this->utility = $utility;
        $this->callback = $callback;
        $this->ifthenpaySql = $ifthenpaySql;
        $this->callbackData = $this->utility->getCallbackData($this->paymentMethod);
        $this->ifthenpayUserAccount = $this->utility->getIfthenpayUserAccount($this->paymentMethod);
        $this->activatedCallback = $this->utility->getActivatedCallback($this->paymentMethod);
        $this->form = $this->ioc->makeWith(Form::class, ['paymentMethod' => $this->paymentMethod, 'paymentMethodNameAlias' => $this->paymentMethodNameAlias]);


        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->ifthenpayGateway = $gateway;
        $this->ifthenpayGateway->setAccount($this->ifthenpayUserAccount);
        $this->options = [];
        $this->configValues = $this->checkConfigValues();
        $this->ifthenpayUpgrade = $ifthenpayUpgrade;
        $this->smarty = $smarty;
    }

    protected function addSandboxAndActivateCallback(): void {
        $this->form->add($this->ioc->makeWith(Input::class, [
            'friendlyName' => 'Sandbox Mode',
            'type' => 'yesno',
            'name' => 'sandboxMode',
            'description' => 'Tick to enable sandbox mode',
        ]));
        $this->form->add($this->ioc->makeWith(Input::class, [
            'friendlyName' => 'Activate Callback',
            'type' => 'yesno',
            'name' => 'activateCallback',
            'description' => 'Tick to activate callback',
        ]));
    }

    private function addCallbackInfoToConfigForm(): void
    {
        if ($this->activatedCallback) {
            $btn = '<button type="button" class="btn btn-success">Callback activated</button>';
        } else {
            $btn = '<button type="button" class="btn btn-danger">Callback not activated</button>';
        }
        $this->form->add($this->ioc->makeWith(Input::class, [
            'type' => 'System',
            'name' => 'UsageNotes',
            'value' =>  $btn . '<br>' . 'Anti-phishing key: <strong>' . $this->callbackData[0] . 
                '</strong><br>' . 'Callback Url: <strong>' . $this->callbackData[1] . '</strong>'
        ]));
    }

    private function getIfthenpayUserAccountFromWebservice(): void
    {
        $this->ifthenpayGateway->authenticate($this->backofficeKey);
        $this->ifthenpayUserAccount = $this->ifthenpayGateway->getAccount($this->paymentMethod);
        if (empty($this->ifthenpayUserAccount)) {
            $this->form->add($this->ioc->makeWith(Input::class, [
                'type' => 'System',
                'name' => 'UsageNotes',
                'value' =>  '<button type="button" class="btn btn-danger">Não tem conta ' . ucfirst($this->paymentMethod) . 
                    '</button>' . '<br><br>' . 'Solicite a criação de conta ' . ucfirst($this->paymentMethod) . 
                    ':<br><a class="btn btn-success" href="mailto:suporte@ifthenpay.com">Enviar email</a><br><br>
                    Após receber os dados da nova conta, atualize os seus dados abaixo:<br> 
                    <button onClick="window.location.reload();" type="button" class="btn btn-success">Atualizar Dados</button>'
            ]));
        }
    }

    private function saveIfthenpayUserAccount(): void
    {
        Capsule::table('tblpaymentgateways')->insert([
            [
                'gateway' => $this->paymentMethod, 
                'setting' => 'userAccount', 
                'value' => serialize($this->ifthenpayUserAccount), 
                'order' => 0
            ],
        ]);
    }

    private function renderCallbackInfo(): void
    {
        if (!empty($this->callbackData)) {
            $this->addCallbackInfoToConfigForm();
        }
    }

    protected function addBackofficeKeyInputToForm(): void
    {
        $this->form->add($this->ioc->makeWith(Input::class, [
            'friendlyName' => 'Backoffice key',
            'type' => 'text',
            'name' => 'backofficeKey',
            'description' => 'Enter your backoffice key provided by Ifthenpay',
        ]));
    }

    protected function addUpgradeModuleToForm(): void
    {
        try {
                $needUpgrade = $this->ifthenpayUpgrade->setPaymentMethod($this->paymentMethod)->checkModuleUpgrade();
                $data = [
                    'updateIfthenpayModuleAvailable' => $needUpgrade['upgrade'] ? true : false,
                    'updateSystemIcon' => $this->utility->getSvgUrl() . '/system-update.svg',
                    'upgradeModuleBulletPoints' => $needUpgrade['upgrade'] ? $needUpgrade['body'] : '',
                    'moduleUpgradeUrlDownload' => $needUpgrade['upgrade'] ? $needUpgrade['download'] : '',
                    'updatedModuleIcon' => $this->utility->getSvgUrl() . '/updated.svg'
                ];  
                $this->smarty->assign($data);
                $html = $this->smarty->fetch('file:' . ROOTDIR . '\modules\gateways\ifthenpay\templates\ifthenpayUpgradeModule.tpl');
                $this->form->add($this->ioc->makeWith(Input::class, [
                    "friendlyName" => "", "type" => "html", 'options' => null, 'description' => $html
                ]));            
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }

    protected function addToOptions(): void
    {
        foreach ($this->ifthenpayGateway->getEntidadeSubEntidade($this->paymentMethod) as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $key2 => $value2) {
                    if (strlen($value2) > 3) {
                        $this->options[$value2] = $value2;
                    }
                }
            } else {
                $this->options[$value] = $value;
            }
        }
    }

    protected function setGatewayBuilderData(): void
    {
        $this->gatewayDataBuilder->setBackofficeKey($this->gatewayVars['backofficeKey']);
    }

    protected function saveCallback(string $url, string $antiPhishingKey, bool $activateCallback)
    {
        try {
            Capsule::table('tblpaymentgateways')
            ->updateOrInsert(
                ['gateway' => $this->paymentMethod, 'setting' => 'urlCallback'],
                ['value' => $url]
            );
            Capsule::table('tblpaymentgateways')
            ->updateOrInsert(
                ['gateway' => $this->paymentMethod, 'setting' => 'chaveAntiPhishing'],
                ['value' => $antiPhishingKey]
            );
            Capsule::table('tblpaymentgateways')
            ->updateOrInsert(
                ['gateway' => $this->paymentMethod, 'setting' => 'activatedCallback'],
                ['value' => $activateCallback ? true : false]
            );
            $this->callbackData = [$antiPhishingKey, $url];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getForm(): array
    {
        if ($this->backofficeKey) {
            $this->addUpgradeModuleToForm();
            $this->renderCallbackInfo();
            if ($this->hasCallback) {
                $this->addSandboxAndActivateCallback();
            }
            if (empty($this->ifthenpayUserAccount)) {
                $this->getIfthenpayUserAccountFromWebservice();            
                $this->saveIfthenpayUserAccount();
                if (!empty($this->ifthenpayUserAccount)) {
                    $this->addPaymentInputsToForm();
                }
            } else {
                $this->ifthenpaySql->setPaymentMethod($this->paymentMethod)->install();
                $this->addPaymentInputsToForm();
                $this->processForm();
                $this->renderCallbackInfo();
                
            }
        } else {
            $this->addBackofficeKeyInputToForm();
        }    
        return $this->form->render();
    }

    public function processForm(): void
    {
        try {
            if (!$this->activatedCallback && !empty($this->configValues)) {
                $this->setGatewayBuilderData();

                $activateCallback = !$this->gatewayVars['sandboxMode'] && $this->gatewayVars['activateCallback'] ? true : false;
                $ifthenpayCallback = $this->ioc->makeWith(Callback::class, ['data' => $this->gatewayDataBuilder]);
                $ifthenpayCallback->make($this->paymentMethod, $this->utility->getCallbackControllerUrl($this->paymentMethod), $activateCallback);

                if (empty($this->callbackData) || !$this->activatedCallback) {
                    $this->activatedCallback = true;
                    $this->saveCallback($ifthenpayCallback->getUrlCallback(), $ifthenpayCallback->getChaveAntiPhishing(), $activateCallback);
                }               
            }
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    abstract protected function addPaymentInputsToForm(): void;
    abstract protected function checkConfigValues(): array;
}
