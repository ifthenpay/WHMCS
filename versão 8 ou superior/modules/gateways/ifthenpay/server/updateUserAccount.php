<?php

require_once(__DIR__ . '/../../../../init.php');

define("ADMINAREA", true);

use WHMCS\Module\Gateway\Ifthenpay\Router\Router;
use WHMCS\Module\Gateway\Ifthenpay\Config\Ifthenpay;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ConfigGatewaysRepositoryInterface;
use WHMCS\Module\GatewaySetting;

$ioc = (new Ifthenpay())->getIoc();
$ifthenpayLogger = $ioc->make(IfthenpayLogger::class);
$propertyName = 'CHANNEL_BACKOFFICE_CONFIG_' . strtoupper($paymentMethod);
$ifthenpayLogger = $ifthenpayLogger->setChannel(constant(IfthenpayLogger::class . '::' . $propertyName))->getLogger();
$configGatewayRepository = $ioc->make(ConfigGatewaysRepositoryInterface::class);
$routerData = [
	'requestMethod' => 'get',
	'configGatewaysRepository' => $configGatewayRepository,
	'requestAction' => 'updateUserAccount',
	'prevRequestAction' => $_GET['pA'],
	'requestData' => $_GET,
	'isFront' => true
];

try {

	$routerData['ifthenpayLogger'] = $ifthenpayLogger;
	$paymentMethod = $routerData['requestData']['paymentMethod'];

	$ioc->makeWith(Router::class, $routerData)->init(function () use ($ioc, $routerData, $ifthenpayLogger, $configGatewayRepository, $paymentMethod) {
		$backofficeKey = $backofficeKey = GatewaySetting::getForGateway($paymentMethod)['backofficeKey'];
		if (!$backofficeKey) {
			$ifthenpayLogger->debug('User account backofficeKey is required', [
				'requestData' => $routerData
			]);
			header('HTTP/1.0 400 Bad Request');
			die('Backoffice key is required');
		}
		$gateway = $ioc->make(Gateway::class);
		$gateway->authenticate($backofficeKey);
		$userAccount = $gateway->getAccount($paymentMethod);
		$configGatewayRepository->createOrUpdate(
			['gateway' => $paymentMethod, 'setting' => 'userAccount'],
			['value' => serialize($userAccount)]
		);
		$configGatewayRepository->deleteWhere(
			[
				'gateway' => $routerData['requestData']['paymentMethod'],
				'setting' => 'userUpdateAccountToken'
			]
		);
		$ifthenpayLogger->debug('User account backofficeKey is required', [
			array_merge($routerData, [
				'backofficeKey' => $backofficeKey,
				'userAccount' => $userAccount
			])
		]);
		header('HTTP/1.0 200 OK');
		die('User account updated with success');
	});
} catch (\Throwable $th) {
	$ifthenpayLogger->error('error updating user account', array_merge($routerData, ['exception' => $th]));
	header('HTTP/1.0 400 Bad Request');
	die($th->getMessage());
}