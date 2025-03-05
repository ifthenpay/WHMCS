<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories;

use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\BaseRepositoryInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface ConfigGatewaysRepositoryInterface extends BaseRepositoryInterface
{
    public function getBackofficeKey(): string;
    public function getIfthenpayUserAccount(string $paymentMethod): array;
    public function getIfthenpayUserActivatedPaymentMethod(string $paymentMethod): string;
    public function getActivatedCallback(string $paymentMethod): string;
    public function getCallbackData(string $paymentMethod): array;
    public function getUserToken(string $paymentMethod, string $action): string;
}