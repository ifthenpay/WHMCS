<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Forms\Composite\Elements;

use WHMCS\Module\Gateway\ifthenpay\Forms\Composite\FieldComposite;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class Form extends FieldComposite
{
    public function __construct(string $paymentMethod, string $paymentMethodNameAlias = null)
    {
        parent::__construct('System', null, null, null, null, $paymentMethodNameAlias ?  $paymentMethodNameAlias : ucfirst($paymentMethod));
    }
   
    public function render(): array
    {
        return array_merge(
            [
                'FriendlyName' => [
                    'Type' => $this->type,
                    'Value' => $this->value,
                ]
            ], parent::render()
        );
    }
}
