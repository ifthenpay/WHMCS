<?php

use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;

require_once(__DIR__ . '/../../../../init.php');


if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
	header('HTTP/1.0 405 Method Not Allowed');
	die(json_encode(['error' => 'Invalid request method.']));
}

if (!isset($_GET['order_id'])) {
	header('HTTP/1.1 400 Bad Request');
	die(json_encode(['error' => 'Bad Request.']));
}


try {
	redirSystemURL("id=" . $_GET['order_id'] . "&ifthenpaysuccess=true", "viewinvoice.php");

} catch (\Throwable $th) {

	IfthenpayLog::error(Config::PIX, 'Error during pix return: ' . $th->__toString());
	redirSystemURL("id=" . $_GET['order_id'] . "&paymentfailed=true", "viewinvoice.php");
}
