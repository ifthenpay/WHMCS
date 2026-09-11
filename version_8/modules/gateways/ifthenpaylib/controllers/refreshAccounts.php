<?php

require_once(__DIR__ . '/../../../../init.php');

use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Services\CcardService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpaygatewayService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\MbwayService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\MultibancoService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\PayshopService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\PixService;


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('HTTP/1.0 405 Method Not Allowed');
	die(json_encode(['error' => 'Invalid request method.']));
}



try {
	$result = false;

	if (!isset($_POST['paymentMethod'])) {
		throw new \Exception('Missing paymentMethod parameter.');
	}

	switch ($_POST['paymentMethod']) {
		case Config::MULTIBANCO:
			$result = MultibancoService::refreshAccounts();
			break;
		case Config::MBWAY:
			$result = MbwayService::refreshAccounts();
			break;
		case Config::PAYSHOP:
			$result = PayshopService::refreshAccounts();
			break;
		case Config::CCARD:
			$result = CcardService::refreshAccounts();
			break;
		case Config::PIX:
			$result = PixService::refreshAccounts();
			break;
		case Config::IFTHENPAYGATEWAY:
			$result = IfthenpaygatewayService::refreshAccounts();
			break;

		default:
			$data = false;
			break;
	}

	$responseData = [
		'success' => $result,
	];
} catch (Exception $e) {
	$responseData = [
		'success' => false,
	];
}


header('Content-Type: application/json');
die(json_encode($responseData));
