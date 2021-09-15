<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Repositories;

use WHMCS\Database\Capsule;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\BaseRepository;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\OrderRepositoryInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class OrderRepository extends BaseRepository implements OrderRepositoryInterface 
{
    protected $table = 'tblorders';
    
    public function getOrderById(int $orderId): array
    {
        $order = Capsule::table($this->table)->where('invoiceid', $orderId)->first();
        return $order ? $this->convertObjectToarray($order) : [];
    }

    public function getAllUnPaidInvoices(string $paymentMethod): array
    {
        return [];
    }
}