<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Hooks;

use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

abstract class CheckoutHook 
{
    protected $vars;
    protected $utility;

	public function __construct(Utility $utility)
	{
        $this->utility = $utility;
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