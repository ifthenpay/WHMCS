<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Strategy\Callback;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackOffline;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackOnline;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Callback\CallbackProcessInterface;


class CallbackStrategy
{
    private $callbackOffline;
    private $callbackOnline;

	public function __construct(CallbackOffline $callbackOffline, CallbackOnline $callbackOnline)
	{
        $this->callbackOffline = $callbackOffline;
        $this->callbackOnline = $callbackOnline;
	}
    
    public function execute(string $paymentType, string $paymentMethod): CallbackProcessInterface
    {
        if ($paymentType === 'offline') {
            return $this->callbackOffline
                ->setPaymentMethod($paymentMethod)
                ->setRequest($_GET)
                ->process();
        } else {
            return $this->callbackOnline
            ->setPaymentMethod($paymentMethod)
            ->setRequest($_GET)
            ->process();
        }
    }
}
