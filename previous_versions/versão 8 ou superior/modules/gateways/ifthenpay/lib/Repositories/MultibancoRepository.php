<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Repositories;

use WHMCS\Module\Gateway\Ifthenpay\Repositories\PaymentRepository;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\MultibancoRepositoryInterface;
use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class MultibancoRepository extends PaymentRepository implements MultibancoRepositoryInterface 
{   
    protected $table = 'ifthenpay_multibanco';

    public function getPaymentByReferencia(string $referencia): array
    {
        return $this->convertObjectToarray(Capsule::table($this->table)->where('referencia', $referencia)->orderBy('id', 'DESC')->first());
    }
}