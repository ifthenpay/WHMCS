<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Request\WebService;
use WHMCS\Module\Gateway\Ifthenpay\Builders\DataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Traits\Payments\FormatReference;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Payment as MasterPayment;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\PaymentMethodInterface;

class Multibanco extends MasterPayment implements PaymentMethodInterface
{
    use FormatReference;

    const DYNAMIC_MB_ENTIDADE = 'MB';
    
    private $entidade;
    private $subEntidade;
    private $validade;
    private $multibancoPedido;


    public function __construct(GatewayDataBuilder $data, string $orderId, string $valor, DataBuilder $dataBuilder, WebService $webService)
    {
        parent::__construct($orderId, $valor, $dataBuilder, $webService);
        $this->entidade = $data->getData()->entidade;
        $this->subEntidade = $data->getData()->subEntidade;
        $this->validade = isset($data->getData()->validade) ? $data->getData()->validade : '999999';
    }

    public function checkValue(): void
    {
        if (intval($this->valor) >= 1000000) {
            throw new \Exception(\Lang::trans('invalidMultibancoValue'));
        }
    }

    private function checkEstado(): void
    {
        if ($this->multibancoPedido['Status'] !== '0') {
            throw new \Exception($this->multibancoPedido['Message']);
        }
    }

    private function setDynamicReferencia(): void
    {
        $this->multibancoPedido = $this->webService->postRequest(
            'https://ifthenpay.com/api/multibanco/reference/init',
            [
                    'mbKey' => $this->subEntidade,
                    "orderId" => $this->orderId,
                    "amount" => $this->valor,
                    "description" => '',
                    "url" => '',
                    "clientCode" => '',
                    "clientName" => '',
                    "clientEmail" => '',
                    "clientUsername" => '',
                    "clientPhone" => '',
                    "expiryDays" => $this->validade
            ],
            true
        )->getResponseJson();
    }

    private function setReferencia()
    {
        
        $this->orderId = "0000" . $this->orderId;
        
        if(strlen($this->subEntidade) === 2){
			//Apenas sao considerados os 5 caracteres mais a direita do order_id
			$seed = substr($this->orderId, (strlen($this->orderId) - 5), strlen($this->orderId));
			$chk_str = sprintf('%05u%02u%05u%08u', $this->entidade, $this->subEntidade, $seed, round($this->valor*100));
		}else {
			//Apenas sao considerados os 4 caracteres mais a direita do order_id
			$seed = substr($this->orderId, (strlen($this->orderId) - 4), strlen($this->orderId));
			$chk_str = sprintf('%05u%03u%04u%08u', $this->entidade, $this->subEntidade, $seed, round($this->valor*100));
		}
        $chk_array=array(3, 30, 9, 90, 27, 76, 81, 34, 49, 5, 50, 15, 53, 45, 62, 38, 89, 17, 73, 51);
        $chk_val=0;
        for ($i = 0; $i < 20; $i++) {
            $chk_int = substr($chk_str, 19-$i, 1);
            $chk_val += ($chk_int%10)*$chk_array[$i];
        }
        $chk_val %= 97;
        $chk_digits = sprintf('%02u', 98-$chk_val);
        //referencia
        return $this->subEntidade.$seed.$chk_digits;
    }

    private function getReferencia(): DataBuilder
    {
        $this->dataBuilder->setEntidade($this->entidade);
        if ($this->entidade === self::DYNAMIC_MB_ENTIDADE) {
            $this->setDynamicReferencia();
            $this->checkEstado();
            $this->dataBuilder->setEntidade($this->multibancoPedido['Entity']);
            $this->dataBuilder->setReferencia($this->multibancoPedido['Reference']);
            $this->dataBuilder->setIdPedido($this->multibancoPedido['RequestId']);
            $this->dataBuilder->setValidade($this->multibancoPedido['ExpiryDate']);
        } else {
            $this->dataBuilder->setReferencia($this->setReferencia());
        }
        $this->dataBuilder->setTotalToPay((string)$this->valor);
        return $this->dataBuilder;
    }

    public function buy(): DataBuilder
    {
        $this->checkValue($this->valor);
        return $this->getReferencia();
    }
}
