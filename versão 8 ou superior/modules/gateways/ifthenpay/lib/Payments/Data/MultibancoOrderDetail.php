<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Payments\Data;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Base\Payments\MultibancoBase;
use WHMCS\Module\Gateway\Ifthenpay\Traits\Payments\FormatReference;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Order\OrderDetailInterface;

class MultibancoOrderDetail extends MultibancoBase implements OrderDetailInterface
{
    use FormatReference;
    
    public function setSmartyVariables(): void
    {
        $this->smartyDefaultData->setPaymentMethod($this->ifthenpayGateway->getAliasPaymentMethods(
            $this->paymentMethod, 'en'));
        $this->smartyDefaultData->setEntidade($this->paymentDataFromDb['entidade']);
        $this->smartyDefaultData->setReferencia($this->formatReference($this->paymentDataFromDb['referencia']));
        $this->smartyDefaultData->setEntityMultibancoLang(\Lang::trans('entityMultibanco'));
        $this->smartyDefaultData->setIfthenpayReferenceLang(\Lang::trans('ifthenpayReference'));
        $this->logSmartyBuilderData();
    }

    public function getOrderDetail(): OrderDetailInterface
    {
        $this->setPaymentTable('ifthenpay_multibanco');
        $this->getFromDatabaseById();
        $this->setSmartyVariables();
        return $this;
    }
}
