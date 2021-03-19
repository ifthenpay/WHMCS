<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Data;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Base\Payments\CCardBase;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Order\OrderDetailInterface;

class CCardOrderDetail extends CCardBase implements OrderDetailInterface
{
    public function setSmartyVariables(): void
    {
        $this->smartyDefaultData->setPaymentMethod($this->ifthenpayGateway->getAliasPaymentMethods(
            $this->paymentMethod, 'en'));
        $this->smartyDefaultData->setOrderId($this->paymentDataFromDb['order_id']);
        $this->smartyDefaultData->setIdPedido($this->paymentDataFromDb['requestId']);
    }

    public function getOrderDetail(): OrderDetailInterface
    {
        $this->setPaymentTable('ifthenpay_ccard');
        $this->getFromDatabaseById();
        $this->setSmartyVariables();
        return $this;
    }
}
