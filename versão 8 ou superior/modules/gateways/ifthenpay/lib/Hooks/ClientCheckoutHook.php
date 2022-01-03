<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Hooks;

use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\ifthenpay\Hooks\CheckoutHook;
use WHMCS\Module\GatewaySetting;

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
        return $this->validateTemplate() ? '<link rel="stylesheet" href="'. $this->utility->getCssUrl() . '/' . $this->mix->create('checkoutPaymentOption.css') . '">' : '';
    }

    private function showPaymentIcon(string $paymentMethod): bool
    {
        return GatewaySetting::getForGateway($paymentMethod)['showPaymentLogo'] === 'on';
    }

    public function execute()
    {
        if ($this->validateTemplate()) {
            $gateways = $this->vars['gateways'];
            $currencyCode = $this->vars['total']->getCurrency()->toArray()['code'];
            foreach($gateways as $key => $gateway) {
                if ($gateway['sysname'] === Gateway::MULTIBANCO) {
                    if ($currencyCode === 'EUR') {
                        if ($this->showPaymentIcon($gateway['sysname'])) {
                            $gateways[$key]['name'] = '<img class="ifthenpayIcon multibancoIcon" src="'. $this->utility->setPaymentLogo(Gateway::MULTIBANCO) . '" alt="'. $gateway['sysname'] .'">';
                        }
                    } else {
                        unset($gateways[$key]);
                    }
                }
                if ($gateway['sysname'] === Gateway::MBWAY) {
                    if ($currencyCode === 'EUR') {
                        if ($this->showPaymentIcon($gateway['sysname'])) {
                            $gateways[$key]['name'] = '<img class="ifthenpayIcon mbwayIcon" src="'. $this->utility->setPaymentLogo(Gateway::MBWAY) . '" alt="'. $gateway['sysname'] .'">';
                        }
                    } else {
                        unset($gateways[$key]);
                    }  
                } 
                if ($gateway['sysname'] === Gateway::PAYSHOP) {
                    if ($currencyCode === 'EUR') {
                        if ($this->showPaymentIcon($gateway['sysname'])) {
                            $gateways[$key]['name'] = '<img class="ifthenpayIcon payshopIcon" src="'. $this->utility->setPaymentLogo(Gateway::PAYSHOP) . '" alt="' . $gateway['sysname'] . '">';
                        }
                    } else {
                        unset($gateways[$key]);
                    }
                } 
                if ($gateway['sysname'] === Gateway::CCARD && $this->showPaymentIcon($gateway['sysname'])) {
                    $gateways[$key]['name'] = '<img class="ifthenpayIcon ccardIcon" src="'. $this->utility->setPaymentLogo(Gateway::CCARD) . '" alt="' . $gateway['sysname'] . '">';
                }
            }			
            return ["gateways" => $gateways];
        } 
    }
    
}