<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Builders;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Contracts\Builders\DataBuilderInterface;

interface SmartyDataBuilderInterface extends DataBuilderInterface
{
    public function setPaymentLogo(string $value): SmartyDataBuilderInterface;
    public function setOrderId(string $value): SmartyDataBuilderInterface;
    public function setStatus(string $value): SmartyDataBuilderInterface;
    public function setMessage(string $value): SmartyDataBuilderInterface;
    public function setResendMbwayNotificationControllerUrl(string $value): SmartyDataBuilderInterface;
    public function setMbwayCountdownShow(string $value): SmartyDataBuilderInterface;
    public function setSpinnerUrl(string $value): SmartyDataBuilderInterface;
    public function setMbwayOrderConfirmUrl(string $value): SmartyDataBuilderInterface;
    public function setErrorSvgUrl(string $value): SmartyDataBuilderInterface;
    public function setErrorPaymentProcessingLang(string $value): SmartyDataBuilderInterface;
    public function setErrorPaymentProcessingDescriptionLang(string $value): SmartyDataBuilderInterface;
    public function setEntityMultibancoLang(string $value): SmartyDataBuilderInterface;
    public function setIfthenpayReferenceLang(string $value): SmartyDataBuilderInterface;
    public function setIfthenpayTotalToPayLang(string $value): SmartyDataBuilderInterface;
    public function setPhoneMbwayLang(string $value): SmartyDataBuilderInterface;
    public function setOrderTitleLang(string $value): SmartyDataBuilderInterface;
    public function setNotReceiveMbwayNotificationLang(string $value): SmartyDataBuilderInterface;
    public function setResendMbwayNotificationLang(string $value): SmartyDataBuilderInterface;
    public function setPayshopDeadlineLang(string $value): SmartyDataBuilderInterface;
    public function setConfirmMbwayPaymentTitleLang(string $value): SmartyDataBuilderInterface;
    public function setMbwayExpireTitleLang(string $value): SmartyDataBuilderInterface;
    public function setMbwayOrderPaidLang(string $value): SmartyDataBuilderInterface;
    public function setMbwayPaymentConfirmedLang(string $value): SmartyDataBuilderInterface;
    public function setIfthenpayPayBy(string $value): SmartyDataBuilderInterface;
}
