<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility;


if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface StatusInterface
{
    public function getTokenStatus(string $token): string;
    public function getStatusSucess(): string;
    public function getStatusError(): string;
    public function getStatusCancel(): string;
}