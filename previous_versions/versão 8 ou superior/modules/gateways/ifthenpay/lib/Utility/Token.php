<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Utility;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\TokenInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ConfigGatewaysRepositoryInterface;

class Token implements TokenInterface {
    
	private $configGatewaysRepository;

    public function __construct(ConfigGatewaysRepositoryInterface $configGatewaysRepository)
	{
        $this->configGatewaysRepository = $configGatewaysRepository;
	}

    public function encrypt(string $input): string 
    {
        return urlencode(base64_encode( $input));
    }

    public function decrypt(string $input): string 
    {
        return base64_decode(urldecode($input));
    }

    public function saveUserToken(string $paymentMethod, string $action): string
    {
        $token = md5((string) rand());
        $this->configGatewaysRepository->createOrUpdate(
            ['gateway' => $paymentMethod, 'setting' => $action . 'UserToken'],
            ['value' => $token]
        );
        return $token;
    }
}