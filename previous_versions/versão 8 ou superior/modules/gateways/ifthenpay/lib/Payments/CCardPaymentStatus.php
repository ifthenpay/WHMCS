<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments;

use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Payments\PaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\PaymentStatusInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}


class CCardPaymentStatus extends PaymentStatus implements PaymentStatusInterface
{
    private $ccardPedido;

    private function checkEstado(): bool
    {
        if (isset($this->ccardPedido['CodigoErro']) && $this->ccardPedido['CodigoErro'] === '0') {
            return true;
        }
        return false;
    }

    private function getCCardEstado(): void
    {
        $this->ccardPedido = $this->webService->postRequest(
            'https://www.ifthenpay.com/IfmbWS/WsIfmb.asmx/GetPaymentsJson',
                [
                    'Chavebackoffice' => $this->data->getData()->backofficeKey,
                    'Entidade' => strtoupper(Gateway::CCARD),
                    'Subentidade' => $this->data->getData()->ccardKey,
                    'dtHrInicio' => '',
                    'dtHrFim' => '',
                    'Referencia' => $this->data->getData()->referencia,
                    'Valor' => $this->data->getData()->totalToPay,
                    'Sandbox' => 0
                ]
        )->getXmlConvertedResponseToArray();
    }

    public function getPaymentStatus(): bool
    {
        $this->getCCardEstado();
        return $this->checkEstado();
    }
}
