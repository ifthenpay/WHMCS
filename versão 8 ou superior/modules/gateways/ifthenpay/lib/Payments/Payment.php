<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Builders\DataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Request\Webservice;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Models\PaymentModelInterface;

class Payment
{
    protected $orderId;
    protected $valor;
    protected $dataBuilder;
    protected $webservice;

    public function __construct(string $orderId, string $valor, DataBuilder $dataBuilder, Webservice $webservice = null)
    {
        $this->orderId = $orderId;
        $this->valor = $this->formatNumber($valor);
        $this->dataBuilder = $dataBuilder;
        $this->webservice = $webservice;
    }

    protected function formatNumber(string $number) : string
    {
        $verifySepDecimal = number_format(99, 2);

        $valorTmp = $number;

        $sepDecimal = substr($verifySepDecimal, 2, 1);

        $hasSepDecimal = true;

        $i = (strlen($valorTmp) -1);

        for ($i; $i!=0; $i-=1) {
            if (substr($valorTmp, $i, 1)=="." || substr($valorTmp, $i, 1)==",") {
                $hasSepDecimal = true;
                $valorTmp = trim(substr($valorTmp, 0, $i))."@".trim(substr($valorTmp, 1+$i));
                break;
            }
        }

        if ($hasSepDecimal!=true) {
            $valorTmp=number_format($valorTmp, 2);

            $i=(strlen($valorTmp)-1);

            for ($i; $i!=1; $i--) {
                if (substr($valorTmp, $i, 1)=="." || substr($valorTmp, $i, 1)==",") {
                    $hasSepDecimal = true;
                    $valorTmp = trim(substr($valorTmp, 0, $i))."@".trim(substr($valorTmp, 1+$i));
                    break;
                }
            }
        }

        for ($i=1; $i!=(strlen($valorTmp)-1); $i++) {
            if (substr($valorTmp, $i, 1)=="." || substr($valorTmp, $i, 1)=="," || substr($valorTmp, $i, 1)==" ") {
                $valorTmp = trim(substr($valorTmp, 0, $i)).trim(substr($valorTmp, 1+$i));
                break;
            }
        }

        if (strlen(strstr($valorTmp, '@'))>0) {
            $valorTmp = trim(substr($valorTmp, 0, strpos($valorTmp, '@'))).trim($sepDecimal).trim(substr($valorTmp, strpos($valorTmp, '@')+1));
        }

        return $valorTmp;
    }

    protected function checkIfPaymentExist(string $orderId, PaymentModelInterface $paymentModel)
    {
        $paymentData = $paymentModel->getByOrderId($orderId);
        return !empty($paymentData) ? $paymentData : false;
    }
}
