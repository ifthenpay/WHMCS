<?php

require_once($_SERVER['CONTEXT_DOCUMENT_ROOT'] . explode("modules/gateways/ifthenpay", $_SERVER['SCRIPT_NAME'], 2)[0] . 'init.php');

define("ADMINAREA", true);

use WHMCS\Module\Gateway\Ifthenpay\Router\Router;
use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;
use WHMCS\Module\Gateway\Ifthenpay\Config\Ifthenpay;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;

$ioc = (new Ifthenpay())->getIoc();
$ioc->makeWith(Router::class, [
    'requestMethod' => 'post',
    'requestAction' => 'GetSubEntidade',
    'requestData' => $_POST
])->init(function() use ($ioc) {
    try {
        $ifthenpayUserAccount = $ioc->make(Utility::class)->getIfthenpayUserAccount('multibanco');
        $ifthenpayGateway = $ioc->make(Gateway::class);
        $ifthenpayGateway->setAccount($ifthenpayUserAccount);
        $subEntidades = json_encode($ifthenpayGateway->getSubEntidadeInEntidade($_POST['entidade']));
        header("Content-Type: application/json", true);
        header('HTTP/1.0 200 OK');
        die($subEntidades);
    } catch (\Throwable $th) {
        header("Content-Type: application/json", true);
        header('HTTP/1.0 400 Bad Request');
        die($th->getMessage());
    }
});