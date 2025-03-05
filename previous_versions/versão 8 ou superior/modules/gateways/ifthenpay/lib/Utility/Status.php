<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Utility;

use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\StatusInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}


class Status implements StatusInterface {
    
    private $statusSucess = "d0749aaba8b833466dfcbb0428e4f89c";
    private $statusError = "bb1ca97ec761fc37101737ba0aa2e7c5";
    private $statusCancel = "9f935beb31030ad0d4d26126c0f39bf2";

    public function getTokenStatus(string $token): string
    {
        switch ($token) {
            case $this->statusSucess:
                return 'success';
            case $this->statusCancel:
                return 'cancel';
            case $this->statusError:
                return 'error';
            default:
                return '';
        }
    }

    /**
     * Get the value of statusSucess
     */ 
    public function getStatusSucess(): string
    {
        return $this->statusSucess;
    }

    /**
     * Get the value of statusError
     */ 
    public function getStatusError(): string
    {
        return $this->statusError;
    }

    /**
     * Get the value of statusCancel
     */ 
    public function getStatusCancel(): string
    {
        return $this->statusCancel;
    }
}