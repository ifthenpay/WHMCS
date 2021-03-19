<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Callback;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Factory\Callback\CallbackDataFactory;

class CallbackData
{
    private $request;
    private $callbackDataFactory;

    public function __construct(CallbackDataFactory $callbackDataFactory)
    {
        $this->callbackDataFactory = $callbackDataFactory;
    }

    public function execute(): array
    {
        return $this->callbackDataFactory
        ->setType($this->request['payment'])
        ->build()
        ->getData($this->request);
    }

    /**
     * Set the value of request
     *
     * @return  self
     */ 
    public function setRequest(array $request)
    {
        $this->request = $request;

        return $this;
    }
}
