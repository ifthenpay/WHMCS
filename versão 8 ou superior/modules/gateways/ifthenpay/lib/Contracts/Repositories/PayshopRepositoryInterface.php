<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface PayshopRepositoryInterface 
{
    public function getPaymentByIdTransacao(string $idTransacao): array;
}