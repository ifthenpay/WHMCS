<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Forms\Composite\Elements;


if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

abstract class FormElement
{
    protected $name;
    protected $friendlyName;
    protected $type;
    protected $options; 
    protected $description;
    protected $value;

    public function __construct(
        string $type, 
        string $name = null, 
        string $friendlyName = null, 
        array $options = null, 
        string $description = null, 
        string $value = null
    )
    {
        $this->name = $name;
        $this->friendlyName = $friendlyName;
        $this->type = $type;
        $this->options = $options;
        $this->description = $description;
        $this->value = $value;
    }



    public function getName()
    {
        return $this->name;
    }

    abstract public function render(): array;
}