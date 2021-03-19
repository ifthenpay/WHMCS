<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Hooks;

use WHMCS\Module\Gateway\ifthenpay\Hooks\CheckoutHook;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class ClientCheckoutHook extends CheckoutHook
{    
    public function validateTemplate(): bool
    {
        return $this->vars['templatefile'] === 'viewcart';
    }
    
    public function executeStyles(): string
    {
        return $this->validateTemplate() ? '<link rel="stylesheet" href="'. $this->utility->getCssUrl() . '/checkoutPaymentOption.css">' : '';
    }

    public function execute()
    {
        if ($this->validateTemplate()) {
            $gateways = $this->vars['gateways'];
            foreach($gateways as $key => $gateway) {
                if ($gateway['sysname'] === 'multibanco') {
                    $gateways[$key]['name'] = '<img class="ifthenpayIcon multibancoIcon" src="'. $this->utility->setPaymentLogo('multibanco') .'" alt="'.$gateway['sysname'].'">';
                }
                
                if ($gateway['sysname'] === 'mbway') {
                    $gateways[$key]['name'] = '<img class="ifthenpayIcon mbwayIcon" src="'. $this->utility->setPaymentLogo('mbway') .'" alt="'.$gateway['sysname'].'">';
                }
                
                if ($gateway['sysname'] === 'payshop') {
                    $gateways[$key]['name'] = '<img class="ifthenpayIcon payshopIcon" src="'. $this->utility->setPaymentLogo('payshop') .'" alt="'.$gateway['sysname'].'">';
                }
                
                if ($gateway['sysname'] === 'ccard') {
                    $gateways[$key]['name'] = '<img class="ifthenpayIcon ccardIcon" src="'. $this->utility->setPaymentLogo('ccard') .'" alt="'.$gateway['sysname'].'">';
                }
                
            }			
            return ["gateways" => $gateways];
        } 
    }
    
}