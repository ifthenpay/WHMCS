<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpaylib\Config;


final class Config
{
	public const MODULE_VERSION = '8.0.0';

	public const LOG_LEVEL = self::LOG_LEVEL_ERROR;
	public const LOG_LEVEL_INFO = 5;
	public const LOG_LEVEL_DEBUG = 4;
	public const LOG_LEVEL_NOTICE = 3;
	public const LOG_LEVEL_WARNING = 2;
	public const LOG_LEVEL_ERROR = 1;



	/* ------------------------------ module codes ------------------------------ */
	// PM_BOILERPLATE

	public const MULTIBANCO_MODULE_CODE = 'ifthenpaymultibanco';
	public const PAYSHOP_MODULE_CODE = 'ifthenpaypayshop';
	public const MBWAY_MODULE_CODE = 'ifthenpaymbway';
	public const CCARD_MODULE_CODE = 'ifthenpayccard';
	public const COFIDIS_MODULE_CODE = 'ifthenpaycofidis';
	public const PIX_MODULE_CODE = 'ifthenpaypix';
	public const IFTHENPAYGATEWAY_MODULE_CODE = 'ifthenpaygateway';



	/* ----------------------------- payment methods ---------------------------- */
	// PM_BOILERPLATE

	public const MULTIBANCO = 'multibanco';
	public const MULTIBANCO_DYNAMIC = 'MB';
	public const MULTIBANCO_NAME = 'Multibanco';

	public const PAYSHOP = 'payshop';
	public const PAYSHOP_NAME = 'Payshop';

	public const MBWAY = 'mbway';
	public const MBWAY_NAME = 'MB WAY';

	public const CCARD = 'ccard';
	public const CCARD_NAME = 'Credit Card';

	public const COFIDIS = 'cofidis';
	public const COFIDIS_NAME = 'Cofidis Pay';

	public const PIX = 'pix';
	public const PIX_NAME = 'Pix';

	public const IFTHENPAYGATEWAY = 'ifthenpaygateway';
	public const IFTHENPAYGATEWAY_NAME = 'Ifthenpay Gateway';

	public const PAYMENT_METHODS_ARRAY = [
		Self::MULTIBANCO_MODULE_CODE,
		Self::PAYSHOP_MODULE_CODE,
		Self::MBWAY_MODULE_CODE,
		Self::CCARD_MODULE_CODE,
		Self::COFIDIS_MODULE_CODE,
		Self::PIX_MODULE_CODE,
		Self::IFTHENPAYGATEWAY_MODULE_CODE
	];



	/* ----------------------------- invoice status ----------------------------- */

	public const INVOICE_STATUS_PENDING = 'Pending';
	public const INVOICE_STATUS_CANCELLED = 'Cancelled';



	/* ------------------------------ record status ----------------------------- */

	public const RECORD_STATUS_INITIALIZED = 'initialized';
	public const RECORD_STATUS_PENDING = 'pending';
	public const RECORD_STATUS_PAID = 'paid';
	public const RECORD_STATUS_CANCELLED = 'cancelled';
	public const RECORD_STATUS_ERROR = 'error';



	/* ----------------------------- database tables ---------------------------- */
	// PM_BOILERPLATE

	public const MULTIBANCO_TABLE = 'ifthenpay_' . self::MULTIBANCO;
	public const PAYSHOP_TABLE = 'ifthenpay_' . self::PAYSHOP;
	public const MBWAY_TABLE = 'ifthenpay_' . self::MBWAY;
	public const CCARD_TABLE = 'ifthenpay_' . self::CCARD;
	public const COFIDIS_TABLE = 'ifthenpay_' . self::COFIDIS;
	public const PIX_TABLE = 'ifthenpay_' . self::PIX;
	public const IFTHENPAYGATEWAY_TABLE = 'ifthenpay_gateway';



	/* -------------------------- common config values -------------------------- */

	public const CF_BACKOFFICE_KEY = 'backofficeKey';
	public const CF_SHOWICON = 'showIcon';
	public const CF_CAN_CANCEL = 'canCancel';
	public const CF_ACCOUNTS = 'accounts';
	public const CF_ANTIPHISHING_KEY = 'antiphishingKey';
	public const CF_CALLBACK_URL = 'callbackUrl';
	public const CF_CALLBACK_STATUS = 'callbackStatus';
	public const CF_CAN_ACTIVATE_CALLBACK = 'canActivateCallback';
	public const CF_MIN_AMOUNT = 'minAmount';
	public const CF_MAX_AMOUNT = 'maxAmount';
	public const CF_INSTALLED_MODULE_VERSION = 'installedModuleVersion';
	public const CF_DEADLINE = 'deadline';
	public const CF_UPGRADE = 'upgrade'; // not an actual config, just an html block for displaying the version
	public const CF_CALLBACK_INFO = 'callbackInfo'; // not an actual config, just an html block for displaying the callback status



