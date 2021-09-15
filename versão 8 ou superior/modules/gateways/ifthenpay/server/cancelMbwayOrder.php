<?php

require_once(__DIR__ . '/../../../../init.php');

define("CLIENTAREA", true);

use WHMCS\Module\GatewaySetting;
use WHMCS\Module\Gateway\Ifthenpay\Router\Router;
use WHMCS\Module\Gateway\Ifthenpay\Config\Ifthenpay;
use WHMCS\Module\Gateway\ifthenpay\Utility\TokenExtra;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Payments\MbWayPaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Repositories\MbWayRepository;

$ioc = (new Ifthenpay('mbway'))->getIoc();
$ifthenpayLogger = $ioc->make(IfthenpayLogger::class);
$ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_PAYMENTS)->getLogger();
$routerData = [
    'requestMethod' => 'post',
    'tokenExtra' => $ioc->make(TokenExtra::class),
    'requestAction' => 'cancelMbwayOrder',
    'requestData' => $_POST
];
$ioc->makeWith(Router::class, $routerData)->setSecretForTokenExtra(GatewaySetting::getForGateway('mbway')['mbwayKey'])->init(function() use ($ioc, $routerData, $ifthenpayLogger) {
    try {
        if(isset($_POST['orderId']) && $_POST['orderId'] !== '') {
            $mbwayPayment = $ioc->make(MbWayRepository::class)->getPaymentByOrderId($_POST['orderId']);
            $ifthenpayLogger->info('mbwayPayment retrieved with success', [
                    'mbwayPayment' => $mbwayPayment, 
                    'routerData' => $routerData
                ]
            );
			$configData =  GatewaySetting::getForGateway('mbway');
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
    } catch (\Throwable $th) {
        $ifthenpayLogger->error('error cancel mbwayOrder - ' . $th->getMessage(), [
                'routerData' => $routerData,
                'exception' => $th
            ]
        );
        header('Content-Type: application/json');
        die(json_encode([
            'error' => $th->getMessage()
        ]));
    }
});