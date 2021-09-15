<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\GatewaySetting;
use WHMCS\Module\Gateway\ifthenpay\Utility\Mix;
use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;
use WHMCS\Module\Gateway\Ifthenpay\Config\Ifthenpay;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\ifthenpay\Utility\TokenExtra;
use WHMCS\Module\Gateway\Ifthenpay\Log\IfthenpayLogger;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Hooks\HooksStrategy;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Data\MbwayCancelOrder;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment\IfthenpayInvoiceUpdate;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment\IfthenpayPaymentStatus;
use WHMCS\Module\Gateway\Ifthenpay\Exceptions\IfthenpayInvoiceUpdateException;

$ifthenpayModuleApp = new Ifthenpay();
$utility = $ifthenpayModuleApp->getIoc()->make(Utility::class);
$gateway = $ifthenpayModuleApp->getIoc()->make(Gateway::class);
$mix = $ifthenpayModuleApp->getIoc()->make(Mix::class);
$hooksStrategy = $ifthenpayModuleApp->getIoc()->make(HooksStrategy::class);
$ifthenpayLogger = $ifthenpayModuleApp->getIoc()->make(IfthenpayLogger::class);
$ifthenpayLogger = $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_HOOKS)->getLogger();
$systemUrl = $utility->getSystemUrl();
$ifthenpayData = [
    'systemUrl' => $systemUrl,
    'lang' => [
        'mbwayPhoneRequired' => Lang::trans('mbwayPhoneRequired'),
        'mbwayPhoneInvalid' => Lang::trans('mbwayPhoneInvalid'),
        'creditCardInvalidMethod' => ADMINLANG::trans('creditCardInvalidMethod')
    ]
];
add_hook('AdminAreaHeadOutput', 1, function($vars) use ($utility, $mix, $ifthenpayLogger) {
    try {
        if ($vars['filename'] === 'configgateways') {
            $ifthenpayLogger->info('add ifthenpayPaymentMethodSetup.css to header', ['hook' => 'AdminAreaHeadOutput']);
            return '<link rel="stylesheet" href="'. $utility->getCssUrl() . '/' . $mix->create('ifthenpayPaymentMethodSetup.css') . '">';
        } else if ($vars['filename'] === 'invoices' || $vars['filename'] === 'ordersadd') {
            $ifthenpayLogger->info('add mbwayPhoneInput.css to header', ['hook' => 'AdminAreaHeadOutput']);
            return '<link rel="stylesheet" href="'. $utility->getCssUrl() . '/' . $mix->create('mbwayPhoneInput.css') . '">';
        }
    } catch (\Throwable $th) {
        $ifthenpayLogger->warning($th->getMessage(), [
                'hook' => 'AdminAreaHeadOutput',
                'vars' => $vars,
                'exception' => $th
            ]
        );
    }
});
add_hook('AdminAreaFooterOutput', 1, function($vars) use ($utility, $systemUrl, $mix, $ifthenpayData, $ifthenpayLogger) {
    try {
        $ifthenpayData = json_encode($ifthenpayData);
        if ($vars['filename'] === 'configgateways') {
            $ifthenpayLogger->info('add adminConfigPage.js to footer', ['hook' => 'AdminAreaFooterOutput']);
            return '<script type="text/javascript">var ifthenpayData = '. $ifthenpayData .'</script> 
                <script type="text/javascript" src="'. $utility->getJsUrl() . '/' . $mix->create('adminConfigPage.js') . '"></script>';
        } else if ($vars['filename'] === 'invoices') {
            $ifthenpayLogger->info('add adminInvoicePage.js to footer', ['hook' => 'AdminAreaFooterOutput']);
            return '<script type="text/javascript">var ifthenpayData = '. $ifthenpayData .'</script>
                <script type="text/javascript" src="'. $utility->getJsUrl() . '/' . $mix->create('adminInvoicePage.js') . '"></script>';
        } else if ($vars['filename'] === 'ordersadd') {
            $ifthenpayLogger->info('add adminOrderAddPage.js to footer', ['hook' => 'AdminAreaFooterOutput']);
            return '<script type="text/javascript">var ifthenpayData = '. $ifthenpayData .'</script>
                <script type="text/javascript" src="'. $utility->getJsUrl() . '/' . $mix->create('adminOrderAddPage.js') . '"></script>';
        } else {
            return '';
        }
    } catch (\Throwable $th) {
        $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_HOOKS)->getLogger()->warning($th->getMessage(), [
                'ifthenpayData' => $ifthenpayData,
                'hook' => 'AdminAreaFooterOutput',
                'vars' => $vars,
                'exception' => $th
            ]
        );
    }   
});

