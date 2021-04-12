<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\ifthenpay\Utility\Utility;
use WHMCS\Module\Gateway\Ifthenpay\Config\Ifthenpay;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;

$ifthenpayModuleApp = new Ifthenpay();
$utility = $ifthenpayModuleApp->getIoc()->make(Utility::class);
$gateway = $ifthenpayModuleApp->getIoc()->make(Gateway::class);
$systemUrl = $utility->getSystemUrl();
add_hook('AdminAreaHeadOutput', 1, function($vars) use ($utility) {
    return '<link rel="stylesheet" href="'. $utility->getCssUrl() . '/ifthenpayPaymentMethodSetup.css">';
});
add_hook('AdminAreaFooterOutput', 1, function($vars) use ($utility, $systemUrl) {
    
    $ifthenpayData = [
        'systemUrl' => $systemUrl,
    ];
    $ifthenpayData = json_encode($ifthenpayData);
    return '<script type="text/javascript">var ifthenpayData = '. $ifthenpayData .'</script>
        <script type="text/javascript" src="'. $utility->getJsUrl() . '/adminConfigPage.js"></script>';
});

add_hook('ShoppingCartCheckoutCompletePage', 1, function($vars) use ($ifthenpayModuleApp, $gateway) {
    if ($gateway->checkIfthenpayPaymentMethod($vars['paymentmethod'])) {
        return $ifthenpayModuleApp->setPaymentMethod($vars['paymentmethod'])->getHooks('clientCheckoutConfirmHook', $vars)->execute();
    }
});

add_hook('ClientAreaHeaderOutput', 1, function($vars) use ($ifthenpayModuleApp) {
    return $ifthenpayModuleApp->getHooks('clientCheckoutHook', $vars)->executeStyles();
});

add_hook('ClientAreaFooterOutput', 1, function($vars) use ($utility) {
    if ($vars['filename'] === 'cart' && $_REQUEST['a'] === 'checkout') {
        $systemUrl = $utility->getSystemUrl();
        return '<link rel="stylesheet" href="'. $utility->getCssUrl() . '/mbwayPhoneInput.css">
            <script type="text/javascript">var systemUrl="'. $systemUrl . '"</script>
            <script src="'. $utility->getJsUrl() . '/checkoutPage.js" type="text/javascript"></script>';
    }
 });

add_hook('ClientAreaPageCart', 1, function($vars) use ($ifthenpayModuleApp) {
    return $ifthenpayModuleApp->getHooks('clientCheckoutHook', $vars)->execute();
});

add_hook('ClientAreaPageViewInvoice', 1, function($vars) use ($ifthenpayModuleApp, $gateway, $utility) {
    if ($gateway->checkIfthenpayPaymentMethod($vars['paymentmethod'])) {
        $systemUrl = $utility->getSystemUrl();
        $vars['notes'] .='<link rel="stylesheet" href="'. $utility->getCssUrl() . '/ifthenpayViewInvoice.css">
        <script type="text/javascript">var systemUrl="'. $systemUrl . '"</script>
        <script src="'. $utility->getJsUrl() . '/invoiceViewPage.js" type="text/javascript"></script>';
        $vars['notes'] .= $ifthenpayModuleApp->setPaymentMethod($vars['paymentmethod'])->getHooks('clientCheckoutConfirmHook', $vars)->execute();
        return $vars;
    }
});