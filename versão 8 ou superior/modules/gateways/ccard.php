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
       $ioc = new Ifthenpay('ccard');
       $ifthenpayLogger = $ioc->getIoc()->make(IfthenpayLogger::class);
       $ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_BACKOFFICE_CONFIG_CCARD)->getLogger();
       return $ioc->getIoc()->make(IfthenpayConfigForms::class)->buildForm();
    } catch (\Throwable $th) {
        if($th instanceof BackOfficeException) {
            $ioc->getIoc()->make(ConfigGatewaysRepositoryInterface::class)->deleteWhere(['gateway' => 'ccard', 'setting' => 'backofficeKey']);
            $ifthenpayLogger->error($th->getMessage(), ['exception' => $th]);
            Session::setAndRelease("ConfigurationError", $th->getMessage());
            $redirect = "error=ccard#m_ccard";
            redir($redirect);
        }
        $ifthenpayLogger->alert($th->getMessage(), ['exception' => $th]);
    }
}
function ccard_link($params) {
    try {
        $ifthenpayContainer = (new Ifthenpay('ccard'))->getIoc();
        $ifthenpayLogger = $ifthenpayContainer->make(IfthenpayLogger::class);
        $ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_PAYMENTS)->getLogger();
        $paymentData = $ifthenpayContainer->make(PaymentFacade::class)->setPaymentMethod('ccard')->setParams($params)->execute();

        if (is_array($paymentData) && $paymentData['status'] === 'pending' && !strpos($_SERVER['REQUEST_URI'], 'admin/invoices')) {
            $ifthenpayLogger->info('redirect user to provider ccard page', [
                    'paymentMethod' => 'ccard',
                    'paymentData' => $paymentData
                ]
            );
            header('Location: ' . $paymentData['paymentUrl']);
                  
        } else if (!is_array($paymentData) && $paymentData->getPaymentGatewayResultData()->status === '0') {
            $ifthenpayLogger->info('redirect user to provider ccard page', [
                    'paymentMethod' => 'ccard',
                    'paymentGatewayResulData' => $paymentData->getPaymentGatewayResultData()
                ]
            );
            header('Location: ' . $paymentData->getPaymentGatewayResultData()->paymentUrl);  
        } else {
            $ifthenpayLogger->info('payment data retrieved with success', [
                    'paymentMethod' => 'ccard',
                    'paymentData' => $paymentData
                ]
            );
            return $paymentData;
        }
    } catch (\Throwable $th) {
        $ifthenpayLogger->error('error processing payment - ' . $th->getMessage(), [
                'paymentMethod' => 'ccard',
                'params' => $params,
                'exception' => $th
            ]
        );
        return '<div class=\"alert alert-danger\">' . $th->getMessage() . '</div>';
    }
}




