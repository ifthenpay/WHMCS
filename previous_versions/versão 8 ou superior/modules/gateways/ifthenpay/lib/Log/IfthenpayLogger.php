<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Log;

use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class IfthenpayLogger
{
    private $logger;
    private $channel;

    const CHANNEL_BACKOFFICE_CONFIG_MULTIBANCO = 'backofficeConfigMultibanco';
    const CHANNEL_BACKOFFICE_CONFIG_MBWAY = 'backofficeConfigMbway';
    const CHANNEL_BACKOFFICE_CONFIG_PAYSHOP = 'backofficeConfigPayshop';
    const CHANNEL_BACKOFFICE_CONFIG_CCARD = 'backofficeConfigCcard';
    const CHANNEL_PAYMENTS = 'payments';
    const CHANNEL_CALLBACK = 'callback';
    const CHANNEL_ASSETS = 'assets';
    const CHANNEL_HOOKS = 'hooks';

    public function getLogger()
    {
        if (!isset($this->logger)) {
            $this->logger = $this->createDefaultLogger();
        }
        return $this->logger;
    }

    protected function createDefaultLogger()
    {
        $dateFormat = "d-m-Y H:i:s";
        $this->logger = new Logger($this->channel);
        $handler = new RotatingFileHandler(__DIR__ . DIRECTORY_SEPARATOR . 'Logs' . DIRECTORY_SEPARATOR . $this->channel . DIRECTORY_SEPARATOR . $this->channel . '.log' , 365);
        $formatter = new LineFormatter(null, $dateFormat, true);
        $formatter->includeStacktraces(true);
        $handler->setFormatter($formatter);
        $this->logger->pushHandler($handler);
        return $this->logger;
    }
    

    /**
     * Set the value of channel
     *
     * @return  self
     */ 
    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    function getChannelBackofficeConst($paymentMethod) {
        return constant('self::CHANNEL_BACKOFFICE_CONFIG_' . strtoupper($paymentMethod));
    }
}
