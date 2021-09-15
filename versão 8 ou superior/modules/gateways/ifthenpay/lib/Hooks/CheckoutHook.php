<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Hooks;

use WHMCS\Module\Gateway\ifthenpay\Utility\Mix;
use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

abstract class CheckoutHook 
{
    protected $vars;
    protected $utility;
    protected $mix;

	public function __construct(Utility $utility, Mix $mix)
	{
        $this->utility = $utility;
        $this->mix = $mix;
    }
    
    abstract public function validateTemplate(): bool;
    
    abstract public function executeStyles(): string;

    abstract public function execute();
    

    /**
     * Set the value of vars
     *
     * @return  self
     */ 
    public function setVars($vars)
    {
        $this->vars = $vars;

        return $this;
    }
}