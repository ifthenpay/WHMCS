<?php

require_once('../../../init.php');

use WHMCS\Module\Gateway\Ifthenpay\Router\Router;
use WHMCS\Module\Gateway\Ifthenpay\Config\Ifthenpay;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Callback\CallbackStrategy;

$ioc = (new Ifthenpay())->getIoc();
$ioc->makeWith(Router::class, [
    'requestMethod' => 'get',
    'requestAction' => null,
    'requestData' => $_GET
])->init(function() use ($ioc, $whmcs) {
    return $ioc->make(CallbackStrategy::class)->setWhmcs($whmcs)->execute('online', 'ccard');
    /*$whmcs->load_function('gateway');
    $whmcs->load_function('invoice');
    $GATEWAY = getGatewayVariables('ccard');

    if( !$GATEWAY["type"] ) {
        exit( "Module Not Activated" );
    }
    $_GET['payment'] = 'ccard';
    $paymentData = $ioc->make(CallbackData::class)->setRequest($_GET)->execute();

    if (empty($paymentData)) {
        logTransaction($GATEWAY['name'], $_GET, 'Pagamento não encontrado');
    } else {
        try {
            $utility = $ioc->make(Utility::class);
            $paymentStatus = $ioc->make(Status::class)
            ->getTokenStatus($ioc->make(Token::class)->decrypt($_GET['qn']));
            $invoiceid = checkCbInvoiceID($paymentData['order_id'], $GATEWAY['name']); # Checks invoice ID is a valid invoice number or ends processing

            if ($paymentStatus === 'success') {
                $order = $utility->getOrderById($paymentData['order_id']);
            
                if ($order['amount'] !== $_GET['amount']) {
                    logTransaction($GATEWAY["paymentmethod"], $_REQUEST, 'Valor não corresponde ao valor da encomenda.');
                    redirSystemURL("id=" . $invoiceid . "&paymentfailed=true", "viewinvoice.php");
                }

                $transid = $_GET['requestId'] . $paymentData['order_id'];
                $amount = $_GET['valor'];
                
                checkCbTransID($transid); # Checks transaction number isn't already in the database and ends processing if it does

                # Successful
                addInvoicePayment($invoiceid, $transid, $amount, $fee, $gatewaymodule); # Apply Payment to Invoice: invoiceid, transactionid, amount paid, fees, modulename
                $utility->saveIfthenpayPayment('ifthenpay_ccard', $paymentData['id']);
                logTransaction($GATEWAY["name"], $paymentData, 'Sucesso: pagamento realizado com sucesso');
                redirSystemURL("id=" . $invoiceid . "&paymentsuccess=true", "viewinvoice.php");

            } else if($paymentStatus === 'cancel') {
                Capsule::table('ifthenpay_ccard')->where('id', $paymentData['id'])->update(['status' => 'cancel']);
                logTransaction($GATEWAY["name"], $paymentData, 'Cliente cancelou o pagamento');
                redirSystemURL("id=" . $invoiceid . "&paymentfailed=true", "viewinvoice.php");
            } else {
                throw new \Exception('Erro ao processar o pagamento');
            }

        } catch (\Throwable $th) {
            Capsule::table('ifthenpay_ccard')->where('id', $paymentData['id'])->update(['status' => 'error']);
            logTransaction($GATEWAY['name'], $_GET, 'Error processing callback - ' . $th->getMessage());
            redirSystemURL("id=" . $invoiceid . "&paymentfailed=true", "viewinvoice.php");
        }
    }*/
});

