<?php

if (!defined("WHMCS")) {
	die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\ifthenpaylib\Lang\IftpLang;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;
use WHMCS\Module\Gateway\ifthenpaylib\Services\IfthenpayService;
use WHMCS\Module\Gateway\ifthenpaylib\Services\UtilsService;



add_hook('AdminAreaHeadOutput', 1, function ($vars) {

	try {
		// only loads on the payment gateway config form page
		if ($vars['filename'] === 'configgateways') {
			$cssFilePath = UtilsService::addCacheBuster(UtilsService::pathToAssetCss('adminStyles.css'));

			IfthenpayLog::info('general_logs', 'loading admin css files');

			return '<link rel="stylesheet" href="' . $cssFilePath . '">';
		}
	} catch (\Throwable $th) {
		IfthenpayLog::error('general_logs', 'Error loading admin css files', $th->__toString());
	}
});



add_hook('AdminAreaFooterOutput', 1, function ($vars) {

	try {
		// only loads on the payment gateway config form page
		if ($vars['filename'] === 'configgateways') {

			$jsFilePath = UtilsService::addCacheBuster(UtilsService::pathToAssetJs('adminConfigGateways.js'));

			$phpVars = [
				'msg_invalid_backoffice_key' => IftpLang::trans('msg_invalid_backoffice_key'),
				'msg_invalid_backoffice_key_example' => IftpLang::trans('msg_invalid_backoffice_key_example'),
				'msg_request_new_account' => IftpLang::trans('msg_request_new_account'),
				'multibanco_dynamic_reference' => IftpLang::trans('multibanco_dynamic_reference'),
				'msg_no_multibanco_accounts_found' => IftpLang::trans('msg_no_multibanco_accounts_found'),
				'msg_no_payshop_accounts_found' => IftpLang::trans('msg_no_payshop_accounts_found'),
				'msg_no_mbway_accounts_found' => IftpLang::trans('msg_no_mbway_accounts_found'),
				'msg_no_ccard_accounts_found' => IftpLang::trans('msg_no_ccard_accounts_found'),
				'msg_no_pix_accounts_found' => IftpLang::trans('msg_no_pix_accounts_found'),
				'msg_no_ifthenpaygateway_accounts_found' => IftpLang::trans('msg_no_ifthenpaygateway_accounts_found'),
				'msg_request_new_gateway_method' => IftpLang::trans('msg_request_new_gateway_method'),
				'msg_are_sure_reset_config' => IftpLang::trans('msg_are_sure_reset_config'),
			];

			$ifthenpayTranslations = 'var ifthenpaytranslations = ' . json_encode($phpVars);

			IfthenpayLog::info('general_logs', 'loading admin javascript files');
			return '<script type="module" src="' . $jsFilePath . '"></script>' .
				'<script>' . $ifthenpayTranslations . '</script>';
		}
	} catch (\Throwable $th) {
		IfthenpayLog::error('general_logs', 'Error loading admin javascript files', $th->__toString());
	}
});



add_hook('ClientAreaPageCart', 1, function ($vars) {

	if (isset($vars['gateways']) && isset($vars['rawtotal'])) {
		$vars['gateways'] = IfthenpayService::filterPaymentMethodsByAvailability($vars['gateways']);
		$vars['gateways'] = IfthenpayService::filterPaymentMethodsByMinMax($vars['gateways'], $vars['rawtotal']);
		return IfthenpayService::injectPaymentMethodLogos($vars['gateways']);
	}
});



add_hook('ClientAreaPageViewInvoice', 1, function ($vars) {

	if (isset($vars['availableGateways']) && isset($vars['model']) && isset($vars['model']['total'])) {

		$vars['availableGateways'] = IfthenpayService::filterPaymentMethodsByAvailability($vars['availableGateways']);
		$vars['availableGateways'] = IfthenpayService::filterPaymentMethodsByMinMax($vars['availableGateways'], $vars['model']['total']);
		return ["availableGateways" => $vars['availableGateways']];
	}
});



add_hook('DailyCronJob', 1, function ($vars) {

	IfthenpayService::cancelExpiredPayments();
});
