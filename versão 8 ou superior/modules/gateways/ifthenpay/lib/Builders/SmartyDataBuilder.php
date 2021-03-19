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

    public function setMessage(string $value): SmartyDataBuilderInterface
    {
        $this->data->message = $value;
        return $this;
    }
}
