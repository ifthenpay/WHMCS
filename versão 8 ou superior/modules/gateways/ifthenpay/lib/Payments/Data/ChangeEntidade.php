<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Data;

use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ConfigGatewaysRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class ChangeEntidade
{
    private $configGatewaysRepository;
    private $gateway;
    private $request;
    private $ifthenpayLogger;
    
	public function __construct(
        ConfigGatewaysRepositoryInterface $configGatewaysRepository, 
        Gateway $gateway,
        IfthenpayLogger $ifthenpayLogger
    )
	{
        $this->configGatewaysRepository = $configGatewaysRepository;
        $this->gateway = $gateway;
        $this->ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_BACKOFFICE_CONFIG_MULTIBANCO)->getLogger();
	}
    
    public function execute(): string
    {
        $ifthenpayUserAccount = $this->configGatewaysRepository->getIfthenpayUserAccount('multibanco');
        $this->ifthenpayLogger->info('ifthenpay user account retrieved with success', ['ifthenpayUserAccount' => $ifthenpayUserAccount, 'className' => get_class($this)]);
        $this->gateway->setAccount($ifthenpayUserAccount);
        return json_encode($this->gateway->getSubEntidadeInEntidade($this->request['entidade']));
    }

    /**
     * Set the value of request
     *
     * @return  self
     */ 
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }
}
