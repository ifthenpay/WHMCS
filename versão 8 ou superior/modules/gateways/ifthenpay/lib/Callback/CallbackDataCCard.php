<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Callback;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackPayment;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Callback\CallbackDataInterface;

class CallbackDataCCard extends CallbackPayment implements CallbackDataInterface
{
    public function getData(array $request): array
    {        
        return $this->repositoryFactory->setType('ccard')->build()->getPaymentByRequestId($request['requestId']);
    }
}
