<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility;


if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface MailInterface
{
    public function setMessage(): void;
    public function sendEmail(): void;     
    public function setSubject(string $subject): self; 
    public function setMessageBody(string $messageBody): self;
    public function setPaymentMethod(string $paymentMethod): self;
    public function setRouterRequestAction(string $routerRequestAction): self;
}