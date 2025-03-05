<?php

require_once(__DIR__ . '/../../../../init.php');

define("CLIENTAREA", true);

use WHMCS\Module\Gateway\Ifthenpay\Router\Router;
use WHMCS\Module\Gateway\Ifthenpay\Config\Ifthenpay;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\MailInterface;

$ioc = (new Ifthenpay())->getIoc();
$ifthenpayLogger = $ioc->make(IfthenpayLogger::class);
$propertyName = 'CHANNEL_BACKOFFICE_CONFIG_' . strtoupper($_POST['paymentMethod']);

$ifthenpayLogger = $ifthenpayLogger->setChannel(constant(IfthenpayLogger::class . '::' . $propertyName))->getLogger();
$routerData = [
	'requestMethod' => 'post',
	'requestAction' => 'addNewAccount',
	'requestData' => $_POST,
	'isFront' => false
];

try {
	$routerData['ifthenpayLogger'] = $ifthenpayLogger;
	$ioc->makeWith(Router::class, $routerData)->init(function () use ($ioc, $routerData, $ifthenpayLogger) {
		$paymentMethod = $routerData['requestData']['paymentMethod'];
		$ioc->make(MailInterface::class)
			->setPaymentMethod($routerData['requestData']['paymentMethod'])
			->setRouterRequestAction($routerData['requestAction'])
			->setSubject('Adicionar conta ' . $paymentMethod . ' ao contracto.')
			->setMessageBody("Associar conta " . $paymentMethod . " ao contrato \n\n")
			->sendEmail();

		$ifthenpayLogger->info(
			'request ' . $paymentMethod . ' account sent with success',
			[
				'routerData' => $routerData
			]
		);
		header('Content-Type: application/json');
		die(json_encode([
			'success' => \AdminLang::trans('addNewAccountSendNotificationSuccess')
		]));
	});
} catch (\Throwable $th) {

	$ifthenpayLogger->error(
		'error sending email requesting new account - ' . $th->getMessage(),
		[
			'routerData' => $routerData,
			'exception' => $th
		]
	);
	header("Content-Type: application/json; charset=UTF-8", true);
	header('HTTP/1.0 400 Bad Request');
	die(json_encode([
		'error' => \AdminLang::trans('addNewAccountSendNotificationError')
	]));
}