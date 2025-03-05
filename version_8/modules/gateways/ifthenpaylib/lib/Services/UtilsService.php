<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpaylib\Services;


use WHMCS\Database\Capsule;
use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;

class UtilsService
{
	public static function getSystemUrl(): string
	{
		$systemUrl = Capsule::table('tblconfiguration')->where('setting', 'SystemURL')->pluck('value')[0];
		return $systemUrl ? $systemUrl : '';
	}



	public static function getUrl(string $urlPath): string
	{
		return self::getSystemUrl() . $urlPath;
	}



	public static function addCacheBuster(string $str): string
	{
		$querySeparator = strpos($str, '?') !== false ? '&' : '?';
		return $str . $querySeparator . 'v=' . str_replace('.', '', Config::MODULE_VERSION);
	}



	public static function getWHMCSVersion(): string
	{
		$version = Capsule::table('tblconfiguration')
			->where('setting', 'Version')
			->value('value');

		if (!$version) {
			$version = 'NA';
		}

		if (preg_match('/^\d+\.\d+\.\d+/', $version, $matches)) {
			$version = $matches[0];
		}

		return $version;
	}



	/**
	 * gets language by admin id in session defaults to english if none found
	 * @return string
	 */
	public static function getLanguage(): string
	{
		// if in admin backoffice
		if (isset($_SESSION['adminid'])) {
			$language = Capsule::table('tbladmins')
				->where('id', $_SESSION['adminid'])
				->value('language');

			if ($language) {
				$language = 'english';
			}
			return $language;
		}

		// if in front
		if (isset($_SESSION['Language']) && $_SESSION['Language'] != '') {
			return $_SESSION['Language'];
		}

		// default to english
		return 'english';
	}



	public static function dateTime(): string
	{
		$timezone = new \DateTimeZone('Europe/Lisbon');
		$dateTime = new \DateTime('now', $timezone);

		return $dateTime->format('Y-m-d H:i:s');
	}



	public static function dateAfterDays(string $numberOfDays, string $format = 'Y-m-d H:i:s'): string
	{
		if ($numberOfDays === '') {
			return '';
		}

		$timezone = new \DateTimeZone('Europe/Lisbon');
		$dateTime = new \DateTime('now', $timezone);
		$dateTime->modify("+$numberOfDays days");

		return $dateTime->format($format);
	}



	public static function generateTransactionId(string $invoiceId = ''): string
	{

		$transactionLength = 20;
		$randomString = bin2hex(random_bytes($transactionLength / 2));

		// If invoiceId is provided, replace the same number of characters
		if (!empty($invoiceId)) {
			$invoiceIdLength = strlen($invoiceId);
			$randomString = substr_replace($randomString, $invoiceId, -$invoiceIdLength);
		}

		return substr($randomString, 0, $transactionLength);
	}



	public static function pathToAssetImage(string $asset = '')
	{
		return '/modules/gateways/ifthenpaylib/assets/images/' . $asset;
	}



	public static function pathToAssetJs(string $asset = '')
	{
		return '/modules/gateways/ifthenpaylib/assets/js/' . $asset;
	}



	public static function pathToAssetCss(string $asset = '')
	{
		return '/modules/gateways/ifthenpaylib/assets/css/' . $asset;
	}



	public static function pathToAssetJson(string $asset = '')
	{
		return '/modules/gateways/ifthenpaylib/assets/json/' . $asset;
	}



	public static function getCountryCodesAsValueNameArray(string $lang): array
	{
		try {

			// Read JSON file contents to array
			$countryCodes = self::getCountryCodesFileContent();

			// get correct language key
			$lang = isset($countryCodes[0]['translations'][$lang]) ? $lang : 'en';

			$countryCodeOptions = [];
			foreach ($countryCodes as $country) {

				$countryCodeOptions[] = [
					'value' => $country['phone_code'],
					'name' => $country['iso2'] . ' +' . $country['phone_code'],
					'desc' => $country['translations'][$lang]
				];
			}

			return $countryCodeOptions;
		} catch (\Throwable $th) {
			IfthenpayLog::error('general_logs', 'Error getting country codes assoc array. ' . $th->__toString());
			return [];
		}
	}



	private static function getCountryCodesFileContent(): array
	{
		$jsonData = [];
		if (file_exists(ROOTDIR . self::pathToAssetJson('CountryCodes.json'))) {
			$jsonData = json_decode(file_get_contents(ROOTDIR . self::pathToAssetJson('CountryCodes.json')), true);
		}
		return $jsonData;
	}
}
