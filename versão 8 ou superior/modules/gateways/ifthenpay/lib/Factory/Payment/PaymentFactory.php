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
use WHMCS\Module\Gateway\Ifthenpay\Payments\Payshop;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Multibanco;
use WHMCS\Module\Gateway\Ifthenpay\Builders\DataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\PaymentMethodInterface;
use WHMCS\Module\Gateway\Ifthenpay\Request\Webservice;


class PaymentFactory extends Factory
{
    private $data;
    private $orderId;
    private $valor;
    private $dataBuilder;
    private $webservice;

    public function __construct(Container $ioc, DataBuilder $dataBuilder, Webservice $webservice = null)
	{
        parent::__construct($ioc);
        $this->dataBuilder = $dataBuilder;
        $this->webservice = $webservice;
    }

    
    public function build(): PaymentMethodInterface
    {
        switch ($this->type) {
            case 'multibanco':
                return new Multibanco($this->data, $this->orderId, $this->valor, $this->dataBuilder);
            case 'mbway':
                return new MbWay($this->data, $this->orderId, $this->valor, $this->dataBuilder, $this->webservice);
            case 'payshop':
                return new Payshop($this->data, $this->orderId, $this->valor, $this->dataBuilder, $this->webservice);
            case 'ccard':
                return new CCard($this->data, $this->orderId, $this->valor, $this->dataBuilder, $this->webservice);
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
