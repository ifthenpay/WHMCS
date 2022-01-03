<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Callback;

use WHMCS\Module\Gateway\Ifthenpay\Callback\CallbackProcess;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Callback\CallbackProcessInterface;

class CallbackOnline extends CallbackProcess implements CallbackProcessInterface
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
                $paymentStatus = $this->status->getTokenStatus(
                    $this->token->decrypt($this->request['qn'])
                );
                $this->ifthenpayLogger->info('payment status decrypt with success', [
                        'paymentMethod' => $this->paymentMethod,
                        'paymentStatus' => $paymentStatus,
                        'requestToken' => $this->request['qn'],
                        'className' => get_class($this)
                    ]
                );
                $this->whmcsInvoiceHistory
                    //->loadWhmcsFunctions()
                    ->setInvoiceId($GATEWAY['name'], $this->paymentData['order_id']);
                $invoiceid = $this->whmcsInvoiceHistory->getInvoiceId();

                if ($paymentStatus === 'success') {
                    $order = $this->invoiceRepository->getOrderById((int)$this->paymentData['order_id']);
                    $this->logCallbackPaymentOrder($order);
                    if ($this->request['sk'] !== $this->tokenExtra->encript(
                        $this->request['id'] . $this->request['amount'] . $this->request['requestId'], $GATEWAY['ccardKey'])) {
                            throw new \Exception(\Lang::trans('invalidSecurityToken'));
                    }
                    $clientCurrency = $this->currencieRepository->findById((string) $this->clientRepository->findById((string) $order['userid'])['currency'])['code'];
                    $orderTotal = $this->convertEuros->execute(
                        $clientCurrency,
                        $order['total']
                    );
                
                    if ($orderTotal !== $this->request['amount']) {
                        logTransaction($GATEWAY["paymentmethod"], $this->request, \Lang::trans('errorPaymentTotal'));
                        redirSystemURL("id=" . $invoiceid . "&paymentfailed=true", "viewinvoice.php");
                    }
                    $transid = $this->request['requestId'] . $this->paymentData['order_id'];
                
                    $this->whmcsInvoiceHistory->processInvoice($this->request['amount'], $this->paymentData, $transid);
                    $this->paymentRepository->update(['status' => 'paid'], (string) $this->paymentData['id']);
                    logTransaction($GATEWAY["name"], $this->paymentData, \Lang::trans('paymentSuccessful'));
                    $this->logCallbackProcess($order, $this->request['amount']);
                    redirSystemURL("id=" . $invoiceid . "&paymentsuccess=true", "viewinvoice.php");
                } else if($paymentStatus === 'cancel') {
                    $this->whmcsInvoiceHistory->cancelInvoice($this->paymentData);
                    $this->paymentRepository->update(['status' => 'cancel'], (string) $this->paymentData['id']);
                    $this->ifthenpayLogger->info('payment cancel by user', [
                            'paymentMethod' => $this->paymentMethod,
                            'paymentData' => $this->paymentData,
                            'className' => get_class($this)
                        ]
                    );
                    redirSystemURL("id=" . $invoiceid . "&paymentfailed=true", "viewinvoice.php");
                } else {
                    throw new \Exception(\Lang::trans('paymentErrorProcessing'));
                }
            }
        } catch (\Throwable $th) {
            $this->paymentRepository->update(['status' => 'error'], (string) $this->paymentData['id']);
            logTransaction($GATEWAY['name'], $this->request, \Lang::trans('errorCallbackProcessing') . $th->getMessage());
            $this->ifthenpayLogger->alert('error processing payment - ' . $th->getMessage(), [
                    'paymentMethod' => $this->paymentMethod,
                    'request' => $this->request,
                    'paymentData' => $this->paymentData,
                    'className' => get_class($this),
                    'exception' => $th
                ]
            );
            redirSystemURL("id=" . $invoiceid . "&paymentfailed=true", "viewinvoice.php");
        }
        
    }
}
