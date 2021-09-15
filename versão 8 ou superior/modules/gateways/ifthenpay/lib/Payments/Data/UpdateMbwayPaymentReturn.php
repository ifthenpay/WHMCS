<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Data;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\MbwayPaymentReturn;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\PaymentReturnInterface;

class UpdateMbwayPaymentReturn extends MbwayPaymentReturn implements PaymentReturnInterface
{
    public function persistToDatabase(): void
    {
        $this->updateToDatabase();
    }
}
