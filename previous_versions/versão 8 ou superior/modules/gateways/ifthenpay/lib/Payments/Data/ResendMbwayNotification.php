<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Data;

use WHMCS\Module\GatewaySetting;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Builders\GatewayDataBuilder;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\MbWayRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ConfigRepositoryInterface;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class ResendMbwayNotification
{
    private $request;
    private $configRepository;
    private $mbwayRepository;
    private $gatewayDataBuilder;
    private $gateway;
    private $systemUrl;
    private $ifthenpayLogger;
    
	public function __construct(
        ConfigRepositoryInterface $configRepository, 
        MbWayRepositoryInterface $mbwayRepository,
        GatewayDataBuilder $gatewayDataBuilder,
        Gateway $gateway,
        IfthenpayLogger $ifthenpayLogger
    )
	{
        $this->configRepository = $configRepository;
        $this->mbwayRepository = $mbwayRepository;
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->gateway = $gateway;
        $this->systemUrl = $this->configRepository->getSystemUrl();
        $this->ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_PAYMENTS)->getLogger();
	}
    

    public function execute(): void
    {
        $orderId = $this->request['orderId'];
        $totalToPay = $this->request['orderTotalPay'];
        $paymentData = $this->gatewayDataBuilder
            ->setMbwayKey(GatewaySetting::getForGateway(Gateway::MBWAY)['mbwayKey'])
            ->setTelemovel($this->request['mbwayTelemovel']);
        $this->ifthenpayLogger->info('mbway resend data set with success', ['gatewayDataBuilder' => $paymentData, 'className' => get_class($this)]);
        $gatewayResult = $this->gateway->execute(
            Gateway::MBWAY,
            $paymentData,
            strval($orderId),
            strval($totalToPay)
        )->getData();
        $this->ifthenpayLogger->info('mbway resend notification gateway executed with success', [
                'gatewayResult' => $gatewayResult, 
                'className' => get_class($this)
            ]
        );
        $this->mbwayRepository->updatePaymentIdPedido($orderId, $gatewayResult->idPedido);
        $this->ifthenpayLogger->info('mbway resend notification saved in database with success', [
                'orderId' => $orderId,
                'idPedido' => $gatewayResult->idPedido,
                'className' => get_class($this)
            ]
        );
    }

    /**
     * Set the value of request
     *
     * @return  self
     */ 
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get the value of systemUrl
     */ 
    public function getSystemUrl()
    {
        return $this->systemUrl;
    }
}
