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
}
