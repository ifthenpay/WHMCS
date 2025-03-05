<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility;


if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface TokenInterface
{
    public function encrypt(string $input): string;
    public function decrypt(string $input): string;
    public function saveUserToken(string $paymentMethod, string $action): string;
}

