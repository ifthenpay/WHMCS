<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface ConfigGatewaysRepositoryInterface 
{
    public function getBackofficeKey(): string;
    public function getIfthenpayUserAccount(string $paymentMethod): array;
    public function getIfthenpayUserPaymentMethods(): array;
    public function getIfthenpayUserActivatedPaymentMethod(string $paymentMethod): string;
    public function getActivatedCallback(string $paymentMethod): string;
    public function getCallbackData(string $paymentMethod): array;
}