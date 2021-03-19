<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Callback;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface CallbackProcessInterface
{
    public function process(): void;
}
