<?php

require_once(__DIR__ . '/../../../../init.php');

define("CLIENTAREA", true);

use WHMCS\Module\GatewaySetting;
use WHMCS\Module\Gateway\Ifthenpay\Router\Router;
use WHMCS\Module\Gateway\Ifthenpay\Config\Ifthenpay;
use WHMCS\Module\Gateway\ifthenpay\Utility\TokenExtra;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\ResendMbwayNotification;

$ioc = (new Ifthenpay('mbway'))->getIoc();
$routerData = [
    'requestMethod' => 'post',
    'tokenExtra' => $ioc->make(TokenExtra::class),
    'requestAction' => 'resendMbwayNotification',
    'requestData' => $_POST
];
$ifthenpayLogger = $ioc->make(IfthenpayLogger::class);
$ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_PAYMENTS)->getLogger();
$ioc->makeWith(Router::class, $routerData)->setSecretForTokenExtra(GatewaySetting::getForGateway('mbway')['mbwayKey'])->init(function() use ($ioc, $routerData, $ifthenpayLogger) {
    try {
        $orderId = $_POST['orderId'];
        $fileName = $_POST['filename'];
        $resendMbwayNotification = $ioc->make(ResendMbwayNotification::class);
        $resendMbwayNotification->setRequest($_GET)->execute();
        $ifthenpayLogger->info('mbway notification resend with success', [ 
                'routerData' => $routerData
            ]
        );
        if ($fileName === 'viewinvoice') {
            header('Location: ' . $resendMbwayNotification->getSystemUrl() . 'viewinvoice.php?id=' . $orderId . '&messageType=success&message=' . \Lang::trans('mbwaySendNotificationSuccess'));
        } else {
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
        }
    } catch (\Throwable $th) {
        $ifthenpayLogger->error('error resending mbway notification - ' . $th->getMessage(), [ 
                'routerData' => $routerData,
                'exception' => $th
            ]
        );
        if ($fileName === 'viewinvoice') {
            header('Location: ' . $resendMbwayNotification->getSystemUrl() . 'viewinvoice.php?id=' . $orderId . '&messageType=error&message=' . Lang::trans('mbwaySendNotificationError'));
        } else {
            header('Content-Type: application/json');
            die(json_encode([
                'error' => \Lang::trans('mbwaySendNotificationError')
            ]));
        }
    }
});