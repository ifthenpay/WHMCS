<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments;

use WHMCS\Module\Gateway\Ifthenpay\Payments\PaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\PaymentStatusInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class MultibancoPaymentStatus extends PaymentStatus implements PaymentStatusInterface
{
    private $multibancoPedido;

    private function checkEstado(): bool
    {
        if (isset($this->multibancoPedido['CodigoErro ']) && $this->multibancoPedido['CodigoErro'] === '0') {
            return true;
        }
        return false;
    }

    private function getMultibancoEstado(): void
    {
        $this->multibancoPedido = $this->webService->getRequest(
            'https://www.ifthenpay.com/IfmbWS/WsIfmb.asmx/GetPaymentsJson',
                [
                    'Chavebackoffice' => $this->data->getData()->backofficeKey,
                    'Entidade' => $this->data->getData()->entidade,
                    'Subentidade' => $this->data->getData()->subEntidade,
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
        $this->getMultibancoEstado();
        $this->ifthenpayLogger->info('multibanco payment status request executed with success', [
                'data' => $this->data,
                'multibancoPedido' => $this->multibancoPedido,
                'className' => get_class($this)
            ]
        );
        return $this->checkEstado();
    }
}