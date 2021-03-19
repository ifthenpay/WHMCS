<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\Ifthenpay\Config\Ifthenpay;


/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related capabilities and
 * settings.
 *
 * @see https://developers.whmcs.com/payment-gateways/meta-data-params/
 *
 * @return array
 */
function ccard_MetaData()
{
    return array(
        'DisplayName' => 'Cartão de Crédito (Ifthenpay)',
        'APIVersion' => '1.1', // Use API Version 1.1
    );
}

/**
 * Define gateway configuration options.
 *
 * The fields you define here determine the configuration options that are
 * presented to administrator users when activating and configuring your
 * payment gateway module for use.
 *
 * Supported field types include:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each field type and their possible configuration parameters are
 * provided in the sample function below.
 *
 * @return array
 */

function ccard_config()
{
    try {
        return (new Ifthenpay('ccard'))->getConfigForm();
    } catch (\Throwable $th) {
        throw $th;
    }
}
function ccard_link($params) {
    try {
        $paymentData = (new Ifthenpay('ccard'))->getPaymentData($params);
            
        if (is_array($paymentData) && $paymentData['status'] === 'pending') {
            header('Location: ' . $paymentData['paymentUrl']);
                  
        } else if (!is_array($paymentData) && $paymentData->getPaymentGatewayResultData()->status === '0') {
            header('Location: ' . $paymentData->getPaymentGatewayResultData()->paymentUrl);  
        } else {
            return $paymentData;
        }
    } catch (\Throwable $th) {
        return $th->getMessage();
    }
}




