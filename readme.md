# Deprecated Versions

**❌ Module versions for WHMCS 5, 6 and 7 are no longer supported.**

    These versions will not receive updates, bug fixes, or security patches.
    These versions will not receive technical support.
    It is strongly recommended to upgrade to Version 1.3.1 or the new version 8.0.0.

</br>

# ⚠️ Attention

This manual was created for version 8.0.0 of the Ifthenpay extension, designed for WHMCS 8.

Download Ifthenpay module for WHMCS.
| | WHMCS 8 |
|----------------------------|-----------------------------------------------------------------------------------------------------|
| Download Installer Files | [ifthenpay v8.0.0](https://github.com/ifthenpay/WHMCS/releases/download/8.0.0/ifthenpay.zip) |

**Disclaimer**: This module version is intended for either a fresh installation of **WHMCS 8** or an upgrade from **Ifthenpay v1.3.1**. Ifthenpay is **not responsible** for any issues arising from migration errors.

</br>

# Ifthenpay payment module for WHMCS 8

Read in ![Portuguese](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/pt.png) [Portuguese](readme.pt.md), and ![English](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/en.png) [English](readme.md)

[1. Introduction](#introduction)

[2. Compatibility](#compatibility)

[3. Installation](#installation)

[4. Configuration](#configuration)

- [Multibanco](#multibanco)
- [MB WAY](#mb-way)
- [Credit Card](#credit-card)
- [Payshop](#payshop)
- [Cofidis Pay](#cofidis-Pay)
- [Pix](#pix)
- [Ifthenpay Gateway](#ifthenpay-gateway)

[5. Other](#other)

- [Support](#support)
- [Request additional account](#request-additional-account)
- [Reset Configuration](#reset-configuration)
- [Callback](#callback)
- [Cronjob](#cronjob)
- [Logs](#logs)
- [Upgrade from older versions](#upgrade-from-older-versions)

[6. Customer usage experience](#customer-usage-experience)

- [Paying with Multibanco](#paying-with-multibanco)
- [Paying with MB WAY](#paying-with-mb-way)
- [Paying with Credit Card](#paying-with-credit-card)
- [Paying with Payshop](#paying-with-payshop)
- [Paying with Cofidis Pay](#paying-with-cofidis-pay)
- [Paying with Pix](#paying-with-pix)
- [Paying with Ifthenpay Gateway](#paying-with-ifthenpay-gateway)

[7. Troubleshoot](#troubleshoot)

[8. License](#license)

</br>

# Introduction

This is ifthenpay's payment gateway module for WHMCS platform, that provides the following payment methods:

![ifthenpay](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/payment_methods_banner.png)
</br>

**Multibanco** is a Portuguese payment method that allows the consumer to pay through a bank reference. It enables the generation of payment references that the consumer can use to pay for their order at an ATM (Multibanco) or through an online home banking service.

**MB WAY** is the first inter-bank solution that enables immediate purchases and transfers through smartphones and tablets. It allows generating a payment request on the consumer's smartphone, and they can authorize the payment for their order through the MB WAY application.

**Payshop** is a Portuguese payment method that allows the consumer to pay with a Payshop reference. It enables the generation of a payment reference that the consumer can use to pay for their order at a Payshop agent or CTT (Portuguese postal service).

**Credit Card** This extension allows generating a payment through Visa or MasterCard, which the consumer can use to pay for their order.

**Cofidis Pay** is a payment solution of up to 12 interest-free installments that makes it easier to pay for purchases by splitting them.

**Pix** is a Brazilian payment method that allows instant and secure money transfers in Brazilian real. Payments can be made via QR code or by entering the recipient’s Pix key in a banking app.

</br>

**Contract with Ifthenpay is required**

See more at [ifthenpay](https://ifthenpay.com).

Membership at [Membership ifthenpay](https://www.ifthenpay.com/aderir/).

**Support**

For support, please create a support ticket at [Support ifthenpay](https://helpdesk.ifthenpay.com/).

</br>

# Compatibility

Follow the table below to verify Ifthenpay's module compatibility with your WHMCS platform version.
| Ifthenpay Module version| WHMCS version 5, 6, and 7 | WHMCS version 8 |
|---------------------------|----------------|--------------------------------|
| Ifthenpay v1.0.0 to v1.3.1 | ❌ Non compatible | ✅ Compatible |
| Ifthenpay v8.0.0 | ❌ Non compatible | ✅ Compatible |

</br>

# Installation

⚠️ **Attention**: If you are updating from versions between 1.0.0 and 1.3.1 to version 8.0.0 and above, please refer to the [Upgrade from older versions](#upgrade-from-older-versions) section.

Download the latest version of ifthenpay's module installation file from the GitHub releases page [ifthenpay-whmcs](https://github.com/ifthenpay/WHMCS/releases).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/installation_download_installer.png)
</br>

Unzip the downloaded file, and paste the contents in the root of your WHMCS platform.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/installation_upload_files.png)
</br>

After uploading the files, access the admin back office of your online store and go to Setup (1) -> Apps & Integrations (2) -> Browse (3) -> Payments (4).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/installation_backoffice_payments.png)
</br>

Scroll down and search for the ifthenpay's payment methods that should now be available for activation.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/Installation_available_payment_methods.png)
</br>

Click on the card of the payment method that you wish to enable (e.g., Multibanco) and click on Activate (1).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/installation_activate_method.png)
</br>

# Configuration

After installing the module and activating a payment method you will be redirected to its configuration page where you need to configure it using your ifthenpay account details.
The configuration page can also be accessed by selecting Payments -> Payment Gateways from the left navigation sidebar and selecting the active payment method you wish to configure, or by clicking Apps & Integrations -> selecting the Active tab and choosing the payment method you wish to configure.

</br>

## Multibanco

The Multibanco payment method generates references that can be used to pay for an order at an ATM or homebanking.
Depending on the account you have contracted with ifthenpay, you may use a static or dynamic type Multibanco account.
</br>

**A static** type account will generate references using an algorithm, and is used if you don't want to set a time limit (in days) for orders paid with Multibanco.
</br>

**A dynamic** type account will generate references per order and is used if you wish to set a time limit (in days) for orders paid with Multibanco.
</br>

Upon inputting a valid Backoffice Key, the Entity and Sub-Entity are automatically loaded.
</br>

The image below shows an example of a minimally functional configuration using a static type Multibanco account.

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/configuration_multibanco_static.png)
</br>

1. **Show on Order Form** - When enabled, displays this payment method option at the checkout of your store;
2. **Display Name** - The payment method name that appears to the consumer during checkout;
3. **Version** - Displays current version and checks if installed version is up to date with latest release;
4. **Backoffice Key** - Input your ifthenpay Backoffice Key to load available Entities and Sub-Entities. Backoffice Key is provided upon contract completion and consists of sets of four digits separated by a hyphen (-), e.g. 1111-1111-1111-1111;
5. **Entity** - Select an Entity, any numeric entity for using static type multibanco account. You can only choose one of the Entities associated with the Backoffice Key;
6. **Sub-entity** - Select a Sub-Entity. You can only choose one of the Sub-Entities associated with the Entity chosen earlier;
7. **Deadline** - (optional) Only available for dynamic type accounts.
8. **Minimum Amount** - (optional) Input minimum value to only display this payment method for orders values above it;
9. **Maximum Amount** - (optional) Input maximum value to only display this payment method for orders values below it;
10. **Show Payment Icon on Checkout** - (optional) When enabled, replaces the payment method Display Name presented in checkout with its respective icon.
11. **Cancel Multibanco Order** - (optional) Only available for dynamic type accounts;
12. **Callback** (optional) Enable to activate Callback, by selecting this option the order state will update when a payment is received;

Click on Save (13) to save the changes.

</br>

The image below shows an example of a minimally functional configuration using a dynamic type Multibanco account.

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/configuration_multibanco_dynamic.png)
</br>

1.  **Entity** - Select an Entity, "Dynamic Reference" for using dynamic type multibanco account. You can only choose one of the Entities associated with the Backoffice Key;
2.  **Sub-entity** - Select a Sub-Entity. You can only choose one of the Sub-Entities associated with the Entity chosen earlier;
3.  **Deadline** - (optional) Select number of days for deadline;
    Examples of deadlines:

	Choosing a deadline of 0 days: If an order is created on 22/03/2025 at 15:30, the generated Multibanco reference will expire on 22/03/2025 at 23:59, which is the end of the day it was generated.

	Choosing a deadline of 1 day: If an order is created on 22/03/2025 at 9:30, the generated Multibanco reference will expire on 23/03/2025 at 23:59, which means the Multibanco reference will be valid for the day it was generated plus 1 day.

	Choosing a deadline of 3 days: If an order is created on 22/03/2025 at 20:30, the generated Multibanco reference will expire on 25/03/2025 at 23:59, which means the Multibanco reference will be valid for the day it was generated plus 3 days.

4.  **Cancel Multibanco Order** - (optional) When enabled, allows the order cancellation cron job to run for this specific method. The cancellation cron job executes with the WHMCS daily cron.

## MB WAY

The MB WAY payment method utilizes a mobile phone number provided by the consumer and generates a payment request within the MB WAY smartphone application. The consumer can then accept or decline the payment.
Upon inputting a valid Backoffice Key, the MB WAY Keys are automatically loaded.
The image below shows an example of a minimally functional configuration.
</br>

The image below shows an example of a minimally functional configuration.

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/configuration_mbway.png)
</br>

1. **Show on Order Form** - When enabled, displays this payment method option at the checkout of your store.
2. **Display Name** - The payment method name that appears to the consumer during checkout.
3. **Version** - Displays current version and checks if installed version is up to date with latest release.
4. **Backoffice Key** - Input your ifthenpay Backoffice Key to load available MB WAY Keys. Backoffice Key is provided upon contract completion and consists of sets of four digits separated by a hyphen (-), e.g. 1111-1111-1111-1111.
5. **MB WAY Key** - Select a Key. You can only choose one of the Keys associated with the Backoffice Key.
6. **Minimum Amount** - (optional) Input minimum value to only display this payment method for orders values above it;
7. **Maximum Amount** - (optional) Input maximum value to only display this payment method for orders values below it;
8. **App Notification Description** - (optional) Modify this string if you wish. Use the string "{{invoice_id}}" to pass the invoice number in the description;
9. **Show Payment Icon on Checkout** - (optional) When enabled, replaces the payment method Display Name presented in checkout with its respective icon;
10. **Show MB WAY Countdown** - (optional) when enabled, will display a countdown in the invoice page and will give feedback of user action (payment completed, refused, error). You may wish to keep this option disable if you are using a third-party checkout module that may conflict with the countdown;
11. **Cancel MB WAY Order** - (optional) When enabled, allows the order cancellation cron job to run for this specific method. The cancellation cron job executes with the WHMCS daily cron;
12. **Callback** (optional) Enable to activate Callback, by selecting this option the order state will update when a payment is received;

Click on Save (13) to save the changes.

</br>

## Credit Card

The Credit Card payment method allows payment with Visa or Mastercard through the ifthenpay gateway.
Upon inputting a valid Backoffice Key, the Credit Card Keys are automatically loaded.
</br>

The image below shows an example of a minimally functional configuration.

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/configuration_ccard.png)
</br>

1. **Show on Order Form** - When enabled, displays this payment method option at the checkout of your store;
2. **Display Name** - The payment method name that appears to the consumer during checkout;
3. **Version** - Displays current version and checks if installed version is up to date with latest release;
4. **Backoffice Key** - Input your ifthenpay Backoffice Key to load available Credit Card Keys. Backoffice Key is provided upon contract completion and consists of sets of four digits separated by a hyphen (-), e.g. 1111-1111-1111-1111;
5. **Credit Card Key** - Select a Key. You can only choose one of the Keys associated with the Backoffice Key.
6. **Minimum Amount** - (optional) Input minimum value to only display this payment method for orders values above it;
7. **Maximum Amount** - (optional) Input maximum value to only display this payment method for orders values below it;
8. **Show Payment Icon on Checkout** - (optional) When enabled, replaces the payment method Display Name presented in checkout with its respective icon.
9. **Cancel Credit Card Order** - (optional) When enabled, allows the order cancellation cron job to run for this specific method. The cancellation cron job executes with the WHMCS daily cron;

Click on Save (10) to save the changes.

</br>

## Payshop

The Payshop payment method generates a reference that can be paid at any Payshop agent or affiliated store.
Upon inputting a valid Backoffice Key, the Payshop Keys are automatically loaded.
</br>

The image below shows an example of a minimally functional configuration.

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/configuration_payshop.png)
</br>

1. **Show on Order Form** - When enabled, displays this payment method option at the checkout of your store;
2. **Display Name** - The payment method name that appears to the consumer during checkout;
3. **Version** - Displays current version and checks if installed version is up to date with latest release;
4. **Backoffice Key** - Input your ifthenpay Backoffice Key to load available Payshop Keys. Backoffice Key is provided upon contract completion and consists of sets of four digits separated by a hyphen (-), e.g. 1111-1111-1111-1111;
5. **Payshop Key** - Select a Key. You can only choose one of the Keys associated with the Backoffice Key.
6. **Deadline** - (optional) Input the number of days to deadline for the Payshop reference. From 1 to 99 days, leave empty if you don't want it to expire.
7. **Minimum Amount** - (optional) Input minimum value to only display this payment method for orders values above it;
8. **Maximum Amount** - (optional) Input maximum value to only display this payment method for orders values below it;
9. **Show Payment Icon on Checkout** - (optional) When enabled, replaces the payment method Display Name presented in checkout with its respective icon.
10. **Cancel Payshop Order** - (optional) When enabled, allows the order cancellation cron job to run for this specific method. The cancellation cron job executes with the WHMCS daily cron;
11. **Callback** (optional) Enable to activate Callback, by selecting this option the order state will update when a payment is received;

Click on Save (12) to save the changes.

</br>

## Cofidis Pay

The Cofidis Pay method redirects the user to the Cofidis page where it is possible to configure the payment in a select number of times.
Upon inputting a valid Backoffice Key, the Cofidis Pay Keys are automatically loaded.
</br>

The image below shows an example of a minimally functional configuration.

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/configuration_cofidis.png)
</br>

1. **Show on Order Form** - When enabled, displays this payment method option at the checkout of your store;
2. **Display Name** - The payment method name that appears to the consumer during checkout;
3. **Version** - Displays current version and checks if installed version is up to date with latest release;
4. **Backoffice Key** - Input your ifthenpay Backoffice Key to load available Cofidis Pay Keys. Backoffice Key is provided upon contract completion and consists of sets of four digits separated by a hyphen (-), e.g. 1111-1111-1111-1111;
5. **Cofidis Pay Key** - Select a Key. You can only choose one of the Keys associated with the Backoffice Key.
6. **Minimum Amount** - (optional) Input minimum value to only display this payment method for orders values above it. Important Notice: On Cofidis Key selection, this input is updated with value configured in ifthenpay's backoffice, and when editing, it can not be lesser than the value specified in ifthenpay's backoffice;
7. **Maximum Amount** - (optional) Input maximum value to only display this payment method for orders values below it. Important Notice: On Cofidis Key selection, this input is updated with value configured in ifthenpay's backoffice, and when editing, it can not be greater than the value specified in ifthenpay's backoffice;
8. **Show Payment Icon on Checkout** - (optional) When enabled, replaces the payment method Display Name presented in checkout with its respective icon.
9. **Cancel Cofidis Pay Order** - (optional) When enabled, allows the order cancellation cron job to run for this specific method. The cancellation cron job executes with the WHMCS daily cron;
10. **Callback** (optional) Enable to activate Callback, by selecting this option the order state will update when a payment is received;

Click on Save (11) to save the changes.

</br>

## Pix

The Pix payment method allows payment with CPF through the ifthenpay gateway.
Upon inputting a valid Backoffice Key, the Pix Keys are automatically loaded.
</br>

The image below shows an example of a minimally functional configuration.

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/configuration_pix.png)
</br>

1. **Show on Order Form** - When enabled, displays this payment method option at the checkout of your store;
2. **Display Name** - The payment method name that appears to the consumer during checkout;
3. **Version** - Displays current version and checks if installed version is up to date with latest release;
4. **Backoffice Key** - Input your ifthenpay Backoffice Key to load available Pix Keys. Backoffice Key is provided upon contract completion and consists of sets of four digits separated by a hyphen (-), e.g. 1111-1111-1111-1111;
5. **Pix Key** - Select a Key. You can only choose one of the Keys associated with the Backoffice Key.
6. **Minimum Amount** - (optional) Input minimum value to only display this payment method for orders values above it;
7. **Maximum Amount** - (optional) Input maximum value to only display this payment method for orders values below it;
8. **Show Payment Icon on Checkout** - (optional) When enabled, replaces the payment method Display Name presented in checkout with its respective icon.
9. **Cancel Pix Order** - (optional) When enabled, allows the order cancellation cron job to run for this specific method. The cancellation cron job executes with the WHMCS daily cron;
10. **Callback** (optional) Enable to activate Callback, by selecting this option the order state will update when a payment is received;

Click on Save (11) to save the changes.

</br>

## Ifthenpay Gateway

The Ifthenpay Gateway payment method allows the consumer to be redirected to a payment gateway page where it is possible to select any of the above payment methods to pay for the order.
Upon inputting a valid Backoffice Key, the Ifthenpay Gateway Keys are automatically loaded.
</br>

The image below shows an example of a minimally functional configuration.

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/configuration_ifthenpaygateway.png)
</br>

1. **Show on Order Form** - When enabled, displays this payment method option at the checkout of your store;
2. **Display Name** - The payment method name that appears to the consumer during checkout;
3. **Version** - Displays current version and checks if installed version is up to date with latest release;
4. **Backoffice Key** - Input your ifthenpay Backoffice Key to load available ifthenpay gateway Keys. Backoffice Key is provided upon contract completion and consists of sets of four digits separated by a hyphen (-), e.g. 1111-1111-1111-1111;
5. **ifthenpay gateway Key** - Select a Key. You can only choose one of the Keys associated with the Backoffice Key.
6. **Payment Methods** - Select a Payment Method Key per each Method and check the checkbox if you want to display it in the gateway page. Click the check box to the left of each payment method to show/hide that payment method in the gateway page, and choose the payment method account in the select box to the right of it. If there are no accounts created for a given payment method, a button will replace the select box, which you may use to request the creation of said account.
7. **Default Payment Method** - Select a Payment Method that will be selected in the gateway page by default.
8. **Gateway Close Button Text** - Text displayed in the "Return to Shop" button in the gateway page;
9. **Description** - Text displayed under amount, as a description, in the gateway page;
10. **Deadline** - (optional) Input the number of days to deadline for the Payshop reference. From 1 to 99 days, leave empty if you don't want it to expire.
11. **Minimum Amount** - (optional) Input minimum value to only display this payment method for orders values above it;
12. **Maximum Amount** - (optional) Input maximum value to only display this payment method for orders values below it;
13. **Show Payment Icon on Checkout** - Display this payment method logo image on checkout, choose from 3 options:

    - OFF - show method title: displays Payment Method Title;
    - ON - show default icon: displays ifthenpay gateway logo;
    - ON - show composite icon: displays a composite image of all the payment method logos you have selected;

14. **Cancel ifthenpay gateway Order** - (optional) When enabled, allows the order cancellation cron job to run for this specific method. The cancellation cron job executes with the WHMCS daily cron;
15. **Callback** (optional) Enable to activate Callback, by selecting this option the order state will update when a payment is received;

Click on Save (11) to save the changes.

</br>

# Other

## Support

On the Apps & Integrations->Payments page, by clicking any of the ifthenpay payment methods card you can find a Support link (1) that redirects you to the ifthenpay support page, where you can create a support ticket.
For your convenience, you can also access this user manual by clicking on the Instructions link (2), which will redirect you to the GitHub page where you can find the user manual.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/other_support.png)
</br>

## Request additional account

If you already have an ifthenpay account but haven't contracted a needed payment method, you can place an automatic request with ifthenpay.
The response time for this request is 1 to 2 business days, with the exception of the Credit Card payment method, which might exceed this time due to validation requirements.
To request the creation of an additional account, access the configuration page of the payment method you wish to contract and input your backoffice key (1), if you don't have any account for that payment method, a dialog window will popup asking if you wish to request an account for that payment method, you can then click the ok button (2) to send an automatic email requesting that payment method account creation.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/request_account.png)
</br>

As a result, the ifthenpay team will add the payment method to your account, updating the list of available payment methods in your extension.
In the case that you already have an account, but for some reason you need another, you may open a support ticket requesting it.

**IMPORTANT:** When requesting an account for the Credit Card payment method, the ifthenpay team will contact you to request more information about your online store and your business before activating the payment method.

</br>

## Reset Configuration

Not an actual reset, but a way to clear the current configuration of the payment method if you need to reconfigure it.
This is useful in the following scenarios:

- If you have acquired a new Backoffice Key and want to assign it to your website, but you already have one currently assigned.
- If you have requested the creation of an additional account by phone or ticket and want to update the list of payment methods to use the new account.
- If you want to reset the configuration of the payment method to reconfigure it.

After successfully configuring a payment method once, the Backoffice Key will become locked and a "Reset" button will be displayed next to it.
To reset, click the "Reset" button (1) and confirm the action by clicking the "OK" button (2).

**Attention, this action will clear the current payment method configuration.**

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/reset_configuration.png)
</br>

## Callback

**IMPORTANT:** Only the Multibanco, MB WAY, Payshop, Cofidis Pay, Pix, and Ifthenpay Gateway payment methods allow activation of the Callback. The Credit Card method changes the order status automatically without using the Callback.

The Callback is a feature that, when enabled, allows your store to receive notifications of successful payments. Upon receiving a successful payment for an invoice, the ifthenpay server communicates with your store, changing the invoice status to "Paid." You can use ifthenpay payments without activating the Callback, but your orders won't automatically update their status.

As mentioned in the configurations above, to activate the Callback, access the extension's configuration page and enable the "Enable Callback" option. After saving the settings, the process of associating your store and payment method with ifthenpay's servers will run, and, if successfully activated, the Callback group will now display the Callback status as a green colored badge "Callback Active" (1), the anti-phishing key (2), and the Callback URL (3).

After activating the Callback, you don't need to take any further action. The Callback is active and functioning.

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/callback.png)
</br>

## Cronjob

A cron job is a scheduled task that is automatically executed at specific intervals in the system, regularly set to repeat everyday. The ifthenpay extension provides a function that is run when the WHMCS daily cron job is executed, it checks the payment status and cancel invoices that haven't been paid within the configured time limit. The table below shows the time limit for each payment method, which the cron job checks and cancels invoices that haven't been paid within the time limit. This time limit can be configured only for the Multibanco with Dynamic References, Payshop and Ifthenpay Gateway payment methods.

| Payment Method     | Payment Deadline               |
| ------------------ | ------------------------------ |
| Multibanco         | No deadline                    |
| Dynamic Multibanco | Configurable from 0 to n days  |
| MB WAY             | 30 minutes                     |
| Payshop            | Configurable from 1 to 99 days |
| Credit Card        | 30 minutes                     |
| Cofidis            | 60 minutes                     |
| Pix                | 30 minutes                     |
| Ifthenpay Gateway  | Configurable from 1 to 99 days |

To activate the cron job, access the extension's configuration page and enable the "Enable Cancel Cron Job" option, then click on Save.

</br>

## Logs

### Location and Purpose

This module has its own log coverage, and the resulting log files can be found at `/modules/gateways/ifthenpaylib/lib/Log/logs/`.
The table below shows the log files and their functions.

| File                 | Function                                                           |
| -------------------- | ------------------------------------------------------------------ |
| cron.log             | Register logs related to the execution of the cancellation cronjob. |
| general_logs.log     | Register logs not related to a single payment method.              |
| multibanco.log       | Register logs related to Multibanco payment method.                |
| mbway.log            | Register logs related to MB WAY payment method.                    |
| payshop.log          | Register logs related to Payshop payment method.                   |
| ccard.log            | Register logs related to Credit Card payment method.               |
| cofidispay.log       | Register logs related to Cofidis Pay payment method.               |
| pix.log              | Register logs related to Pix payment method.                       |
| ifthenpaygateway.log | Register logs related to Ifthenpay Gateway payment method.         |

### Log levels

To prevent unnecessarily filling the log files, the module only registers error level events. If there is a need to debug and analyze the lower level events, you may access the config file at `/modules/gateways/ifthenpaylib/lib/Config/Config.php`, and set the log level to info by:

editing line 12:

	public const LOG_LEVEL = self::LOG_LEVEL_ERROR;

into:

	public const LOG_LEVEL = self::LOG_LEVEL_INFO;


# Upgrade from older versions

At the time of the writing of this document, the previous (older) version is v1.3.1, the newest version 8.0.0 has some changes that are automatically updated, mainly changes to the database tables of ifthenpay methods.

To make the transition as smooth as possible follow this small guide:
**Important Note**: Before proceeding, keep in mind that both version of the module can not coexist at the same time, once the newer is activated the older will no longer work properly, and these actions are irreversible without interacting directly with the database.
You may experience a period where the invoices are either not cancelled automatically or updated as paid by the callback, this is due to the difference in parameters from the older to the newer version.

## Upload installation files
Upload module installation files to the root of WHMCS, it will warn you about replacing the hooks files `/includes/hooks/ifthenpay.php`, accept it and proceed.

## Activate payment methods
After uploading the files, access the admin back office and go to Setup (1) -> Apps & Integrations (2) -> Browse (3) -> Payments (4).

The image below shows the newer payment methods distinguishable by the "V2" suffix.
Taking Credit Card as example, we will activate the newer version "Ifthenpay Credit Card V2" (1) to later replace the older version (2).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/upgrade_from_older_payments.png)
</br>

Activate the payment method, Credit Card in this case by clicking the "Activate" button (1).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/upgrade_from_older_activate.png)
</br>

Configure the payment method (1) (refer to [Configuration](#configuration) for other methods) and click the "Save Changes" button (2).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/upgrade_from_older_configure.png)
</br>

Go to the older method, it will show abnormal icon sizes, since it should have lost the CSS styling when replacing the hooks file during the installation of the newer version. Click "Deactivate" button (1).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/upgrade_from_older_older_method.png)
</br>

Expand the select box (1) and select the equivalent payment method to replace with (2), should have the same name if not change, and click the "Deactivate" button (3).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/upgrade_from_older_deactivate_older.png)
</br>

Now repeat for other payment methods you may need.



# Customer usage experience

The following describes the consumer user experience when using ifthenpay payment methods on a "stock" installation of WHMCS 8. Please note that this experience might change with the addition of third-party checkout extensions.

## Selecting payment method

On the checkout page, the consumer can choose the payment method.
If the show payment icon is disabled, the payment method name will be displayed.
The payment method name can be edited in the configuration page in the field "Display Name".
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/ux_checkout_select_payment_text.png)
</br>

If the configuration option "Show Payment Icon on Checkout" is enabled, the icon will be displayed in place of the payment title.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/ux_checkout_select_payment_icon.png)
</br>

The payment method Ifthenpay Gateway provides an additional option to show the icons of the payment methods that will be available inside the ifthenpay gateway page. By selecting the option "ON - show composite icon" in the "Show Payment Icon on Checkout" field.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/ux_checkout_select_payment_icon_composite.png)
</br>

When the user selects a payment method and clicks the "Complete Order" button he will be redirected to the invoice page.

## Paying with Multibanco

The Multibanco payment details will be displayed with entity, reference, deadline and the amount to pay.
</br>

**Note**: In the case of assigning a static Multibanco account or Multibanco with Dynamic References without setting an expiry date, the payment deadline will not be displayed.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_multibanco_details.png)

</br>

## Paying with MB WAY

The MB WAY mobile phone form will be displayed, the user must select the correct country code (1), input a valid number (2) that is already associated with the MB WAY App and click the "Pay" button (3).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_mbway_form.png)
</br>

If the configuration option "Show MB WAY Countdown" is disabled, the user will be displayed a simple message, and the consumer will receive a notification in the MB WAY App to authorize the payment.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_mbway_details_no_countdown.png)
</br>

If the configuration option "Show MB WAY Countdown" is enabled, a countdown timer will be displayed, and the consumer will receive a notification in the MB WAY App to authorize the payment.
If the countdown reaches zero, the consumer can click on the "Resend Mbway notification" button to receive a new notification in the MB WAY app.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_mbway_details_countdown.png)
</br>

When using the countdown, the invoice page will update in accordance with the consumer actions or any errors that might happen.

The payment confirmed status will be displayed after the consumer confirms the payment in their MB WAY App.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_mbway_details_paid.png)
</br>

The expired status will be displayed after reaching the end of the countdown.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_mbway_details_expired.png)
</br>

The rejected by user status will be displayed after the consumer refuses payment in their MB WAY App.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_mbway_details_rejected_by_user.png)
</br>

The refused status will be displayed after a verification from MB WAY returns an error related to MB WAY App association.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_mbway_details_refused.png)
</br>

The error status will be displayed after inputting an invalid phone number, or an error as occurred either on MB WAY or ifthenpay.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_mbway_details_error.png)
</br>

## Paying with Credit Card

A "Pay" button (1) will be displayed, which the consumer must click to be redirected to the Credit Card page.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_ccard_form.png)
</br>

User will be redirected to the Credit Card gateway page.
Fill in the credit card details, card number (1), expiration date (2), security code (3), Name on Card (4), and click on Pay (5).
You can go back (6), returning to the checkout page.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_ccard_gateway_page.png)
</br>

## Paying with Payshop

The Payshop payment details will be displayed with reference, deadline and the amount to pay.
</br>

**Note**: In the case of configuring payshop method without setting an expiry date, the payment deadline will not be displayed.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_payshop_details.png)

</br>

## Paying with Cofidis Pay

A "Pay" button (1) will be displayed, which the consumer must click to be redirected to the Cofidis Pay page.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_cofidis_form.png)
</br>

User will be redirected to the Cofidis Pay page, in which he will have to go through a number of steps to conclude.

### Login/Registration

Here, the user may login (1) or, if he does not have an account, sign up with Cofidis Pay (2)
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_cofidis_gateway_page_1.png)
</br>

### Installments and Personal Information

Choose number of installments, and edit billing and personal data if necessary.

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_cofidis_gateway_page_2.png)

