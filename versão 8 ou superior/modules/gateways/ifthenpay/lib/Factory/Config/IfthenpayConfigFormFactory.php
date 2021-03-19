<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Factory\Config;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use Smarty;
use Illuminate\Container\Container;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Factory;
use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;
use WHMCS\Module\Gateway\Ifthenpay\Forms\ConfigForm;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Callback\Callback;
use WHMCS\Module\Gateway\Ifthenpay\Config\IfthenpaySql;
use WHMCS\Module\Gateway\ifthenpay\Forms\CCardConfigForm;
use WHMCS\Module\Gateway\Ifthenpay\Forms\MbwayConfigForm;
use WHMCS\Module\Gateway\Ifthenpay\Config\IfthenpayUpgrade;
use WHMCS\Module\Gateway\Ifthenpay\Forms\PayshopConfigForm;
use WHMCS\Module\Gateway\Ifthenpay\Forms\MultibancoConfigForm;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;

class IfthenpayConfigFormFactory extends Factory
{
    private $gatewayVars;
    private $gateway; 
    private $gatewayDataBuilder; 
    private $utility; 
    private $callback;
    private $ifthenpaySql;
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
        parent::__construct($ioc);
        $this->gatewayVars = $gatewayVars;
        $this->gateway = $gateway;
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->utility = $utility;
        $this->callback = $callback;
        $this->ifthenpaySql = $ifthenpaySql;
        $this->ifthenpayUpgrade = $ifthenpayUpgrade;
        $this->smarty = $smarty;
	}
    
    public function build(
    ): ConfigForm {
        switch ($this->type) {
            case 'multibanco':
                return new MultibancoConfigForm(
                    $this->ioc, 
                    $this->gatewayVars,
                    $this->gateway,
                    $this->gatewayDataBuilder,
                    $this->utility,
                    $this->callback,
                    $this->ifthenpaySql,
                    $this->ifthenpayUpgrade,
                    $this->smarty
                );
            case 'mbway':
                return new MbwayConfigForm(
                    $this->ioc,
                    $this->gatewayVars,
                    $this->gateway,
                    $this->gatewayDataBuilder,
                    $this->utility,
                    $this->callback,
                    $this->ifthenpaySql,
                    $this->ifthenpayUpgrade,
                    $this->smarty
                );
            case 'payshop':
                return new PayshopConfigForm(
                    $this->ioc,
                    $this->gatewayVars,
                    $this->gateway,
                    $this->gatewayDataBuilder,
                    $this->utility,
                    $this->callback,
                    $this->ifthenpaySql,
                    $this->ifthenpayUpgrade,
                    $this->smarty
                );
            case 'ccard':
                return new CCardConfigForm(
                    $this->ioc,
                    $this->gatewayVars,
                    $this->gateway,
                    $this->gatewayDataBuilder,
                    $this->utility,
                    $this->callback,
                    $this->ifthenpaySql,
                    $this->ifthenpayUpgrade,
                    $this->smarty
                );
            default:
                throw new \Exception('Unknown Admin Config Form');
        }
    }
}
