<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Repositories;

use WHMCS\Module\Gateway\Ifthenpay\Repositories\BaseRepository;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ClientRepositoryInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class ClientRepository extends BaseRepository implements ClientRepositoryInterface 
{
    protected $table = 'tblclients';    
}