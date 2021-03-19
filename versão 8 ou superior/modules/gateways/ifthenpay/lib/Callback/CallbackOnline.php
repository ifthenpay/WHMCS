<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Callback;

use WHMCS\Database\Capsule;
use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackProcess;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Callback\CallbackProcessInterface;

class CallbackOnline extends CallbackProcess implements CallbackProcessInterface
{
        
    public function process(): void
    {
        $this->whmcs->load_function('gateway');
        $this->whmcs->load_function('invoice');
        $GATEWAY = getGatewayVariables($this->paymentMethod);

        $_GET['payment'] = $this->paymentMethod;
        $this->setPaymentData();

        if (empty($this->paymentData)) {
            logTransaction($GATEWAY['name'], $this->request, 'Pagamento não encontrado');
        } else {
            try {
                $paymentStatus = $this->status->getTokenStatus(
                    $this->token->decrypt($this->request['qn'])
                );
                $invoiceid = checkCbInvoiceID($this->paymentData['order_id'], $GATEWAY['name']); # Checks invoice ID is a valid invoice number or ends processing

                if ($paymentStatus === 'success') {
                    $order = $this->utility->getOrderById($this->paymentData['order_id']);
                
                    if ($order['amount'] !== $this->request['amount']) {
                        logTransaction($GATEWAY["paymentmethod"], $this->request, 'Valor não corresponde ao valor da encomenda.');
                        redirSystemURL("id=" . $invoiceid . "&paymentfailed=true", "viewinvoice.php");
                    }

                    $transid = $this->request['requestId'] . $this->paymentData['order_id'];
                    $amount = $this->request['valor'];
                    
                    checkCbTransID($transid); # Checks transaction number isn't already in the database and ends processing if it does

                    # Successful
                    addInvoicePayment($invoiceid, $transid, $amount, $fee, $gatewaymodule); # Apply Payment to Invoice: invoiceid, transactionid, amount paid, fees, modulename
                    $this->utility->saveIfthenpayPayment('ifthenpay_' . $this->paymentMethod, (string)$this->paymentData['id']);
                    logTransaction($GATEWAY["name"], $this->paymentData, 'Sucesso: pagamento realizado com sucesso');
                    redirSystemURL("id=" . $invoiceid . "&paymentsuccess=true", "viewinvoice.php");
                } else if($paymentStatus === 'cancel') {
                    Capsule::table('ifthenpay_' . $this->paymentMethod)->where('id', $this->paymentData['id'])->update(['status' => 'cancel']);
                    logTransaction($GATEWAY["name"], $this->paymentData, 'Cliente cancelou o pagamento');
                    redirSystemURL("id=" . $invoiceid . "&paymentfailed=true", "viewinvoice.php");
                } else {
                    throw new \Exception('Erro ao processar o pagamento');
                }

            } catch (\Throwable $th) {
                Capsule::table('ifthenpay_' . $this->paymentMethod)->where('id', $this->paymentData['id'])->update(['status' => 'error']);
                logTransaction($GATEWAY['name'], $this->request, 'Error processing callback - ' . $th->getMessage());
                redirSystemURL("id=" . $invoiceid . "&paymentfailed=true", "viewinvoice.php");
            }
        }
    }
}
