<?php

require_once(__DIR__ . '/../../../../init.php');

define("CLIENTAREA", true);

use WHMCS\Module\Gateway\Ifthenpay\Router\Router;
use WHMCS\Module\Gateway\Ifthenpay\Config\Ifthenpay;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\MailInterface;

$ioc = (new Ifthenpay())->getIoc();
$ifthenpayLogger = $ioc->make(IfthenpayLogger::class);
$ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_BACKOFFICE_CONFIG_MULTIBANCO)->getLogger();
$routerData = [
    'requestMethod' => 'post',
    'requestAction' => 'requestDynamicMultibancoAccount',
    'requestData' => $_POST,
    'isFront' => false
];

try {
       
    $routerData['ifthenpayLogger'] = $ifthenpayLogger;
    
    $ioc->makeWith(Router::class, $routerData)->init(function() use ($ioc, $routerData, $ifthenpayLogger) {
        $ioc->make(MailInterface::class)
            ->setPaymentMethod(Gateway::MULTIBANCO)
            ->setRouterRequestAction($routerData['requestAction'])
            ->setSubject('Adicionar conta multibanco dinâmica ao contracto.')
            ->setMessageBody("Associar conta multibanco dinâmica ao contrato \n\n")
            ->sendEmail();  
        $ifthenpayLogger->info('request multibanco dynamic account sent with success', [ 
                'routerData' => $routerData
            ]
        );
        header('Content-Type: application/json');
        die(json_encode([
            'success' => \AdminLang::trans('multibancoDynamicSendNotificationSuccess')
        ]));
    });
} catch (\Throwable $th) {
    $ifthenpayLogger->error('error sending email requesting multibanco dynamic account - ' . $th->getMessage(), [ 
            'routerData' => $routerData,
            'exception' => $th
        ]
    );
    header("Content-Type: application/json; charset=UTF-8", true);
    header('HTTP/1.0 400 Bad Request');
    
    die(json_encode([
        'error' => \AdminLang::trans('multibancoDynamicSendNotificationError')
    ]));
}

