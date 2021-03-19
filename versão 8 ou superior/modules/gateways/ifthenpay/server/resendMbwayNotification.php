<?php

require_once($_SERVER['CONTEXT_DOCUMENT_ROOT'] . explode("modules/gateways/ifthenpay", $_SERVER['SCRIPT_NAME'], 2)[0] . 'init.php');

define("CLIENTAREA", true);

use WHMCS\Database\Capsule;
use WHMCS\Module\GatewaySetting;
use WHMCS\Module\Gateway\Ifthenpay\Router\Router;
use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;
use WHMCS\Module\Gateway\Ifthenpay\Config\Ifthenpay;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;

$ioc = (new Ifthenpay('mbway'))->getIoc();
$ioc->makeWith(Router::class, [
    'requestMethod' => 'get',
    'requestAction' => 'resendMbwayNotification',
    'requestData' => $_GET
])->init(function() use ($ioc) {
    try {
        $orderId = $_GET['orderId'];
        $mbwayTelemovel = $_GET['mbwayTelemovel']; 
        $totalToPay = $_GET['orderTotalPay'];
        $fileName = $_GET['filename'];
        $systemUrl = $ioc->make(Utility::class)->getSystemUrl();
        $paymentData = $ioc->make(GatewayDataBuilder::class)
            ->setMbwayKey(GatewaySetting::getForGateway('mbway')['mbwayKey'])
            ->setTelemovel($mbwayTelemovel);
        $gatewayResult = $ioc->make(Gateway::class)->execute(
            'mbway',
            $paymentData,
            strval($orderId),
            strval($totalToPay)
        )->getData();
        Capsule::table('ifthenpay_mbway')->where('order_id', $orderId)->update(['id_transacao' => $gatewayResult->idPedido]);
        if ($fileName === 'viewinvoice') {
            header('Location: ' . $systemUrl . 'viewinvoice.php?id=' . $orderId . '&messageType=success&message=MB WAY notification sent with success. Confirm payment on your MB WAY app.');
        } else {
            header('Location: ' . $systemUrl . 'cart.php?a=complete&messageType=success&message=MB WAY notification sent with success. Confirm payment on your MB WAY app.');
        }
        echo "";
        exit;
    } catch (\Throwable $th) {
        if ($fileName === 'viewinvoice') {
            header('Location: ' . $systemUrl . 'viewinvoice.php?id=' . $orderId . '&messageType=error&message=Error sending MB WAY notification.');
        } else {
            header('Location: ' . $systemUrl . 'cart.php?a=complete&messageType=error&message=Error sending MB WAY notification.');
        }
        echo "Error sending MB WAY notification.";
        exit;
    }
});