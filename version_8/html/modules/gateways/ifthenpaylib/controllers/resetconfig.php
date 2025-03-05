<?php

require_once(__DIR__ . '/../../../../init.php');


use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Services\MultibancoService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\PayshopService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\MbwayService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\CcardService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\CofidisService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpaygatewayService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\PixService;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('HTTP/1.0 405 Method Not Allowed');
	die(json_encode(['error' => 'Invalid request method.']));
}


if (!isset($_POST['paymentMethod'])) {
	header('HTTP/1.1 400 Bad Request');
	die(json_encode(['error' => 'Bad Request.']));
}

// PM_BOILERPLATE
try {

	$paymentMethod = $_POST['paymentMethod'];

	switch ($paymentMethod) {
		case Config::MULTIBANCO:
			MultibancoService::resetConfig();
			break;
		case Config::PAYSHOP:
			PayshopService::resetConfig();
			break;
		case Config::MBWAY:
			MbwayService::resetConfig();
			break;
		case Config::CCARD:
			CcardService::resetConfig();
			break;
		case Config::COFIDIS:
			CofidisService::resetConfig();
			break;
		case Config::PIX:
			PixService::resetConfig();
			break;
		case Config::IFTHENPAYGATEWAY:
			IfthenpaygatewayService::resetConfig();
			break;
		default:
			# code...
			break;
	}


	$responseData = [
		'success' => true
	];
	IfthenpayLog::info('general_logs', $paymentMethod . ' admin config reseting successful');

} catch (\Throwable $th) {
	IfthenpayLog::error('general_logs', 'Unexpected error reseting '. $paymentMethod .' admin Config', $th);

	$responseData = [
		'success' => false,
	];
}


header('Content-Type: application/json');
die(json_encode($responseData));
