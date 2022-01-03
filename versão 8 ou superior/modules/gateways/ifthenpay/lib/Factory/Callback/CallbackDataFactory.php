<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Factory\Callback;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use Illuminate\Container\Container;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Factory;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackDataCCard;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackDataMbway;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackDataPayshop;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackDataMultibanco;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Repository\RepositoryFactory;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Callback\CallbackDataInterface;

class CallbackDataFactory extends Factory
{
    private $repositoryFactory;
    
    public function __construct(Container $ioc, RepositoryFactory $repositoryFactory)
	{
        parent::__construct($ioc);
        $this->repositoryFactory = $repositoryFactory;
    }

    public function build(): CallbackDataInterface
    {
        switch ($this->type) {
            case Gateway::MULTIBANCO:
                return new CallbackDataMultibanco($this->repositoryFactory);
            case Gateway::MBWAY:
                return new CallbackDataMbway($this->repositoryFactory);
            case Gateway::PAYSHOP:
                return new CallbackDataPayshop($this->repositoryFactory);
            case Gateway::CCARD:
                return new CallbackDataCCard($this->repositoryFactory);
            default:
                throw new \Exception('Unknown Callback Data Class');
        }
    }
}
