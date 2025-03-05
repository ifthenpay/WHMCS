<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Data;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Base\Payments\MbwayBase;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\PaymentReturnInterface;

class MbwayPaymentReturn extends MbwayBase implements PaymentReturnInterface
{
    public function getPaymentReturn(): PaymentReturnInterface
    {
        $this->setPaymentTable('ifthenpay_mbway');
        $this->setGatewayBuilderData();
        $this->paymentGatewayResultData = $this->ifthenpayGateway->execute(
            $this->paymentDefaultData->paymentMethod,
            $this->gatewayBuilder,
            strval($this->paymentDefaultData->orderId),
            strval($this->paymentDefaultData->totalToPay)
        )->getData();
        $this->logPaymentGatewayResultData();
        $this->persistToDatabase();
        if (isset($_COOKIE['mbwayCountdownShow'])) {
            $_COOKIE['mbwayCountdownShow'] = 'true';
        } else {
            setcookie('mbwayCountdownShow', 'true');
            $_COOKIE['mbwayCountdownShow'] = 'true';
        }
        $this->ifthenpayLogger->info('mbwayCountdownShow cookie set with success', ['cookies' => $_COOKIE]);
        return $this;
    }
}
