<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments;

if (!defined("WHMCS")) {
	die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Request\WebService;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Multibanco;
use WHMCS\Module\Gateway\ifthenpay\Builders\DataBuilder;
use WHMCS\Module\Gateway\ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\ifthenpay\Factory\Payment\PaymentFactory;

class Gateway
{
	const MULTIBANCO = 'multibanco';
	const MBWAY = 'mbway';
	const PAYSHOP = 'payshop';
	const CCARD = 'ccard';
	const CCARD_ALIAS = 'credit card (ifthenpay)';

	private $webService;
	private $paymentFactory;
	private $account;
	private $paymentMethods = [self::MULTIBANCO, self::MBWAY, self::PAYSHOP, self::CCARD];
	private $paymentMethodsCanCancel = [self::MULTIBANCO, self::MBWAY, self::CCARD, self::PAYSHOP];
	private $aliasPaymentMethods = [
		self::MULTIBANCO => [
			'en' => 'Multibanco',
			'pt' => 'Multibanco',
		],
		self::MBWAY => [
			'en' => 'MB WAY',
			'pt' => 'MB WAY',
		],
		self::PAYSHOP => [
			'en' => 'Payshop',
			'pt' => 'Payshop',
		],
		self::CCARD => [
			'en' => 'Credit Card',
			'pt' => 'Cartão de Crédito',
		],

	];

	public function __construct(WebService $webService, PaymentFactory $paymentFactory)
	{
		$this->webService = $webService;
		$this->paymentFactory = $paymentFactory;
	}

	public function getAliasPaymentMethods(string $paymentMethod, string $isoCodeLanguage): string
	{
		return $this->aliasPaymentMethods[$paymentMethod][$isoCodeLanguage];
	}

	public function getPaymentMethodsType(): array
	{
		return $this->paymentMethods;
	}

	public function getPaymentMethodsCanCancel(): array
	{
		return $this->paymentMethodsCanCancel;
	}

	public function checkIfthenpayPaymentMethod(string $paymentMethod): bool
	{
		if (in_array(strtolower($paymentMethod), $this->paymentMethods)) {
			return true;
		} else if (strtolower($paymentMethod) === self::CCARD_ALIAS) {
			return true;
		} else {
			return false;
		}
	}

	public function authenticate(string $backofficeKey): void
	{
		$authenticate = $this->webService->postRequest(
			'https://www.ifthenpay.com/IfmbWS/ifmbws.asmx/' .
			'getEntidadeSubentidadeJsonV2',
			[
				'chavebackoffice' => $backofficeKey,
			]
		)->getResponseJson();

		if (!$authenticate[0]['Entidade'] && empty($authenticate[0]['SubEntidade'])) {
			throw new \Exception(is_null(\Lang::trans('backofficeKeyInvalid')) ? \AdminLang::trans('backofficeKeyInvalid') : \Lang::trans('backofficeKeyInvalid'));
		} else {
			$this->account = $authenticate;
		}
	}

	public function getAccount(string $paymentMethod): array
	{
		return array_filter(
			$this->account,
			function ($value) use ($paymentMethod) {
				if ($paymentMethod === self::MULTIBANCO && (is_numeric($value['Entidade']) || $value['Entidade'] === Multibanco::DYNAMIC_MB_ENTIDADE)) {
					return $value;
				} elseif ($paymentMethod === self::MBWAY) {
					return $value['Entidade'] === strtoupper($paymentMethod);
				} elseif ($paymentMethod === self::PAYSHOP) {
					return $value['Entidade'] === strtoupper($paymentMethod);
				} elseif ($paymentMethod === self::CCARD) {
					return $value['Entidade'] === strtoupper($paymentMethod);
				}
			}
		);
	}

	public function setAccount(array $account)
	{
		$this->account = $account;
	}

	public function getPaymentMethods(): array
	{
		$userPaymentMethods = [];

		foreach ($this->account as $account) {
			if (in_array(strtolower($account['Entidade']), $this->paymentMethods)) {
				$userPaymentMethods[] = strtolower($account['Entidade']);
			} elseif (is_numeric($account['Entidade'])) {
				$userPaymentMethods[] = $this->paymentMethods[0];
			}
		}
		return array_unique($userPaymentMethods);
	}



	public function getSubEntitiesByEntity(string $entity): array
	{
		$subEntities = [];


		foreach ($this->account as $item) {
			if ($item['Entidade'] === $entity) {
				$subEntities = $item['SubEntidade'];
			}
		}

		return $subEntities;
	}


	public function getSubEntidadeInEntidade(string $entidade): array
	{
		return array_filter(
			$this->account,
			function ($value) use ($entidade) {
				return $value['Entidade'] === $entidade;
			}
		);
	}

	public function getEntidadeSubEntidade(string $paymentMethod): array
	{
		$list = null;
		if ($paymentMethod === self::MULTIBANCO) {
			$list = array_filter(
				array_column($this->account, 'Entidade'),
				function ($value) {
					return is_numeric($value) || $value === Multibanco::DYNAMIC_MB_ENTIDADE;
				}
			);
		} else {
			$list = [];
			foreach (array_column($this->account, 'SubEntidade', 'Entidade') as $key => $value) {
				if ($key === strtoupper($paymentMethod)) {
					$list[] = $value;
				}
			}
		}
		return $list;
	}

	public function checkDynamicMb(array $userAccount): bool
	{
		$multibancoDynamicKey = array_filter(
			array_column($userAccount, 'Entidade'),
			function ($value) {
				return $value === Multibanco::DYNAMIC_MB_ENTIDADE;
			}
		);
		if ($multibancoDynamicKey) {
			return true;
		}
		return false;
	}


	public function execute(string $paymentMethod, GatewayDataBuilder $data, string $orderId, string $valor): DataBuilder
	{
		$paymentMethod = $this->paymentFactory
			->setType($paymentMethod)
			->setData($data)
			->setOrderId($orderId)
			->setValor($valor)
			->build();
		return $paymentMethod->buy();
	}
}