add_hook('ShoppingCartCheckoutCompletePage', 1, function($vars) use ($gateway, $hooksStrategy, $ifthenpayLogger) {
    try {
        if ($gateway->checkIfthenpayPaymentMethod($vars['paymentmethod'])) {
            return $hooksStrategy->execute('clientCheckoutConfirmHook', $vars)->execute();
        }
    } catch (\Throwable $th) {
        $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_HOOKS)->getLogger()->warning($th->getMessage(), [
                'paymentMethod' => $vars['paymentMethod'],
                'hook' => 'ShoppingCartCheckoutCompletePage',
                'vars' => $vars,
                'exception' => $th
            ]
        );
    }
});

add_hook('ClientAreaHeaderOutput', 1, function($vars) use ($hooksStrategy, $utility, $mix, $ifthenpayLogger) {
    try {
        if ($vars['filename'] === 'clientarea' && $_REQUEST['action'] === 'masspay' && $_REQUEST['all'] === 'true') {
            $ifthenpayLogger->info('add ifthenpayViewInvoice.css and mbwayPhoneInput.css to header', ['hook' => 'ClientAreaHeaderOutput']);
            return '<link rel="stylesheet" href="'. $utility->getCssUrl() . '/' . $mix->create('ifthenpayViewInvoice.css') . '">
            <link rel="stylesheet" href="'. $utility->getCssUrl() . '/' . $mix->create('mbwayPhoneInput.css') . '">';
        }
        if ($vars['filename'] === 'clientarea' && $_REQUEST['action'] === 'addfunds') {
            $ifthenpayLogger->info('add ifthenpayViewInvoice.css and mbwayPhoneInput.css to header', ['hook' => 'ClientAreaHeaderOutput']);
            return '<link rel="stylesheet" href="'. $utility->getCssUrl() . '/' . $mix->create('ifthenpayViewInvoice.css') . '">
            <link rel="stylesheet" href="'. $utility->getCssUrl() . '/' . $mix->create('mbwayPhoneInput.css') . '">';
        }
        return $hooksStrategy->execute('clientCheckoutHook', $vars)->executeStyles();
    } catch (\Throwable $th) {
        $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_HOOKS)->getLogger()->warning($th->getMessage(), [
                'paymentMethod' => $vars['paymentMethod'],
                'hook' => 'ClientAreaHeaderOutput',
                'vars' => $vars,
                'exception' => $th
            ]
        );
    }
});

