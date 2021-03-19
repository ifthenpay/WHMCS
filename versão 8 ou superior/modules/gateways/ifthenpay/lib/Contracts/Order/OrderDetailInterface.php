<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Order;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface OrderDetailInterface
{
    public function setSmartyVariables(): void;
    public function getOrderDetail(): OrderDetailInterface;
}
