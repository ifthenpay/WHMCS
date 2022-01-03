<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories;

use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\BaseRepositoryInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface CurrencieRepositoryInterface extends BaseRepositoryInterface
{
    public function getCurrencieByCode(string $currencyCode): array;
}