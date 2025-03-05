<?php

require_once(__DIR__ . '/../../../../init.php');

use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Module\Gateway\ifthenpaylib\Services\MbwayService;


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('HTTP/1.0 405 Method Not Allowed');
	die(json_encode(['error' => 'Invalid request method.']));
}

if (!isset($_POST['invoiceId']) || !isset($_POST['countdownExpired'])) {
	header('HTTP/1.1 400 Bad Request');
	die(json_encode(['error' => 'Bad Request.']));
}


try {
	$invoiceId = $_POST['invoiceId'];
	$countdownExpired = $_POST['countdownExpired'] == 'true' ? true : false;
	
	$statusHtml = MbwayService::checkMbwayStatusAndGetHtmlStatus($invoiceId, $countdownExpired);

	$responseData = [
		'success' => true,
		'html' => $statusHtml
	];

	header('Content-Type: application/json');
	die(json_encode($responseData));
} catch (\Throwable $th) {
	IfthenpayLog::info(Config::MBWAY, 'Error fetching mbway payment status: ' . $th->__toString());

	header('Content-Type: application/json');
	die(json_encode(['success' => false]));
}