add_hook('ClientAreaFooterOutput', 1, function($vars) use ($utility, $mix, $ifthenpayModuleApp, $ifthenpayData, $ifthenpayLogger) {
    try {
        //$systemUrl = $utility->getSystemUrl();
        $tokenExtra = $ifthenpayModuleApp->getIoc()->make(TokenExtra::class);
        $orderId = $_SESSION["orderdetails"]["InvoiceID"];
        $ifthenpayData['cancelMbwayOrderUrl'] = $ifthenpayData['systemUrl'] . 'modules/gateways/ifthenpay/server/cancelMbwayOrder.php?action=cancelMbwayOrder&sk=' . 
            $tokenExtra->encript($orderId . 'cancelMbwayOrder', GatewaySetting::getForGateway('mbway')['mbwayKey']);
        $ifthenpayData['orderId'] = $orderId;
        if ($vars['filename'] === 'cart' && $_REQUEST['a'] === 'checkout') {
            $ifthenpayLogger->info('add checkoutPage.js and mbwayPhoneInput.css to footer', ['hook' => 'ClientAreaFooterOutput']);
            return '<link rel="stylesheet" href="'. $utility->getCssUrl() . '/' . $mix->create('mbwayPhoneInput.css') . '">
                <script type="text/javascript">var ifthenpayData='. json_encode($ifthenpayData) . '</script>
                <script src="'. $utility->getJsUrl() . '/' . $mix->create('checkoutPage.js') . '" type="text/javascript"></script>';
        }
        if ($vars['filename'] === 'cart' && $_REQUEST['a'] === 'complete') {
            $ifthenpayLogger->info('add mbwayCountdownConfirmPage.js to footer', ['hook' => 'ClientAreaFooterOutput']);
            return '<script type="text/javascript">var ifthenpayData='. json_encode($ifthenpayData) . '</script>
                <script src="'. $utility->getJsUrl() . '/' . $mix->create('mbwayCountdownConfirmPage.js') . '" type="text/javascript"></script>';
        }

        if ($vars['filename'] === 'clientarea' && $_REQUEST['action'] === 'masspay' && $_REQUEST['all'] === 'true') {
            $ifthenpayLogger->info('add invoiceViewPage.js to footer', ['hook' => 'ClientAreaFooterOutput']);
            return  '<script type="text/javascript">var ifthenpayData='. json_encode($ifthenpayData) . '</script>
            <script src="'. $utility->getJsUrl() . '/' . $mix->create('invoiceViewPage.js') . '" type="text/javascript"></script>';
        }
        if ($vars['filename'] === 'clientarea' && $_REQUEST['action'] === 'addfunds') {
            $ifthenpayLogger->info('add addFundsPage.js to footer', ['hook' => 'ClientAreaFooterOutput']);
            return  '<script type="text/javascript">var ifthenpayData='. json_encode($ifthenpayData) . '</script>
            <script src="'. $utility->getJsUrl() . '/' . $mix->create('addFundsPage.js') . '" type="text/javascript"></script>';
        }
    } catch (\Throwable $th) {
        $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_HOOKS)->getLogger()->warning($th->getMessage(), [
                'hook' => 'ClientAreaFooterOutput',
                'vars' => $vars,
                'exception' => $th
            ]
        );
    }
 });

add_hook('ClientAreaPageCart', 1, function($vars) use ($hooksStrategy, $ifthenpayLogger) {
    try {
        return $hooksStrategy->execute('clientCheckoutHook', $vars)->execute();
    } catch (\Throwable $th) {
        $ifthenpayLogger->setChannel($ifthenpayLogger::CHANNEL_HOOKS)->getLogger()->warning($th->getMessage(), [
                'hook' => 'ClientAreaPageCart',
                'vars' => $vars,
                'exception' => $th
            ]
        );
    }
});

add_hook('ClientAreaPageViewInvoice', 1, function($vars) use ($hooksStrategy, $gateway, $utility, $mix, $ifthenpayData, $ifthenpayLogger, $ifthenpayModuleApp) {
    try {
        if ($gateway->checkIfthenpayPaymentMethod($vars['paymentmethod'])) {
            $tokenExtra = $ifthenpayModuleApp->getIoc()->make(TokenExtra::class);
            $orderId = $vars['invoiceid'];
            $ifthenpayData['cancelMbwayOrderUrl'] = $ifthenpayData['systemUrl'] . 'modules/gateways/ifthenpay/server/cancelMbwayOrder.php?action=cancelMbwayOrder&sk=' . 
                $tokenExtra->encript($orderId . 'cancelMbwayOrder', GatewaySetting::getForGateway('mbway')['mbwayKey']);
            $ifthenpayData['orderId'] = $orderId;
            $vars['notes'] .='<link rel="stylesheet" href="'. $utility->getCssUrl() . '/' . $mix->create('ifthenpayViewInvoice.css') . '">
            <script type="text/javascript">var ifthenpayData='. json_encode($ifthenpayData) . '</script>
            <script src="'. $utility->getJsUrl() . '/' . $mix->create('invoiceViewPage.js') . '" type="text/javascript"></script>';
            $ifthenpayLogger->info('add ifthenpayViewInvoice.css and invoiceViewPage.js to $vars[notes]', ['hook' => 'ClientAreaPageViewInvoice']);
            $vars['notes'] .= $hooksStrategy->execute('clientCheckoutConfirmHook', $vars)->execute();
            return $vars;
        }
    } catch (\Throwable $th) {
        $ifthenpayLogger->warning($th->getMessage(), [
                'hook' => 'ClientAreaPageCart',
                'vars' => $vars,
                'exception' => $th
            ]
        );
    }
});

