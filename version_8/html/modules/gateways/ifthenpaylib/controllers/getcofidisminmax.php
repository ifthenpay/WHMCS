<?php

require_once(__DIR__ . '/../../../../init.php');


use WHMCS\Module\Gateway\ifthenpaylib\Services\CofidisService;


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.0 405 Method Not Allowed');
	die(json_encode(['error' => 'Invalid request method.']));
}

if (!(isset($_POST['key']) && preg_match('/^[A-Z]{3}-\d{6}$/', $_POST['key']))){
	header('HTTP/1.1 400 Bad Request');
	die(json_encode(['error' => 'Bad Request.']));
}


$data = CofidisService::getMinMaxFromIfthenpay($_POST['key']);


$responseData = [
	'success' => true,
	'data' => $data
];


header('Content-Type: application/json');
die(json_encode($responseData));
