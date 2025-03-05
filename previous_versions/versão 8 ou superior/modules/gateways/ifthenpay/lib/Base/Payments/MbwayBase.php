<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Base\Payments;

if (!defined("WHMCS")) {
	die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Base\PaymentBase;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Builders\SmartyDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Builders\PaymentDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\TokenInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\UtilityInterface;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Repository\RepositoryFactory;
use WHMCS\Module\Gateway\Ifthenpay\Utility\Time as IftpTime;


class MbwayBase extends PaymentBase
{
	protected $paymentMethod = Gateway::MBWAY;

	public function __construct(
		PaymentDataBuilder $paymentDefaultData,
		GatewayDataBuilder $gatewayBuilder,
		Gateway $ifthenpayGateway,
		array $whmcsGatewaySettings,
		UtilityInterface $utility,
		RepositoryFactory $repositoryFactory,
		IfthenpayLogger $ifthenpayLogger,
		TokenInterface $token,
		SmartyDataBuilder $smartyDefaultData = null
	) {
		parent::__construct(
			$paymentDefaultData,
			$gatewayBuilder,
			$ifthenpayGateway,
			$whmcsGatewaySettings,
			$utility,
			$repositoryFactory,
			$ifthenpayLogger,
			$smartyDefaultData
		);
		$this->token = $token;
	}

	protected function setGatewayBuilderData(): void
	{
		$this->gatewayBuilder->setMbwayKey($this->whmcsGatewaySettings['mbwayKey']);
		$this->gatewayBuilder->setTelemovel($_POST['mbwayPhoneNumber']);
		$this->logGatewayBuilderData();
	}

	protected function saveToDatabase(): void
	{
		$paymentData = [
			'id_transacao' => $this->paymentGatewayResultData->idPedido,
			'telemovel' => $this->paymentGatewayResultData->telemovel,
			'order_id' => $this->paymentDefaultData->orderId,
			'created_at' => IftpTime::getCurrentDateTimeStringForLisbon(),
			'status' => 'pending'
		];
		$this->paymentRepository->createOrUpdate(['order_id' => $this->paymentDefaultData->orderId], $paymentData);
		$this->logSavePaymentDataInDatabase($paymentData);
	}

	protected function updateToDatabase(): void
	{
		$paymentData = [
			'id_transacao' => $this->paymentGatewayResultData->idPedido,
			'telemovel' => $this->paymentGatewayResultData->telemovel,
			'order_id' => $this->paymentDefaultData->orderId,
			'status' => 'pending'
		];
		$this->paymentRepository->createOrUpdate(['order_id' => $this->paymentDefaultData->orderId], $paymentData);
		$this->logSavePaymentDataInDatabase($paymentData, 'update');
	}
}
