<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Data;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Base\Payments\MbwayBase;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Order\OrderDetailInterface;
use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;

class MbwayOrderDetail extends MbwayBase implements OrderDetailInterface
{
    public function setSmartyVariables(): void
    {
        $this->smartyDefaultData->setPaymentMethod($this->ifthenpayGateway->getAliasPaymentMethods(
            $this->paymentMethod, 'en'));
        $this->smartyDefaultData->setOrderId($this->paymentDataFromDb['order_id']);
        $this->smartyDefaultData->setTelemovel($this->paymentDataFromDb['telemovel']);
        $this->smartyDefaultData->setIdPedido($this->paymentDataFromDb['id_transacao']);
        $this->smartyDefaultData->setResendMbwayNotificationControllerUrl(
            Utility::getSystemUrl() . 'modules/gateways/ifthenpay/server/resendMbwayNotification.php?action=resendMbwayNotification&orderId=' . 
                $this->paymentDefaultData->orderId . '&mbwayTelemovel=' . $this->paymentDataFromDb['telemovel'] .
                '&orderTotalPay=' . $this->paymentDefaultData->totalToPay . '&filename=' . $this->params['filename']
        );
    }

    public function getOrderDetail(): OrderDetailInterface
    {
        $this->setPaymentTable('ifthenpay_mbway');
        $this->getFromDatabaseById();
        $this->setSmartyVariables();
        return $this;
    }
}
