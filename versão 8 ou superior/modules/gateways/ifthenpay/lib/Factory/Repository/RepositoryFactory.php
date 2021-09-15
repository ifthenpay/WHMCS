<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Factory\Repository;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Factory\Factory;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\CCardRepository;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\MbWayRepository;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\PaymentRepository;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\PayshopRepository;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\MultibancoRepository;

class RepositoryFactory extends Factory
{    
    public function build(): PaymentRepository {
        switch (strtolower($this->type)) {
            case 'multibanco':
                return new MultibancoRepository();
            case 'mbway':
                return new MbWayRepository();
            case 'payshop':
                return new PayshopRepository();
            case 'ccard':
                return new CCardRepository();
            default:
                throw new \Exception('Unknown Repository Class');
        }
    }
}
