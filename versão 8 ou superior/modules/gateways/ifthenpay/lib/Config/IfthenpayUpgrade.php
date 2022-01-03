<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Config;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Request\WebService;


class IfthenpayUpgrade
{
    const MODULE_VERSION = '1.1.1';
    private $webService;
    

	public function __construct(WebService $webService)
	{
        $this->webService = $webService;
	}

    public function checkModuleUpgrade(): array
    {
        $response = $this->webService->getRequest('https://ifthenpay.com/modulesUpgrade/whmcs/upgrade.json')->getResponseJson();
        if (version_compare(str_replace('v', '', $response['version']), self::MODULE_VERSION, '>')) {
            return [
                'upgrade' => true,
                'body' => $response['description'],
                'download' => $response['download']
            ];
        }
        return [
            'upgrade' => false,
        ]; 
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
