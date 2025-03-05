<?php

require_once(__DIR__ . '/../../../../init.php');


use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Services\CcardService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\CofidisService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpaygatewayService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\MbwayService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\MultibancoService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\PayshopService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\PixService;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('HTTP/1.0 405 Method Not Allowed');
	die(json_encode(['error' => 'Invalid request method.']));
}

if (!(isset($_POST['backofficeKey']) && preg_match('/^\d{4}-\d{4}-\d{4}-\d{4}$/', $_POST['backofficeKey']))) {
	header('HTTP/1.1 400 Bad Request');
	die(json_encode(['error' => 'Bad Request.']));
}

if (!isset($_POST['paymentMethod'])) {
	header('HTTP/1.1 400 Bad Request');
	die(json_encode(['error' => 'Bad Request.']));
}

// PM_BOILERPLATE
try {
	switch ($_POST['paymentMethod']) {
		case Config::MULTIBANCO:
			$data = MultibancoService::getEntitiesByBackofficKey($_POST['backofficeKey']);
			break;
		case Config::MBWAY:
			$data = MbwayService::getKeysByBackofficKey($_POST['backofficeKey']);
			break;
		case Config::PAYSHOP:
			$data = PayshopService::getKeysByBackofficKey($_POST['backofficeKey']);
			break;
		case Config::CCARD:
			$data = CcardService::getKeysByBackofficKey($_POST['backofficeKey']);
			break;
		case Config::COFIDIS:
			$data = CofidisService::getKeysByBackofficKey($_POST['backofficeKey']);
			break;
		case Config::PIX:
			$data = PixService::getKeysByBackofficKey($_POST['backofficeKey']);
			break;
		case Config::IFTHENPAYGATEWAY:
			$data = IfthenpaygatewayService::getKeysByBackofficKey($_POST['backofficeKey']);
			break;

		default:
			$data = false;
			break;
	}


	$responseData = [
		'success' => true,
		'data' => $data
	];
} catch (\Throwable $th) {
	$responseData = [
		'success' => false,
		'data' => false
	];
}

header('Content-Type: application/json');
die(json_encode($responseData));
