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
    return $ioc->make(CallbackStrategy::class)->setWhmcs($whmcs)->execute('offline', 'multibanco');
});

