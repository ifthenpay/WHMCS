<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface MbWayRepositoryInterface 
{
    public function getPaymentByIdPedido(string $idPedido): array;
    public function updatePaymentIdPedido(string $orderId, string $idPedido): void;
}