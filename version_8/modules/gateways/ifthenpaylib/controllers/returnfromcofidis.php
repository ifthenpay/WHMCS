<?php

use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Module\Gateway\ifthenpaylib\Services\CofidisService;

// Require libraries needed for gateway module functions.
require_once __DIR__ . '/../../../../init.php';
require_once __DIR__ . '/../../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../../includes/invoicefunctions.php';


if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
	header('HTTP/1.0 405 Method Not Allowed');
	die(json_encode(['error' => 'Invalid request method.']));
}

if (!isset($_GET['Success']) || !isset($_GET['order_id'])) {
	header('HTTP/1.1 400 Bad Request');
	die(json_encode(['error' => 'Bad Request.']));
}


try {
	if (!(isset($_GET['Success']) && $_GET['Success'] == 'True')) {
		redirSystemURL("id=" . $_GET['order_id'] . "&paymentfailed=true", "viewinvoice.php");
	}
	CofidisService::handleReturnFromCofidis($_GET);
	redirSystemURL("id=" . $_GET['order_id'] . "&ifthenpaysuccess=true", "viewinvoice.php");

} catch (\Throwable $th) {

	IfthenpayLog::error(Config::COFIDIS, 'Error during cofidis return: ' . $th->__toString());

	redirSystemURL("id=" . $_GET['order_id'] . "&paymentfailed=true", "viewinvoice.php");
}
