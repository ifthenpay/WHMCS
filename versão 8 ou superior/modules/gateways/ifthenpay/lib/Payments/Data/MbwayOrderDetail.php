<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Data;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Base\Payments\MbwayBase;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Order\OrderDetailInterface;
use WHMCS\Module\GatewaySetting;

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
            $this->utility->getSystemUrl() . 'modules/gateways/ifthenpay/server/resendMbwayNotification.php?action=resendMbwayNotification&orderId=' . 
                $this->paymentDefaultData->orderId . '&mbwayTelemovel=' . $this->paymentDataFromDb['telemovel'] .
                '&orderTotalPay=' . $this->paymentDefaultData->totalToPay . '&filename=' . $this->params['filename'] . '&sk=' . 
                    $this->tokenExtra->encript($this->paymentDefaultData->orderId . 'resendMbwayNotification', GatewaySetting::getForGateway('mbway')['mbwayKey'])
        );
        $this->smartyDefaultData->setMbwayCountdownShow(isset($_COOKIE['mbwayCountdownShow']) ? $_COOKIE['mbwayCountdownShow'] : 'false');
        $this->smartyDefaultData->setPhoneMbwayLang(\Lang::trans('phoneMbway'));
        $this->smartyDefaultData->setOrderTitleLang(\Lang::trans('orderTitle'));
        $this->smartyDefaultData->setNotReceiveMbwayNotificationLang(\Lang::trans('notReceiveMbwayNotification'));
        $this->smartyDefaultData->setResendMbwayNotificationLang(\Lang::trans('resendMbwayNotification'));
        $this->smartyDefaultData->setConfirmMbwayPaymentTitleLang(\Lang::trans('confirmMbwayPaymentTitle'));
        $this->smartyDefaultData->setMbwayExpireTitleLang(\Lang::trans('mbwayExpireTitle'));
        $this->smartyDefaultData->setMbwayOrderPaidLang(\Lang::trans('mbwayOrderPaid'));
        $this->smartyDefaultData->setMbwayPaymentConfirmedLang(\Lang::trans('mbwayPaymentConfirmed'));
        $this->logSmartyBuilderData();
    }

    public function getOrderDetail(): OrderDetailInterface
    {
        $this->setPaymentTable('ifthenpay_mbway');
        $this->getFromDatabaseById();
        $this->setSmartyVariables();
        return $this;
    }
}
