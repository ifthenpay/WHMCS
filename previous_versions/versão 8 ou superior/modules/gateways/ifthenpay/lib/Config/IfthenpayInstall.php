<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Config;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Config\InstallerInterface;

abstract class IfthenpayInstall implements InstallerInterface
{
    protected $paymentMethod;
    protected $ifthenpayLogger;

    public function __construct(IfthenpayLogger $ifthenpayLogger)
	{
        $this->ifthenpayLogger = $ifthenpayLogger;
	}
    

    /**
     * Set the value of paymentMethod
     *
     * @return  self
     */ 
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
        $this->ifthenpayLogger = $this->ifthenpayLogger->setChannel($this->ifthenpayLogger->getChannelBackofficeConst($this->paymentMethod))->getLogger();

        return $this;
    }

    abstract public function install(): void;
    abstract public function uninstall(): void;
}
