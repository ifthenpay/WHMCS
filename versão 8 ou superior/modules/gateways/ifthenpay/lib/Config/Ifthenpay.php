<?php

namespace WHMCS\Module\Gateway\Ifthenpay\Config;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use Smarty;
use GuzzleHttp\Client;
use WHMCS\Module\GatewaySetting;
use Illuminate\Container\Container;
use WHMCS\Module\Gateway\ifthenpay\Utility\Mix;
use WHMCS\Module\Gateway\ifthenpay\Utility\Token;
use WHMCS\Module\Gateway\ifthenpay\Utility\Status;
use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Callback\Callback;
use WHMCS\Module\Gateway\Ifthenpay\Request\WebService;
use WHMCS\Module\Gateway\ifthenpay\Utility\TokenExtra;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Builders\DataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackData;
use WHMCS\Module\Gateway\Ifthenpay\Facades\PaymentFacade;
use WHMCS\Module\Gateway\ifthenpay\Hooks\EmailPreSendHook;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackOnline;
use WHMCS\Module\Gateway\Ifthenpay\Config\IfthenpayUpgrade;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackOffline;
use WHMCS\Module\Gateway\ifthenpay\Hooks\ClientCheckoutHook;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackValidate;
use WHMCS\Module\Gateway\Ifthenpay\Builders\SmartyDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\PaymentDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Payments\MbWayPaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\ChangeEntidade;
use WHMCS\Module\Gateway\Ifthenpay\Payments\WhmcsInvoiceHistory;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\CCardRepository;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\MbWayRepository;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\OrderRepository;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Hooks\HooksStrategy;
use WHMCS\Module\Gateway\Ifthenpay\Payments\PayshopPaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\ConfigRepository;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Payment\PaymentFactory;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\MbwayCancelOrder;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\InvoiceRepository;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\PayshopRepository;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Payment\StrategyFactory;
use WHMCS\Module\Gateway\ifthenpay\Hooks\ClientCheckoutConfirmHook;
use WHMCS\Module\Gateway\Ifthenpay\Payments\MultibancoPaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\MultibancoRepository;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Payment\OrderDetailFactory;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Callback\CallbackStrategy;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Form\IfthenpayConfigForms;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Callback\CallbackDataFactory;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Payment\PaymentReturnFactory;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Payment\PaymentStatusFactory;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Repository\RepositoryFactory;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\ResendMbwayNotification;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\ConfigGatewaysRepository;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment\IfthenpayOrderDetail;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment\IfthenpayInvoiceUpdate;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment\IfthenpayPaymentReturn;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment\IfthenpayPaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment\IfthenpayInvoiceCreated;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Config\IfthenpayConfigFormFactory;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Payment\UpdatePaymentReturnFactory;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\CCardRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\MbWayRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\OrderRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ConfigRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\InvoiceRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\PayshopRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\MultibancoRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ConfigGatewaysRepositoryInterface;

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
        $this->ioc->bind(IfthenpayLogger::class, function () {
            return new IfthenpayLogger();
        });
        $this->ioc->bind(Mix::class, function() {
            return new Mix($this->ioc->make(IfthenpayLogger::class));
        });
        $this->ioc->bind(Utility::class, function () {
                return new Utility($this->ioc->make(ConfigRepositoryInterface::class));
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
        $this->ioc->bind(IfthenpaySql::class, function() {
            return new IfthenpaySql($this->ioc->make(IfthenpayLogger::class));
        });
        $this->ioc->bind(IfthenpayConfigFormFactory::class, function () {
            return new IfthenpayConfigFormFactory(
                    $this->ioc,
                    GatewaySetting::getForGateway($this->paymentMethod), 
                    $this->ioc->make(Gateway::class), 
                    $this->ioc->make(GatewayDataBuilder::class),
                    $this->ioc->make(ConfigGatewaysRepositoryInterface::class),
                    $this->ioc->make(Utility::class),
                    $this->ioc->make(Callback::class),
                    $this->ioc->make(IfthenpaySql::class),
                    $this->ioc->make(IfthenpayUpgrade::class),
                    $this->ioc->make(Smarty::class),
                    $this->ioc->make(IfthenpayLogger::class)
                );
            }
        );
        $this->ioc->bind(IfthenpayConfigForms::class, function () {
                return new IfthenpayConfigForms($this->paymentMethod, $this->ioc->make(IfthenpayConfigFormFactory::class));
            }
        );
        $this->ioc->bind(ClientCheckoutHook::class, function () {
                return new ClientCheckoutHook($this->ioc->make(Utility::class), $this->ioc->make(Mix::class), $this->ioc->make(IfthenpayLogger::class));
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
                    $this->ioc->make(RepositoryFactory::class),
                    $this->ioc->make(IfthenpayLogger::class),
                    $this->ioc->make(Token::class),
                    $this->ioc->make(Status::class),
                    $this->ioc->make(TokenExtra::class)
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
                    $this->ioc->make(RepositoryFactory::class),
                    $this->ioc->make(IfthenpayLogger::class),
                    $this->ioc->make(Token::class),
                    $this->ioc->make(Status::class),
                    $this->ioc->make(TokenExtra::class)
                );
            }
        );
        $this->ioc->bind(StrategyFactory::class, PaymentReturnFactory::class);
        $this->ioc->bind(IfthenpayPaymentReturn::class, function () {
                return new IfthenpayPaymentReturn(
                    $this->ioc->make(PaymentDataBuilder::class),
                    $this->ioc->make(SmartyDataBuilder::class), 
                    $this->ioc->make(Utility::class),
                    $this->ioc->make(PaymentReturnFactory::class),
                    $this->ioc->make(IfthenpayLogger::class)
                );
            }
        );
        $this->ioc->bind(StrategyFactory::class, OrderDetailFactory::class);
        $this->ioc->bind(IfthenpayOrderDetail::class, function () {
                return new IfthenpayOrderDetail(
                    $this->ioc->make(PaymentDataBuilder::class),
                    $this->ioc->make(SmartyDataBuilder::class), 
                    $this->ioc->make(Utility::class),
                    $this->ioc->make(OrderDetailFactory::class),
                    $this->ioc->make(IfthenpayLogger::class) 
                );
            }
        );
        $this->ioc->bind(ClientCheckoutConfirmHook::class, function () {
                return new ClientCheckoutConfirmHook(
                    $this->ioc->make(Utility::class), 
                    $this->ioc->make(IfthenpayOrderDetail::class),
                    $this->ioc->make(Mix::class)
                );
            }
        );
        $this->ioc->bind(Token::class, function () {
                return new Token();
            }
        );
        $this->ioc->bind(CallbackDataFactory::class, function() {
            return new CallbackDataFactory($this->ioc, $this->ioc->make(RepositoryFactory::class));
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
        $this->ioc->bind(WhmcsInvoiceHistory::class, function () {
            return new WhmcsInvoiceHistory($this->ioc->make(InvoiceRepository::class), $this->ioc->make(IfthenpayLogger::class));
        });
        $this->ioc->bind(TokenExtra::class, function () {
            return new TokenExtra();
        });
        $this->ioc->bind(CallbackOffline::class, function() {
                return new CallbackOffline(
                    $this->ioc->make(CallbackData::class), 
                    $this->ioc->make(CallbackValidate::class), 
                    $this->ioc->make(RepositoryFactory::class),
                    $this->ioc->make(InvoiceRepository::class),
                    $this->ioc->make(WhmcsInvoiceHistory::class),
                    $this->ioc->make(IfthenpayLogger::class)
                );
            }
        );
        $this->ioc->bind(CallbackOnline::class, function() {
                return new CallbackOnline(
                    $this->ioc->make(CallbackData::class), 
                    $this->ioc->make(CallbackValidate::class), 
                    $this->ioc->make(RepositoryFactory::class),
                    $this->ioc->make(InvoiceRepository::class),
                    $this->ioc->make(WhmcsInvoiceHistory::class),
                    $this->ioc->make(IfthenpayLogger::class),
                    $this->ioc->make(Status::class),
                    $this->ioc->make(Token::class),
                    $this->ioc->make(TokenExtra::class)
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
        $this->ioc->bind(RepositoryFactory::class, function () {
            return new RepositoryFactory($this->ioc);
        });
        $this->ioc->bind(PaymentFacade::class, function() {
            return new PaymentFacade(
                $this->ioc->make(RepositoryFactory::class), 
                $this->ioc->make(IfthenpayPaymentReturn::class),
                $this->ioc->make(IfthenpayLogger::class)
            );
        });
        $this->ioc->bind(IfthenpayInvoiceCreated::class, function () {
            return new IfthenpayInvoiceCreated(
                $this->ioc->make(PaymentDataBuilder::class),
                $this->ioc->make(SmartyDataBuilder::class), 
                $this->ioc->make(Utility::class),
                $this->ioc->make(OrderDetailFactory::class),
                $this->ioc->make(IfthenpayLogger::class)
            );
        }
    );
        $this->ioc->bind(EmailPreSendHook::class, function () {
            return new EmailPreSendHook($this->ioc->make(Utility::class), $this->ioc->make(IfthenpayInvoiceCreated::class), $this->ioc->make(Mix::class));
        });
        $this->ioc->bind(HooksStrategy::class, function () {
            return new HooksStrategy(
                $this->ioc->make(ClientCheckoutHook::class),
                $this->ioc->make(ClientCheckoutConfirmHook::class),
                $this->ioc->make(EmailPreSendHook::class)
            );
        });
        $this->ioc->bind(CCardRepositoryInterface::class, CCardRepository::class);
        $this->ioc->bind(ConfigGatewaysRepositoryInterface::class, ConfigGatewaysRepository::class);
        $this->ioc->bind(ConfigRepositoryInterface::class, ConfigRepository::class);
        $this->ioc->bind(MbWayRepositoryInterface::class, MbWayRepository::class);
        $this->ioc->bind(MultibancoRepositoryInterface::class, MultibancoRepository::class);
        $this->ioc->bind(PayshopRepositoryInterface::class, PayshopRepository::class);
        $this->ioc->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->ioc->bind(InvoiceRepositoryInterface::class, OrderRepository::class);
        
        $this->ioc->bind(ResendMbwayNotification::class, function () {
            return new ResendMbwayNotification(
                $this->ioc->make(ConfigRepositoryInterface::class),
                $this->ioc->make(MbWayRepositoryInterface::class),
                $this->ioc->make(GatewayDataBuilder::class),
                $this->ioc->make(Gateway::class),
                $this->ioc->make(IfthenpayLogger::class)
            );
        });
        $this->ioc->bind(ChangeEntidade::class, function () {
            return new ChangeEntidade(
                $this->ioc->make(ConfigGatewaysRepositoryInterface::class), 
                $this->ioc->make(Gateway::class), 
                $this->ioc->make(IfthenpayLogger::class)
            );
        });
        $this->ioc->bind(MultibancoPaymentStatus::class, function () {
            return new MultibancoPaymentStatus($this->ioc->make(WebService::class), $this->ioc->make(IfthenpayLogger::class));
        });
        $this->ioc->bind(MbWayPaymentStatus::class, function () {
            return new MbWayPaymentStatus($this->ioc->make(WebService::class), $this->ioc->make(IfthenpayLogger::class));
        });
        $this->ioc->bind(PayshopPaymentStatus::class, function() {
            return new PayshopPaymentStatus($this->ioc->make(WebService::class), $this->ioc->make(IfthenpayLogger::class));
        });
        $this->ioc->bind(PaymentStatusFactory::class, function () {
            return new PaymentStatusFactory(
                $this->ioc,
                $this->ioc->make(GatewayDataBuilder::class),
                $this->ioc->make(InvoiceRepository::class),
                $this->ioc->make(RepositoryFactory::class),
                $this->ioc->make(WhmcsInvoiceHistory::class),
                $this->ioc->make(IfthenpayLogger::class),
                GatewaySetting::getForGateway($this->paymentMethod)
            );
        });
        $this->ioc->bind(MbwayCancelOrder::class, function () {
            return new MbwayCancelOrder(
                $this->ioc->make(GatewayDataBuilder::class),
                $this->ioc->make(MbWayPaymentStatus::class),
                $this->ioc->make(InvoiceRepository::class),
                $this->ioc->make(MbWayRepository::class),
                $this->ioc->make(WhmcsInvoiceHistory::class),
                $this->ioc->make(IfthenpayLogger::class),
                GatewaySetting::getForGateway($this->paymentMethod)
            );
        });
        $this->ioc->bind(UpdatePaymentReturnFactory::class, function() {
            return new UpdatePaymentReturnFactory(
                $this->ioc, 
                $this->ioc->make(GatewayDataBuilder::class),
                $this->ioc->make(Gateway::class),
                GatewaySetting::getForGateway($this->paymentMethod),
                $this->ioc->make(Utility::class),
                $this->ioc->make(RepositoryFactory::class),
                $this->ioc->make(IfthenpayLogger::class),
                $this->ioc->make(Token::class),
                $this->ioc->make(Status::class)
            );
        });
        $this->ioc->bind(IfthenpayInvoiceUpdate::class, function () {
            return new IfthenpayInvoiceUpdate(
                $this->ioc->make(PaymentDataBuilder::class),
                $this->ioc->make(SmartyDataBuilder::class),
                $this->ioc->make(Utility::class),
                $this->ioc->make(UpdatePaymentReturnFactory::class),
                $this->ioc->make(InvoiceRepository::class),
                $this->ioc->make(Gateway::class),
                $this->ioc->make(IfthenpayLogger::class)
            );
        });
        $this->ioc->bind(IfthenpayPaymentStatus::class, function () {
            return new IfthenpayPaymentStatus($this->ioc->make(PaymentStatusFactory::class));
        });
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