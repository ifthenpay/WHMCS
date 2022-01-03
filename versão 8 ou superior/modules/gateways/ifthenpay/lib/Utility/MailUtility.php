<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Utility;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Module\Mail;
use WHMCS\Mail\Message;
use WHMCS\Config\Setting;
use WHMCS\Module\GatewaySetting;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\MailInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\TokenInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Utility\UtilityInterface;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\AdminRepositoryInterface;

class MailUtility implements MailInterface
{
   private $message;
   private $subject;
   private $messageBody;
   private $utility;
   private $paymentMethod;
   private $token;
   private $adminRepository;
   private $routerRequestAction;

	public function __construct(
        Message $message, 
        UtilityInterface $utility, 
        TokenInterface $token, 
        AdminRepositoryInterface $adminRepository
    )
	{
        $this->message = $message;        
        $this->utility = $utility;
        $this->token = $token;
        $this->adminRepository = $adminRepository;
	}

    private function setUpdateUserAccountUrl(): string
    {
        return $this->utility->getSystemUrl() . 
            'modules/gateways/ifthenpay/server/updateUserAccount.php?action=updateUserAccount&paymentMethod=' . 
            $this->paymentMethod . '&userToken=' . $this->token->saveUserToken($this->paymentMethod, $this->routerRequestAction);
    }

    private function setDefaultMessageBody(): array
    {
        return $this->messageBody = [
            "backofficeKey: " . GatewaySetting::getForGateway($this->paymentMethod)['backofficeKey'] .  "\n\n",
            "Email Cliente: " .  $this->adminRepository->getAdminEmail() . "\n\n",
            "Update User Account: " .  $this->setUpdateUserAccountUrl() . "\n\n",
            "Pedido enviado automaticamente pelo sistema WHMCS da loja [" . Setting::getValue("CompanyName") . "]"
        ];
    }  

    public function setMessage(): void
    {
        $this->message->setType('admin');
        $this->message->setSubject($this->subject);
        $this->message->addRecipient("to", 'ricardocarvalho@ifthenpay.com', 'Ifthenpay');
        $this->message->setBodyAndPlainText(implode(" ", $this->messageBody));
    }

    public function sendEmail(): void
    {
        $this->setMessage();
        Mail::factory()->send($this->message);
    }
    
   public function setSubject(string $subject): self
   {
      $this->subject = $subject;

      return $this;
   }

    public function setMessageBody(string $messageBody): self
    {
        array_unshift($this->setDefaultMessageBody(), $messageBody);
        return $this;
    }

    public function setPaymentMethod(string $paymentMethod): self
    {
       $this->paymentMethod = $paymentMethod;
       return $this;
    }

   /**
    * Set the value of routerRequestAction
    *
    * @return  self
    */ 
   public function setRouterRequestAction(string $routerRequestAction): self
   {
      $this->routerRequestAction = $routerRequestAction;

      return $this;
   }
}
