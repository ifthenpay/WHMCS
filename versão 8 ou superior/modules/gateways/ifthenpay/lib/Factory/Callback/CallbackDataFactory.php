<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Factory\Callback;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use Illuminate\Container\Container;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Factory;
use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackDataCCard;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackDataMbway;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackDataPayshop;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackDataMultibanco;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Callback\CallbackDataInterface;

class CallbackDataFactory extends Factory
{
    private $utility;
    
    public function __construct(Container $ioc, Utility $utility)
	{
        parent::__construct($ioc);
        $this->utility = $utility;
    }

    public function build(): CallbackDataInterface
    {
        switch ($this->type) {
            case 'multibanco':
                return new CallbackDataMultibanco($this->utility);
            case 'mbway':
                return new CallbackDataMbway($this->utility);
            case 'payshop':
                return new CallbackDataPayshop($this->utility);
            case 'ccard':
                return new CallbackDataCCard($this->utility);
            default:
                throw new \Exception('Unknown Callback Data Class');
        }
    }
}
