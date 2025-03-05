<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Repositories;

use WHMCS\Module\Gateway\Ifthenpay\Repositories\PaymentRepository;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\CCardRepositoryInterface;
use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class CCardRepository extends PaymentRepository implements CCardRepositoryInterface 
{   
    protected $table = 'ifthenpay_ccard';

    public function getPaymentByRequestId(string $requestId): array
    {
        return $this->convertObjectToarray(Capsule::table($this->table)->where('requestId', $requestId)->first());
    }
}