	/* ------------------------ multibanco config values ------------------------ */

	public const CF_MULTIBANCO_ENTITY = 'entity';
	public const CF_MULTIBANCO_SUBENTITY = 'subentity';



	/* ------------------------ payshop config values ------------------------ */

	public const CF_PAYSHOP_KEY = 'key';



	/* ------------------------ mbway config values ------------------------ */

	public const CF_MBWAY_KEY = 'key';
	public const CF_MBWAY_SHOW_COUNTDOWN = 'showCountdown';
	public const CF_MBWAY_NOTIFICATION_DESCRIPTION = 'notificationDescription';



	/* ------------------------ ccard config values ------------------------ */

	public const CF_CCARD_KEY = 'key';



	/* ------------------------ cofidis config values ------------------------ */

	public const CF_COFIDIS_KEY = 'key';

	// Cofidis status
	public const COFIDIS_STATUS_INITIATED = 'INITIATED';  // (pending)
	public const COFIDIS_STATUS_CANCELED = 'CANCELED'; // 1 (canceled)
	public const COFIDIS_STATUS_PENDING_INVOICE = 'PENDING_INVOICE'; // approved but must wait validation, can still fail (pending)
	public const COFIDIS_STATUS_NOT_APPROVED = 'NOT_APPROVED'; // (after pending invoice) (failed)
	public const COFIDIS_STATUS_FINANCED = 'FINANCED'; // (after pending invoice) this means installment contract is accepted (processed)
	public const COFIDIS_STATUS_EXPIRED = 'EXPIRED'; // (expired)
	public const COFIDIS_STATUS_TECHNICAL_ERROR = 'TECHNICAL_ERROR'; // (failed)


	/* ------------------------ pix config values ------------------------ */

	public const CF_PIX_KEY = 'key';



	/* ------------------------ ifthenpaygateway config values ------------------------ */

	public const CF_IFTHENPAYGATEWAY_KEY = 'key';
	public const CF_IFTHENPAYGATEWAY_PAYMENT_METHODS = 'paymentMethods';
	public const CF_IFTHENPAYGATEWAY_DEFAULT_PAYMENT = 'defaultPaymentMethod';
	public const CF_IFTHENPAYGATEWAY_CLOSE_BTN_LABEL = 'closeBtnLabel';
	public const CF_IFTHENPAYGATEWAY_DESCRIPTION = 'description';	
	public const CF_IFTHENPAYGATEWAY_FRONT_ICON = 'frontIcon';
	public const CF_IFTHENPAYGATEWAY_GATEWAY_PAYMENT_METHODS = 'gatewayPaymentMethods';



	// PM_BOILERPLATE



	/* --------------------------- mbway status codes --------------------------- */

	public const MBWAY_STATUS_CODE_PENDING = '123'; // Transaction pending payment,
	public const MBWAY_STATUS_CODE_PAID = '000'; // Transaction successfully completed (Payment confirmed),
	public const MBWAY_STATUS_CODE_REJECTED_BY_USER = '020'; // Transaction rejected by the user.
	public const MBWAY_STATUS_CODE_EXPIRED = '101'; // Transaction expired (the user has 4 minutes to accept the payment in the MB WAY App before expiring).
	public const MBWAY_STATUS_CODE_DECLINED = '122'; // Transaction declined to the user.
	public const MBWAY_STATUS_CODE_FAIL = '9999'; // not related to api, its used to pass that something went wrong.



	/* -------------------------- callback error codes -------------------------- */

	public const CB_ERROR_INVALID_PARAMS = 5;
	public const CB_ERROR_RECORD_NOT_FOUND = 10;
	public const CB_ERROR_INVALID_PAYMENT_METHOD = 20;
	public const CB_ERROR_CALLBACK_NOT_ACTIVE = 30;
	public const CB_ERROR_INVALID_ANTIPHISHING_KEY = 40;
	public const CB_ERROR_ORDER_NOT_FOUND = 50;
	public const CB_ERROR_INVALID_AMOUNT = 60;
	public const CB_ERROR_UNCONFIGURED_METHOD = 70;
	public const CB_ERROR_INVALID_SECRET = 80;
	public const CB_ERROR_ALREADY_PAID = 90;



	/* ----------------------------- callback params ---------------------------- */

	public const CB_ECOMMERCE = 'ec';
	public const CB_MODULE_VERSION = 'mv';
	public const CB_PAYMENT_METHOD = 'pm';
	public const CB_ANTIPHISHING_KEY = 'apk';
	public const CB_ORDER_ID = 'oid';
	public const CB_ENTITY = 'ent';
	public const CB_REFERENCE = 'ref';
	public const CB_TRANSACTION_ID = 'tid';
	public const CB_AMOUNT = 'val';
	public const CB_FEE = 'fee';
	public const USE_FEE = false;



	/* ------------------------- callback Urls subString ------------------------ */
	// PM_BOILERPLATE

