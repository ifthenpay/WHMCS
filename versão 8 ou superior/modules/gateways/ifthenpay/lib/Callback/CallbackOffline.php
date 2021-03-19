<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Callback;

use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackProcess;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Callback\CallbackProcessInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class CallbackOffline extends CallbackProcess implements CallbackProcessInterface
{
    public function process(): void
    {
        $this->whmcs->load_function('gateway');
        $this->whmcs->load_function('invoice');
        $GATEWAY = getGatewayVariables($this->paymentMethod);

        $this->request['payment'] = $this->paymentMethod;
        
        $this->setPaymentData();
        
        if (empty($this->paymentData)) {
            logTransaction($GATEWAY['name'], $this->request, 'Pagamento não encontrado');
        } else {
            try {
                $order = $this->utility->getOrderById($this->paymentData['order_id']);
                $this->callbackValidate->setHttpRequest($this->request)
                ->setOrder($order)
                ->setConfigurationChaveAntiPhishing($GATEWAY['chaveAntiPhishing'])
                ->setPaymentDataFromDb($this->paymentData)
                ->validate();

                if ($this->paymentData['entidade']) {
                    $transid = $this->paymentData['entidade'] . $this->paymentData['referencia'] . $this->paymentData['order_id'];
                } else {
                    $transid = $this->paymentData['id_transacao'] . $this->paymentData['order_id'];
                }
                
                $amount = $this->request['valor'];
                
                $invoiceid = checkCbInvoiceID($this->paymentData['order_id'], $GATEWAY['name']); # Checks invoice ID is a valid invoice number or ends processing

                checkCbTransID($transid); # Checks transaction number isn't already in the database and ends processing if it does

                # Successful
                addInvoicePayment($invoiceid, $transid, $amount, $fee, $gatewaymodule); # Apply Payment to Invoice: invoiceid, transactionid, amount paid, fees, modulename
                $this->utility->saveIfthenpayPayment('ifthenpay_' . $this->paymentMethod, (string)$this->paymentData['id']);
                logTransaction($GATEWAY["name"], $this->paymentData, 'Sucesso: pagamento realizado com sucesso');
                http_response_code(200);
                die('ok');              
            } catch (\Throwable $th) {
                logTransaction($GATEWAY['name'], $this->request, 'Error processing callback - ' . $th->getMessage());
                http_response_code(400);
                die($th->getMessage());
            }
        }
    }
}
