<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Utility;

use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\TokenExtraInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class TokenExtra implements TokenExtraInterface {

    public function encript(string $input, string $secret): string 
    {
        return hash_hmac('sha256', $input, $secret);
    }
}