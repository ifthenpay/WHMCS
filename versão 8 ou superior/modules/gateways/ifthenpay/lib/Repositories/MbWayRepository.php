<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Repositories;

use WHMCS\Module\Gateway\Ifthenpay\Repositories\PaymentRepository;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\MbWayRepositoryInterface;
use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class MbWayRepository extends PaymentRepository implements MbWayRepositoryInterface 
{   
    protected $table = 'ifthenpay_mbway';

    public function getPaymentByIdPedido(string $idPedido): array
    {
        return $this->convertObjectToarray(Capsule::table($this->table)->where('id_transacao', $idPedido)->first());
    }

    public function updatePaymentIdPedido(string $orderId, string $idPedido): void
    {
        Capsule::table($this->table)->where('order_id', $orderId)->update(['id_transacao' => $idPedido]);
    }
}