<?php

require_once(__DIR__ . '/../../../../init.php');

use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpaygatewayService;
use WHMCS\Module\GatewaySetting;



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.0 405 Method Not Allowed');
	die(json_encode(['error' => 'Invalid request method.']));
}


if (!(isset($_POST['key']) && preg_match('/^[A-Z]{4}-\d{6}$/', $_POST['key']))){
	header('HTTP/1.1 400 Bad Request');
	die(json_encode(['error' => 'Bad Request.']));
}

if ((isset($_POST['backofficeKey']) && preg_match('/^\d{4}-\d{4}-\d{4}-\d{4}$/', $_POST['backofficeKey']))){
	$backofficeKey = $_POST['backofficeKey'];
}else{
	$backofficeKey = GatewaySetting::getValue(Config::IFTHENPAYGATEWAY, Config::CF_BACKOFFICE_KEY) ?? '';
}



$paymentMethodsSelectHtml = IfthenpaygatewayService::getPaymentMethodsSelectHtml(['backofficeKey' => $backofficeKey, 'key' => $_POST['key']]);

$defaultPaymentMethodSelectHtml =  IfthenpaygatewayService::getDefaultPaymentMethodSelectHtml(['backofficeKey' => $backofficeKey, 'key' => $_POST['key']]);


$responseData = [
	'success' => true,
	'data' => [
		'paymentMethodsSelectHtml' => $paymentMethodsSelectHtml,
		'defaultPaymentMethodSelectHtml' => $defaultPaymentMethodSelectHtml
	]
];


header('Content-Type: application/json');
die(json_encode($responseData));
