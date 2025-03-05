<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpaylib\Log;


use WHMCS\Module\Gateway\ifthenpaylib\Config\Config;

class IfthenpayLog
{

	public static function info(string $target, string $message, mixed $data = [])
	{
		if (Config::LOG_LEVEL >= Config::LOG_LEVEL_INFO) {
			self::log($target, 'INFO', $message, $data);
		}
	}

	public static function debug(string $target, string $message, mixed $data = [])
	{
		if (Config::LOG_LEVEL >= Config::LOG_LEVEL_DEBUG) {
			self::log($target, 'DEBUG', $message, $data);
		}
	}

	public static function error(string $target, string $message, mixed $data = [])
	{
		if (Config::LOG_LEVEL >= Config::LOG_LEVEL_ERROR) {
			self::log($target, 'ERROR', $message, $data);
		}
	}

	public static function warning(string $target, string $message, mixed $data = [])
	{
		if (Config::LOG_LEVEL >= Config::LOG_LEVEL_WARNING) {
			self::log($target, 'WARNING', $message, $data);
		}
	}

	public static function notice(string $target, string $message, mixed $data = [])
	{
		if (Config::LOG_LEVEL >= Config::LOG_LEVEL_NOTICE) {
			self::log($target, 'NOTICE', $message, $data);
		}
	}



	public static function log(string $target, string $level, string $message, mixed $data): void
	{
		$filePath = __DIR__ . "/logs/{$target}.log";
		$timeStamp = date('Y-m-d H:i:s');

		// Format the data (object or array) as JSON if it is an array or object
		if (is_array($data) || is_object($data)) {
			$data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		}

		$formattedMessage = "[$timeStamp] [$level] $message: $data" . PHP_EOL;


		if (!file_exists(dirname($filePath))) {
			mkdir(dirname($filePath), 0777, true);
		}

		file_put_contents($filePath, $formattedMessage, FILE_APPEND);
	}
}
