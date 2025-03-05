<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Callback;

use WHMCS\Module\Gateway\Ifthenpay\Factory\Repository\RepositoryFactory;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}


class CallbackPayment
{
    protected $repositoryFactory;

    public function __construct(RepositoryFactory $repositoryFactory)
    {
        $this->repositoryFactory = $repositoryFactory;
    }
}
