<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpaylib\Lang;

use WHMCS\Module\Gateway\ifthenpaylib\Services\UtilsService;
use WHMCS\Module\Gateway\ifthenpaylib\Log\IfthenpayLog;



class IftpLang
{

	private static $translations = [];

	private static function loadTranslations()
	{
		self::$translations['english'] = file_exists(__DIR__ . '/english.php') ? include_once __DIR__ . '/english.php' : [];
		self::$translations['portuguese'] = file_exists(__DIR__ . '/portuguese.php') ? include_once __DIR__ . '/portuguese.php' : [];
		self::$translations['spanish'] = file_exists(__DIR__ . '/spanish.php') ? include_once __DIR__ . '/spanish.php' : [];
	}



	private static function getLang(): string
	{
		$language = $_SESSION['language'] ?? UtilsService::getLanguage();

		switch ($language) {
			case 'portugues':
			case 'portuguese':
			case 'portuguese-br':
			case 'portuguese-pt':
				return 'portuguese';
			case 'espanol':
			case 'spanish':
				return 'spanish';
			default:
				// any other language
				return 'english';
		}


		return $language;
	}


	/**
	 * convert language to code, necessary to use with APIs
	 * @return string
	 */
	public static function getLangCode(): string
	{
		$lang = self::getLang();

		switch ($lang) {
			case 'portuguese':
				return 'pt';
			case 'spanish':
				return 'es';
			default:
				// any other language
				return 'en';
		}
	}



	public static function trans($key, array $params = []): string
	{
		try {
			// Load all translations only once
			if (empty(self::$translations)) {
				self::loadTranslations();
			}

			$lang = self::getLang();

			$translation = self::$translations[$lang][$key] ?? $key;

			if (!empty($params)) {
				foreach ($params as $key => $value) {
					$translation = str_replace("{%$key%}", $value, $translation);
				}
			}

			return $translation;
		} catch (\Throwable $th) {
			return $key;
			IfthenpayLog::error('general_logs', 'Unexpected error loading translation', $th->__toString());
		}
	}
}
