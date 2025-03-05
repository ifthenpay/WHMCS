<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments;

use WHMCS\Module\Gateway\Ifthenpay\Request\WebService;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class PaymentStatus
{
    protected $data;
    protected $webService;
    protected $ifthenpayLogger;

    public function __construct(WebService $webService, IfthenpayLogger $ifthenpayLogger)
    {
        $this->webService = $webService;
        $this->ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_PAYMENTS)->getLogger();
    }

    /**
     * Set the value of data
     *
     * @return  self
     */
    public function setData(GatewayDataBuilder $data): PaymentStatus
    {
        $this->data = $data;
        $this->ifthenpayLogger->info('payment status data set with success', ['data' => $this->data, 'className' => get_class($this)]);
        return $this;
    }
}