add_hook('DailyCronJob', 1, function($vars) use ($ifthenpayModuleApp, $ifthenpayLogger) {
    try {
        $ifthenpayModuleApp->setPaymentMethod('mbway');
        $ifthenpayModuleApp->getIoc()->make(MbwayCancelOrder::class)->cancelOrder();
        foreach (['multibanco', 'mbway', 'payshop'] as $paymentMethod) {
            if (getGatewayVariables($paymentMethod)["type"]) {
                $ifthenpayModuleApp->setPaymentMethod($paymentMethod);
                $ifthenpayPaymentStatus = $ifthenpayModuleApp->getIoc()->make(IfthenpayPaymentStatus::class);
                $ifthenpayPaymentStatus->setPaymentMethod($paymentMethod)->execute();
            }
        }
    } catch (\Throwable $th) {
        $ifthenpayLogger->warning($th->getMessage(), [
                'hook' => 'DailyCronJob',
                'vars' => $vars,
                'exception' => $th
            ]
        );
    }
});

function checkInvoiceType(string $invoiceType): bool
{
    define('INVOICE_CREATED', 'Invoice Created');
    define('INVOICE_PAYMENT_REMINDER', 'Invoice Payment Reminder');
    define('FIRST_INVOICE_OVERDUE_NOTICE', 'First Invoice Overdue Notice');
    define('SECOND_INVOICE_OVERDUE_NOTICE', 'Second Invoice Overdue Notice');
    define('THIRD_INVOICE_OVERDUE_NOTICE', 'Third Invoice Overdue Notice');
    define('INVOICE_MODIFIED', 'Invoice Modified');

    if ($invoiceType === INVOICE_CREATED) {
        return true;
    } else if ($invoiceType === INVOICE_PAYMENT_REMINDER) {
        return true;
    } else if ($invoiceType === FIRST_INVOICE_OVERDUE_NOTICE) {
        return true;
    } else if ($invoiceType === SECOND_INVOICE_OVERDUE_NOTICE ) {
        return true;
    } else if ($invoiceType === THIRD_INVOICE_OVERDUE_NOTICE) {
        return true;
    } else if ($invoiceType === INVOICE_MODIFIED) {
        return true;
    } else {
        return false;
    }
}

add_hook('EmailPreSend', 1, function($vars) use ($gateway, $hooksStrategy, $ifthenpayLogger) {
    try {
        $merge_fields = $vars['mergefields'];
        if (checkInvoiceType($vars['messagename']) && $gateway->checkIfthenpayPaymentMethod($merge_fields['invoice_payment_method'])) {
            if ($vars['messagename'] === INVOICE_CREATED || $vars['messagename'] === INVOICE_MODIFIED) {
                $merge_fields['invoice_html_contents'] .= $hooksStrategy->execute('emailPreSendHook', $merge_fields)->execute();
            } else if ($vars['messagename'] === INVOICE_PAYMENT_REMINDER || $vars['messagename'] === FIRST_INVOICE_OVERDUE_NOTICE || 
                $vars['messagename'] === SECOND_INVOICE_OVERDUE_NOTICE || $vars['messagename'] === THIRD_INVOICE_OVERDUE_NOTICE) {
                $merge_fields['invoice_payment_method'] = $merge_fields['invoice_payment_method'] . '<br>' . $hooksStrategy->execute('emailPreSendHook', $merge_fields)->execute();
            }
        }
        return $merge_fields;
    } catch (\Throwable $th) {
        $ifthenpayLogger->warning($th->getMessage(), [
                'hook' => 'EmailPreSend',
                'vars' => $vars,
                'exception' => $th
            ]
        );
    }
});

add_hook('UpdateInvoiceTotal', 1, function($vars) use ($ifthenpayModuleApp, $ifthenpayLogger) {
    try {
        if (strpos($_SERVER['REQUEST_URI'], 'invoices.php') !== false) {
            $ifthenpayModuleApp->getIoc()->make(IfthenpayInvoiceUpdate::class)->setParams($vars)->execute();
        }
    } catch (\Throwable $th) {
        if($th instanceof IfthenpayInvoiceUpdateException) {
            logActivity($th->getMessage() . ' - Invoice ID: ' . $vars['invoiceid']);
            $ifthenpayLogger->warning($th->getMessage(), [
                    'hook' => 'UpdateInvoiceTotal',
                    'vars' => $vars,
                    'exception' => $th
                ]
            );
        } else {
            $ifthenpayLogger->warning($th->getMessage(), [
                    'hook' => 'UpdateInvoiceTotal',
                    'vars' => $vars,
                    'exception' => $th
                ]
            );       
        }
    }
});