# Attention
The following manual was created for version 1.3.0 of the ifthenpay extension, designed for WHMCS 8.


Download versions of the ifthenpay extension for WHMCS.
|                            | WHMCS 8                                                                                             |
|----------------------------|-----------------------------------------------------------------------------------------------------|
| Link to download installer | [ifthenpay v1.3.0](https://github.com/ifthenpay/WHMCS/releases/download/v1.3.0/ifthenpay.ocmod.zip) |

</br>
</br>

# Ifthenpay payment extension for WHMCS 8

Read in ![Portuguese](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/pt.png) [Portuguese](README.pt.md), and ![English](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/en.png) [English](README.md)

[1. Introduction](#introduction)

[2. Compatibility](#compatibility)

[3. Installation](#installation)

[4. Configuration](#configuration)
  * [Backoffice Key](#backoffice-key)
  * [Multibanco](#multibanco)
  * [Multibanco with Dynamic References](#multibanco-with-dynamic-references)
  * [MB WAY](#mb-way)
  * [Credit Card](#credit-card)
  * [Payshop](#payshop)

[5. Other](#other)
  * [Support](#support)
  * [Request additional account](#request-additional-account)
  * [Reset Configuration](#reset-configuration)
  * [Callback](#callback)
  * [Cronjob](#cronjob)


[6. Customer usage experience](#customer-usage-experience)
  * [Paying order with Multibanco](#paying-order-with-multibanco)
  * [Paying order with Payshop](#paying-order-with-payshop)
  * [Paying order with MB WAY](#paying-order-with-mb-way)
  * [Paying order with Credit Card](#paying-order-with-credit-card)


</br>

# Introduction
![ifthenpay](https://ifthenpay.com/images/all_payments_logo_final.png)

</br>

**This is the ifthenpay extension for the WHMCS ecommerce platform.**

**Multibanco** is a Portuguese payment method that allows the consumer to pay through a bank reference. This extension enables the generation of payment references that the consumer can use to pay for their order at an ATM (Multibanco) or through an online home banking service. This extension utilizes ifthenpay, one of the various available gateways in Portugal.

**MB WAY** is the first inter-bank solution that enables immediate purchases and transfers through smartphones and tablets. This extension allows generating a payment request on the consumer's smartphone, and they can authorize the payment for their order through the MB WAY application. This extension utilizes ifthenpay, one of the various available gateways in Portugal.

**Payshop** is a Portuguese payment method that allows the consumer to pay with a Payshop reference. This extension enables the generation of a payment reference that the consumer can use to pay for their order at a Payshop agent or CTT (Portuguese postal service). This extension uses ifthenpay, one of the various gateways available in Portugal.

**Credit Card** This extension allows generating a payment through Visa or MasterCard, which the consumer can use to pay for their order. This extension uses ifthenpay, one of the various gateways available in Portugal.

**Contract with Ifthenpay is required**

See more at [ifthenpay](https://ifthenpay.com). 

Membership at [Membership ifthenpay](https://www.ifthenpay.com/aderir/).

**Support**

For support, please create a support ticket at [Support ifthenpay](https://helpdesk.ifthenpay.com/).

</br>

# Compatibility

Use the table below to check the compatibility of the Ifthenpay extension with your online store:
|                           | WHMCS 8 [8.1.0 - 8.7.3] |
|---------------------------|-----------------------------|
| ifthenpay v1.3.0          | Compatible                  |

</br>


# Installation
Download the installation file of the ifthenpay module for WHMCS 8 from the GitHub page [ifthenpay-whmcs](https://github.com/ifthenpay/WHMCS/releases/tag/v1.3.0).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/download_installer.png)
</br>

Unzip the downloaded file, and paste the contents to the root of your online store.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/upload_ocmodzip.png)
</br>

After uploading the files, access the admin back office of your online store and go to Setup (1) -> Apps & Integrations (2) -> Browse (3) -> Payments (4).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/backoffice_payments.png)
</br>

Search for the ifthenpay module payment method that you wish to enable (e.g., Multibanco) and click on Activate (1).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/activate_method.png)
</br>


# Configuration

After installing the module and activating a payment method you will be redirected to its configuration page where you need to configure it using your ifthenpay account details.
The configuration page can also be accessed by selecting Payments -> Payment Gateways from the left navigation sidebar and selecting the active payment method you wish to configure, or by clicking Apss & Integrations -> selecting the Active tab and choosing the payment method you wish to configure. 

</br>


## Backoffice Key

Each payment method configuration requires entering the Backoffice Key to load the available accounts. The Backoffice Key is provided upon contract completion and consists of sets of four digits separated by an hyphen (-).
Below is an example for Multibanco, and this action is the same for other payment methods as well.
Enter the Backoffice Key (1) and click on Save (2). The page will reload, displaying the configuration form again, but with the available accounts loaded and additional configuration options.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/config_save_backofficekey.png)

</br>


## Multibanco

The Multibanco payment method generates references using an algorithm and is used if you don't want to set a time limit (in days) for orders paid with Multibanco.
The Entity and Sub-Entity are automatically loaded upon entering the Backoffice Key.
Configure the payment method. The image below shows an example of a minimally functional configuration.

1. **Show on Order Form** - When enabled, displays this payment method option at the checkout of your store.
2. **Display Name** - The payment method name that appears to the consumer during checkout.
3. **Sandbox Mode** - When enabled, prevents callback activation with server.
4. **Activate Callback** - When enabled, the order status will be updated when the payment is received.
5. **Show Payment Icon on Checkout** - (optional) When enabled, replaces the payment method Display Name presented in checkout with its respective icon.
6. **Cancel Multibanco Order** - (optional) When enabled, allows the order cancellation cron job to run for this specific method.
7. **Entity** - Select an Entity. You can only choose one of the Entities associated with the Backoffice Key.
8. **Sub-entity** - Select a Sub-Entity. You can only choose one of the Sub-Entities associated with the Entity chosen earlier.

Click on Save (9) to save the changes.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/config_multibanco.png)

</br>


## Multibanco with Dynamic References

The Multibanco payment method with Dynamic References generates references per order and is used if you wish to set a time limit (in days) for orders paid with Multibanco.
The Entity and Multibanco Key are automatically loaded upon entering the Backoffice Key.
Configure the payment method. The image below shows an example of a minimally functional configuration.

Follow the steps for configuring Multibanco (as indicated above) with the following change:

1. **Entity** - Select "Dynamic Multibanco References," this entity will only be available for selection if you've entered into a contract for the creation of a Dynamic Multibanco References account.
2. **Sub Entity** - Select a Sub Entity/Multibanco Key. You can only choose one of the Multibanco Keys associated with the Entity chosen earlier.
3. **Deadline** - Select the number of days the Multibanco reference will be valid. Leaving this field empty will mean that the Multibanco reference does not expire.


Examples of deadlines:
- Choosing a deadline of 0 days: If an order is created on 22/03/2023 at 15:30, the generated Multibanco reference will expire on 22/03/2023 at 23:59, which is the end of the day it was generated.
- Choosing a deadline of 1 day: If an order is created on 22/03/2023 at 9:30, the generated Multibanco reference will expire on 23/03/2023 at 23:59, which means the Multibanco reference will be valid for the day it was generated plus 1 day.
- Choosing a deadline of 3 days: If an order is created on 22/03/2023 at 20:30, the generated Multibanco reference will expire on 25/03/2023 at 23:59, which means the Multibanco reference will be valid for the day it was generated plus 3 days.

Click on Save (4) to save the changes.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/config_multibanco_dynamic.png)

</br>


## MB WAY

The MB WAY payment method utilizes a mobile phone number provided by the consumer and generates a payment request within the MB WAY smartphone application. The consumer can then accept or decline the payment.
The MB WAY Keys are automatically loaded upon entering the Backoffice Key.
Configure the payment method. The image below shows an example of a minimally functional configuration.


1. **Show on Order Form** - When enabled, displays this payment method option at the checkout of your store.
2. **Display Name** - The payment method name that appears to the consumer during checkout.
3. **Sandbox Mode** - When enabled, prevents callback activation with server.
4. **Activate Callback** - When enabled, the order status will be updated when the payment is received.
5. **Show Payment Icon on Checkout** - (optional) When enabled, replaces the payment method Display Name presented in checkout with its respective icon.
6. **Cancel MB WAY Order** - (optional) When enabled, allows the order cancellation cron job to run for this specific method.
7. **Mbway Key** - Select a Key. You can only choose one of the Keys associated with the Backoffice Key.

Click on Save (8) to save the changes.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/config_mbway.png)

</br>


## Credit Card

The Credit Card payment method allows payment with Visa or Mastercard through the ifthenpay gateway.
The Credit Card Keys are automatically loaded upon entering the Backoffice Key.
Configure the payment method. The image below shows an example of a minimally functional configuration.

1. **Show on Order Form** - When enabled, displays this payment method option at the checkout of your store.
2. **Display Name** - The payment method name that appears to the consumer during checkout.
3. **Show Payment Icon on Checkout** - (optional) When enabled, replaces the payment method Display Name presented in checkout with its respective icon.
4. **Cancel Credit Card Order** - (optional) When enabled, allows the order cancellation cron job to run for this specific method.
5. **Ccard Key** - Select a Key. You can only choose one of the Keys associated with the Backoffice Key.

Click on Save (6) to save the changes.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/config_ccard.png)

</br>


## Payshop

The Payshop payment method generates a reference that can be paid at any Payshop agent or affiliated store.
The Payshop Keys are automatically loaded upon entering the Backoffice Key.
Configure the payment method. The image below shows an example of a minimally functional configuration.

1. **Show on Order Form** - When enabled, displays this payment method option at the checkout of your store.
2. **Display Name** - The payment method name that appears to the consumer during checkout.
3. **Sandbox Mode** - When enabled, prevents callback activation with server.
4. **Activate Callback** - When enabled, the order status will be updated when the payment is received.
5. **Show Payment Icon on Checkout** - (optional) When enabled, replaces the payment method Display Name presented in checkout with its respective icon.
6. **Cancel Payshop Order** - (optional) When enabled, allows the order cancellation cron job to run for this specific method.
7. **Payshop Key** - Select a Key. You can only choose one of the Keys associated with the Backoffice Key.
8. **Deadline** - Select the number of days to deadline for the Payshop reference. From 1 to 99 days, leave empty if you don't want it to expire.


Click on Save (9) to save the changes.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/config_payshop.png)

</br>


# Other
  
## Support

On the Apps & Integrations->Payments page, by clicking any of the ifthenpay payment methods card you can find a Support link (1) that redirects you to the ifthenpay support page, where you can create a support ticket.
For your convenience, you can also access this user manual by clicking on the Instructions link (2), which will redirect you to the GitHub page where you can find the user manual.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/support.png)
</br>


 ## Request additional account

If you already have an ifthenpay account but haven't contracted a needed payment method, you can place an automatic request with ifthenpay.
The response time for this request is 1 to 2 business days, with the exception of the Credit Card payment method, which might exceed this time due to validation requirements.
To request the creation of an additional account, access the configuration page of the payment method you wish to contract and click on Send Email (1).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/request_account.png)
</br>

In the case that you already have a Multibanco account with static references and need an account for Multibanco with dynamic references, you can do so on the Multibanco configuration page by clicking on Send Email (1) below Request Multibanco deadline.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/request_account_multibanco_dynamic.png)
</br>

As a result, the ifthenpay team will add the payment method to your account, updating the list of available payment methods in your extension.

**IMPORTANT:** When requesting an account for the Credit Card payment method, the ifthenpay team will contact you to request more information about your online store and your business before activating the payment method.

</br>


## Reset Configuration

Not an actual reset, but a way to clear the current configuration of the payment method if you need to reconfigure it.
This is useful in the following scenarios:
- If you have acquired a new Backoffice Key and want to assign it to your website, but you already have one currently assigned.
- If you have requested the creation of an additional account by phone or ticket and want to update the list of payment methods to use the new account.
- If you want to reset the configuration of the payment method to reconfigure it.


In the configuration of the selected payment method, click on the Deactivate button (1), select an alternative payment method (2) and click the modal Deactivate (3) button to confirm.

**Attention, this action will clear the current payment method configuration.**

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/deactivate.png)
</br>

After deactivating the payment method, you can now reactivate it again at the Apps & Integrations->Payments page.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/activate_method.png)

</br>
After activating, you will be prompted to enter the Backoffice Key again.
</br>


## Callback

**IMPORTANT:** Only the Multibanco, MB WAY, and Payshop payment methods allow activating the Callback. The Credit Card method changes the order status automatically without using the Callback.

The Callback is a feature that, when enabled, allows your store to receive notifications of successful payments. Upon receiving a successful payment for an order, the ifthenpay server communicates with your store, changing the order status to "Processing." You can use ifthenpay payments without activating the Callback, but your orders won't automatically update their status.

As mentioned in the configurations above, to activate the Callback, access the extension's configuration page and enable the "Enable Callback" option. After saving the settings, the process of associating your store and payment method with ifthenpay's servers will run, and a new "Callback" group (for information only) will appear, showing the Callback status (1), the anti-phishing key (2), and the Callback URL (3).

After activating the Callback, you don't need to take any further action. The Callback is active and functioning.

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/callback.png)

</br>


## Cronjob

A cron job is a scheduled task that is automatically executed at specific intervals in the system, regularly set to repeat everyday. The ifthenpay extension provides a function that is run when the WHMCS cron job is executed, it checks the payment status and cancel orders that haven't been paid within the configured time limit. The table below shows the time limit for each payment method, which the cron job checks and cancels orders that haven't been paid within the time limit. This time limit can be configured only for the Multibanco with Dynamic References and Payshop payment methods.

| Payment Method     | Payment Deadline               |
|--------------------|--------------------------------|
| Multibanco         | No deadline                    |
| Dynamic Multibanco | Configurable from 1 to n days  |
| MB WAY             | 30 minutes                     |
| Payshop            | Configurable from 1 to 99 days |
| Credit Card        | 30 minutes                     |

To activate the cron job, access the extension's configuration page and enable the "Enable Cancel Cron Job" option, then click on Save.

</br>



# Customer usage experience

The following describes the consumer user experience when using ifthenpay payment methods on a "stock" installation of WHMCS 8. Please note that this experience might change with the addition of one-page checkout extensions.

On the checkout page, the consumer can choose the payment method.
If the show payment icon is disabled, the payment method name will be displayed.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/checkout_no_icons.png)
</br>

If the show payment icon is enabled, the icon will be displayed.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/checkout_icons.png)

</br>


## Paying order with Multibanco

Select the Multibanco payment method (1) and click on Complete Order (2).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/select_multibanco.png)
</br>


The invoice page will be displayed, showing the entity, reference, deadline, and the amount to pay.
Note: In the case of assigning a static Multibanco account or Multibanco with Dynamic References without setting an expiry date, the payment deadline will not be displayed.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/invoice_multibanco.png)

</br>


## Paying order with Payshop

Select the Payshop payment method (1) and click on Complete Order (2).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/select_payshop.png)
</br>

The invoice page will be displayed, showing the reference, deadline, and the amount to pay.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/invoice_payshop.png)

</br>



## Paying order with MB WAY

Select the MB WAY payment method (1) and click on Complete Order (2).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/select_mbway.png)
</br>

User will be presented with a form to enter the mobile phone number (1) and click on Pay Now (2).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/invoice_mbway_start.png)
</br>

A countdown timer will be displayed, and the consumer will receive a notification in the MB WAY app to authorize the payment.
If the countdown reaches zero, the consumer can click on the "Resend Mbway notification" button to receive a new notification in the MB WAY app.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/invoice_mbway_end.png)
</br>



When the consumer authorizes the payment in the MB WAY app and payment is recieved, the countdown is replaced with "Order paid!" panel.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/invoice_mbway_paid.png)

</br>


## Paying order with Credit Card

Select the Credit Card payment method (1) and click on Complete Order (2).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/select_ccard.png)
</br>

User will be redirected to the ifthenpay Credit Card gateway page.
Fill in the credit card details, card number (1), expiration date (2), security code (3), Name on Card (4), and click on Pay (5).
You can go back (6), returning to the checkout page.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/invoice_ccard.png)
</br>



You have reached the end of the ifthenpay extension manual for WHMCS 8.