	public const MULTIBANCO_CALLBACK_STRING = '?ec={ec}&mv={mv}&apk=[ANTI_PHISHING_KEY]&oid=[ID]&ent=[ENTITY]&ref=[REFERENCE]&val=[AMOUNT]&fee=[FEE]&pm=[PAYMENT_METHOD]';
	public const PAYSHOP_CALLBACK_STRING = '?ec={ec}&mv={mv}&apk=[ANTI_PHISHING_KEY]&oid=[ID]&tid=[REQUEST_ID]&ref=[REFERENCE]&val=[AMOUNT]&fee=[FEE]&pm=[PAYMENT_METHOD]';
	public const MBWAY_CALLBACK_STRING = '?ec={ec}&mv={mv}&apk=[ANTI_PHISHING_KEY]&oid=[ID]&tid=[REQUEST_ID]&val=[AMOUNT]&fee=[FEE]&pm=[PAYMENT_METHOD]';
	public const COFIDIS_CALLBACK_STRING = '?ec={ec}&mv={mv}&apk=[ANTI_PHISHING_KEY]&oid=[ID]&tid=[REQUEST_ID]&val=[AMOUNT]&fee=[FEE]&pm=[PAYMENT_METHOD]';
	public const PIX_CALLBACK_STRING = '?ec={ec}&mv={mv}&apk=[ANTI_PHISHING_KEY]&oid=[ID]&tid=[REQUEST_ID]&val=[AMOUNT]&fee=[FEE]&pm=[PAYMENT_METHOD]';
	public const IFTHENPAYGATEWAY_CALLBACK_STRING = '?ec={ec}&mv={mv}&apk=[ANTI_PHISHING_KEY]&oid=[ID]&ent=[ENTITY]&ref=[REFERENCE]&tid=[REQUEST_ID]&val=[AMOUNT]&fee=[FEE]&pm=[PAYMENT_METHOD]';



	/* ------------------------ whmcs custom controllers ------------------------ */

	public const COFIDIS_RETURN_URL_STRING = 'modules/gateways/ifthenpaylib/controllers/returnfromcofidis.php?order_id=[ORDER_ID]';
	public const PIX_RETURN_URL_STRING = 'modules/gateways/ifthenpaylib/controllers/returnfrompix.php?order_id=[ORDER_ID]';



	/* --------------------------- ifthenpay API URLs --------------------------- */
	// PM_BOILERPLATE

	public const API_URL_GET_LATEST_VERSION = 'https://ifthenpay.com/modulesUpgrade/whmcs/upgrade.json';

	public const API_URL_GET_ACCOUNTS_BY_BACKOFFICE = 'https://www.ifthenpay.com/IfmbWS/ifmbws.asmx/getEntidadeSubentidadeJsonV2';

	public const API_URL_GET_GATEWAY_KEYS = 'https://ifthenpay.com/IfmbWS/ifthenpaymobile.asmx/GetGatewayKeys';

	public const API_URL_ACTIVATE_CALLBACK = 'https://api.ifthenpay.com/endpoint/callback/activation';

	public const API_URL_MULTIBANCO_DYNAMIC_SET_REQUEST = 'https://api.ifthenpay.com/multibanco/reference/init';

	public const API_URL_PAYSHOP_SET_REQUEST = 'https://ifthenpay.com/api/payshop/reference/';

	public const API_URL_MBWAY_SET_REQUEST = 'https://api.ifthenpay.com/spg/payment/mbway';

	public const API_URL_CCARD_SET_REQUEST = 'https://api.ifthenpay.com/creditcard/init/';

	public const API_URL_COFIDIS_SET_REQUEST = 'https://api.ifthenpay.com/cofidis/init/';

	public const API_URL_GET_MBWAY_STATUS = 'https://api.ifthenpay.com/spg/payment/mbway/status';

	public const API_URL_COFIDIS_GET_MAX_MIN_AMOUNT = 'https://ifthenpay.com/api/cofidis/limits/';

	public const API_URL_COFIDIS_GET_PAYMENT_STATUS = 'https://ifthenpay.com/api/cofidis/status';

	public const API_URL_IFTHENPAYGATEWAY_SET_REQUEST = 'https://api.ifthenpay.com/gateway/pinpay/';

	public const API_URL_GET_IFTHENPAY_AVAILABLE_METHODS = 'https://api.ifthenpay.com/gateway/methods/available';

	public const API_URL_ACCOUNTS_OF_GATEWAY_KEY = 'https://ifthenpay.com/IfmbWS/ifthenpaymobile.asmx/GetAccountsByGatewayKey';

	public const API_URL_PIX_SET_REQUEST = 'https://api.ifthenpay.com/pix/init/';



	/* --------------------------- ifthenpay Contacts --------------------------- */

	public const CONTACT_EMAIL_SUPPORT = 'suporte@ifthenpay.com';
}
