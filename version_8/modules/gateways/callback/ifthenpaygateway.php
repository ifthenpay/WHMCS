<?php



use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpaygatewayService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpayService;

// Require libraries needed for gateway module functions.
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';




try {
	IfthenpaygatewayService::validateCallbackAntiphishingKey($_GET);
	IfthenpayService::handlePaymentMethodCallback($_GET);

	IfthenpayLog::info(Config::IFTHENPAYGATEWAY, 'CallbackCtrl - Callback processing successful. ', $_GET);
	http_response_code(200);
	die('ok');
} catch (\Throwable $th) {

	$code = $th->getCode() ?? '000';

	if ($code == '90') {
		http_response_code(200);
		IfthenpayLog::notice(Config::IFTHENPAYGATEWAY, 'CallbackCtrl - Callback processing soft failure (already paid): ' . $th->__toString());
		die('warning - order already paid');
	}
	if ($code == '000') {
		IfthenpayLog::error(Config::IFTHENPAYGATEWAY, 'CallbackCtrl - Callback processing failure: ' . $th->__toString());
		http_response_code(500);
		die('Error - internal server error');
	}

	IfthenpayLog::warning(Config::IFTHENPAYGATEWAY, 'CallbackCtrl - Callback processing failure (code ' . $th->getCode() . '): ' . $th->__toString());
	http_response_code(400);
	die('Fail - ' . $code);
}
