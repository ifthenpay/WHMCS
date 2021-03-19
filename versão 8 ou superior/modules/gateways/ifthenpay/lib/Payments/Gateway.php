<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\ifthenpay\Builders\DataBuilder;
use WHMCS\Module\Gateway\ifthenpay\Factory\Payment\PaymentFactory;
use WHMCS\Module\Gateway\ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Request\WebService;

class Gateway
{
    private $webservice;
    private $paymentFactory;
    private $account;
    private $paymentMethods = ['multibanco', 'mbway', 'payshop', 'ccard'];
    private $aliasPaymentMethods = [
        'multibanco' => [
            'en' => 'Multibanco',
            'pt' => 'Multibanco',
        ],
        'mbway' => [
            'en' => 'MB WAY',
            'pt' => 'MB WAY',
        ],
        'payshop' => [
            'en' => 'Payshop',
            'pt' => 'Payshop',
        ],
        'ccard' => [
            'en' => 'Credit Card',
            'pt' => 'Cartão de Crédito',
        ],
        
    ];

    public function __construct(WebService $webservice, PaymentFactory $paymentFactory)
    {
        $this->webservice = $webservice;
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

    public function checkIfthenpayPaymentMethod(string $paymentMethod): bool
    {
        if (in_array(strtolower($paymentMethod), $this->paymentMethods)) {
            return true;
        }
        return false;
    }

    public function authenticate(string $backofficeKey): void
    {
            $authenticate = $this->webservice->postRequest(
                'https://www.ifthenpay.com/IfmbWS/ifmbws.asmx/' .
                'getEntidadeSubentidadeJsonV2',
                [
                   'chavebackoffice' => $backofficeKey,
                ]
            )->getResponseJson();

        if (!$authenticate[0]['Entidade'] && empty($authenticate[0]['SubEntidade'])) {
            throw new \Exception('Backoffice key is invalid');
        } else {
            $this->account = $authenticate;
        }
    }

    public function getAccount(string $paymentMethod): array
    {
        return array_filter(
            $this->account,
            function ($value) use ($paymentMethod) {
                if($paymentMethod === 'multibanco' && is_numeric($value['Entidade'])) {
                    return $value;
                } elseif ($paymentMethod === 'mbway') {
                    return $value['Entidade'] === strtoupper($paymentMethod);
                } elseif ($paymentMethod === 'payshop') {
                    return $value['Entidade'] === strtoupper($paymentMethod);
                } elseif ($paymentMethod === 'ccard') {
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
        if ($paymentMethod === 'multibanco') {
            $list = array_filter(
                array_column($this->account, 'Entidade'),
                function ($value) {
                    return is_numeric($value);
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
