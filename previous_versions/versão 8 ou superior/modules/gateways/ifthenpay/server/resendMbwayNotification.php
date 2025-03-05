<?php

require_once(__DIR__ . '/../../../../init.php');

define("CLIENTAREA", true);

use WHMCS\Module\Gateway\Ifthenpay\Router\Router;
use WHMCS\Module\Gateway\Ifthenpay\Config\Ifthenpay;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\ResendMbwayNotification;

$ioc = (new Ifthenpay())->getIoc();
$ifthenpayLogger = $ioc->make(IfthenpayLogger::class);
$ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_PAYMENTS)->getLogger();
$routerData = [
    'requestMethod' => 'get',
    'requestAction' => 'resendMbwayNotification',
    'prevRequestAction' => $_GET['pA'],
    'requestData' => $_GET
];
try {
    $routerData['ifthenpayLogger'] = $ifthenpayLogger;
    $ioc->makeWith(Router::class, $routerData)->init(function() use ($ioc, $routerData, $ifthenpayLogger) {
        $orderId = $routerData['requestData']['orderId'];
        $fileName = $routerData['requestData']['filename'];
        $resendMbwayNotification = $ioc->make(ResendMbwayNotification::class);
        $resendMbwayNotification->setRequest($_GET)->execute();
        $ifthenpayLogger->info('mbway notification resend with success', [ 
                'routerData' => $routerData
            ]
        );
            if (isset($_COOKIE['mbwayCountdownShow'])) {
                $_COOKIE['mbwayCountdownShow'] = 'true';
            } else {
                setcookie('mbwayCountdownShow', 'true');
                $_COOKIE['mbwayCountdownShow'] = 'true';
            }
            $ifthenpayLogger->info('mbway mbwayCountdownShow set with success', [ 
                    'routerData' => $routerData,
                    'cookie' => $_COOKIE
                ]
            );
            header('Content-Type: application/json');
            die(json_encode([
                'success' => \Lang::trans('mbwaySendNotificationSuccess')
            ]));
    });
} catch (\Throwable $th) {
    $ifthenpayLogger->error('error resending mbway notification - ' . $th->getMessage(), [ 
            'routerData' => $routerData,
            'exception' => $th
        ]
    );
    header("Content-Type: application/json; charset=UTF-8", true);
    header('HTTP/1.0 400 Bad Request');
    die(json_encode([
        'error' => \Lang::trans('mbwaySendNotificationError')
    ]));
}

