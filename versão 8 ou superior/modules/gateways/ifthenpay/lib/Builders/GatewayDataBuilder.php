<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Builders;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\ifthenpay\Contracts\Builders\GatewayDataBuilderInterface;

class GatewayDataBuilder extends DataBuilder implements GatewayDataBuilderInterface
{
    public function setSubEntidade(string $value): GatewayDataBuilderInterface
    {
        $this->data->subEntidade = $value;
        return $this;
    }

    public function setMbwayKey(string $value): GatewayDataBuilderInterface
    {
        $this->data->mbwayKey = $value;
        return $this;
    }

    public function setPayshopKey(string $value): GatewayDataBuilderInterface
    {
        $this->data->payshopKey = $value;
        return $this;
    }

    public function setCCardKey(string $value): GatewayDataBuilderInterface
    {
        $this->data->ccardKey = $value;
        return $this;
    }
}
