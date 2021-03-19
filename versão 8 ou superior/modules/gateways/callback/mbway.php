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
])->init(function() use($ioc, $whmcs) {
    return $ioc->make(CallbackStrategy::class)->setWhmcs($whmcs)->execute('offline', 'mbway');
    /*$whmcs->load_function('gateway');
    $whmcs->load_function('invoice');
    $GATEWAY = getGatewayVariables('mbway');

    if( !$GATEWAY["type"] ) {
        exit( "Module Not Activated" );
    }
    $_GET['payment'] = 'mbway';
    $paymentData = $ioc->make(CallbackData::class)->setRequest($_GET)->execute();

    if (empty($paymentData)) {
        logTransaction($GATEWAY['name'], $_GET, 'Pagamento não encontrado');
    } else {
        try {
            $utility = $ioc->make(Utility::class);
            $order = $utility->getOrderById($paymentData['order_id']);
            $ioc->make(CallbackValidate::class)
            ->setHttpRequest($_GET)
            ->setOrder($order)
            ->setConfigurationChaveAntiPhishing($GATEWAY['chaveAntiPhishing'])
            ->setPaymentDataFromDb($paymentData)
            ->validate();                

            $transid = $paymentData['id_transacao'] . $paymentData['telemovel'] . $paymentData['order_id'];
            $amount = $_GET['valor'];
            
            $invoiceid = checkCbInvoiceID($paymentData['order_id'], $GATEWAY['name']); # Checks invoice ID is a valid invoice number or ends processing

            checkCbTransID($transid); # Checks transaction number isn't already in the database and ends processing if it does

            # Successful
            addInvoicePayment($invoiceid, $transid, $amount, $fee, $gatewaymodule); # Apply Payment to Invoice: invoiceid, transactionid, amount paid, fees, modulename
            $utility->saveIfthenpayPayment('ifthenpay_mbway', $paymentData['id']);
            logTransaction($GATEWAY["name"], $paymentData, 'Sucesso: pagamento realizado com sucesso');
            
            
        } catch (\Throwable $th) {
            logTransaction($GATEWAY['name'], $_GET, 'Error processing callback - ' . $th->getMessage());
        }
    }*/
});

