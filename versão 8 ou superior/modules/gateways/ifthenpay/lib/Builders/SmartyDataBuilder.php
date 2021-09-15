<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Builders;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Builders\DataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Builders\SmartyDataBuilderInterface;

class SmartyDataBuilder extends DataBuilder implements SmartyDataBuilderInterface
{

    public function setPaymentLogo(string $value): SmartyDataBuilderInterface
    {
        $this->data->paymentLogo = $value;
        return $this;
    }

    public function setOrderId(string $value): SmartyDataBuilderInterface
    {
        $this->data->orderId = $value;
        return $this;
    }

    public function setStatus(string $value): SmartyDataBuilderInterface
    {
        $this->data->status = $value;
        return $this;
    }

    public function setResendMbwayNotificationControllerUrl(string $value): SmartyDataBuilderInterface
    {
        $this->data->resendMbwayNotificationControllerUrl = $value;
        return $this;
    }

    public function setMbwayCountdownShow(string $value): SmartyDataBuilderInterface
    {
        $this->data->mbwayCountdownShow = $value;
        return $this;
    }

    public function setSpinnerUrl(string $value): SmartyDataBuilderInterface
    {
        $this->data->sppinerUrl = $value;
        return $this;
    }

    public function setMbwayOrderConfirmUrl(string $value): SmartyDataBuilderInterface
    {
        $this->data->mbwayOrderConfirmUrl = $value;
        return $this;
    }

    public function setErrorSvgUrl(string $value): SmartyDataBuilderInterface
    {
        $this->data->errorSvgUrl = $value;
        return $this;
    }

    public function setMessage(string $value): SmartyDataBuilderInterface
    {
        $this->data->message = $value;
        return $this;
    }

    public function setErrorPaymentProcessingLang(string $value): SmartyDataBuilderInterface
    {
        $this->data->errorPaymentProcessing = $value;
        return $this;
    }
    public function setErrorPaymentProcessingDescriptionLang(string $value): SmartyDataBuilderInterface
    {
        $this->data->errorPaymentProcessingDescription = $value;
        return $this;
    }
    public function setEntityMultibancoLang(string $value): SmartyDataBuilderInterface
    {
        $this->data->entityMultibanco = $value;
        return $this;
    }
    public function setIfthenpayReferenceLang(string $value): SmartyDataBuilderInterface
    {
        $this->data->ifthenpayReference = $value;
        return $this;
    }
    public function setIfthenpayTotalToPayLang(string $value): SmartyDataBuilderInterface
    {
        $this->data->ifthenpayTotalToPay = $value;
        return $this;
    }
    public function setPhoneMbwayLang(string $value): SmartyDataBuilderInterface
    {
        $this->data->phoneMbway = $value;
        return $this;
    }
    public function setOrderTitleLang(string $value): SmartyDataBuilderInterface
    {
        $this->data->orderTitle = $value;
        return $this;
    }
    public function setNotReceiveMbwayNotificationLang(string $value): SmartyDataBuilderInterface
    {
        $this->data->notReceiveMbwayNotification = $value;
        return $this;
    }
    public function setResendMbwayNotificationLang(string $value): SmartyDataBuilderInterface
    {
        $this->data->resendMbwayNotification = $value;
        return $this;
    }
    public function setPayshopDeadlineLang(string $value): SmartyDataBuilderInterface
    {
        $this->data->payshopDeadline = $value;
        return $this;
    }
    public function setConfirmMbwayPaymentTitleLang(string $value): SmartyDataBuilderInterface
    {
        $this->data->confirmMbwayPaymentTitle = $value;
        return $this;
    }
    public function setMbwayExpireTitleLang(string $value): SmartyDataBuilderInterface
    {
        $this->data->mbwayExpireTitle = $value;
        return $this;
    }
    public function setMbwayOrderPaidLang(string $value): SmartyDataBuilderInterface
    {
        $this->data->mbwayOrderPaid = $value;
        return $this;
    }
    public function setMbwayPaymentConfirmedLang(string $value): SmartyDataBuilderInterface
    {
        $this->data->mbwayPaymentConfirmed = $value;
        return $this;
    }
    public function setIfthenpayPayBy(string $value): SmartyDataBuilderInterface
    {
        $this->data->ifthenpayPayBy = $value;
        return $this;
    }
}