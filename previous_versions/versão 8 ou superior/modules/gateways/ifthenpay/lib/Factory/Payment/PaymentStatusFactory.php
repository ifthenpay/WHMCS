<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Factory\Payment;

use Illuminate\Container\Container;
use WHMCS\Module\Gateway\Ifthenpay\Factory\Factory;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Request\WebService;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Payments\CCardPaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Payments\MbWayPaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Payments\PayshopPaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Payments\MultibancoPaymentStatus;

use WHMCS\Module\Gateway\Ifthenpay\Contracts\Payments\PaymentStatusInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class PaymentStatusFactory extends Factory
{
    private $webService;
    private $ifthenpayLogger;

    public function __construct(
        Container $ioc, 
        WebService $webService, 
        IfthenpayLogger $ifthenpayLogger
    )
	{
        parent::__construct($ioc);
        $this->webService = $webService;
        $this->ifthenpayLogger = $ifthenpayLogger;
    }

    public function build(): PaymentStatusInterface {
        switch ($this->type) {
            case Gateway::MULTIBANCO:
                return new MultibancoPaymentStatus(
                    $this->webService,
                    $this->ifthenpayLogger
                );
            case Gateway::MBWAY:
                return new MbWayPaymentStatus(
                    $this->webService,
                    $this->ifthenpayLogger
                );
            case Gateway::PAYSHOP:
                return new PayshopPaymentStatus(
                    $this->webService,
                    $this->ifthenpayLogger
                );
            case Gateway::CCARD:
                return new CCardPaymentStatus(
                    $this->webService,
                    $this->ifthenpayLogger
                );
            default:
                throw new \Exception('Unknown Payment Change Status Class');
        }
    }
}
