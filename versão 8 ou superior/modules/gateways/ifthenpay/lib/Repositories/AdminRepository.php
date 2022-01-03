<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Repositories;

use WHMCS\Database\Capsule;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\BaseRepository;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\AdminRepositoryInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class AdminRepository extends BaseRepository implements AdminRepositoryInterface 
{
    protected $table = 'tbladmins';
    
    public function getAdminEmail(): string
    {
        $adminEmail = Capsule::table($this->table)->select('email')->first()->email;
        return $adminEmail ? $adminEmail : '';
    }
}