<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Callback;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackPayment;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Callback\CallbackDataInterface;

class CallbackDataMbway extends CallbackPayment implements CallbackDataInterface
{
    public function getData(array $request): array
    {
        $paymentRepository = $this->repositoryFactory->setType(Gateway::MBWAY)->build();
        
        $data = $paymentRepository->getPaymentByIdPedido($request['id_pedido']);;
        if ($data && !empty($data)) {
            return $data;
        } else {
            return $paymentRepository->getPaymentByIdOrderId($request['referencia']);
        }
    }
}
