<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Hooks;

use Smarty;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\ifthenpay\Hooks\CheckoutHook;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\MixInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\UtilityInterface;
use WHMCS\Module\Gateway\Ifthenpay\Strategy\Payment\IfthenpayOrderDetail;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class ClientCheckoutConfirmHook extends CheckoutHook
{
    private $paymentMethod;
    private $ifthenpayOrderDetail;

    public function __construct(UtilityInterface $utility, IfthenpayOrderDetail $ifthenpayOrderDetail, MixInterface $mix)
	{
        parent::__construct($utility, $mix);
        $this->ifthenpayOrderDetail = $ifthenpayOrderDetail;
    }
    
    public function validateTemplate(): bool
    {
        if ($this->vars['filename'] === 'cart' && $_REQUEST['a'] === 'complete') {
            return true;
        } else if ($this->vars['filename'] === 'viewinvoice' || $_REQUEST['a'] === 'complete') {
            return true;
        } else {
            return false;
        }

    }
    
    public function executeStyles(): string
    {
        return $this->validateTemplate() ? '<link rel="stylesheet" href="'. $this->utility->getCssUrl() . '/' . $this->mix->create('ifthenpayConfirmPage.css') . '">' : '';
    }

    public function execute()
    {
        if (($this->vars['action'] === 'complete' || $this->vars['filename'] === 'viewinvoice') && $this->vars['paymentmethod'] === $this->paymentMethod) {
            $ifthenpayOrderDetail = $this->ifthenpayOrderDetail->setParams($this->vars)->execute();
            if (($this->vars['paymentmethod'] === Gateway::CCARD || strtolower($this->vars['paymentmethod']) === Gateway::CCARD_ALIAS) && $ifthenpayOrderDetail->getPaymentDataFromDb()['status'] === 'pending') {
                $paymentUrl = $_SESSION['paymentUrl'];
                unset($_SESSION['paymentUrl']);
                header('Location: ' . $paymentUrl);
            } else {
                $smarty = new Smarty;
            
                $smarty->assign($ifthenpayOrderDetail->getSmartyVariables()->setStatus('ok')->toArray());

                if ($_GET['message']) {
                    $messageType = $_GET['messageType'] === 'success' ? 'alert-success' : 'alert-error';
                    return '<div class="alert ' . $messageType .' ">' . $_GET['message'] . '</div>';
                }

                return $this->executeStyles() . $smarty->fetch('file:' . ROOTDIR . '\modules\gateways\ifthenpay\templates\ifthenpayPaymentReturn.tpl');
            }
        } 
    }
    

    /**
     * Set the value of paymentMethod
     *
     * @return  self
     */ 
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }
}