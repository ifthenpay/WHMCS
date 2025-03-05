<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Factory;

use Illuminate\Container\Container;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class Factory 
{
    protected $type;
    protected $ioc;

	public function __construct(Container $ioc)
	{
        $this->ioc = $ioc;
    }
    
     /**
     * Set the value of type
     *
     * @return  self
     */ 
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }  
}