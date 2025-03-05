<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Factory\Repository;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Factory\Factory;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\CCardRepository;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\MbWayRepository;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\PaymentRepository;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\PayshopRepository;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\MultibancoRepository;

class RepositoryFactory extends Factory
{    
    public function build(): PaymentRepository {
        switch (strtolower($this->type)) {
            case Gateway::MULTIBANCO:
                return new MultibancoRepository();
            case Gateway::MBWAY:
                return new MbWayRepository();
            case Gateway::PAYSHOP:
                return new PayshopRepository();
            case Gateway::CCARD:
                return new CCardRepository();
            default:
                throw new \Exception('Unknown Repository Class');
        }
    }
}
