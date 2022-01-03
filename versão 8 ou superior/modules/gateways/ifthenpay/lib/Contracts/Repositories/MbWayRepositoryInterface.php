<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories;

use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\BaseRepositoryInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface MbWayRepositoryInterface extends BaseRepositoryInterface
{
    public function getPaymentByIdPedido(string $idPedido): array;
    public function updatePaymentIdPedido(string $orderId, string $idPedido): void;
}