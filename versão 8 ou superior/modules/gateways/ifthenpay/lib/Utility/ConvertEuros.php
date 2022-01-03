<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Utility;

use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\ConvertEurosInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\CurrencieRepositoryInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}


class ConvertEuros implements ConvertEurosInterface {

    private $currencieRepository;
    
    public function __construct(CurrencieRepositoryInterface $currencieRepository)
	{
        $this->currencieRepository = $currencieRepository;
	}

    public function execute(string $currencyCode, $totalToPay)
    {
        if ($currencyCode !== 'EUR') {
            return convertCurrency($totalToPay, $currencyCode, $this->currencieRepository->getCurrencieByCode('EUR')['id']);
        } else {
            return $totalToPay;
        }
    }
}
