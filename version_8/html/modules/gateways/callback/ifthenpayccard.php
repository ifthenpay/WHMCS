<?php

use WHMCS\Module\Gateway\ifthenpaylib\Services\CcardService;


// Require libraries needed for gateway module functions.
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';


// Ccard does not have a callback since it expects to validate payment on return to shop page
// Thus, the return and validation of payment is handled here

try {
	CcardService::handleCallback($_GET);
	redirSystemURL("id=" . $_GET['id'] . "&paymentsuccess=true", "viewinvoice.php");
} catch (\Throwable $th) {
	redirSystemURL("id=" . $_GET['id'] . "&paymentfailed=true", "viewinvoice.php");
}
