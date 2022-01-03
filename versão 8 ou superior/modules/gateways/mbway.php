<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Session;
use WHMCS\Module\Gateway\Ifthenpay\Config\Ifthenpay;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Facades\PaymentFacade;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\MixInterface;
use WHMCS\Module\Gateway\Ifthenpay\Exceptions\BackOfficeException;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\TokenInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\UtilityInterface;
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
        $ioc = new Ifthenpay(Gateway::MBWAY);
        $ifthenpayLogger = $ioc->getIoc()->make(IfthenpayLogger::class);
        $ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_BACKOFFICE_CONFIG_MBWAY)->getLogger();
       return $ioc->getIoc()->make(IfthenpayConfigForms::class)->buildForm();
    } catch (\Throwable $th) {
        if($th instanceof BackOfficeException) {
            $ioc->getIoc()->make(ConfigGatewaysRepositoryInterface::class)->deleteWhere(['gateway' => Gateway::MBWAY, 'setting' => 'backofficeKey']);
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
        $ifthenpayContainer = (new Ifthenpay(Gateway::MBWAY))->getIoc();
        $ifthenpayLogger = $ifthenpayContainer->make(IfthenpayLogger::class);
        $ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_PAYMENTS)->getLogger();
        $utility = $ifthenpayContainer->make(UtilityInterface::class);
        $mix = $ifthenpayContainer->make(MixInterface::class);
        $systemUrl = $utility->getSystemUrl();
        $ifthenpayData = [
            'systemUrl' => $systemUrl,
            'lang' => [
                'mbwayPhoneRequired' => Lang::trans('mbwayPhoneRequired'),
                'mbwayPhoneInvalid' => Lang::trans('mbwayPhoneInvalid'),
            ]
        ];
        $token = $ifthenpayContainer->make(TokenInterface::class);
        $orderId = $params['invoiceid'];
        $ifthenpayData['cancelMbwayOrderUrl'] = $ifthenpayData['systemUrl'] . '
            modules/gateways/ifthenpay/server/cancelMbwayOrder.php?action=cancelMbwayOrder&userToken=' . 
            $token->saveUserToken(Gateway::MBWAY, 'cancelMbwayOrder') . '&paymentMethod=' . Gateway::MBWAY . '&orderId=' . $orderId;
        $ifthenpayData['cancelMbwayOrderUrl'] = preg_replace('/\s+/', '', $ifthenpayData['cancelMbwayOrderUrl']);
        $ifthenpayData['orderId'] = $orderId;
        $ifthenpayData = json_encode($ifthenpayData);
        $ifthenpayContainer->make(PaymentFacade::class)->setPaymentMethod(Gateway::MBWAY)->setParams($params);
        $code = '';
        if ($_GET['id']) {
            $code .= '<link rel="stylesheet" href="'. $utility->getCssUrl() . '/' . $mix->create('ifthenpayViewInvoice.css') . '">
                <script type="text/javascript">var ifthenpayData='. $ifthenpayData . '</script>
                <script src="'. $utility->getJsUrl() . '/' . $mix->create('invoiceViewPage.js') . '" type="text/javascript"></script>
                <form action="viewinvoice.php?id='. $orderId . '" method="POST" id="formIfthenpayMbway"><div class="field required" id="ifthenpayMbwayPhoneDiv">
                    <div class="control input-container">
                    <img src="'. $systemUrl .'/modules/gateways/ifthenpay/svg/mbway.svg" class="icon" alt="mbway logo">
                    <input name="mbwayPhoneNumber" class="text input-field" type="number" id="phone_number" placeholder="'. \Lang::trans('mbwayPhoneNumber') .'"  required>
                    <input type="submit" value="' . $params['langpaynow'] . '" id="mbwayPhoneBtnSubmit" class="btn btn-danger">
                </div></div></form>';
        }
        
        if (isset($_POST['mbwayPhoneNumber'])) {
            $ifthenpayContainer->make(PaymentFacade::class)->setPaymentMethod(Gateway::MBWAY)->setParams($params)->execute();
            $code = '<link rel="stylesheet" href="'. $utility->getCssUrl() . '/' . $mix->create('ifthenpayViewInvoice.css') . '">
            <script type="text/javascript">var ifthenpayData='. $ifthenpayData . '</script>
            <script src="'. $utility->getJsUrl() . '/' . $mix->create('invoiceViewPage.js') . '" type="text/javascript"></script>';
        }
        return $code;
    } catch (\Throwable $th) {
        logTransaction(Gateway::MBWAY, $th->getMessage(), "Error", $params);
        $ifthenpayLogger->error('error processing payment - ' . $th->getMessage(), [
                'paymentMethod' => Gateway::MBWAY,
                'requestPost' => $_POST,
                'cookie' => $_COOKIE,
                'params' => $params,
                'exception' => $th
            ]
        );
        return '<div class=\"alert alert-danger\">' . $th->getMessage() . '</div>';
    }
}




