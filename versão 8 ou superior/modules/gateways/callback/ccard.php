<?php

require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';

use WHMCS\Module\Gateway\Ifthenpay\Router\Router;
use WHMCS\Module\Gateway\Ifthenpay\Config\Ifthenpay;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Callback\CallbackStrategy;

try {
    $ioc = (new Ifthenpay())->getIoc();
    $ioc->makeWith(Router::class, [
        'requestMethod' => 'get',
        'requestAction' => null,
        'requestData' => $_GET,
        'isCallback' => true
    ])->init(function() use ($ioc) {
        if ($_GET['chave'] && $_GET['requestId'] && $_GET['orderId']) {
            return $ioc->make(CallbackStrategy::class)->execute('offline', Gateway::CCARD);    
        } else {
            return $ioc->make(CallbackStrategy::class)->execute('online', Gateway::CCARD);
        }
        
    });
} catch (\Throwable $th) {
    header('HTTP/1.0 400 Bad Request');
    die($th->getMessage());
}


