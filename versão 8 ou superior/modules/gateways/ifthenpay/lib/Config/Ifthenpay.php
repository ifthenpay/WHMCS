<?php

namespace WHMCS\Module\Gateway\Ifthenpay\Config;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use Smarty;
use GuzzleHttp\Client;
use WHMCS\Database\Capsule;
use WHMCS\Module\GatewaySetting;
use Illuminate\Container\Container;
use WHMCS\Module\Gateway\ifthenpay\Utility\Token;
use WHMCS\Module\Gateway\ifthenpay\Utility\Status;
use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Callback\Callback;
use WHMCS\Module\Gateway\ifthenpay\Hooks\CheckoutHook;
use WHMCS\Module\Gateway\Ifthenpay\Request\WebService;
use WHMCS\Module\Gateway\Ifthenpay\Builders\DataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackData;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackOnline;
use WHMCS\Module\Gateway\Ifthenpay\Config\IfthenpayUpgrade;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackOffline;
use WHMCS\Module\Gateway\ifthenpay\Hooks\ClientCheckoutHook;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackValidate;
use WHMCS\Module\Gateway\Ifthenpay\Builders\SmartyDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\PaymentDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Payment\PaymentFactory;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Payment\StrategyFactory;
use WHMCS\Module\Gateway\ifthenpay\Hooks\ClientCheckoutConfirmHook;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Payment\OrderDetailFactory;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Callback\CallbackStrategy;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Form\IfthenpayConfigForms;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Callback\CallbackDataFactory;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Payment\PaymentReturnFactory;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment\IfthenpayOrderDetail;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment\IfthenpayPaymentReturn;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Config\IfthenpayConfigFormFactory;

class Ifthenpay
{
    private $ioc;
    private $paymentMethod;

	public function __construct(string $paymentMethod = null)
	{
        $this->ioc = new Container();
        $this->paymentMethod = $paymentMethod;
        $this->bindDependencies();
    }

