<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Callback;

use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackProcess;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Callback\CallbackProcessInterface;

/*if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}*/

class CallbackOffline extends CallbackProcess implements CallbackProcessInterface
{
    public function process(): void
    {
        try {
            $GATEWAY = getGatewayVariables($this->paymentMethod);
            $this->logGatewayDataRetrieved($GATEWAY);
    
            $this->request['payment'] = $this->paymentMethod;
            
            $this->setPaymentData();
            
            if (empty($this->paymentData)) {
                logTransaction($GATEWAY['name'], $this->request, \Lang::trans('errorCallbackPaymentNotFound'));
                $this->logCallbackDataNotFound();
            } else {
                    $order = $this->invoiceRepository->getOrderById((int)$this->paymentData['order_id']);
                    $this->logCallbackPaymentOrder($order);
                    $this->callbackValidate->setHttpRequest($this->request)
                    ->setOrder($order)
                    ->setConfigurationChaveAntiPhishing($GATEWAY['chaveAntiPhishing'])
                    ->setPaymentDataFromDb($this->paymentData)
                    ->validate();
                    $this->ifthenpayLogger->info('callback validated with success', [
                            'paymentMethod' => $this->paymentMethod,
                            'order' => $order,
                            'request' => $this->request,
                            'className' => get_class($this)
                        ]
                    );
                    $amount = $this->request['valor'];
                    $this->whmcsInvoiceHistory
                        //->loadWhmcsFunctions()
                        ->setTransactionId($this->paymentData)
                        ->setInvoiceId($GATEWAY['name'], $this->paymentData['order_id'])
                        ->processInvoice($amount, $this->paymentData);
                    $this->paymentRepository->update(['status' => 'paid'], (string) $this->paymentData['id']);
                    $this->logCallbackProcess($order, $amount);
                    http_response_code(200);
                    die('ok');              
            }
        } catch (\Throwable $th) {
            logTransaction($GATEWAY['name'], $this->request, \Lang::trans('errorCallbackProcessing') . $th->getMessage());
            $this->ifthenpayLogger->alert('error processing callback - ' . $th->getMessage(), [
                    'paymentMethod' => $this->paymentMethod,
                    'request' => $this->request,
                    'className' => get_class($this),
                    'exception' => $th
                ]
            );
            http_response_code(400);
            die($th->getMessage());
        }
       
    }
}
