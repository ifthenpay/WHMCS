<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Data;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Base\Payments\PayshopBase;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Order\OrderDetailInterface;

class PayshopOrderDetail extends PayshopBase implements OrderDetailInterface
{
    public function setSmartyVariables(): void
    {
        $this->smartyDefaultData->setPaymentMethod($this->ifthenpayGateway->getAliasPaymentMethods(
            $this->paymentMethod, 'en'));
        $this->smartyDefaultData->setReferencia($this->paymentDataFromDb['referencia']);
        $this->smartyDefaultData->setValidade(!empty($this->paymentDataFromDb) ? 
        (new \DateTime($this->paymentDataFromDb['validade']))->format('d-m-Y') : '');
        $this->smartyDefaultData->setIdPedido($this->paymentDataFromDb['id_transacao']);
    }

    public function getOrderDetail(): OrderDetailInterface
    {
        $this->setPaymentTable('ifthenpay_payshop');
        $this->getFromDatabaseById();
        $this->setSmartyVariables();
        return $this;
    }
}
