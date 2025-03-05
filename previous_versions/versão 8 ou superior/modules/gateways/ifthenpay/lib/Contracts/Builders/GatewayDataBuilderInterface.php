<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Builders;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Contracts\Builders\DataBuilderInterface;

interface GatewayDataBuilderInterface extends DataBuilderInterface
{
    public function setSubEntidade(string $value): GatewayDataBuilderInterface;
    public function setMbwayKey(string $value): GatewayDataBuilderInterface;
    public function setPayshopKey(string $value): GatewayDataBuilderInterface;
    public function setCCardKey(string $value): GatewayDataBuilderInterface;
}
