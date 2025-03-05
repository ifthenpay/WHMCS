<?php 

require('../../../init.php');
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
    ])->init(function() use($ioc, $whmcs) {
        return $ioc->make(CallbackStrategy::class)->execute('offline', Gateway::PAYSHOP);
    });
} catch (\Throwable $th) {
    header('HTTP/1.0 400 Bad Request');
    die($th->getMessage());
}


