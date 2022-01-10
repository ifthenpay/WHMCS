<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Router;

use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ConfigGatewaysRepositoryInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class Router 
{
    private $requestMethod;
    private $requestAction;
    private $prevRequestAction;
    private $requestData;
    private $isFront;
    private $configGatewaysRepository;
    private $isCallback;

	public function __construct(
        string $requestMethod,
        ConfigGatewaysRepositoryInterface $configGatewaysRepository = null, 
        string $requestAction = null,
        string $prevRequestAction = null,
        array $requestData = null, 
        bool $isFront = true,
        bool $isCallback = false
    )
	{
        $this->requestMethod = $requestMethod;
        $this->configGatewaysRepository = $configGatewaysRepository;
        $this->requestAction = $requestAction;
        $this->prevRequestAction = $prevRequestAction;
        $this->requestData = $requestData;
        $this->isFront = $isFront;
        $this->isCallback = $isCallback;
    }
    
    private function validateRequestMethod(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== strtoupper($this->requestMethod)) {
            throw new \Exception('request method not valid');
        }
    }

    private function validateRequestAction(): void
    {
        if (!is_null($this->requestData['action']) && $this->requestData['action'] !== $this->requestAction) {
            throw new \Exception('request action not valid');
        }
    }

    private function validateUserAccountToken(): void
    {
        $userAccountToken = $this->configGatewaysRepository->getUserToken($this->requestData['paymentMethod'], $this->prevRequestAction);
        if (!isset($this->requestData['userToken']) || (!is_null($userAccountToken) && 
        $this->requestData['userToken'] !== $userAccountToken)) {
            throw new \Exception('user token request not valid');
        }
    }
    
    public function init(callable $function): void
    {
        $this->validateRequestMethod();
        $this->validateRequestAction();
        if ($this->isFront && !$this->isCallback) {
            $this->validateUserAccountToken();
        }
        call_user_func ( $function );
    }
}