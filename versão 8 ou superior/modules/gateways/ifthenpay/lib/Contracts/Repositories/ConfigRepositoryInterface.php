<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories;

use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\BaseRepositoryInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface ConfigRepositoryInterface extends BaseRepositoryInterface
{
    public function getSystemUrl(): string;
}