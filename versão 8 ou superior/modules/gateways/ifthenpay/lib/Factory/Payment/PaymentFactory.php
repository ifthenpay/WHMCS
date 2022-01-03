<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Factory\Payment;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use Illuminate\Container\Container;
use WHMCS\Module\Gateway\Ifthenpay\Payments\CCard;
use WHMCS\Module\Gateway\Ifthenpay\Payments\MbWay;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Factory;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Payshop;
use WHMCS\Module\Gateway\Ifthenpay\Request\WebService;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Multibanco;
use WHMCS\Module\Gateway\Ifthenpay\Builders\DataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\PaymentMethodInterface;


class PaymentFactory extends Factory
{
    private $data;
    private $orderId;
    private $valor;
    private $dataBuilder;
    private $webService;

    public function __construct(Container $ioc, DataBuilder $dataBuilder, WebService $webService)
	{
        parent::__construct($ioc);
        $this->dataBuilder = $dataBuilder;
        $this->webService = $webService;
    }

    
    public function build(): PaymentMethodInterface
    {
        switch ($this->type) {
            case Gateway::MULTIBANCO:
                return new Multibanco($this->data, $this->orderId, $this->valor, $this->dataBuilder, $this->webService);
            case Gateway::MBWAY:
                return new MbWay($this->data, $this->orderId, $this->valor, $this->dataBuilder, $this->webService);
            case Gateway::PAYSHOP:
                return new Payshop($this->data, $this->orderId, $this->valor, $this->dataBuilder, $this->webService);
            case Gateway::CCARD:
                return new CCard($this->data, $this->orderId, $this->valor, $this->dataBuilder, $this->webService);
            default:
                throw new Exception("Unknown Payment Class");
        }
    }

    /**
     * Set the value of orderId
     *
     * @return  self
     */ 
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Set the value of valor
     *
     * @return  self
     */ 
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Set the value of data
     *
     * @return  self
     */ 
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
