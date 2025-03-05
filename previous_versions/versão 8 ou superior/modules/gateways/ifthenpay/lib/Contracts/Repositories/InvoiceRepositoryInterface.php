<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories;

use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\OrderRepositoryInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface InvoiceRepositoryInterface extends OrderRepositoryInterface 
{
    public function getAllUnPaidInvoices(string $paymentMethod): array;
}