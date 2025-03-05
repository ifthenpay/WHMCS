<?php

require_once(__DIR__ . '/../../../../init.php');

use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\GatewaySetting;



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.0 405 Method Not Allowed');
	die(json_encode(['error' => 'Invalid request method.']));
}

if (!isset($_POST['entity'])){
	header('HTTP/1.1 400 Bad Request');
	die(json_encode(['error' => 'Bad Request.']));
}

// always expected to get the stored accounts and not through api request
$accounts = GatewaySetting::getValue(Config::MULTIBANCO_MODULE_CODE, Config::CF_ACCOUNTS) ?? '';
if ($accounts != '') {
	$accounts = json_decode(GatewaySetting::getValue(Config::MULTIBANCO_MODULE_CODE, Config::CF_ACCOUNTS), true);
}

$subEntityArray = '';
if ($accounts[$_POST['entity']]) {
	$subEntityArray = $accounts[$_POST['entity']];
}

$responseData = [
	'success' => true,
	'data' => $subEntityArray
];


header('Content-Type: application/json');
die(json_encode($responseData));
