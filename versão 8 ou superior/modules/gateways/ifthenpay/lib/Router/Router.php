<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Router;

use WHMCS\Module\Gateway\ifthenpay\Utility\TokenExtra;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class Router 
{
    private $requestMethod;
    private $requestAction;
    private $requestData;
    private $tokenExtra;
    private $secretForTokenExtra;
    private $isFront;

	public function __construct(string $requestMethod, TokenExtra $tokenExtra = null, string $requestAction = null, array $requestData = null, bool $isFront = true)
	{
        $this->requestMethod = $requestMethod;
        $this->requestAction = $requestAction;
        $this->requestData = $requestData;
        $this->tokenExtra = $tokenExtra;
        $this->isFront = $isFront;
    }
    
    private function validateRequestMethod(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== strtoupper($this->requestMethod)) {
            die('request method not valid');
        }
    }

    private function validateRequestAction(): void
    {
        if (!is_null($this->requestData['action']) && $this->requestData['action'] !== $this->requestAction) {
            die('request action not valid');
        }
    }

    private function validateToken(): void
    {
        if (!is_null($this->requestData['action']) && $this->requestData['sk'] !== $this->tokenExtra->encript($this->requestData['orderId'] . $this->requestData['action'], $this->secretForTokenExtra)) {
            die('request token not valid');
        }
    }
    
    public function init(callable $function): void
    {
        $this->validateRequestMethod();
        $this->validateRequestAction();
        if ($this->isFront) {
            $this->validateToken();
        }
        call_user_func ( $function );
    }

    /**
     * Set the value of secretForTokenExtra
     *
     * @return  self
     */ 
    public function setSecretForTokenExtra($secretForTokenExtra)
    {
        $this->secretForTokenExtra = $secretForTokenExtra;

        return $this;
    }
}