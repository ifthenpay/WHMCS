<?php

require_once(__DIR__ . '/../../../../init.php');

define("CLIENTAREA", true);

use WHMCS\Module\GatewaySetting;
use WHMCS\Module\Gateway\Ifthenpay\Router\Router;
use WHMCS\Module\Gateway\Ifthenpay\Config\Ifthenpay;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Payments\MbWayPaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\MbWayRepository;

$ioc = (new Ifthenpay())->getIoc();
$ifthenpayLogger = $ioc->make(IfthenpayLogger::class);
$ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_PAYMENTS)->getLogger();
$routerData = [
    'requestMethod' => 'get',
    'requestAction' => 'cancelMbwayOrder',
    'requestData' => $_GET,
    'isFront' => true
];

try {
    $routerData['ifthenpayLogger'] = $ifthenpayLogger;
    $ioc->makeWith(Router::class, $routerData)->init(function() use ($ioc, $routerData, $ifthenpayLogger) {
        $orderId = $routerData['requestData']['orderId'];
        if($orderId && $orderId !== '') {
            $mbwayPayment = $ioc->make(MbWayRepository::class)->getPaymentByOrderId($orderId);
            $ifthenpayLogger->info('mbwayPayment retrieved with success', [
                    'mbwayPayment' => $mbwayPayment, 
                    'routerData' => $routerData
                ]
            );
            $configData =  GatewaySetting::getForGateway(Gateway::MBWAY);
            $ifthenpayLogger->info('mbway config data retrieved with success', [
                    'configData' => $configData,  
                    'routerData' => $routerData
                ]
            );
            $gatewayDataBuilder = $ioc->make(GatewayDataBuilder::class);
            $mbwayPaymentStatus = $ioc->make(MbWayPaymentStatus::class);
            $gatewayDataBuilder->setMbwayKey($configData['mbwayKey']);
            $gatewayDataBuilder->setIdPedido($mbwayPayment['id_transacao']);
            $ifthenpayLogger->info('mbway gatewayBuilderData set with success', [
                    'gatewayDataBuilder' => $gatewayDataBuilder,  
                    'routerData' => $routerData
                ]
            );
            if ($mbwayPayment['status'] === 'paid' || $mbwayPaymentStatus->setData($gatewayDataBuilder)->getPaymentStatus()) {
                $ifthenpayLogger->info('mbway payment status is paid', [
                        'routerData' => $routerData
                    ]
                );
                header('Content-Type: application/json');
                die(json_encode([
                    'orderStatus' => 'paid'
                ]));
            } else {
                $ifthenpayLogger->info('mbway payment status is pending', [
                        'routerData' => $routerData
                    ]
                );
                header('Content-Type: application/json');
                die(json_encode([
                    'orderStatus' => 'pending'
                ]));
            }
        } else {
            $ifthenpayLogger->warning('mbway payment orderId is missing', [
                    'routerData' => $routerData
                ]
            );
            header('Content-Type: application/json');
            die(json_encode([
                'error' => 'orderId is required'
            ]));
        }
    });
} catch (\Throwable $th) {
    $ifthenpayLogger->error('error cancel mbwayOrder - ' . $th->getMessage(), [
            'routerData' => $routerData,
            'exception' => $th
        ]
    );
    header("Content-Type: application/json; charset=UTF-8", true);
    header('HTTP/1.0 400 Bad Request');
    die(json_encode([
        'error' => $th->getMessage()
    ]));
}

