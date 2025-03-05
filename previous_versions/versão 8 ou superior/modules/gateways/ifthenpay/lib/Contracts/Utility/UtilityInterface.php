<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility;


if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface UtilityInterface
{
    public function getSystemUrl(): string;
    public function getImgUrl(): string;
    public function getJsUrl(): string;
    public function getCssUrl(): string;
    public function getSvgUrl(): string;
    public function getTemplatesUrl(): string;
    public function getCallbackControllerUrl(string $paymentMethod): string;
    public function setPaymentLogo(string $paymentMethod): string;
    public function convertObjectToarray(object  $object = null): array;
}