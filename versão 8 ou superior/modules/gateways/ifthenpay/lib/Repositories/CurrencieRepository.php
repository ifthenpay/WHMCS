<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Repositories;

use WHMCS\Database\Capsule;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\BaseRepository;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\CurrencieRepositoryInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class CurrencieRepository extends BaseRepository implements CurrencieRepositoryInterface 
{
    protected $table = 'tblcurrencies';
    
    public function getCurrencieByCode(string $currencyCode): array
    {
        $currencie = $this->convertObjectToArray(Capsule::table($this->table)->where("code", "=", $currencyCode)->first());
        return !empty($currencie) ? $currencie : [];
    }
    
}