<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Repositories;

use WHMCS\Database\Capsule;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\BaseRepository;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ConfigRepositoryInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class ConfigRepository extends BaseRepository implements ConfigRepositoryInterface 
{
    protected $table = 'tblconfiguration';
    
    public function getSystemUrl(): string
    {
        $systemUrl = Capsule::table($this->table)->where('setting', 'SystemURL')->pluck('value')[0];
        return $systemUrl ? $systemUrl : '';
    }
}