    private function bindDependencies(): void
    {
        $this->ioc->bind(Utility::class, function () {
                return new Utility();
            }
        );
        $this->ioc->bind(Client::class, function () {
                return new Client();
            }
        );
        $this->ioc->bind(WebService::class, function () {
                return new WebService($this->ioc->make(Client::class));
            }
        );
        $this->ioc->bind(PaymentFactory::class, function () {
                return new PaymentFactory(
                    $this->ioc, 
                    $this->ioc->make(DataBuilder::class),
                    $this->ioc->make(Webservice::class)
                );
            }
        );
        $this->ioc->bind(Gateway::class, function () {
                return new Gateway($this->ioc->make(WebService::class), $this->ioc->make(PaymentFactory::class));
            }
        );
        $this->ioc->bind(GatewayDataBuilder::class, function () {
                return new GatewayDataBuilder();
            }
        );
       /* $this->ioc->bind(Callback::class, function () {
                return new Callback($this->ioc->make(GatewayDataBuilder::class), $this->ioc->make(WebService::class));
            }
        );*/
        $this->ioc->bind(IfthenpayUpgrade::class, function (){
            return new IfthenpayUpgrade($this->ioc->make(Webservice::class));
        });
        $this->ioc->bind(Smarty::class, function() {
            return new Smarty();
        });
        $this->ioc->bind(IfthenpayConfigFormFactory::class, function () {
            return new IfthenpayConfigFormFactory(
                    $this->ioc,
                    GatewaySetting::getForGateway($this->paymentMethod), 
                    $this->ioc->make(Gateway::class), 
                    $this->ioc->make(GatewayDataBuilder::class),
                    $this->ioc->make(Utility::class),
                    $this->ioc->make(Callback::class),
                    $this->ioc->make(IfthenpaySql::class),
                    $this->ioc->make(IfthenpayUpgrade::class),
                    $this->ioc->make(Smarty::class)
                );
            }
        );
        $this->ioc->bind(IfthenpayConfigForms::class, function () {
                return new IfthenpayConfigForms($this->paymentMethod, $this->ioc->make(IfthenpayConfigFormFactory::class));
            }
        );
        $this->ioc->bind(ClientCheckoutHook::class, function () {
                return new ClientCheckoutHook($this->ioc->make(Utility::class));
            }
        );
        $this->ioc->bind(PaymentDataBuilder::class, function () {
                return new PaymentDataBuilder();
            }
        );
        $this->ioc->bind(SmartyDataBuilder::class, function () {
                return new SmartyDataBuilder();
            }
        );
        $this->ioc->bind(OrderDetailFactory::class, function () {
                return new OrderDetailFactory(
                    $this->ioc, 
                    $this->ioc->make(GatewayDataBuilder::class),
                    $this->ioc->make(Gateway::class),
                    GatewaySetting::getForGateway($this->paymentMethod),
                    $this->ioc->make(Utility::class),
                    $this->ioc->make(Token::class),
                    $this->ioc->make(Status::class)
                );
            }
        );
        $this->ioc->bind(PaymentReturnFactory::class, function () {
                return new PaymentReturnFactory(
                    $this->ioc, 
                    $this->ioc->make(GatewayDataBuilder::class),
                    $this->ioc->make(Gateway::class),
                    GatewaySetting::getForGateway($this->paymentMethod),
                    $this->ioc->make(Utility::class),
                    $this->ioc->make(Token::class),
                    $this->ioc->make(Status::class)
                );
            }
        );
        $this->ioc->bind(StrategyFactory::class, PaymentReturnFactory::class);
        $this->ioc->bind(IfthenpayPaymentReturn::class, function () {
                return new IfthenpayPaymentReturn(
                    $this->ioc->make(PaymentDataBuilder::class),
                    $this->ioc->make(SmartyDataBuilder::class), 
                    $this->ioc->make(Utility::class),
                    $this->ioc->make(PaymentReturnFactory::class) 
                );
            }
        );
        $this->ioc->bind(StrategyFactory::class, OrderDetailFactory::class);
        $this->ioc->bind(IfthenpayOrderDetail::class, function () {
                return new IfthenpayOrderDetail(
                    $this->ioc->make(PaymentDataBuilder::class),
                    $this->ioc->make(SmartyDataBuilder::class), 
                    $this->ioc->make(Utility::class),
                    $this->ioc->make(OrderDetailFactory::class) 
                );
            }
        );
        
        $this->ioc->bind(ClientCheckoutConfirmHook::class, function () {
                return new ClientCheckoutConfirmHook(
                    $this->ioc->make(Utility::class), 
                    $this->ioc->make(IfthenpayOrderDetail::class));
            }
        );
        $this->ioc->bind(Token::class, function () {
                return new Token();
            }
        );
        $this->ioc->bind(CallbackDataFactory::class, function() {
            return new CallbackDataFactory($this->ioc, $this->ioc->make(Utility::class));
        });
        $this->ioc->bind(CallbackData::class, function() {
            return new CallbackData($this->ioc->make(CallbackDataFactory::class));
        });
        $this->ioc->bind(CallbackValidate::class, function() {
            return new CallbackValidate();
        });
        $this->ioc->bind(Status::class, function() {
            return new Status();
        });
        $this->ioc->bind(CallbackOffline::class, function() {
                return new CallbackOffline(
                    $this->ioc->make(CallbackData::class), 
                    $this->ioc->make(CallbackValidate::class), 
                    $this->ioc->make(Utility::class)
                );
            }
        );
        $this->ioc->bind(CallbackOnline::class, function() {
                return new CallbackOnline(
                    $this->ioc->make(CallbackData::class), 
                    $this->ioc->make(CallbackValidate::class), 
                    $this->ioc->make(Utility::class),
                    $this->ioc->make(Status::class),
                    $this->ioc->make(Token::class)
                );
            }
        );
        $this->ioc->bind(CallbackStrategy::class, function() {
                return new CallbackStrategy(
                    $this->ioc->make(CallbackOffline::class),
                    $this->ioc->make(CallbackOnline::class)
                );
            }
        );        
    }

    public function getConfigForm(): array
    {
        return $this->ioc->make(IfthenpayConfigForms::class)->buildForm();
    }

    public function getHooks(string $type, array $vars): CheckoutHook
    {
        if ($type === 'clientCheckoutHook') {
            return $this->ioc->make(ClientCheckoutHook::class)->setVars($vars);
        } else {
            return $this->ioc->make(ClientCheckoutConfirmHook::class)->setVars($vars)->setPaymentMethod($vars['paymentmethod']);
        }
    }

    public function getPaymentData(array $params)
    {
        $paymentData = $this->ioc->make(Utility::class)->convertObjectToarray(
            Capsule::table('ifthenpay_' . $params['paymentmethod'])->where('order_id', $params['invoiceid'])->first()
        );
            
        if (empty($paymentData)) {
            //fazer o bind do paymentReturn e orderDetail
            //eu coloquei um setter para os params, assim já podemos fazer o bind sem usar makeWith
            return $this->ioc->make(IfthenpayPaymentReturn::class)->setParams($params)->execute();
        } else {
            return $paymentData;
        }
    }
    /**
     * Get the value of ioc
     */ 
    public function getIoc()
    {
        return $this->ioc;
    }

    /**
     * Set the value of paymentMethod
     *
     * @return  self
     */ 
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }
}