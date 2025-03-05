<?php

require_once(__DIR__ . '/../../../../init.php');

use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Module\Mail;
use WHMCS\Mail\Message;
use WHMCS\Config\Setting;
use WHMCS\Module\Gateway\ifthenpaylib\Lang\IftpLang;


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('HTTP/1.0 405 Method Not Allowed');
	die(json_encode(['error' => 'Invalid request method.']));
}


if (!isset($_POST['paymentMethod'])) {
	header('HTTP/1.1 400 Bad Request');
	die(json_encode(['error' => 'Bad Request.']));
}



try {
	$paymentMethod = $_POST['paymentMethod'];
	$backofficeKey = $_POST['backofficeKey'];

	$responseData = [
		'success' => true,
		'message' => IftpLang::trans('msg_success_sending_account_request')
	];

	$message = new Message();
	$message->setType("admin");
	$message->setSubject(strtoupper($paymentMethod) . ': Ativação de Serviço');
	$message->addRecipient('to', Config::CONTACT_EMAIL_SUPPORT, 'Ifthenpay');

	$templateVars = [
		"backofficeKey" => $backofficeKey,
		"customerEmail" => Setting::getValue("Email") ?? '',
		"paymentMethod" => $paymentMethod,
		"ecommercePlatform" => "WHMCS",
		"moduleVersion" => Config::MODULE_VERSION,
		"storeName" => Setting::getValue("CompanyName") ?? ''
	];

	// load template
	$smarty = new Smarty;
	$smarty->setCompileDir(ROOTDIR . '/templates_c'); // only setting compiledir because it will create one on this folder otherwise
	$smarty->assign($templateVars);
	$body =  $smarty->fetch(ROOTDIR . '/modules/gateways/ifthenpaylib/lib/templates/mail/requestNewAccount.tpl');

	$message->setBodyAndPlainText($body);

	Mail::factory()->send($message);
	IfthenpayLog::info('general_logs', 'Request new account email sent with success.');

} catch (\Throwable $th) {
	IfthenpayLog::error('general_logs', 'Failed to send new account request email.' . $th->__toString());
	$responseData = [
		'success' => false,
		'message' => IftpLang::trans('msg_error_sending_account_request')
	];
}



header('Content-Type: application/json');
die(json_encode($responseData));
