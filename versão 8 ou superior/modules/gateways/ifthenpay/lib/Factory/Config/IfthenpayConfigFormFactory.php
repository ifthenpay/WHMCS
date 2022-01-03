<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Factory\Config;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use Smarty;
use Illuminate\Container\Container;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Factory;
use WHMCS\Module\Gateway\Ifthenpay\Forms\ConfigForm;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Callback\Callback;
use WHMCS\Module\Gateway\Ifthenpay\Config\IfthenpaySql;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\ifthenpay\Forms\CCardConfigForm;
use WHMCS\Module\Gateway\Ifthenpay\Forms\MbwayConfigForm;
use WHMCS\Module\Gateway\Ifthenpay\Config\IfthenpayUpgrade;
use WHMCS\Module\Gateway\Ifthenpay\Forms\PayshopConfigForm;
use WHMCS\Module\Gateway\Ifthenpay\Forms\MultibancoConfigForm;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\UtilityInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ConfigGatewaysRepositoryInterface;

class IfthenpayConfigFormFactory extends Factory
{
    private $gatewayVars;
    private $gateway; 
    private $gatewayDataBuilder; 
    private $configGatewaysRepository; 
    private $utility;
    private $callback;
    private $ifthenpaySql;
    private $ifthenpayUpgrade;
    private $smarty;
    private $ifthenpayLogger;

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
        parent::__construct($ioc);
        $this->gatewayVars = $gatewayVars;
        $this->gateway = $gateway;
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->configGatewaysRepository = $configGatewaysRepository;
        $this->utility = $utility;
        $this->callback = $callback;
        $this->ifthenpaySql = $ifthenpaySql;
        $this->ifthenpayUpgrade = $ifthenpayUpgrade;
        $this->smarty = $smarty;
        $this->ifthenpayLogger = $ifthenpayLogger;
	}
    
    public function build(
    ): ConfigForm {
        switch ($this->type) {
            case Gateway::MULTIBANCO:
                return new MultibancoConfigForm(
                    $this->ioc, 
                    $this->gatewayVars,
                    $this->gateway,
                    $this->gatewayDataBuilder,
                    $this->configGatewaysRepository,
                    $this->utility,
                    $this->callback,
                    $this->ifthenpaySql,
                    $this->ifthenpayUpgrade,
                    $this->smarty,
                    $this->ifthenpayLogger
                );
            case Gateway::MBWAY:
                return new MbwayConfigForm(
                    $this->ioc,
                    $this->gatewayVars,
                    $this->gateway,
                    $this->gatewayDataBuilder,
                    $this->configGatewaysRepository,
                    $this->utility,
                    $this->callback,
                    $this->ifthenpaySql,
                    $this->ifthenpayUpgrade,
                    $this->smarty,
                    $this->ifthenpayLogger
                );
            case Gateway::PAYSHOP:
                return new PayshopConfigForm(
                    $this->ioc,
                    $this->gatewayVars,
                    $this->gateway,
                    $this->gatewayDataBuilder,
                    $this->configGatewaysRepository,
                    $this->utility,
                    $this->callback,
                    $this->ifthenpaySql,
                    $this->ifthenpayUpgrade,
                    $this->smarty,
                    $this->ifthenpayLogger
                );
            case Gateway::CCARD:
                return new CCardConfigForm(
                    $this->ioc,
                    $this->gatewayVars,
                    $this->gateway,
                    $this->gatewayDataBuilder,
                    $this->configGatewaysRepository,
                    $this->utility,
                    $this->callback,
                    $this->ifthenpaySql,
                    $this->ifthenpayUpgrade,
                    $this->smarty,
                    $this->ifthenpayLogger
                );
            default:
                throw new \Exception('Unknown Admin Config Form');
        }
    }
}
