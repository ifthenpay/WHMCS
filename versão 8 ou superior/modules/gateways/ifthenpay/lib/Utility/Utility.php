<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Utility;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ConfigRepositoryInterface;

class Utility
{
    private $ifthenpayPathLib = 'modules/gateways/ifthenpay';
    private $configRepository;

	public function __construct(ConfigRepositoryInterface $configRepository)
	{
        $this->configRepository = $configRepository;
	}
    
    public function getSystemUrl(): string
    {
        return $this->configRepository->getSystemUrl();
    }

    public function getImgUrl(): string
    {
        return $this->getSystemUrl() . $this->ifthenpayPathLib . '/img';
    }

    public function getJsUrl(): string
    {
        return $this->getSystemUrl() . $this->ifthenpayPathLib . '/js';
    }

    public function getCssUrl(): string
    {
        return $this->getSystemUrl() . $this->ifthenpayPathLib . '/css';
    }

    public function getSvgUrl(): string
    {
        return $this->getSystemUrl() . $this->ifthenpayPathLib . '/svg';
    }

    public function getTemplatesUrl(): string
    {
        return $this->getSystemUrl() . $this->ifthenpayPathLib . '/templates';
    }  

    public function getCallbackControllerUrl(string $paymentMethod): string
    {
        return $this->getSystemUrl() . 'modules/gateways/callback/' . $paymentMethod . '.php';  
    }

    public function setPaymentLogo(string $paymentMethod): string
    {
        return $this->getSvgUrl() . '/' . $paymentMethod . '.svg';
    }

    public function convertObjectToarray(object  $object = null): array
    {
        return !is_null($object) ? json_decode(
            json_encode($object), true) : [];
    }

}
