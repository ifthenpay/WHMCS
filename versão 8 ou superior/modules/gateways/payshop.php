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
function payshop_MetaData()
{
    return array(
        'DisplayName' => 'Payshop',
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

function payshop_config()
{
    try {
        return (new Ifthenpay('payshop'))->getConfigForm();
    } catch (\Throwable $th) {
        throw $th;
    }
}
function payshop_link($params) {
    try {
        (new Ifthenpay('payshop'))->getPaymentData($params);
    } catch (\Throwable $th) {
        throw $th;
    }
}



