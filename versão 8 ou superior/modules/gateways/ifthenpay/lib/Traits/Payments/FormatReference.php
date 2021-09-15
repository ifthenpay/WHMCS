<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Traits\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

trait FormatReference
{
    protected function formatReference(string $reference): string
    {
        return trim(strrev(chunk_split(strrev($reference),3, ' ')));
    }
}





