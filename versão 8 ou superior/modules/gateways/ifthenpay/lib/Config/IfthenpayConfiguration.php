<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Config;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Contracts\Config\InstallerInterface;

class IfthenpayConfiguration implements InstallerInterface
{
    private $configurationNames;
    private $userPaymentMethods;

    public function __construct(array $userPaymentMethods)
    {
        $this->userPaymentMethods = $userPaymentMethods;
        $this->configurationNames = [
            'IFTHENPAY_USER_PAYMENT_METHODS',
            'IFTHENPAY_BACKOFFICE_KEY',
            'IFTHENPAY_USER_PAYMENT_METHODS',
            'IFTHENPAY_USER_ACCOUNT',
            'IFTHENPAY_UPDATE_USER_ACCOUNT_TOKEN',
        ];
    }

    private function uninstallByPaymentMethod(): void
    {
        foreach ($this->userPaymentMethods as $paymentMethod) {
            if ($paymentMethod) {
                \Configuration::deleteByName('IFTHENPAY_' . $paymentMethod . '_OS_WAITING');
                \Configuration::deleteByName('IFTHENPAY_' . $paymentMethod . '_OS_CONFIRMED');
                \Configuration::deleteByName('IFTHENPAY_' . \Tools::strtoupper($paymentMethod));
                \Configuration::deleteByName('IFTHENPAY_ACTIVATE_NEW_' . \Tools::strtoupper($paymentMethod) .  '_ACCOUNT');
                \Configuration::deleteByName('IFTHENPAY_' . \Tools::strtoupper($paymentMethod) . '_URL_CALLBACK');
                \Configuration::deleteByName('IFTHENPAY_' . \Tools::strtoupper($paymentMethod) . '_CHAVE_ANTI_PHISHING');

                switch ($paymentMethod) {
                    case 'multibanco':
                        \Configuration::deleteByName('IFTHENPAY_MULTIBANCO_ENTIDADE');
                        \Configuration::deleteByName('IFTHENPAY_MULTIBANCO_SUBENTIDADE');
                        break;
                    case 'mbway':
                        \Configuration::deleteByName('IFTHENPAY_MBWAY_KEY');
                        break;
                    case 'payshop':
                        \Configuration::deleteByName('IFTHENPAY_PAYSHOP_KEY');
                        \Configuration::deleteByName('IFTHENPAY_PAYSHOP_VALIDADE');
                        break;
                    default:
                }
            }
        }
    }
    public function install(): void
    {
        //not need install
    }

    public function uninstall(): void
    {
        foreach ($this->configurationNames as $configurationName) {
            \Configuration::deleteByName($configurationName);
        }
        if ($this->userPaymentMethods) {
            $this->uninstallByPaymentMethod();
        }
    }
}
