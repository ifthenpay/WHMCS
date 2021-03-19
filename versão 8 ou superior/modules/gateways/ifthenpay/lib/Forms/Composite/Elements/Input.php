<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Forms\Composite\Elements;

use WHMCS\Module\Gateway\ifthenpay\Forms\Composite\Elements\FormElement;


if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}


class Input extends FormElement
{
    private $container = [];

    public function render(): array
    {
        $this->container['Type'] = $this->type;
        if ($this->friendlyName) {
            $this->container['FriendlyName'] = $this->friendlyName;
        }
        if ($this->options) {
            $this->container['Options'] = $this->options;
        }
        if ($this->value) {
            $this->container['Value'] = $this->value;
        }
        if ($this->description) {
            $this->container['Description'] = $this->description;
        }
        
        return $this->container;
    }
}
