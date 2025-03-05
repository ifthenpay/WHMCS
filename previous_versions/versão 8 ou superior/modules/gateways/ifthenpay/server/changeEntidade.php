<?php

require_once(__DIR__ . '/../../../../init.php');

define("ADMINAREA", true);

use WHMCS\Module\Gateway\Ifthenpay\Router\Router;
use WHMCS\Module\Gateway\Ifthenpay\Config\Ifthenpay;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\ChangeEntidade;

$ioc = (new Ifthenpay())->getIoc();
$ifthenpayLogger = $ioc->make(IfthenpayLogger::class);
$ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_BACKOFFICE_CONFIG_MULTIBANCO)->getLogger();
$routerData = [
    'requestMethod' => 'post',
    'requestAction' => 'GetSubEntidade',
    'requestData' => $_POST,
    'isFront' => false
];

try {
    
    $routerData['ifthenpayLogger'] = $ifthenpayLogger;
    $ioc->makeWith(Router::class, $routerData)->init(function() use ($ioc, $routerData, $ifthenpayLogger) {
        header("Content-Type: application/json", true);
        header('HTTP/1.0 200 OK');
        die($ioc->make(ChangeEntidade::class)->setRequest($_POST)->execute());
    });
} catch (\Throwable $th) {
    $ifthenpayLogger->error('error changing entidade', array_merge($routerData, ['exception' => $th]));
    header("Content-Type: application/json; charset=UTF-8", true);
    header('HTTP/1.0 400 Bad Request');
    die(json_encode([
        'error' => $th->getMessage()
    ]));
}

