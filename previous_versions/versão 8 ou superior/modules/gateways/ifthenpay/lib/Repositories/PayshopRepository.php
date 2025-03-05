<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Repositories;

use WHMCS\Module\Gateway\Ifthenpay\Repositories\PaymentRepository;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\PayshopRepositoryInterface;
use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class PayshopRepository extends PaymentRepository implements PayshopRepositoryInterface 
{   
    protected $table = 'ifthenpay_payshop';

    public function getPaymentByIdTransacao(string $idTransacao): array
    {
        return $this->convertObjectToarray(Capsule::table($this->table)->where('id_transacao', $idTransacao)->first());
    }
}