1. Select the number of installments you wish;
2. Verify the summary of the the payment plan;
3. Fill in your personal and billing data;
4. Upload identification files;
5. Click "Avançar" to continue;
   </br>

### Terms And Conditions

Read the Terms and Conditions, select "Li e autorizo" (1) to accept, and click "Avançar" (2) button to proceed.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_cofidis_gateway_page_3.png)
</br>

### Agreement formalization

Click "Enviar Código" (1) to send an authentication code to you phone.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_cofidis_gateway_page_4.png)
</br>

### Agreement formalization authentication code

Input the authentication code received on phone (1), and click the button "Confirmar Código" (2) to proceed.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_cofidis_gateway_page_5.png)
</br>

### Summary and Payment

Fill in your credit card details (1)(number, expiration date and CW), and click "Validar" button (2);
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_cofidis_gateway_page_6.png)
</br>

### Success and return to store

The payment contract was successful, the user can now return to the shop by either waiting for an automatic redirect or clicking the "sair" button.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_cofidis_gateway_page_6.png)
</br>

## Paying with Pix

The Pix form will be displayed, the user must input their name (1), CPF (2), email (3), and click the "Pay" button (4).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_pix_form.png)
</br>

User will be redirected to the Pix page.
Here it is possible to proceed with payment with one of two options:

- Reading QR code (1) with mobile phone;
- Copy the Pix code (2) and pay with online banking; Important Note: In order to be redirected back to the store after paying, this page must be left open. If closed the consumer will still be able to pay, as long as he has already read the Pix code, he will only not be redirected back to the store.
  ![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_pix_gateway_page.png)
  </br>

After being redirected back to store, the user may be displayed a message informing the success of the operation, but
verification of transaction is in progress.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_pix_gateway_return.png)
</br>

## Paying with Ifthenpay Gateway

A "Pay" button (1) will be displayed, which the consumer must click to be redirected to the ifthenpay gateway page.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_ifthenpaygateway_form.png)
</br>

User will be redirected to the ifthenpay gateway page.
Here the user can verify the amount and select one of the payment methods available in the gateway page.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_ifthenpaygateway_gateway_page_1.png)
</br>

Choosing a payment method,
When choosing an offline payment method like Multibanco or Payshop, the user must either note down the payment details, and click the "Concluir" button (2), or use homebanking application to pay immediately and confirm the payment by clicking "Confirmo o Pagamento" button (3).
</br>

When choosing an online payment method like MB WAY, Credit Card, Pix, Google Pay, and Apple Pay, the user must follow the instructions in the gateway and fill the necessary fields to proceed. When finished the, click "Concluir" button (2) to return to shop.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_ifthenpaygateway_gateway_page_2.png)
</br>


# Troubleshoot
He we will talk about some common problems.

## Lack of permissions for log files
You may install the module, and later on, and error gets thrown in the module, having the logger register an event, it may so happen that instead of registering the event, another error is thrown due to inability to register the log.
The module's logger requires permissions to create, read, and write for the log files, so make sure to give enough permissions to do so.



# License

This project is licensed under the GNU General Public License v3.0 (GPLv3).

    You are free to use, modify, and distribute this software as long as you follow the GPLv3 terms.
    Redistribution for profit must comply with GPLv3.
    This software comes without any warranty.

For full details, see the LICENSE file or read the GNU GPLv3.
