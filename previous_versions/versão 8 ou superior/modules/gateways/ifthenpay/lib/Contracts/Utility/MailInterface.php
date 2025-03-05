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
    public function setSubject(string $subject): MailInterface; 
    public function setMessageBody(string $messageBody): MailInterface;
    public function setPaymentMethod(string $paymentMethod): MailInterface;
    public function setRouterRequestAction(string $routerRequestAction): MailInterface;
}