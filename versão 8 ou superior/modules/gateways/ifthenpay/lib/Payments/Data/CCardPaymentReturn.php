<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Data;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Base\Payments\CCardBase;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\PaymentReturnInterface;

class CCardPaymentReturn extends CCardBase implements PaymentReturnInterface
{
    public function getPaymentReturn(): PaymentReturnInterface
    {
        $this->setPaymentTable('ifthenpay_ccard');
        $this->setGatewayBuilderData();
        $this->paymentGatewayResultData = $this->ifthenpayGateway->execute(
            $this->paymentDefaultData->paymentMethod,
            $this->gatewayBuilder,
            strval($this->paymentDefaultData->orderId),
            strval($this->paymentDefaultData->totalToPay)
        )->getData();
        $this->saveToDatabase();
        return $this;
    }
}
