<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Utility;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

class Utility
{
    private $ifthenpayPathLib = 'modules/gateways/ifthenpay';

    public function getSystemUrl(): string
    {
        $systemUrl = Capsule::table('tblconfiguration')->where('setting', 'SystemURL')->pluck('value')[0];
        return $systemUrl ? $systemUrl : '';
    }

    public function getBackofficeKey(): string
    {
        $backofficeKey = Capsule::table('tblpaymentgateways')->where('setting', 'backofficeKey')->pluck('value')[0];
        return $backofficeKey ? $backofficeKey : '';
    }

    public function getIfthenpayUserAccount(string $paymentMethod): array
    {
        $ifthenpayUserAccount = Capsule::table('tblpaymentgateways')->where([
            'gateway' => $paymentMethod,
            'setting' => 'userAccount'])->pluck('value')[0];
        return $ifthenpayUserAccount ? unserialize($ifthenpayUserAccount) : []; 
    }

    public function getIfthenpayUserPaymentMethods(): array
    {
        $ifthenpayUserPaymentMethods = Capsule::table('tblpaymentgateways')->where('setting', 'ifthenpayUserPaymentMethods')->pluck('value')[0];
        return $ifthenpayUserPaymentMethods ? unserialize($ifthenpayUserPaymentMethods) : []; 
    }

    public function getIfthenpayUserActivatedPaymentMethod(string $paymentMethod): string
    {
        $userActivatePaymentMethod = Capsule::table('tblpaymentgateways')->where('setting', $paymentMethod)->pluck('value')[0]; 
        return $userActivatePaymentMethod ? $userActivatePaymentMethod : '';
    }

    public function getActivatedCallback(string $paymentMethod): string
    {
        $activatedCallback = Capsule::table('tblpaymentgateways')->where([
            'gateway' => $paymentMethod,
            'setting' => 'activatedCallback'
        ])->pluck('value')[0]; 
        return $activatedCallback ? $activatedCallback : '';
    }

    public function getCallbackData(string $paymentMethod): array
    {
        $callbackData = Capsule::table('tblpaymentgateways')
            ->where('gateway', $paymentMethod)->get()->filter(function ($value, $key) {
                if ($value->setting === 'chaveAntiPhishing' || $value->setting === 'urlCallback') {
                    return $value;
                }
            })->pluck('value');
        return $callbackData ? $callbackData->toArray() : [];
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

    public function getOrderById(string $orderId): array
    {
        $order = Capsule::table('tblorders')
            ->where('id', $orderId)->first();
        return $order ? $this->convertObjectToarray($order) : [];
    }

    public function saveIfthenpayPayment(string $databaseTable, string $paymentId): void
    {
        Capsule::table($databaseTable)->where('id', $paymentId)->update(['status' => 'paid']);
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
