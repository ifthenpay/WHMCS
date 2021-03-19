<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Callback;

use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}


class CallbackPayment
{
    protected $utility;

    public function __construct(Utility $utility)
    {
        $this->utility = $utility;
    }
}
