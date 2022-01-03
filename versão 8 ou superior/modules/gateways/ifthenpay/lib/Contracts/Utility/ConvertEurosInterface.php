<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility;


if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface ConvertEurosInterface
{
    public function execute(string $currencyCode, $totalToPay);
}

