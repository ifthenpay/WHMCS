<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Forms\Composite;

use WHMCS\Module\Gateway\ifthenpay\Forms\Composite\Elements\FormElement;


if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

abstract class FieldComposite extends FormElement
{
    /**
     * @var FormElement[]
     */
    protected $fields = [];

    /**
     * The methods for adding/removing sub-objects.
     */
    public function add(FormElement $field): void
    {
        $name = $field->getName();
        if (isset($this->fields[$name])) {
            $this->fields[$name]->addToValue($field);
        } else {
            $this->fields[$name] = $field;
        }
    }

    public function remove(FormElement $component): void
    {
        $this->fields = array_filter($this->fields, function ($child) use ($component) {
            return $child != $component;
        });
    }

    public function render(): array
    {
        $output = [];
        
        foreach ($this->fields as $name => $field) {
            $output[$name] = $field->render();
        }
        
        return $output;
    }
}
