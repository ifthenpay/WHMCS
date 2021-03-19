<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Router;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class Router 
{
    private $requestMethod;
    private $requestAction;
    private $requestData;

	public function __construct(string $requestMethod, string $requestAction = null, array $requestData = null)
	{
        $this->requestMethod = $requestMethod;
        $this->requestAction = $requestAction;
        $this->requestData = $requestData;
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
    
    public function init(callable $function): void
    {
        $this->validateRequestMethod();
        $this->validateRequestAction();
        call_user_func ( $function );
    }
}