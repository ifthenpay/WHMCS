<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Forms;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use Smarty;
use Illuminate\Container\Container;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Callback\Callback;
use WHMCS\Module\Gateway\Ifthenpay\Config\IfthenpaySql;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Config\IfthenpayUpgrade;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\ifthenpay\Forms\Composite\Elements\Form;
use WHMCS\Module\Gateway\Ifthenpay\Exceptions\BackOfficeException;
use WHMCS\Module\Gateway\ifthenpay\Forms\Composite\Elements\Input;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\UtilityInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ConfigGatewaysRepositoryInterface;

abstract class ConfigForm
{
    protected $ioc;
    protected $backofficeKey;
    protected $callbackData;
    protected $ifthenpayUserAccount;
    protected $activatedCallback;
    protected $paymentMethodNameAlias;
    protected $configGatewaysRepository;
    protected $callback;
    protected $ifthenpaySql;
    protected $utility;
    protected $paymentMethod;
    protected $form;
    protected $gatewayDataBuilder;
    protected $ifthenpayGateway;
    protected $options;
    protected $configValues;
    protected $gatewayVars;
    protected $hasCallback = true;
    private $ifthenpayUpgrade;
    protected $smarty;
    protected $ifthenpayLogger;

    public function __construct(
        Container $ioc,
        array $gatewayVars,
        Gateway $gateway, 
        GatewayDataBuilder $gatewayDataBuilder, 
        ConfigGatewaysRepositoryInterface $configGatewaysRepository,
        UtilityInterface $utility,
        Callback $callback,
        IfthenpaySql $ifthenpaySql,
        IfthenpayUpgrade $ifthenpayUpgrade,
        Smarty $smarty,
        IfthenpayLogger $ifthenpayLogger
    )
    {
        $this->ioc = $ioc;
        $this->gatewayVars = $gatewayVars;
        $this->backofficeKey = $this->gatewayVars['backofficeKey'];
        $this->configGatewaysRepository = $configGatewaysRepository;
        $this->utility = $utility;
        $this->callback = $callback;
        $this->ifthenpaySql = $ifthenpaySql;
        $this->callbackData = $this->configGatewaysRepository->getCallbackData($this->paymentMethod);
        $this->ifthenpayUserAccount = $this->configGatewaysRepository->getIfthenpayUserAccount($this->paymentMethod);
        $this->activatedCallback = $this->configGatewaysRepository->getActivatedCallback($this->paymentMethod);
        $this->form = $this->ioc->makeWith(
            Form::class, ['paymentMethod' => $this->paymentMethod, 'paymentMethodNameAlias' => method_exists($this, 'getPaymentMethodNameAlias') ? $this->getPaymentMethodNameAlias() : $this->paymentMethodNameAlias]
        );
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->ifthenpayGateway = $gateway;
        $this->ifthenpayGateway->setAccount($this->ifthenpayUserAccount);
        $this->options = [];
        $this->configValues = $this->checkConfigValues();
        $this->ifthenpayUpgrade = $ifthenpayUpgrade;
        $this->smarty = $smarty;
        $this->ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger->getChannelBackofficeConst($this->paymentMethod))->getLogger();
    }

    protected function addSandboxAndActivateCallback(): void {
        $this->form->add($this->ioc->makeWith(Input::class, [
            'friendlyName' => \AdminLang::trans('sandboxMode'),
            'type' => 'yesno',
            'name' => 'sandboxMode',
            'description' => \AdminLang::trans('sandboxModeDescription'),
        ]));
        $this->ifthenpayLogger->info('sandbox mode input added with success to form');
        $this->form->add($this->ioc->makeWith(Input::class, [
            'friendlyName' => \AdminLang::trans('callbackActivate'),
            'type' => 'yesno',
            'name' => 'activateCallback',
            'description' => \AdminLang::trans('callbackActivateDescription'),
        ]));
        $this->ifthenpayLogger->info('activate callback input added with success to form');
    }

    protected function addShowPaymentIconCheckout(): void
    {
        $this->form->add($this->ioc->makeWith(Input::class, [
            'friendlyName' => \AdminLang::trans('showPaymentLogo'),
            'type' => 'yesno',
            'name' => 'showPaymentLogo',
            'description' => \AdminLang::trans('showPaymentLogoDescription'),
        ]));
        $this->ifthenpayLogger->info('show payment icon input added with success to form');
    }

    private function addCallbackInfoToConfigForm(): void
    {
        if ($this->activatedCallback) {
            $btn = '<button type="button" class="btn btn-success">' . \AdminLang::trans('callbackActivated') . '</button>';
        } else {
            $btn = '<button type="button" class="btn btn-danger">' . \AdminLang::trans('callbackNotActivated') . '</button>';
        }
        $this->ifthenpayLogger->info('callback activated label added with success', ['activatedCallback' => $this->activatedCallback]);
        $this->form->add($this->ioc->makeWith(Input::class, [
            'type' => 'System',
            'name' => 'UsageNotes',
            'value' =>  $btn . '<br>' . \AdminLang::trans('antiPhishingKey') . $this->callbackData[0] . 
                '</strong><br>' . \AdminLang::trans('urlCallback') . $this->callbackData[1] . '</strong>'
        ]));
        $this->ifthenpayLogger->info('callback data info added with success', ['urlCallback' => $this->callbackData[1], 'antiPhishingKey' => $this->callbackData[0]]);
    }

    private function getIfthenpayUserAccountFromWebservice(): void
    {
        try {
            $this->ifthenpayGateway->authenticate($this->backofficeKey);
            $this->ifthenpayLogger->info('backoffice key authenticated with success', ['backofficeKey' => $this->backofficeKey]);
            $this->ifthenpayUserAccount = $this->ifthenpayGateway->getAccount($this->paymentMethod);
            $this->ifthenpayLogger->info('user account retrieved with success', ['userAccount' => $this->ifthenpayUserAccount]);
            if (empty($this->ifthenpayUserAccount)) {
                $this->form->add($this->ioc->makeWith(Input::class, [
                    'type' => 'System', 
                    'name' => 'UsageNotes', 
                    'value' =>  '<button type="button" class="btn btn-danger">' . \AdminLang::trans('notAccount' . ucfirst($this->paymentMethod)) . 
                        '</button>' . '<br><br>' . \AdminLang::trans('requestAccount' . ucfirst($this->paymentMethod)) . 
                        ':<br><a id="requestNewAccount" class="btn btn-success" href="" data-paymentmethod="' . $this->paymentMethod . '">' . \AdminLang::trans('sendEmailNewAccount') . '</a><br><br>' .
                        \AdminLang::trans('updateAccountDescription')
                ]));
                $this->ifthenpayLogger->info('user with no account field notification added to form with success');
            }
            
        } catch (\Throwable $th) {
            $this->ifthenpayLogger->error('error authenticating backoffice', ['backofficeKey' => $this->backofficeKey, 'exception' => $th]);
            throw new BackOfficeException($th->getMessage());
        }
        
    }

    private function saveIfthenpayUserAccount(): void
    {
        $this->configGatewaysRepository->create(
            [
                'gateway' => $this->paymentMethod, 
                'setting' => 'userAccount', 
                'value' => serialize($this->ifthenpayUserAccount), 
                'order' => 0
            ]
        );
        $this->ifthenpayLogger->info('ifthenpay user account saved with success', ['userAccount' => $this->ifthenpayUserAccount]);
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
            'friendlyName' => \AdminLang::trans('backofficeKey'),
            'type' => 'text',
            'name' => 'backofficeKey',
            'description' => \AdminLang::trans('backofficeKeyDescription'),
        ]));
        $this->ifthenpayLogger->info('backoffice key input added to form with success');
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
                    'updatedModuleIcon' => $this->utility->getSvgUrl() . '/updated.svg',
                    'ifthenpayNewUpdateTitle' => \ADMINLANG::trans('ifthenpayNewUpdateTitle'),
                    'downloadUpdateIfthenpay' => \ADMINLANG::trans('downloadUpdateIfthenpay'),
                    'ifthenpayNoUpdate' => \ADMINLANG::trans('ifthenpayNoUpdate')
                ];  
                $this->smarty->assign($data);
                $html = $this->smarty->fetch('file:' . ROOTDIR . '\modules\gateways\ifthenpay\templates\ifthenpayUpgradeModule.tpl');
                $this->form->add($this->ioc->makeWith(Input::class, [
                    "friendlyName" => "", 'name' => 'htmlField', "type" => "html", 'options' => null, 'description' => $html
                ]));
                $this->ifthenpayLogger->info('upgrade module notification added with success to form', ['needUpgrade' => $$needUpgrade]);            
        } catch (\Throwable $th) {
            $this->ifthenpayLogger->error('error checking upgrade module notification', ['exception' => $th]);
            throw $th;
        }
        
    }

    protected function addToOptions(bool $afterSave = false): void
    {
        if (!$afterSave) {
            $this->options = [
                'default' => \AdminLang::trans('multibancoEntityDescription')
            ];
        }
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
        $this->ifthenpayLogger->info('ifthenpay account options retrieved with success', [
                'options' => $this->options
            ]
        );
    }

    protected function setGatewayBuilderData(): void
    {
        $this->gatewayDataBuilder->setBackofficeKey($this->gatewayVars['backofficeKey']);
    }

    protected function saveCallback(string $url, string $antiPhishingKey, bool $activateCallback)
    {
        try {
            $this->configGatewaysRepository->createOrUpdate(
                ['gateway' => $this->paymentMethod, 'setting' => 'urlCallback'],
                ['value' => $url]
            );
            $this->configGatewaysRepository->createOrUpdate(
                ['gateway' => $this->paymentMethod, 'setting' => 'chaveAntiPhishing'],
                ['value' => $antiPhishingKey]
            );
            $this->configGatewaysRepository->createOrUpdate(
                ['gateway' => $this->paymentMethod, 'setting' => 'activatedCallback'],
                ['value' => $activateCallback ? true : false]
            );
            $this->callbackData = [$antiPhishingKey, $url];
            $this->ifthenpayLogger->info('callback activation data saved with success', [
                    'paymentMethod' => $this->paymentMethod,
                    'url' => $url,
                    'antiPhishingKey' =>$antiPhishingKey,
                ]
            );
        } catch (\Throwable $th) {
            $this->ifthenpayLogger->error('error saving callback activation data in database', [
                    'paymentMethod' => $this->paymentMethod,
                    'url' => $url,
                    'antiPhishingKey' =>$antiPhishingKey,
                    'activateCallback' => $activateCallback,
                    'exception' => $th

                ]
            );
            throw $th;
        }
    }

    public function getForm(): array
    {
        if ($this->backofficeKey) {
            $this->addUpgradeModuleToForm();
            //$this->renderCallbackInfo();
            if ($this->hasCallback) {
                $this->addSandboxAndActivateCallback();
            }
            if (empty($this->ifthenpayUserAccount)) {
                $this->getIfthenpayUserAccountFromWebservice();            
                $this->saveIfthenpayUserAccount();
                if (!empty($this->ifthenpayUserAccount)) {
                    $this->addShowPaymentIconCheckout();
                    $this->addPaymentInputsToForm();
                }
            } else {
                $this->ifthenpaySql->setPaymentMethod($this->paymentMethod)->install();
                $this->ifthenpayLogger->info('payments tables created with success in database');
                $this->addShowPaymentIconCheckout();
                $this->addPaymentInputsToForm();
                $this->processForm();
                $this->renderCallbackInfo();
                
            }
        } else {
            $this->addBackofficeKeyInputToForm();
        }
        $this->ifthenpayLogger->info('backoffice payment method form build with success');    
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
                    $this->activatedCallback = $activateCallback;
                    $this->saveCallback($ifthenpayCallback->getUrlCallback(), $ifthenpayCallback->getChaveAntiPhishing(), $activateCallback);
                }
                $this->ifthenpayLogger->info('callback data build with success', [
                        'activateCallback' => $activateCallback,
                        'urlCallback' => $ifthenpayCallback->getUrlCallback(),
                        'antiPhishingKey' => $ifthenpayCallback->getChaveAntiPhishing()
                    ]
                );               
            }
        } catch (\Throwable $th) {
            $this->ifthenpayLogger->error('error building callback data', [
                    'activateCallback' => $activateCallback,
                    'data' => $this->gatewayDataBuilder,
                    'paymentMethod' => $this->paymentMethod,
                    'exception' => $th
                ]
            );
            throw $th;
        }
    }

    abstract protected function addPaymentInputsToForm(): void;
    abstract protected function checkConfigValues(): array;
}
