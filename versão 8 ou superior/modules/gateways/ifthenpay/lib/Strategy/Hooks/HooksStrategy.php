<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Strategy\Hooks;

use WHMCS\Module\Gateway\ifthenpay\Hooks\CheckoutHook;
use WHMCS\Module\Gateway\ifthenpay\Hooks\EmailPreSendHook;
use WHMCS\Module\Gateway\ifthenpay\Hooks\ClientCheckoutHook;
use WHMCS\Module\Gateway\ifthenpay\Hooks\ClientCheckoutConfirmHook;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class HooksStrategy
{
    private $clientCheckoutHook;
    private $clientCheckoutConfirmHook;
    private $emailPreSendHook;

	public function __construct(ClientCheckoutHook $clientCheckoutHook, ClientCheckoutConfirmHook $clientCheckoutConfirmHook, EmailPreSendHook $emailPreSendHook)
	{
        $this->clientCheckoutHook = $clientCheckoutHook;
        $this->clientCheckoutConfirmHook = $clientCheckoutConfirmHook;
        $this->emailPreSendHook = $emailPreSendHook;
	}
    
    public function execute(string $type, array $vars): CheckoutHook
    {
        switch ($type) {
            case 'clientCheckoutHook':
                return $this->clientCheckoutHook->setVars($vars);
                break;
            case 'clientCheckoutConfirmHook':
                return $this->clientCheckoutConfirmHook->setVars($vars)->setPaymentMethod($vars['paymentmethod']);
                break;
            case 'emailPreSendHook':
                return $this->emailPreSendHook->setVars($vars)->setPaymentMethod($vars['invoice_payment_method']);
            default:
                throw new \Exception('Unknown Hook Class');
        }
    }
}