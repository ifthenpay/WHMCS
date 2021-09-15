<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Repositories;

use WHMCS\Module\Gateway\Ifthenpay\Repositories\BaseRepository;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ConfigGatewaysRepositoryInterface;
use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class ConfigGatewaysRepository extends BaseRepository implements ConfigGatewaysRepositoryInterface 
{
    protected $table = 'tblpaymentgateways';
    
    public function getBackofficeKey(): string
    {
        $backofficeKey = Capsule::table($this->table)->where('setting', 'backofficeKey')->pluck('value')[0];
        return $backofficeKey ? $backofficeKey : '';
    }

    public function getIfthenpayUserAccount(string $paymentMethod): array
    {
        $ifthenpayUserAccount = Capsule::table($this->table)->where([
            'gateway' => $paymentMethod,
            'setting' => 'userAccount'])->pluck('value')[0];
        return $ifthenpayUserAccount ? unserialize($ifthenpayUserAccount) : []; 
    }

    public function getIfthenpayUserPaymentMethods(): array
    {
        $ifthenpayUserPaymentMethods = Capsule::table($this->table)->where('setting', 'ifthenpayUserPaymentMethods')->pluck('value')[0];
        return $ifthenpayUserPaymentMethods ? unserialize($ifthenpayUserPaymentMethods) : []; 
    }

    public function getIfthenpayUserActivatedPaymentMethod(string $paymentMethod): string
    {
        $userActivatePaymentMethod = Capsule::table($this->table)->where('setting', $paymentMethod)->pluck('value')[0]; 
        return $userActivatePaymentMethod ? $userActivatePaymentMethod : '';
    }

    public function getActivatedCallback(string $paymentMethod): string
    {
        $activatedCallback = Capsule::table($this->table)->where([
            'gateway' => $paymentMethod,
            'setting' => 'activatedCallback'
        ])->pluck('value')[0]; 
        return $activatedCallback ? $activatedCallback : '';
    }

    public function getCallbackData(string $paymentMethod): array
    {
        $callbackData = Capsule::table($this->table)->where('gateway', $paymentMethod)->get()->filter(function ($value, $key) {
                if ($value->setting === 'chaveAntiPhishing' || $value->setting === 'urlCallback') {
                    return $value;
                }
            })->pluck('value');
        return $callbackData ? $callbackData->toArray() : [];
    }
}