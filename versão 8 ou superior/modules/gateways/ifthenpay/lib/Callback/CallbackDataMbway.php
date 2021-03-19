<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Callback;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackPayment;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Callback\CallbackDataInterface;

class CallbackDataMbway extends CallbackPayment implements CallbackDataInterface
{
    public function getData(array $request): array
    {
        $data = $this->utility->convertObjectToarray(Capsule::table('ifthenpay_mbway')->where('id_transacao', $request['id_pedido'])->first());
        if ($data && !empty($data)) {
            return $data;
        } else {
            return $this->utility->convertObjectToarray(Capsule::table('ifthenpay_mbway')->where('order_id', $request['referencia'])->first());
        }
    }
}
