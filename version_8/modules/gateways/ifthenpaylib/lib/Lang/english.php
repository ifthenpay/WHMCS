<?php

return [

	/* ------------------------------ Admin Common ------------------------------ */

	'your_current_version' => 'Your current version is',
	'current_version_installed' => 'Current version installed is',
	'a_new_version' => 'A new version',
	'is_available' => 'is available.',
	'download' => 'Download',
	'module_up_to_date' => 'Your module is up to date.',
	'backoffice_key' => 'Backoffice Key',
	'reset' => 'Reset',
	'callback' => 'Callback',
	'callback_desc' => 'Enable to activate Callback.',
	'show_payment_icon' => 'Show Payment Icon on Checkout',
	'show_payment_icon_desc' => 'Enable to show payment icon during checkout.',
	'deadline' => 'Deadline',
	'callback_inactive' => 'Callback Inactive',
	'callback_active' => 'Callback Active',
	'anti_phishing_key' => 'Anti-phishing Key:',
	'callback_url' => 'Callback URL:',
	'version' => 'Version',
	'no_deadline' => 'No deadline',
	'min_amount' => 'Minimum Amount',
	'max_amount' => 'Maximum Amount',
	'min_amount_desc' => 'Only display this payment method for orders with total value greater than inserted value. Leave empty to always display this payment method.',
	'max_amount_desc' => 'Only display this payment method for orders with total value less than inserted value. Leave empty to always display this payment method.',
	'cofidis_min_amount_desc' => 'Only display this payment method for orders with total value greater than inserted value. Inputted value can not be less than defined value in ifthenpay backoffice.',
	'cofidis_max_amount_desc' => 'Only display this payment method for orders with total value less than inserted value. Inputted value can not be greater than defined value in ifthenpay backoffice.',
	'none' => 'None',
	'msg_are_sure_reset_config' => 'This action will clear this payment method\'s current configuration, please confirm to proceed.',



	/* ---------------------------- Admin Common ---------------------------- */

	'msg_invalid_minimum_amount' => 'Invalid Minimum Amount',
	'msg_invalid_maximum_amount' => 'Invalid Maximum Amount',
	'msg_invalid_min_max_amount' => 'Minimum Amount Value must be lesser than Maximum Amount Value',
	'msg_invalid_deadline' => 'Invalid Deadline',
	'msg_invalid_backoffice_key' => 'Invalid Backoffice Key.',
	'msg_invalid_backoffice_key_example' => 'Invalid Backoffice Key. Must have format: 1111-1111-1111-1111.',
	'msg_request_new_account' => 'Do you wish to request a new Account?',
	'msg_invalid_key' => 'Invalid Key',
	'msg_success_sending_account_request' => 'Account request sent with success.',
	'msg_error_sending_account_request' => 'Error sending account request email, for more information check the file general_logs at modules/gateways/ifthenpaylib/lib/Log/logs/general_logs.log.',



	/* ---------------------------- Admin Multibanco ---------------------------- */

	'entity' => 'Entity',
	'subentity' => 'Sub-Entity',
	'multibanco_dynamic_reference' => 'Dynamic Reference',
	'multibanco_deadline_desc' => 'Only available for Dynamic Multibanco. Select the "0" to expire the reference in the same day of its creation at 23:59. Select "No deadline" to never expire.',
	'cancel_multibanco' => 'Cancel Multibanco Order',
	'cancel_multibanco_desc' => 'Enable to automatically cancel Multibanco orders if reference has expired. Only works with dynamic references. Executes together with daily cron.',
	'msg_invalid_entity' => 'Invalid Entity',
	'msg_invalid_subentity' => 'Invalid SubEntity',
	'msg_no_multibanco_accounts_found' => 'No Multibanco accounts found for your Backoffice Key.',
	'msg_error_updating_multibanco_database' => 'Error updating multibanco database table, for more information check the file multibanco at modules/gateways/ifthenpaylib/lib/Log/logs/multibanco.log.',



	/* ---------------------------- Admin Payshop ---------------------------- */

	'payshop_key' => 'Payshop Key',
	'payshop_deadline_desc' => 'Input the days to deadline, between 1 and 99 days. Leave empty to never expire.',
	'msg_invalid_payshop_key' => 'Invalid Payshop Key',
	'cancel_payshop' => 'Cancel Payshop Order',
	'cancel_payshop_desc' => 'Enable to automatically cancel Payshop orders if reference has expired. Executes together with daily cron.',
	'msg_no_payshop_accounts_found' => 'No Payshop accounts found for your Backoffice Key.',
	'msg_error_updating_payshop_database' => 'Error updating payshop database table, for more information check the file payshop at modules/gateways/ifthenpaylib/lib/Log/logs/payshop.log.',



	/* ---------------------------- Admin MB WAY ---------------------------- */

	'mbway_key' => 'MB WAY Key',
	'show_mbway_countdown' => 'Show MB WAY Countdown',
	'show_mbway_countdown_desc' => 'Enable to show MB WAY\'s 4-minute countdown on invoice',
	'cancel_mbway' => 'Cancel MB WAY Order',
	'cancel_mbway_desc' => 'Enable to automatically cancel MB WAY orders if not paid 30 minutes after creation. Executes together with daily cron.',
	'msg_invalid_mbway_key' => 'Invalid MB WAY Key',
	'msg_no_mbway_accounts_found' => 'No MB WAY accounts found for your Backoffice Key.',
	'mbway_payment_invoice' => 'MB WAY Payment Invoice {{invoice_id}}',
	'notification_description' => 'App Notification Description',
	'notification_description_desc' => 'Small description displayed to customer in MB WAY phone App, use the string {{invoice_id}} to pass the invoice_id number.',
	'msg_invalid_notification_description' => 'Inválid Notification Description, may not contain any special characters besides {{invoice_id}}, and can not have more than 100 characters.',
	'msg_error_updating_mbway_database' => 'Error updating mbway database table, for more information check the file mbway at modules/gateways/ifthenpaylib/lib/Log/logs/mbway.log.',



	/* ---------------------------- Admin Ccard ---------------------------- */

	'ccard_key' => 'Credit Card Key',
	'msg_invalid_ccard_key' => 'Invalid Credit Card Key',
	'cancel_ccard' => 'Cancel Credit Card Order',
	'cancel_ccard_desc' => 'Enable to automatically cancel Credit Card orders if not paid 30 minutes after creation. Executes together with daily cron.',
	'msg_no_ccard_accounts_found' => 'No Credit Card accounts found for your Backoffice Key.',
	'msg_error_updating_ccard_database' => 'Error updating ccard database table, for more information check the file ccard at modules/gateways/ifthenpaylib/lib/Log/logs/ccard.log.',



	/* ---------------------------- Admin Cofidis ---------------------------- */

	'cofidis_key' => 'Cofidis Pay Key',
	'msg_invalid_cofidis_key' => 'Invalid Cofidis Pay Key',
	'cancel_cofidis' => 'Cancel Cofidis Pay Order',
	'cancel_cofidis_desc' => 'Enable to automatically cancel Cofidis Pay orders if not paid 60 minutes after creation. Executes together with daily cron.',
	'msg_no_cofidis_accounts_found' => 'No Cofidis accounts found for your Backoffice Key.',
	'msg_error_updating_cofidis_database' => 'Error updating cofidis database table, for more information check the file cofidis at modules/gateways/ifthenpaylib/lib/Log/logs/cofidis.log.',
	'msg_invalid_minimum_amount_ifthenpay' => 'Invalid minimum amount value, must be greater or equal than value defined in ifthenpay backoffice.',
	'msg_invalid_maximum_amount_ifthenpay' => 'Invalid minimum amount value, must be lesser or equal than value defined in ifthenpay backoffice.',



	/* ---------------------------- Admin ifthenpaygateway ---------------------------- */

	'ifthenpaygateway_key' => 'Gateway Key',
	'ifthenpaygateway_payment_methods' => 'Payment Methods',
	'ifthenpaygateway_default_payment_method' => 'Default Payment Method',
	'show_icon_on_default' => 'ON - show default icon',
	'show_icon_on_composite_image' => 'ON - show composite icon',
	'show_icon_off_method_name' => 'OFF - show method title',
	'ifthenpaygateway_deadline_desc' => 'Input the days to deadline, between 1 and 99 days. Leave empty to never expire.',
	'close_btn_label' => 'Gateway Close Button Text',
	'close_btn_label_desc' => 'Replaces the return button text in the gateway page. Leave empty to use default.',
	'ifthenpaygateway_description' => 'Description',
	'ifthenpaygateway_description_desc' => 'Description displayed in the ifthenpay gateway page under the amount.',
	'btn_request_gateway_method' => 'Request Gateway Method',
	'ifthenpaygateway_select_a_gateway_key' => 'Please select a Ifthenpay Gateway key to view this field.',
	'msg_invalid_ifthenpaygateway_key' => 'Invalid Ifthenpay Gateway Key',
	'cancel_ifthenpaygateway' => 'Cancel Ifthenpay Gateway Order',
	'cancel_ifthenpaygateway_desc' => 'Enable to automatically cancel Ifthenpay Gateway orders if link has expired. Executes together with daily cron.',
	'msg_no_ifthenpaygateway_accounts_found' => 'No Ifthenpay Gateway accounts found for your Backoffice Key.',
	'msg_request_new_gateway_method' => 'Do you wish to request a {%method%} Gateway Method?',
	'msg_error_updating_ifthenpaygateway_database' => 'Error updating ifthenpaygateway database table, for more information check the file ifthenpaygateway at modules/gateways/ifthenpaylib/lib/Log/logs/ifthenpaygateway.log.',
	'msg_invalid_ifthenpaygateway_description' => 'Invalid Description, can not have more than 200 characters.',
	'msg_invalid_ifthenpaygateway_close_btn' => 'Invalid Close Button Label, can not have more than 50 characters.',


	/* ---------------------------- Admin Pix ---------------------------- */

	'pix_key' => 'Pix Key',
	'msg_invalid_pix_key' => 'Invalid Pix Key',
	'cancel_pix' => 'Cancel Pix Order',
	'cancel_pix_desc' => 'Enable to automatically cancel Pix orders if not paid 30 minutes after creation. Executes together with daily cron.',
	'msg_no_pix_accounts_found' => 'No Pix accounts found for your Backoffice Key.',
	'msg_error_updating_pix_database' => 'Error updating pix database table, for more information check the file pix at modules/gateways/ifthenpaylib/lib/Log/logs/pix.log.',



	/* -------------------------- Front Payment Details e forms ------------------------- */

	'multibanco' => 'Multibanco',
	'mbway' => 'MB WAY',
	'payshop' => 'Payshop',
	'ccard' => 'Credit Card',
	'cofidis' => 'Cofidis Pay',
	'pix' => 'Pix',
	'ifthenpaygateway' => 'Ifthenpay Gateway',

	'pay_btn' => 'Pay',
	'pay' => 'Pay',
	'pay_by' => 'Pay by',
	'pay_with' => 'Pay with',
	'entity_label' => 'Entity',
	'reference_label' => 'Reference',
	'deadline_label' => 'Deadline',
	'amount_label' => 'Amount',
	'payment_process_completed' => 'Payment process completed.',
	'wait_for_payment_verification' => 'Verification is in progress.',



	/* --------------------------- Front Payment cofidis -------------------------- */

	'cofidis_desc_line_1' => 'COFIDIS PAY',
	'cofidis_desc_line_2' => ' - up to 12 interest-free installments',
	'cofidis_desc_line_3' => 'You will be redirected to a secure page to make the payment.',
	'cofidis_desc_line_4' => 'Pay for your order in installments without interest or charges using your debit or credit card.',
	'cofidis_desc_line_5' => 'Payment of installments will be made to the customer\'s debit or credit card through a payment solution based on a factoring contract between Cofidis and the Merchant. Find out more at Cofidis, registered with Banco de Portugal under number 921.',

	

	/* --------------------------- Front Payment mbway -------------------------- */

	'mobile_number' => 'Mobile Number',
	'msg_mbway_invalid_number' => 'Invalid MB WAY mobile number.',
	'mbway_status_payment_confirmed' => 'Payment confirmed!',
	'mbway_status_payment_rejected_by_user' => 'Payment rejected by user!',
	'mbway_status_payment_expired' => 'Payment expired!',
	'mbway_status_payment_declined' => 'Payment declined!',
	'mbway_status_payment_error' => 'Payment error!',
	'notification_sent' => 'A notification has been sent to your smartphone MB WAY app.',
	'resend_mbway_notification' => 'Resend notification!',



	/* --------------------------- Front Payment pix -------------------------- */

	'pix_name_label' => 'Name',
	'pix_cpf_label' => 'CPF',
	'pix_email_label' => 'Email',

	'msg_pix_invalid_name' => 'Invalid Name.',
	'msg_pix_invalid_cpf' => 'Invalid CPF.',
	'msg_pix_invalid_email' => 'Invalid Email.',

];
