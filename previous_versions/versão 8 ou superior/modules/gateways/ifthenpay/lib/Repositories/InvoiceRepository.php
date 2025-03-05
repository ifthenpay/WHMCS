<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Repositories;

use WHMCS\Database\Capsule;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\BaseRepository;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\InvoiceRepositoryInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class InvoiceRepository extends BaseRepository implements InvoiceRepositoryInterface 
{
    protected $table = 'tblinvoices';
    
    public function getOrderById(int $orderId): array
    {
        $order = Capsule::table($this->table)->where('id', $orderId)->first();
        return $order ? $this->convertObjectToarray($order) : [];
    }

    public function getAllUnPaidInvoices(string $paymentMethod): array
    {
        $unPaidInvoices = Capsule::table($this->table)->where(['paymentmethod' => $paymentMethod, 'status' => 'Unpaid'])->get()->toArray();
        $unPaidInvoicesConverted = [];
        if (!empty($unPaidInvoices)) {
            foreach ($unPaidInvoices as $object) {
                $unPaidInvoicesConverted[] = $this->convertObjectToarray($object);
            }
        }
        return $unPaidInvoicesConverted;
    }
}