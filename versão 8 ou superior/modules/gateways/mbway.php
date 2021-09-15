<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Session;
use WHMCS\Module\Gateway\Ifthenpay\Config\Ifthenpay;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Facades\PaymentFacade;
use WHMCS\Module\Gateway\Ifthenpay\Exceptions\BackOfficeException;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Form\IfthenpayConfigForms;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\ConfigGatewaysRepositoryInterface;

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
function mbway_MetaData()
{
    return array(
        'DisplayName' => 'MB WAY',
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

function mbway_config()
{
    try {
        $ioc = new Ifthenpay('mbway');
        $ifthenpayLogger = $ioc->getIoc()->make(IfthenpayLogger::class);
        $ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_BACKOFFICE_CONFIG_MBWAY)->getLogger();
       return $ioc->getIoc()->make(IfthenpayConfigForms::class)->buildForm();
    } catch (\Throwable $th) {
        if($th instanceof BackOfficeException) {
            $ioc->getIoc()->make(ConfigGatewaysRepositoryInterface::class)->deleteWhere(['gateway' => 'mbway', 'setting' => 'backofficeKey']);
            $ifthenpayLogger->error($th->getMessage(), ['exception' => $th]);
            Session::setAndRelease("ConfigurationError", $th->getMessage());
            $redirect = "error=mbway#m_mbway";
            redir($redirect);
        }
        $ifthenpayLogger->alert($th->getMessage(), ['exception' => $th]);
    }
}
function mbway_link($params) {
    try {
        $ifthenpayContainer = (new Ifthenpay('mbway'))->getIoc();
        $ifthenpayLogger = $ifthenpayContainer->make(IfthenpayLogger::class);
        $ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_PAYMENTS)->getLogger();
        if (!$_POST['mbwayPhoneNumber'] && !$_COOKIE['mbwayPhoneNumber']) {
            throw new \Exception(Lang::trans('mbwayPhoneRequired'));
        } else {
            $ifthenpayContainer->make(PaymentFacade::class)->setPaymentMethod('mbway')->setParams($params)->execute();
        }
    } catch (\Throwable $th) {
        logTransaction('mbway', $th->getMessage(), "Error", $params);
        $ifthenpayLogger->error('error processing payment - ' . $th->getMessage(), [
                'paymentMethod' => 'mbway',
                'requestPost' => $_POST,
                'cookie' => $_COOKIE,
                'params' => $params,
                'exception' => $th
            ]
        );
        return '<div class=\"alert alert-danger\">' . $th->getMessage() . '</div>';
    }
}




