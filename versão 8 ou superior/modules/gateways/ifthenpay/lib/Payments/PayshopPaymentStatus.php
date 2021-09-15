<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments;

use WHMCS\Module\Gateway\Ifthenpay\Payments\PaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\PaymentStatusInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class PayshopPaymentStatus extends PaymentStatus implements PaymentStatusInterface
{
   
    private $payshopPedido;
    
    private function checkEstado(): bool
    {
        if (isset($this->multibancoPedido['CodigoErro']) && $this->payshopPedido['CodigoErro'] === '1') {
            return true;
        }
        return false;
    }

    private function getPayshopEstado(): void
    {
        $this->payshopPedido = $this->webservice->postRequest(
            'https://www.ifthenpay.com/IfmbWS/WsIfmb.asmx/GetPaymentsJson',
                [
                    'Chavebackoffice' => $this->data->getData()->backofficeKey,
                    'Entidade' => 'PAYSHOP',
                    'Subentidade' => $this->data->getData()->payshopKey,
                    'dtHrInicio' => '',
                    'dtHrFim' => '',
                    'Referencia' => $this->data->getData()->referencia,
                    'Valor' => '',
                    'Sandbox' => 0
                ]
        )->getXmlConvertedResponseToArray();
    }

    public function getPaymentStatus(): bool
    {
        $this->getPayshopEstado();
        $this->ifthenpayLogger->info('payshop payment status request executed with success', [
                'data' => $this->data,
                'payshopPedido' => $this->payshopPedido,
                'className' => get_class($this)
            ]
        );
        return $this->checkEstado();
    }
}