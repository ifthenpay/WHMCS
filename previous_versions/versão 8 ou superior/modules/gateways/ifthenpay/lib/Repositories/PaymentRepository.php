<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Repositories;

use WHMCS\Module\Gateway\Ifthenpay\Repositories\BaseRepository;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\PaymentRepositoryInterface;
use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface 
{    
    public function getPaymentByOrderId(string $orderId): array
    {
        return $this->convertObjectToarray(Capsule::table($this->table)->where('order_id', $orderId)->first());
    }

    public function updatePaymentByOrderId(array $data, string $orderId): void
    {
        Capsule::table($this->table)->where('order_id', $orderId)->update($data);
    }
}